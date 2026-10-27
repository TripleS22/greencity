<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    use HasFactory;

    protected $table = 'warga';

    protected $fillable = [
        'nama',
        'unit',
        'telepon',
        'email',
        'alamat',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }
}
