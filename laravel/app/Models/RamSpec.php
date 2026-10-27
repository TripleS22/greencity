<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RamSpec extends Model
{
    protected $fillable = ['spec'];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'ram_id');
    }
}
