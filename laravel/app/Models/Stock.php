<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Stock extends Model
{
    use LogsActivity;

    protected $fillable = [
        'item_name', 'unit_type', 'unit_price', 'stock_in', 'stock_out',
        'total_stock', 'minimum_limit', 'location_id', 'user_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function histories()
    {
        return $this->hasMany(StockHistory::class);
    }
}
