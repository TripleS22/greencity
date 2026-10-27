<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DeviceModel extends Model
{
    use LogsActivity;

    protected $table = 'device_models';
    protected $fillable = ['brand_id', 'name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'device_model_id');
    }
}
