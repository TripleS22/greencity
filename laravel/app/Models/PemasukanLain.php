<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemasukanLain extends Model
{
    protected $table = 'pemasukan_lains';
    protected $fillable = ['tanggal', 'keterangan', 'jumlah'];
    
    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2'
    ];
}
