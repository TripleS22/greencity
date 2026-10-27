<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    protected $fillable = ['stock_id', 'type', 'quantity', 'date', 'notes'];

    protected $casts = [
        'date' => 'date',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
