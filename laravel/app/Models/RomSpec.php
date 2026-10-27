<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RomSpec extends Model
{
    protected $fillable = ['spec'];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'rom_id');
    }
}
