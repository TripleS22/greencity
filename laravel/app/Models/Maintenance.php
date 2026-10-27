<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Maintenance extends Model
{
    use LogsActivity;

    protected $fillable = ['location_id', 'category', 'photo', 'description', 'date'];

    protected $casts = [
        'date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
