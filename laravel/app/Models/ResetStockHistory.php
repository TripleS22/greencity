<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetStockHistory extends Model
{
    protected $fillable = [
        'filename',
        'data_snapshot',
        'reset_by',
    ];

    protected $casts = [
        'data_snapshot' => 'array',
    ];
}
