<?php

namespace App\Console\Commands;

use App\Models\DBR;
use App\Models\Inventory;
use App\Models\Population;
use Illuminate\Console\Command;

class SyncDbrToInventory extends Command
{
    protected $signature = 'sync:dbr-inventory {--dry-run : Show what would change without making changes}';
    protected $description = 'Sync existing DBR spare_parts data to Inventory stock and unit_status to Population';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE — No changes will be made.');
        }

        $this->info('');
        $this->info('========================================');
        $this->info(' STEP 1: Sync unit_status DBR → Population');
        $this->info('========================================');

        $dbrs = DBR::select('unit_code', 'unit_status')
            ->whereNotNull('unit_code')
            ->whereNotNull('unit_status')
            ->orderBy('date', 'desc')
            ->get()
            ->unique('unit_code'); // Latest status per unit

        foreach ($dbrs as $dbr) {
            $population = Population::where('unit_code', $dbr->unit_code)->first();
            if (!$population) {
                $this->warn("  ⚠ Population not found for unit: {$dbr->unit_code}");
                continue;
            }

            $oldStatus = $population->unit_status;
            $newStatus = $dbr->unit_status;

            if (strtolower(trim($oldStatus)) !== strtolower(trim($newStatus))) {
                $this->line("  📝 {$dbr->unit_code}: '{$oldStatus}' → '{$newStatus}'");
                if (!$dryRun) {
                    $population->update(['unit_status' => $newStatus]);
                }
            } else {
                $this->line("  ✓ {$dbr->unit_code}: already '{$oldStatus}' (OK)");
            }
        }

        $this->info('');
        $this->info('========================================');
        $this->info(' STEP 2: Sync spare_parts DBR → Inventory');
        $this->info('========================================');

        // Collect total spare part usage from ALL DBR records
        $dbrsWithParts = DBR::whereNotNull('spare_parts')->get();
        $this->info("  Found {$dbrsWithParts->count()} DBR records with spare parts.");

        // Build total usage map: component_name => total_qty_used_in_dbr
        $dbrUsageMap = [];
        foreach ($dbrsWithParts as $dbr) {
            $parts = $dbr->spare_parts;
            if (!is_array($parts)) continue;

            foreach ($parts as $part) {
                $component = $part['component'] ?? null;
                $qty = (int) ($part['qty'] ?? 0);
                if (!$component || $qty <= 0) continue;

                $dbrUsageMap[$component] = ($dbrUsageMap[$component] ?? 0) + $qty;
            }
        }

        if (empty($dbrUsageMap)) {
            $this->info('  No spare parts found in any DBR. Nothing to sync.');
            return 0;
        }

        $this->info('');
        $this->info('  DBR Total Usage Summary:');
        $this->table(
            ['Spare Part', 'Total Qty in DBR'],
            collect($dbrUsageMap)->map(fn ($qty, $name) => [$name, $qty])->values()->toArray()
        );

        $this->info('');
        $this->info('  Comparing with current Inventory...');
        $this->info('');

        $changes = [];

        foreach ($dbrUsageMap as $componentName => $totalDbrQty) {
            $inventory = Inventory::where('item_sparepart', $componentName)->first();

            if (!$inventory) {
                $this->warn("  ⚠ Inventory not found: '{$componentName}' — SKIPPED");
                continue;
            }

            $currentOut = (int) ($inventory->out ?? 0);

            // The DBR says total usage is $totalDbrQty
            // If inventory.out is already >= totalDbrQty, it's been accounted for
            // Otherwise, we need to add the difference
            $deficit = $totalDbrQty - $currentOut;

            if ($deficit <= 0) {
                $this->line("  ✓ {$componentName}: out={$currentOut}, DBR needs={$totalDbrQty} → Already sufficient");
                continue;
            }

            $changes[] = [
                'name' => $componentName,
                'inventory_id' => $inventory->id,
                'current_out' => $currentOut,
                'dbr_total' => $totalDbrQty,
                'deficit' => $deficit,
            ];

            $this->line("  📝 {$componentName}: out={$currentOut} → needs +{$deficit} (DBR total={$totalDbrQty})");
        }

        if (empty($changes)) {
            $this->info('');
            $this->info('  ✅ All inventory already in sync. No changes needed!');
            return 0;
        }

        $this->info('');
        $this->info('  Summary of changes to apply:');
        $this->table(
            ['Spare Part', 'Current Out', 'DBR Total', 'To Add'],
            collect($changes)->map(fn ($c) => [$c['name'], $c['current_out'], $c['dbr_total'], '+' . $c['deficit']])->toArray()
        );

        if ($dryRun) {
            $this->warn('');
            $this->warn('  DRY RUN — No changes applied. Run without --dry-run to apply.');
            return 0;
        }

        if (!$this->confirm('Apply these changes to the Inventory?')) {
            $this->warn('  Cancelled.');
            return 0;
        }

        foreach ($changes as $change) {
            $inventory = Inventory::find($change['inventory_id']);
            $inventory->keterangan = 'Sync dari DBR spare_parts (historical)';
            $inventory->out = ($inventory->out ?? 0) + $change['deficit'];
            $inventory->save();
            $this->info("  ✅ {$change['name']}: out updated (+{$change['deficit']})");
        }

        $this->info('');
        $this->info('🎉 Sync completed successfully!');
        return 0;
    }
}
