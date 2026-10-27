<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparePartRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'mechanic_analysis_id',
        'inventory_id',
        'quantity',
        'status',
        'admin_note',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::updated(function ($request) {
            // Check if status was changed to 'approved'
            if ($request->wasChanged('status') && $request->status === 'approved' && $request->getOriginal('status') !== 'approved') {
                $inventory = $request->inventory;
                if ($inventory) {
                    $unitCode = $request->mechanicAnalysis?->unit_code ?? 'unknown unit';

                    // Update Inventory stock out and metadata
                    $inventory->keterangan = "Penggantian spare part (Approved) untuk unit {$unitCode}";
                    $inventory->out = ($inventory->out ?? 0) + $request->quantity;
                    $inventory->save();

                    // Sync with DBR
                    $dbr = DBR::where('mechanic_analysis_id', $request->mechanic_analysis_id)->first();
                    if ($dbr) {
                        $spareParts = $dbr->spare_parts ?? [];
                        
                        $sellingPrice = $inventory->selling_price ?? 0;
                        $serviceCharge = $inventory->service_charge ?? 0;
                        $amount = $sellingPrice * $request->quantity;
                        $totalServiceCharge = $serviceCharge * $request->quantity;
                        $total = $amount + $totalServiceCharge;
                        
                        $newPart = [
                            'component' => $inventory->item_sparepart,
                            'qty' => $request->quantity,
                            'unit' => $inventory->unit,
                            'purchase_price' => $inventory->price_per_unit ?? 0,
                            'price_per_pcs' => $sellingPrice,
                            'amount' => $amount,
                            'service_charge' => $totalServiceCharge,
                            'total' => $total,
                        ];
                        
                        $spareParts[] = $newPart;
                        
                        $dbr->spare_parts = $spareParts;
                        
                        // Update sales totals
                        $dbr->sales_sparepart = collect($spareParts)->sum('amount');
                        $dbr->sales_service = collect($spareParts)->sum('service_charge');
                        
                        // Also update first component/qty for backward compatibility or display if needed
                        if (count($spareParts) === 1) {
                            $dbr->component = $newPart['component'];
                            $dbr->qty = $newPart['qty'];
                            $dbr->unit = $newPart['unit'];
                            $dbr->purchase_price = $newPart['purchase_price'];
                            $dbr->price_per_pcs = $newPart['price_per_pcs'];
                            $dbr->amount = $newPart['amount'];
                            $dbr->service_charge = $newPart['service_charge'];
                            $dbr->total = $newPart['total'];
                        } else {
                            $dbr->component = 'Multiple Items (' . count($spareParts) . ')';
                        }
                        
                        // Skip DBR's inventory sync since we already handled it above
                        $dbr->skipInventorySync = true;
                        $dbr->save();
                    }
                }
            }
        });
    }

    public function mechanicAnalysis()
    {
        return $this->belongsTo(MechanicAnalysis::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
