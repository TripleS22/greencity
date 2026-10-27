<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Group extends Model
{
    use LogsActivity;

    protected $fillable = ['name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
