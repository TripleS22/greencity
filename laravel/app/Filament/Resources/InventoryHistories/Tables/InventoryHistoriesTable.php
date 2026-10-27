<?php

namespace App\Filament\Resources\InventoryHistories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryHistoriesExport;
use App\Exports\InventoryHistoriesDetailExport;
use App\Models\Inventory;

class InventoryHistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inventory.item_sparepart')
                    ->label('Item Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('begins')
                    ->label('Awal')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('in')
                    ->label('Masuk')
                    ->numeric()
                    ->sortable()
                    ->color('success'),
                TextColumn::make('return_in')
                    ->label('Retur Masuk')
                    ->numeric()
                    ->sortable()
                    ->color('warning'),
                TextColumn::make('out')
                    ->label('Keluar')
                    ->numeric()
                    ->sortable()
                    ->color('danger'),
                TextColumn::make('return_out')
                    ->label('Cancel Use')
                    ->numeric()
                    ->sortable()
                    ->color('info'),
                TextColumn::make('ends_balance')
                    ->label('Akhir')
                    ->numeric()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),
                TextColumn::make('price_per_unit')
                    ->label('Price Unit')
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->getStateUsing(fn ($record) => $record->price_per_unit ?? $record->inventory?->price_per_unit)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('selling_price')
                    ->label('Selling Price')
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->getStateUsing(fn ($record) => $record->selling_price ?? $record->inventory?->selling_price)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_purchase_price')
                    ->label('Purchase')
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->sortable()
                    ->color('success')
                    ->getStateUsing(function ($record) {
                        if ($record->total_purchase_price !== null) return $record->total_purchase_price;
                        $price = $record->inventory?->price_per_unit ?? 0;
                        return $price * ($record->in - $record->return_in);
                    }),
                TextColumn::make('cogs')
                    ->label('COGS')
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->sortable()
                    ->color('danger')
                    ->getStateUsing(function ($record) {
                        if ($record->cogs !== null) return $record->cogs;
                        $price = $record->inventory?->price_per_unit ?? 0;
                        return $price * ($record->out - $record->return_out);
                    }),
                TextColumn::make('total_sales_price')
                    ->label('Sales')
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->sortable()
                    ->color('info')
                    ->getStateUsing(function ($record) {
                        if ($record->total_sales_price !== null) return $record->total_sales_price;
                        $price = $record->inventory?->selling_price ?? 0;
                        return $price * ($record->out - $record->return_out);
                    }),
                TextColumn::make('income')
                    ->label('Income')
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->getStateUsing(function ($record) {
                        if ($record->income !== null) return $record->income;
                        $sellingPrice = $record->inventory?->selling_price ?? 0;
                        $costPrice = $record->inventory?->price_per_unit ?? 0;
                        $qty = ($record->out - $record->return_out);
                        return ($sellingPrice - $costPrice) * $qty;
                    })
                    ->color(fn ($state) => ($state ?? 0) >= 0 ? 'success' : 'danger'),
                TextColumn::make('price_begins_balance')
                    ->label('Price Stock Awal')
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(function ($record) {
                        if ($record->price_begins_balance !== null) return $record->price_begins_balance;
                        $price = $record->inventory?->price_per_unit ?? 0;
                        return $price * $record->begins;
                    }),
                TextColumn::make('price_end_balance')
                    ->label('Price End Balance')
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->getStateUsing(function ($record) {
                        if ($record->price_end_balance !== null) return $record->price_end_balance;
                        $price = $record->inventory?->price_per_unit ?? 0;
                        return $price * $record->ends_balance;
                    }),
                TextColumn::make('keterangan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('transaction_date')
                    ->label('Tanggal Transaksi')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Sistem')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('inventory_id')
                    ->label('Item Sparepart')
                    ->options(fn () => Inventory::pluck('item_sparepart', 'id')->toArray())
                    ->searchable(),
                SelectFilter::make('type')
                    ->label('Tipe Transaksi')
                    ->options([
                        'masuk' => 'Stock Masuk',
                        'retur_masuk' => 'Retur Masuk',
                        'keluar' => 'Stock Keluar',
                        'retur_keluar' => 'Cancel Use',
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['value'] === 'masuk', fn ($q) => $q->where('in', '>', 0))
                            ->when($data['value'] === 'retur_masuk', fn ($q) => $q->where('return_in', '>', 0))
                            ->when($data['value'] === 'keluar', fn ($q) => $q->where('out', '>', 0))
                            ->when($data['value'] === 'retur_keluar', fn ($q) => $q->where('return_out', '>', 0));
                    }),
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('date_to')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['date_from'], fn ($q, $date) => $q->whereDate('transaction_date', '>=', $date))
                            ->when($data['date_to'], fn ($q, $date) => $q->whereDate('transaction_date', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                // ── Export Rekap (total per item) ────────────────────────────
                Action::make('export')
                    ->label('Export Rekap')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->form([
                        Select::make('inventory_id')
                            ->label('Item Sparepart')
                            ->options(fn () => Inventory::pluck('item_sparepart', 'id')->toArray())
                            ->searchable()
                            ->placeholder('Semua Item'),
                        Select::make('type')
                            ->label('Tipe Transaksi')
                            ->options([
                                'masuk'       => 'Stock Masuk',
                                'retur_masuk' => 'Retur Masuk',
                                'keluar'      => 'Stock Keluar',
                                'retur_keluar'=> 'Cancel Use',
                            ])
                            ->placeholder('Semua Tipe'),
                        DatePicker::make('date_from')
                            ->label('Dari Tanggal'),
                        DatePicker::make('date_to')
                            ->label('Sampai Tanggal'),
                    ])
                    ->action(function (array $data) {
                        $inventoryId = $data['inventory_id'] ?? null;
                        $type        = $data['type']         ?? null;
                        $dateFrom    = $data['date_from']    ?? null;
                        $dateTo      = $data['date_to']      ?? null;

                        $fileName = 'inventory-rekap';
                        if ($inventoryId) {
                            $item = Inventory::find($inventoryId);
                            $fileName .= '-' . \Illuminate\Support\Str::slug($item->item_sparepart ?? 'item');
                        }
                        if ($type)     $fileName .= '-' . $type;
                        if ($dateFrom) $fileName .= '-from-' . $dateFrom;
                        if ($dateTo)   $fileName .= '-to-'   . $dateTo;
                        $fileName .= '.xlsx';

                        return Excel::download(
                            new InventoryHistoriesExport($dateFrom, $dateTo, $inventoryId, $type),
                            $fileName
                        );
                    }),

                // ── Export Detail Per Tanggal (tidak di-total) ───────────────
                Action::make('export_detail')
                    ->label('Export Detail Per Tanggal')
                    ->icon('heroicon-o-calendar-days')
                    ->color('info')
                    ->form([
                        Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                1  => 'Januari',
                                2  => 'Februari',
                                3  => 'Maret',
                                4  => 'April',
                                5  => 'Mei',
                                6  => 'Juni',
                                7  => 'Juli',
                                8  => 'Agustus',
                                9  => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember',
                            ])
                            ->placeholder('Pilih Bulan (opsional)'),
                        Select::make('tahun')
                            ->label('Tahun')
                            ->options(function () {
                                $years = [];
                                $current = (int) now()->year;
                                for ($y = $current; $y >= $current - 5; $y--) {
                                    $years[$y] = (string) $y;
                                }
                                return $years;
                            })
                            ->default((string) now()->year)
                            ->placeholder('Pilih Tahun'),
                        Select::make('inventory_id')
                            ->label('Item Sparepart')
                            ->options(fn () => Inventory::pluck('item_sparepart', 'id')->toArray())
                            ->searchable()
                            ->placeholder('Semua Item'),
                        Select::make('type')
                            ->label('Tipe Transaksi')
                            ->options([
                                'masuk'       => 'Stock Masuk',
                                'retur_masuk' => 'Retur Masuk',
                                'keluar'      => 'Stock Keluar',
                                'retur_keluar'=> 'Cancel Use',
                            ])
                            ->placeholder('Semua Tipe'),
                    ])
                    ->action(function (array $data) {
                        $month       = isset($data['bulan'])        ? (int) $data['bulan']        : null;
                        $year        = isset($data['tahun'])        ? (int) $data['tahun']        : null;
                        $inventoryId = $data['inventory_id']        ?? null;
                        $type        = $data['type']                ?? null;

                        $fileName = 'inventory-detail';
                        if ($month && $year) {
                            $fileName .= '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . $year;
                        } elseif ($year) {
                            $fileName .= '-' . $year;
                        }
                        if ($inventoryId) {
                            $item = Inventory::find($inventoryId);
                            $fileName .= '-' . \Illuminate\Support\Str::slug($item->item_sparepart ?? 'item');
                        }
                        if ($type) $fileName .= '-' . $type;
                        $fileName .= '.xlsx';

                        return Excel::download(
                            new InventoryHistoriesDetailExport(
                                month: $month,
                                year: $year,
                                inventoryId: $inventoryId,
                                type: $type,
                            ),
                            $fileName
                        );
                    }),

                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
