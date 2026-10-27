<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Population extends Model
{
    use HasFactory;

    protected $table = 'population';

    const STATUS_RFU = 'RFU';
    const STATUS_BREAKDOWN = 'Breakdown';
    const STATUS_POP = 'POP';

    public static function getStatusCounts(): array
    {
        // Get status counts from Population table
        $counts = self::query()
            ->selectRaw("LOWER(TRIM(unit_status)) as status, count(*) as count")
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $rfu = $counts[strtolower(self::STATUS_RFU)] ?? 0;
        $bd = $counts[strtolower(self::STATUS_BREAKDOWN)] ?? 0;
        $total = array_sum($counts);

        // Get BD Major/Minor classification from DBR table (category_bd column)
        $bdCategories = \App\Models\DBR::query()
            ->selectRaw("LOWER(TRIM(category_bd)) as category, count(DISTINCT unit_code) as count")
            ->whereRaw("LOWER(TRIM(unit_status)) = ?", [strtolower(self::STATUS_BREAKDOWN)])
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        $bdMajor = $bdCategories['bd major'] ?? 0;
        $bdMinor = $bdCategories['bd minor'] ?? 0;

        return [
            'rfu' => $rfu,
            'breakdown' => $bd,
            'bd_major' => $bdMajor,
            'bd_minor' => $bdMinor,
            'total' => $total,
            'other' => $total - $rfu - $bd,
        ];
    }

    protected static function booted()
    {
        static::created(function ($population) {
            // Automatically register to periodic service monitoring
            \App\Models\PeriodicService::firstOrCreate(
                ['unit_code' => $population->unit_code],
                [
                    'service_interval_km' => 8000,
                    'average_daily_ritase' => 0
                ]
            );
        });
    }

    protected $fillable = [
        'unit_code',
        'unit_status',
        'eqp_type',
        'model',
        'brand',
        'eqp_class',
        'machine_s_n',
        'engine_s_n',
        'activity',
        'delivery_date',
        'warranty_status',
        'year',
        'project',
        'owner',
        'maintenance_by',
        'loc_unit',
        'active',
    ];

    /**
     * Get the breakdowns for the unit.
     */
    public function dbrs()
    {
        return $this->hasMany(DBR::class, 'unit_code', 'unit_code');
    }

    /**
     * Get the driver reports for the unit.
     */
    public function driverReports()
    {
        return $this->hasMany(DriverReport::class, 'unit_code', 'unit_code');
    }

    /**
     * Get the mechanic reports for the unit.
     */
    public function mechanicReports()
    {
        return $this->hasMany(MechanicReport::class, 'unit_code', 'unit_code');
    }

    /**
     * Get the periodic service monitoring for the unit.
     */
    public function periodicService()
    {
        return $this->hasOne(PeriodicService::class, 'unit_code', 'unit_code');
    }
}
