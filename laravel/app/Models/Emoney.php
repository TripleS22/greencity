<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Emoney extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'tanggal_diberikan',
        'nomor_body',
        'nomor_kartu',
        'masa_aktif',
        'status',
        'keterangan',
        'location_id',
        'employee_id'
    ];

    protected $casts = [
        'tanggal_diberikan' => 'date',
        'masa_aktif' => 'date',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
}
