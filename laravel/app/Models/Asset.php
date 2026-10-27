<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Asset extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'asset_type', 'code_asset', 'photo', 'description', 'access_code',
        'brand_id', 'device_model_id', 'location_id', 'group_id', 'employee_id',
        'ram_id', 'rom_id', 'cpu_id', 'vga_id',
    ];

    public const TYPES = [
        'hp' => 'HP (Handphone)',
        'desktop' => 'Desktop',
        'laptop' => 'Laptop',
        'monitor' => 'Monitor',
        'printer' => 'Printer',
        'printer_thermal' => 'Printer Thermal',
        'cctv' => 'CCTV',
        'modem' => 'Modem',
        'router' => 'Router',
        'tv' => 'TV',
    ];

    public const TYPE_CODES = [
        'hp' => 1, 'desktop' => 2, 'laptop' => 3, 'printer' => 4,
        'printer_thermal' => 5, 'cctv' => 6, 'modem' => 7, 'router' => 8,
        'keyboard' => 9, 'mouse' => 10, 'monitor' => 12,
        'tv' => 13,
    ];

    /**
     * Generate 9-digit asset code: OOO-TTT-SSS
     * OOO = outlet_code (3 digits), TTT = type code (3 digits), SSS = sequence (3 digits)
     */
    public static function generateCode(int $outletCode, string $assetType): string
    {
        $typeCode = self::TYPE_CODES[$assetType] ?? 0;
        $seq = self::where('asset_type', $assetType)
            ->whereHas('location', fn($q) => $q->where('outlet_code', $outletCode))
            ->count() + 1;

        return str_pad($outletCode, 3, '0', STR_PAD_LEFT)
             . str_pad($typeCode, 3, '0', STR_PAD_LEFT)
             . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public const TYPE_ICONS = [
        'hp' => 'fas fa-mobile-alt',
        'desktop' => 'fas fa-desktop',
        'laptop' => 'fas fa-laptop',
        'monitor' => 'fas fa-tv',
        'printer' => 'fas fa-print',
        'printer_thermal' => 'fas fa-receipt',
        'cctv' => 'fas fa-video',
        'modem' => 'fas fa-wifi',
        'router' => 'fas fa-network-wired',
        'tv' => 'fas fa-tv',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function deviceModel()
    {
        return $this->belongsTo(DeviceModel::class, 'device_model_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function ram()
    {
        return $this->belongsTo(RamSpec::class, 'ram_id');
    }

    public function rom()
    {
        return $this->belongsTo(RomSpec::class, 'rom_id');
    }

    public function cpu()
    {
        return $this->belongsTo(CpuSpec::class, 'cpu_id');
    }

    public function vga()
    {
        return $this->belongsTo(VgaSpec::class, 'vga_id');
    }
}
