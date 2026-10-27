<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DBR extends Model
{
    use HasFactory;

    protected $table = 'dbr';

    const STATUS_RFU = 'RFU';
    const STATUS_BREAKDOWN = 'Breakdown';

    /**
     * Flag to skip inventory sync (used when SparePartRequest already handles it).
     */
    public bool $skipInventorySync = false;

    protected static function booted()
    {
        static::saving(function ($dbr) {
            // Automatically update duration_db string before saving
            // We use the accessor value to set the actual attribute
            $dbr->attributes['duration_db'] = $dbr->duration_db;

            // Automatically calculate total
            $dbr->total = ($dbr->sales_sparepart ?? 0) + ($dbr->sales_service ?? 0);

            // Remarks logic
            $status = trim(strtolower($dbr->unit_status ?? ''));
            
            // Check approval status directly for better reliability during saving
            $isApproved = false;
            if ($dbr->mechanic_analysis_id) {
                $isApproved = \App\Models\MechanicReport::where('mechanic_analysis_id', $dbr->mechanic_analysis_id)
                    ->where('status_approval', 'Approved')
                    ->exists();
            }

            if ($status === strtolower(self::STATUS_BREAKDOWN) && !$isApproved) {
                $dbr->remarks = 'Continue';
            } elseif ($status === strtolower(self::STATUS_RFU) || $isApproved) {
                $dbr->remarks = null;
            }
        });

        static::saved(function ($dbr) {
            // Always force sync unit_status to Population on every save to ensure dashboard accuracy
            if ($dbr->unit_code && $dbr->unit_status) {
                \App\Models\Population::where('unit_code', $dbr->unit_code)
                    ->update(['unit_status' => $dbr->unit_status]);
            }

            // Sync spare_parts changes to Inventory
            // We compare original and current to be absolutely sure we catch changes
            if (!$dbr->skipInventorySync) {
                $oldParts = collect($dbr->getOriginal('spare_parts') ?? []);
                $newParts = collect($dbr->spare_parts ?? []);

                // If both are empty and no changes needed, skip
                if ($oldParts->isEmpty() && $newParts->isEmpty()) {
                    return;
                }

                // Build qty maps for comparison
                $oldQtyMap = $oldParts->groupBy('component')->map(fn ($items) => $items->sum('qty'));
                $newQtyMap = $newParts->groupBy('component')->map(fn ($items) => $items->sum('qty'));

                $allComponents = $oldQtyMap->keys()->merge($newQtyMap->keys())->unique();

                foreach ($allComponents as $componentName) {
                    if (!$componentName) continue;

                    $oldQty = (int) ($oldQtyMap[$componentName] ?? 0);
                    $newQty = (int) ($newQtyMap[$componentName] ?? 0);
                    $delta = $newQty - $oldQty;

                    if ($delta === 0) continue;

                    $inventory = \App\Models\Inventory::where('item_sparepart', $componentName)->first();
                    if (!$inventory) continue;

                    $unitCode = $dbr->unit_code ?? 'unknown';

                    if ($delta > 0) {
                        $inventory->keterangan = "Pemakaian spare part dari DBR untuk unit {$unitCode}";
                        $inventory->out = ($inventory->out ?? 0) + $delta;
                    } else {
                        $inventory->keterangan = "Pengembalian spare part dari DBR untuk unit {$unitCode}";
                        $inventory->return_out = ($inventory->return_out ?? 0) + abs($delta);
                    }

                    $inventory->save();
                }
            }
        });
    }

    protected $fillable = [
        'mechanic_analysis_id',
        'date',
        'week',
        'eqp_type',
        'unit_code',
        'brand',
        'model',
        'shift_down',
        'location_unit',
        'category_bd',
        'date_bd',
        'time_start_bd',
        'smu_down',
        'time_start',
        'time_finish',
        'smu_ready',
        'duration_db',
        'project',
        'status_code',
        'mtc_type',
        'status_bd',
        'problem_reported',
        'activity',
        'unit_status',
        'remarks',
        'count_mtc_type',
        'task_down',
        'nama_driver',
        'nama_mechanic_satu',
        'nama_mechanic_dua',
        'component',
        'qty',
        'unit',
        'purchase_price',
        'price_per_pcs',
        'amount',
        'service_charge',
        'total',
        'sales_sparepart',
        'sales_service',
        'spare_parts',
    ];

    protected $casts = [
        'spare_parts' => 'array',
        'date' => 'date',
        'date_bd' => 'date',
        'purchase_price' => 'decimal:4',
        'price_per_pcs' => 'decimal:4',
        'amount' => 'decimal:4',
        'service_charge' => 'decimal:4',
        'total' => 'decimal:4',
        'sales_sparepart' => 'decimal:4',
        'sales_service' => 'decimal:4',
    ];

    /**
     * Get the duration calculation in Indonesian format.
     */
    public function getDurationDbAttribute()
    {
        if (!$this->time_start || !$this->time_finish || !$this->date || !$this->date_bd) {
            return '-';
        }

        try {
            $start = \Illuminate\Support\Carbon::parse($this->date_bd->format('Y-m-d') . ' ' . $this->time_start);
            $finish = \Illuminate\Support\Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->time_finish);

            if ($finish->lt($start)) {
                return '-';
            }

            $diff = $start->diff($finish);

            $parts = [];
            if ($diff->d > 0) $parts[] = $diff->d . ' Hari';
            if ($diff->h > 0) $parts[] = $diff->h . ' Jam';
            if ($diff->i > 0) $parts[] = $diff->i . ' Menit';
            if ($diff->s > 0 && empty($parts)) $parts[] = $diff->s . ' Detik';

            return empty($parts) ? '0 Menit' : implode(' ', $parts);
        } catch (\Exception $e) {
            return '-';
        }
    }

    /**
     * Get the duration calculation in hours as a float.
     */
    public function getDurationHoursAttribute()
    {
        if (!$this->time_start || !$this->time_finish || !$this->date || !$this->date_bd) {
            return 0;
        }

        try {
            $start = \Illuminate\Support\Carbon::parse($this->date_bd->format('Y-m-d') . ' ' . $this->time_start);
            $finish = \Illuminate\Support\Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->time_finish);

            if ($finish->lt($start)) {
                return 0;
            }

            return $start->diffInMinutes($finish) / 60;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get the unit associated with the report.
     */
    public function unit()
    {
        return $this->belongsTo(Population::class, 'unit_code', 'unit_code');
    }

    /**
     * Get the population associated with the report.
     */
    public function population()
    {
        return $this->belongsTo(Population::class, 'unit_code', 'unit_code');
    }

    /**
     * Get the mechanic analysis associated with the DBR.
     */
    public function mechanicAnalysis()
    {
        return $this->belongsTo(MechanicAnalysis::class);
    }

    /**
     * Get the mechanic report through mechanic analysis.
     */
    public function mechanicReport()
    {
        return $this->hasOne(MechanicReport::class, 'mechanic_analysis_id', 'mechanic_analysis_id');
    }
}
