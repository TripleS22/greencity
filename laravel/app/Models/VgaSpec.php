<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VgaSpec extends Model
{
    protected $fillable = ['spec'];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'vga_id');
    }
}
