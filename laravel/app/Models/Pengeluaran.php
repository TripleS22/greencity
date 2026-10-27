<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';

    protected $fillable = [
        'kategori_id',
        'keterangan',
        'jumlah',
        'tanggal',
        'bukti',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal' => 'date',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'kategori_id');
    }
}
