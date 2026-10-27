<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'contact_person',
        'notes',
    ];

    /**
     * Get all inventory items supplied by this vendor.
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
