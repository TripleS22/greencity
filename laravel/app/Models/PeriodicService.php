<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodicService extends Model
{
    protected $fillable = [
        'unit_code',
        'last_service_date',
        'last_service_smu',
        'service_interval_km',
        'average_daily_ritase',
        'last_notified_at',
    ];

    protected $casts = [
        'last_service_date' => 'date',
        'last_notified_at' => 'datetime',
    ];

    public function population()
    {
        return $this->belongsTo(Population::class, 'unit_code', 'unit_code');
    }

    public function project_data()
    {
        return $this->hasOneThrough(
            Project::class,
            Population::class,
            'unit_code', 
            'name',      
            'unit_code', 
            'project'    
        );
    }
}
