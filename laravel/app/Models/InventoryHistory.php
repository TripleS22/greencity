<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use HasFactory;

    protected $table = 'inventory_histories';

    protected $fillable = [
        'inventory_id',
        'begins',
        'in',
        'return_in',
        'out',
        'return_out',
        'ends_balance',
        'price_per_unit',
        'selling_price',
        'total_purchase_price',
        'cogs',
        'total_sales_price',
        'income',
        'price_begins_balance',
        'price_end_balance',
        'keterangan',
        'transaction_date',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    /**
     * Get the inventory item that owns the history.
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}
