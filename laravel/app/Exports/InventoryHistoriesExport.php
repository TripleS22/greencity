<?php

namespace App\Exports;

use App\Models\InventoryHistory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class InventoryHistoriesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected ?string $dateFrom;
    protected ?string $dateTo;
    protected ?int $inventoryId;
    protected ?string $type;

    public function __construct(?string $dateFrom = null, ?string $dateTo = null, ?int $inventoryId = null, ?string $type = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->inventoryId = $inventoryId;
        $this->type = $type;
    }

    public function collection()
    {
        $query = InventoryHistory::query()
            ->select('inventory_id')
            ->selectRaw('SUM(CASE WHEN `in` > 0 THEN `in` ELSE 0 END) as total_in')
            ->selectRaw('SUM(CASE WHEN `return_in` > 0 THEN `return_in` ELSE 0 END) as total_return_in')
            ->selectRaw('SUM(CASE WHEN `out` > 0 THEN `out` ELSE 0 END) as total_out')
            ->selectRaw('SUM(CASE WHEN `return_out` > 0 THEN `return_out` ELSE 0 END) as total_return_out')
            ->selectRaw('SUM(COALESCE(total_purchase_price, 0)) as sum_purchase')
            ->selectRaw('SUM(COALESCE(cogs, 0)) as sum_cogs')
            ->selectRaw('SUM(COALESCE(total_sales_price, 0)) as sum_sales')
            ->selectRaw('SUM(COALESCE(income, 0)) as sum_income')
            ->with('inventory')
            ->groupBy('inventory_id');

        if ($this->inventoryId) $query->where('inventory_id', $this->inventoryId);
        
        if ($this->type === 'masuk') $query->where('in', '>', 0);
        elseif ($this->type === 'retur_masuk') $query->where('return_in', '>', 0);
        elseif ($this->type === 'keluar') $query->where('out', '>', 0);
        elseif ($this->type === 'retur_keluar') $query->where('return_out', '>', 0);

        if ($this->dateFrom) $query->whereDate('transaction_date', '>=', $this->dateFrom);
        if ($this->dateTo) $query->whereDate('transaction_date', '<=', $this->dateTo);

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Item Sparepart',
            'Stock Awal',
            'Total Masuk',
            'Total Retur Masuk',
            'Total Keluar',
            'Total Cancel Use',
            'Stock Akhir',
            'Unit Price',
            'Selling Price',
            'Total Purchase Price',
            'Total COGS',
            'Total Sales Price',
            'Total Income',
            'Stock Awal Value',
            'Stock Akhir Value',
        ];
    }

    public function map($row): array
    {
        $inventory = $row->inventory;
        $price = (int) ($inventory?->price_per_unit ?? 0);
        $sellPrice = (int) ($inventory?->selling_price ?? 0);

        // Get first and last records for the specific period to determine actual stock levels
        $first = InventoryHistory::where('inventory_id', $row->inventory_id)
            ->when($this->dateFrom, fn($q) => $q->whereDate('transaction_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('transaction_date', '<=', $this->dateTo))
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->first();

        $last = InventoryHistory::where('inventory_id', $row->inventory_id)
            ->when($this->dateFrom, fn($q) => $q->whereDate('transaction_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('transaction_date', '<=', $this->dateTo))
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $stockAwal = (int) ($first?->begins ?? 0);
        $stockAkhir = (int) ($last?->ends_balance ?? 0);

        // Totals logic: use sums from DB, or fallback to current price * total movement
        $purchase = (int) $row->sum_purchase;
        if ($purchase == 0 && ($row->total_in - $row->total_return_in) > 0) {
            $purchase = $price * ($row->total_in - $row->total_return_in);
        }

        $cogs = (int) $row->sum_cogs;
        if ($cogs == 0 && ($row->total_out - $row->total_return_out) > 0) {
            $cogs = $price * ($row->total_out - $row->total_return_out);
        }

        $sales = (int) $row->sum_sales;
        if ($sales == 0 && ($row->total_out - $row->total_return_out) > 0) {
            $sales = $sellPrice * ($row->total_out - $row->total_return_out);
        }

        $income = (int) $row->sum_income;
        if ($income == 0 && $sales > 0) {
            $income = $sales - $cogs;
        }

        // Snapshots value
        $valAwal = (int) ($first?->price_begins_balance ?? ($price * $stockAwal));
        $valAkhir = (int) ($last?->price_end_balance ?? ($price * $stockAkhir));

        return [
            $inventory->item_sparepart ?? '-',
            $stockAwal,
            (int) $row->total_in,
            (int) $row->total_return_in,
            (int) $row->total_out,
            (int) $row->total_return_out,
            $stockAkhir,
            $price,
            $sellPrice,
            $purchase,
            $cogs,
            $sales,
            $income,
            $valAwal,
            $valAkhir,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
