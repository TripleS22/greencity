<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetPurnaGuna extends Model
{
    protected $fillable = ['name', 'qty', 'price_range', 'total_price', 'status'];
}
