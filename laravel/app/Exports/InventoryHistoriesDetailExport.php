<?php

namespace App\Exports;

use App\Models\InventoryHistory;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Export inventory history per-record (tanpa total/grouping).
 * Diurutkan berdasarkan nama item, lalu tanggal ascending.
 * Bisa difilter per bulan-tahun dan/atau per item.
 */
class InventoryHistoriesDetailExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected ?int $month;
    protected ?int $year;
    protected ?int $inventoryId;
    protected ?string $type;
    protected ?string $dateFrom;
    protected ?string $dateTo;

    public function __construct(
        ?int $month = null,
        ?int $year = null,
        ?int $inventoryId = null,
        ?string $type = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
    ) {
        $this->month       = $month;
        $this->year        = $year;
        $this->inventoryId = $inventoryId;
        $this->type        = $type;
        $this->dateFrom    = $dateFrom;
        $this->dateTo      = $dateTo;
    }

    public function query()
    {
        $query = InventoryHistory::query()
            ->with('inventory')
            ->when($this->inventoryId, fn ($q) => $q->where('inventory_id', $this->inventoryId))
            ->when($this->month && $this->year, fn ($q) => $q
                ->whereMonth('transaction_date', $this->month)
                ->whereYear('transaction_date', $this->year)
            )
            ->when(!($this->month && $this->year) && $this->dateFrom, fn ($q) => $q->whereDate('transaction_date', '>=', $this->dateFrom))
            ->when(!($this->month && $this->year) && $this->dateTo,   fn ($q) => $q->whereDate('transaction_date', '<=', $this->dateTo))
            ->when($this->type === 'masuk',       fn ($q) => $q->where('in', '>', 0))
            ->when($this->type === 'retur_masuk', fn ($q) => $q->where('return_in', '>', 0))
            ->when($this->type === 'keluar',      fn ($q) => $q->where('out', '>', 0))
            ->when($this->type === 'retur_keluar',fn ($q) => $q->where('return_out', '>', 0))
            ->orderBy('inventory_id', 'asc')
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc');

        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Item Sparepart',
            'Keterangan',
            'Stok Awal',
            'Masuk',
            'Retur Masuk',
            'Keluar',
            'Cancel Use',
            'Stok Akhir',
            'Harga Unit (Beli)',
            'Harga Unit (Jual)',
            'Total Pembelian',
            'COGS',
            'Total Penjualan',
            'Income',
            'Nilai Stok Awal',
            'Nilai Stok Akhir',
        ];
    }

    protected int $rowNumber = 0;

    public function map($row): array
    {
        $this->rowNumber++;

        $inventory = $row->inventory;
        $price     = (int) ($row->price_per_unit  ?? $inventory?->price_per_unit  ?? 0);
        $sellPrice = (int) ($row->selling_price   ?? $inventory?->selling_price   ?? 0);

        $purchase = $row->total_purchase_price !== null
            ? (int) $row->total_purchase_price
            : $price * max(0, (int) $row->in - (int) $row->return_in);

        $cogs = $row->cogs !== null
            ? (int) $row->cogs
            : $price * max(0, (int) $row->out - (int) $row->return_out);

        $sales = $row->total_sales_price !== null
            ? (int) $row->total_sales_price
            : $sellPrice * max(0, (int) $row->out - (int) $row->return_out);

        $income = $row->income !== null
            ? (int) $row->income
            : ($sales - $cogs);

        $valAwal  = $row->price_begins_balance !== null
            ? (int) $row->price_begins_balance
            : $price * (int) $row->begins;

        $valAkhir = $row->price_end_balance !== null
            ? (int) $row->price_end_balance
            : $price * (int) $row->ends_balance;

        return [
            $this->rowNumber,
            $row->transaction_date ? \Carbon\Carbon::parse($row->transaction_date)->format('d/m/Y') : ($row->created_at?->format('d/m/Y') ?? '-'),
            $inventory?->item_sparepart ?? '-',
            $row->keterangan ?? '-',
            (int) $row->begins,
            (int) $row->in,
            (int) $row->return_in,
            (int) $row->out,
            (int) $row->return_out,
            (int) $row->ends_balance,
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
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2E7D32'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        if ($this->month && $this->year) {
            $bulan = \Carbon\Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F Y');
            return 'History ' . $bulan;
        }

        return 'Inventory History';
    }
}
