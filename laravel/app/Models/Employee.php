<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Employee extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['name', 'jabatan', 'group_id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
