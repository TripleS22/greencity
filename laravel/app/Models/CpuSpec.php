<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpuSpec extends Model
{
    protected $fillable = ['spec'];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'cpu_id');
    }
}
