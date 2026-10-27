<?php

namespace App\Exports;

use App\Models\Inventory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class InventoriesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected $data;

    public function __construct($data = null)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data ?: Inventory::all();
    }

    public function headings(): array
    {
        return [
            'Item Sparepart',
            'PCS/LTL',
            'Unit',
            'Price Per Unit',
            'Margin (%)',
            'Selling Price',
            'Stock Awal',
            'Stock Masuk',
            'Retur Masuk',
            'Stock Keluar',
            'Cancel Use',
            'Stock Akhir',
            'Stock Awal (Price)',
            'Total Purchase Price',
            'COGS',
            'Total Sales Price',
            'Income',
            'Price End Balance',
        ];
    }

    public function map($inventory): array
    {
        return [
            data_get($inventory, 'item_sparepart'),
            data_get($inventory, 'pcs_ltl'),
            data_get($inventory, 'unit'),
            data_get($inventory, 'price_per_unit'),
            data_get($inventory, 'margin'),
            data_get($inventory, 'selling_price'),
            data_get($inventory, 'begins'),
            data_get($inventory, 'in'),
            data_get($inventory, 'return_in'),
            data_get($inventory, 'out'),
            data_get($inventory, 'return_out'),
            data_get($inventory, 'ends_balance'),
            data_get($inventory, 'price_begins_balance'),
            data_get($inventory, 'total_purchase_price'),
            data_get($inventory, 'cogs'),
            data_get($inventory, 'total_sales_price'),
            data_get($inventory, 'income'),
            data_get($inventory, 'price_end_balance'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $totalRow = $highestRow + 1;

                // Calculate totals for numeric columns
                $inventories = $this->collection();
                
                $totalBegins = $inventories->sum('begins');
                $totalIn = $inventories->sum('in');
                $totalReturnIn = $inventories->sum('return_in');
                $totalOut = $inventories->sum('out');
                $totalReturnOut = $inventories->sum('return_out');
                $totalEndsBalance = $inventories->sum('ends_balance');
                $totalPriceBegins = $inventories->sum('price_begins_balance');
                $totalPurchasePrice = $inventories->sum('total_purchase_price');
                $totalCogs = $inventories->sum('cogs');
                $totalSalesPrice = $inventories->sum('total_sales_price');
                $totalIncome = $inventories->sum('income');
                $totalPriceEndBalance = $inventories->sum('price_end_balance');

                // Set total row values
                $sheet->setCellValue('A' . $totalRow, 'TOTAL');
                $sheet->setCellValue('G' . $totalRow, $totalBegins);
                $sheet->setCellValue('H' . $totalRow, $totalIn);
                $sheet->setCellValue('I' . $totalRow, $totalReturnIn);
                $sheet->setCellValue('J' . $totalRow, $totalOut);
                $sheet->setCellValue('K' . $totalRow, $totalReturnOut);
                $sheet->setCellValue('L' . $totalRow, $totalEndsBalance);
                $sheet->setCellValue('M' . $totalRow, $totalPriceBegins);
                $sheet->setCellValue('N' . $totalRow, $totalPurchasePrice);
                $sheet->setCellValue('O' . $totalRow, $totalCogs);
                $sheet->setCellValue('P' . $totalRow, $totalSalesPrice);
                $sheet->setCellValue('Q' . $totalRow, $totalIncome);
                $sheet->setCellValue('R' . $totalRow, $totalPriceEndBalance);

                // Style the total row
                $sheet->getStyle('A' . $totalRow . ':R' . $totalRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4472C4'],
                    ],
                ]);
            },
        ];
    }
}
