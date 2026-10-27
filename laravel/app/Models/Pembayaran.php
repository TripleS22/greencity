<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'warga_id',
        'bulan',
        'jumlah',
        'status',
        'metode_bayar',
        'snap_token',
        'order_id',
        'transaction_id',
        'tanggal_bayar',
        'catatan',
        'qr_content',
        'doitpay_reference_no',
        'qr_expired_at',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
        'qr_expired_at' => 'datetime',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function isLunas()
    {
        return $this->status === 'lunas';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }
}
