<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'item_sparepart',
        'pcs_ltl',
        'unit',
        'margin',
        'price_per_unit',
        'selling_price',
        'service_charge',
        'begins',
        'in',
        'return_in',
        'out',
        'return_out',
        'ends_balance',
        'selisih',
        'total_purchase_price',
        'cogs',
        'total_sales_price',
        'income',
        'price_end_balance',
        'price_begins_balance',
        'vendor_id',
    ];

    /**
     * Virtual attribute for custom history description.
     *
     * @var string|null
     */
    public $keterangan;

    /**
     * Virtual attribute for custom transaction date.
     *
     * @var string|null
     */
    public $transaction_date;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saving(function ($inventory) {
            // Stock balance
            $begins = (int) ($inventory->begins ?? 0);
            $in = (int) ($inventory->in ?? 0);
            $returnIn = (int) ($inventory->return_in ?? 0);
            $out = (int) ($inventory->out ?? 0);
            $returnOut = (int) ($inventory->return_out ?? 0);

            $inventory->ends_balance = $begins + ($in - $returnIn) - ($out - $returnOut);

            // Financial calculations
            $pricePerUnit = (float) ($inventory->price_per_unit ?? 0);
            $sellingPrice = (float) ($inventory->selling_price ?? 0);
            $endsBalance = (float) $inventory->ends_balance;

            $inventory->total_purchase_price = $pricePerUnit * ($in - $returnIn);
            $inventory->cogs = $pricePerUnit * ($out - $returnOut);
            $inventory->price_begins_balance = $pricePerUnit * $begins;
            $inventory->total_sales_price = $sellingPrice * ($out - $returnOut);
            $inventory->income = $inventory->total_sales_price - $inventory->cogs;
            $inventory->price_end_balance = $pricePerUnit * $endsBalance;
        });

        static::updated(function ($inventory) {
            // Check if stock-related fields changed
            if ($inventory->wasChanged(['in', 'out', 'begins', 'return_in', 'return_out'])) {
                // Calculate deltas
                $diffIn = ($inventory->in ?? 0) - ($inventory->getOriginal('in') ?? 0);
                $diffReturnIn = ($inventory->return_in ?? 0) - ($inventory->getOriginal('return_in') ?? 0);
                $diffOut = ($inventory->out ?? 0) - ($inventory->getOriginal('out') ?? 0);
                $diffReturnOut = ($inventory->return_out ?? 0) - ($inventory->getOriginal('return_out') ?? 0);

                // If this is a reset (all metrics set to 0), we don't record it as movement
                // because it creates confusing negative deltas in the history.
                // The ResetStockHistory already captures the state before reset.
                if ($inventory->in == 0 && $inventory->out == 0 && $inventory->return_in == 0 && $inventory->return_out == 0 && ($diffIn < 0 || $diffOut < 0)) {
                    return;
                }

                $pricePerUnit = (float) ($inventory->price_per_unit ?? 0);
                $sellingPrice = (float) ($inventory->selling_price ?? 0);

                $totalPurchase = $pricePerUnit * ($diffIn - $diffReturnIn);
                $cogs = $pricePerUnit * ($diffOut - $diffReturnOut);
                $totalSales = $sellingPrice * ($diffOut - $diffReturnOut);

                $inventory->histories()->create([
                    'begins' => $inventory->getOriginal('ends_balance'),
                    'in' => $diffIn,
                    'return_in' => $diffReturnIn,
                    'out' => $diffOut,
                    'return_out' => $diffReturnOut,
                    'ends_balance' => $inventory->ends_balance,
                    'price_per_unit' => $pricePerUnit,
                    'selling_price' => $sellingPrice,
                    'total_purchase_price' => $totalPurchase,
                    'cogs' => $cogs,
                    'total_sales_price' => $totalSales,
                    'income' => $totalSales - $cogs,
                    'price_begins_balance' => $pricePerUnit * ($inventory->getOriginal('ends_balance') ?? 0),
                    'price_end_balance' => $pricePerUnit * $inventory->ends_balance,
                    'keterangan' => $inventory->keterangan ?? 'Penyesuaian stok (Manual)',
                    'transaction_date' => $inventory->transaction_date ?? now(),
                ]);
            }
        });

        static::created(function ($inventory) {
            $pricePerUnit = (float) ($inventory->price_per_unit ?? 0);
            $sellingPrice = (float) ($inventory->selling_price ?? 0);

            $in = (int) ($inventory->in ?? 0);
            $returnIn = (int) ($inventory->return_in ?? 0);
            $out = (int) ($inventory->out ?? 0);
            $returnOut = (int) ($inventory->return_out ?? 0);

            $totalPurchase = $pricePerUnit * ($in - $returnIn);
            $cogs = $pricePerUnit * ($out - $returnOut);
            $totalSales = $sellingPrice * ($out - $returnOut);

            $inventory->histories()->create([
                'begins' => $inventory->begins,
                'in' => $in,
                'return_in' => $returnIn,
                'out' => $out,
                'return_out' => $returnOut,
                'ends_balance' => $inventory->ends_balance,
                'price_per_unit' => $pricePerUnit,
                'selling_price' => $sellingPrice,
                'total_purchase_price' => $totalPurchase,
                'cogs' => $cogs,
                'total_sales_price' => $totalSales,
                'income' => $totalSales - $cogs,
                'price_begins_balance' => $pricePerUnit * ($inventory->begins ?? 0),
                'price_end_balance' => $pricePerUnit * $inventory->ends_balance,
                'keterangan' => $inventory->keterangan ?? 'stock awal',
                'transaction_date' => $inventory->transaction_date ?? now(),
            ]);
        });
    }

    /**
     * Get the history for the inventory item.
     */
    public function histories()
    {
        return $this->hasMany(InventoryHistory::class, 'inventory_id');
    }

    /**
     * Get the mechanic analyses associated with this spare part.
     */
    public function mechanicAnalyses()
    {
        return $this->belongsToMany(MechanicAnalysis::class, 'mechanic_analysis_inventory');
    }

    /**
     * Get the vendor for this inventory item.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
