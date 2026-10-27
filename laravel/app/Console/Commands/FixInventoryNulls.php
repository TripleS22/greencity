<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixInventoryNulls extends Command
{
    protected $signature = 'inventory:fix-nulls';
    protected $description = 'Fix NULL values in inventory numeric columns (set to 0)';

    public function handle()
    {
        $numericColumns = [
            'begins', 'in', 'out', 'return_in', 'return_out',
            'ends_balance', 'price_per_unit', 'selling_price',
            'service_charge', 'margin', 'pcs_ltl',
            'total_purchase_price', 'cogs', 'total_sales_price',
            'income', 'price_end_balance', 'price_begins_balance',
        ];

        $this->info('Fixing NULL values in inventory table...');
        $total = 0;

        foreach ($numericColumns as $col) {
            $count = DB::table('inventory')->whereNull($col)->update([$col => 0]);
            if ($count > 0) {
                $this->line("  Fixed <fg=yellow>{$col}</> = NULL → 0 : <fg=green>{$count} rows</>");
                $total += $count;
            }
        }

        $this->info("Done! Total fixed: {$total} values.");

        return self::SUCCESS;
    }
}
