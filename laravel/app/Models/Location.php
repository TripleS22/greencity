<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Location extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'outlet_code', 'address'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
