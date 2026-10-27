<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Brand extends Model
{
    use LogsActivity;

    protected $fillable = ['name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function deviceModels()
    {
        return $this->hasMany(DeviceModel::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
