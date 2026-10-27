<?php

namespace App\Filament\Resources\Inventories\Pages;

use App\Filament\Resources\Inventories\InventoryResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class ImportInventory extends Page
{
    protected static string $resource = InventoryResource::class;

    protected string $view = 'filament.resources.inventories.pages.import-inventory';

    protected static ?string $title = 'Import Inventory dari Excel';

    /** Kolom database yang tersedia untuk mapping */
    public array $dbColumns = [
        'item_sparepart' => 'Item Sparepart *',
        'pcs_ltl'        => 'PCS/LTL',
        'unit'           => 'Unit',
        'margin'         => 'Margin (%)',
        'price_per_unit' => 'Harga Beli/Unit',
        'selling_price'  => 'Harga Jual/Unit',
        'service_charge' => 'Service Charge',
        'begins'         => 'Stok Awal',
        'in'             => 'Masuk',
        'return_in'      => 'Retur Masuk',
        'out'            => 'Keluar',
        'return_out'     => 'Retur Keluar',
    ];

    public function getViewData(): array
    {
        $headers  = session('inv_import_headers', []);
        $preview  = session('inv_import_preview', []);
        $hasFile  = session()->has('inv_import_path');
        $result   = session('inv_import_result');

        // Auto-detect mapping
        $autoMapping = [];
        if ($hasFile && !empty($headers)) {
            $autoMapping = $this->autoDetectMapping($headers);
        }

        // Determine step
        if ($result !== null) {
            $step = 'result';
        } elseif ($hasFile) {
            $step = 'mapping';
        } else {
            $step = 'upload';
        }

        return [
            'step'        => $step,
            'excelHeaders'=> $headers,
            'previewRows' => $preview,
            'autoMapping' => $autoMapping,
            'dbColumns'   => $this->dbColumns,
            'result'      => $result,
        ];
    }

    private function autoDetectMapping(array $headers): array
    {
        $autoMap = [
            'item_sparepart' => ['item', 'sparepart', 'nama', 'name', 'part', 'barang'],
            'pcs_ltl'        => ['pcs', 'ltl', 'pak'],
            'unit'           => ['unit', 'satuan'],
            'margin'         => ['margin'],
            'price_per_unit' => ['harga_beli', 'harga beli', 'buy_price', 'cost', 'price', 'harga'],
            'selling_price'  => ['harga_jual', 'harga jual', 'sell_price', 'jual', 'selling'],
            'service_charge' => ['service', 'jasa', 'charge'],
            'begins'         => ['begins', 'stok_awal', 'stok awal', 'saldo_awal', 'opening', 'awal'],
            'in'             => ['masuk', 'barang masuk', 'barang_masuk'],
            'return_in'      => ['retur_masuk', 'retur masuk', 'return_in'],
            'out'            => ['keluar', 'barang keluar', 'barang_keluar'],
            'return_out'     => ['retur_keluar', 'retur keluar', 'return_out'],
        ];

        $mapping = [];
        foreach (array_keys($this->dbColumns) as $dbCol) {
            $mapping[$dbCol] = '';
            $keywords = $autoMap[$dbCol] ?? [];

            foreach ($headers as $header) {
                $normalized = mb_strtolower(trim($header));
                $normalized = preg_replace('/\s+/', '_', $normalized);

                foreach ($keywords as $kw) {
                    if (str_contains($normalized, $kw) || str_contains($kw, $normalized)) {
                        $mapping[$dbCol] = $header;
                        break 2;
                    }
                }
            }
        }

        return $mapping;
    }

    public function clearResult(): void
    {
        session()->forget('inv_import_result');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back_to_list')
                ->label('Kembali ke Daftar')
                ->url(InventoryResource::getUrl('index'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray'),
        ];
    }
}
