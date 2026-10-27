<?php

namespace App\Filament\Resources\Inventories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoriesExport;

class InventoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('item_sparepart')
                    ->searchable()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),
                \Filament\Tables\Columns\TextColumn::make('vendor.name')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: false),
                \Filament\Tables\Columns\TextColumn::make('pcs_ltl')
                    ->label('PCS/LTL')
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('unit')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('price_per_unit')
                    ->label('Harga Beli')
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('selling_price')
                    ->label('Harga Jual')
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('service_charge')
                    ->label('Jasa')
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('begins')
                    ->label('Awal')
                    ->numeric()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('in')
                    ->label('Masuk')
                    ->numeric()
                    ->sortable()
                    ->color('success'),
                \Filament\Tables\Columns\TextColumn::make('return_in')
                    ->label('Retur Masuk')
                    ->numeric()
                    ->sortable()
                    ->color('warning')
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('out')
                    ->label('Keluar')
                    ->numeric()
                    ->sortable()
                    ->color('danger'),
                \Filament\Tables\Columns\TextColumn::make('return_out')
                    ->label('Cancel Use')
                    ->numeric()
                    ->sortable()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('ends_balance')
                    ->label('Akhir')
                    ->numeric()
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),
                \Filament\Tables\Columns\TextInputColumn::make('selisih')
                    ->label('Selisih')
                    ->type('number')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('total_purchase_price')
                    ->label('Total Purchase')
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('cogs')
                    ->label('COGS')
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('total_sales_price')
                    ->label('Total Sales')
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('income')
                    ->label('Income')
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->sortable()
                    ->color(fn ($record) => ($record->income ?? 0) >= 0 ? 'success' : 'danger')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('price_begins_balance')
                    ->label('Price Stock Awal')
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\TextColumn::make('price_end_balance')
                    ->label('Price End Balance')
                    ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('vendor_id')
                    ->label('Vendor')
                    ->relationship('vendor', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Vendor'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('reset_stock')
                        ->label('Jadikan Stok Awal')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalDescription('Apakah Anda yakin ingin menjadikan stok akhir saat ini sebagai stok awal? Nilai masuk, keluar, dan retur akan direset menjadi 0.')
                        ->action(function ($record) {
                            $filename = 'inventory-reset-' . $record->item_sparepart . '-' . now()->format('Y-m-d-His') . '.xlsx';
                            
                            // Snapshot data
                            $dataSnapshot = $record->toArray();
                            
                            // Save History
                            \App\Models\ResetStockHistory::create([
                                'filename' => $filename,
                                'data_snapshot' => [$dataSnapshot],
                                'reset_by' => auth()->user()->name ?? 'System',
                            ]);

                            $record->keterangan = 'Jadikan stok awal';
                            $record->transaction_date = now();
                            $record->begins = $record->ends_balance;
                            $record->in = 0;
                            $record->return_in = 0;
                            $record->out = 0;
                            $record->return_out = 0;
                            $record->save();

                            \Filament\Notifications\Notification::make()
                                ->title('Stok berhasil direset')
                                ->body('Snapshot data telah disimpan di History Reset.')
                                ->success()
                                ->send();
                        }),
                    Action::make('stock_in')
                        ->label('Stock Masuk')
                        ->icon('heroicon-o-plus-circle')
                        ->color('success')
                        ->form([
                            DatePicker::make('transaction_date')
                                ->label('Tanggal')
                                ->default(now())
                                ->required(),
                            TextInput::make('jumlah')
                                ->numeric()
                                ->required()
                                ->minValue(1),
                            TextInput::make('keterangan')
                                ->default('Stock masuk')
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            $record->keterangan = $data['keterangan'];
                            $record->transaction_date = $data['transaction_date'];
                            $record->in += $data['jumlah'];
                            $record->save();
                        }),
                    Action::make('return_in')
                        ->label('Retur Masuk')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('warning')
                        ->form([
                            DatePicker::make('transaction_date')
                                ->label('Tanggal')
                                ->default(now())
                                ->required(),
                            TextInput::make('jumlah')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->maxValue(fn (\Illuminate\Database\Eloquent\Model $record) => max(0, $record->in - $record->return_in))
                                ->helperText(fn (\Illuminate\Database\Eloquent\Model $record) => 'Maksimal Retur: ' . max(0, $record->in - $record->return_in))
                                ->hint('Mengurangi Stock Masuk'),
                            TextInput::make('keterangan')
                                ->default('Retur barang masuk (ke supplier)')
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            $record->keterangan = $data['keterangan'];
                            $record->transaction_date = $data['transaction_date'];
                            $record->return_in += $data['jumlah'];
                            $record->save();
                        }),
                    Action::make('stock_out')
                        ->label('Stock Keluar')
                        ->icon('heroicon-o-minus-circle')
                        ->color('danger')
                        ->form([
                            DatePicker::make('transaction_date')
                                ->label('Tanggal')
                                ->default(now())
                                ->required(),
                            TextInput::make('jumlah')
                                ->numeric()
                                ->required()
                                ->minValue(1),
                            TextInput::make('keterangan')
                                ->default('Stock keluar')
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            $record->keterangan = $data['keterangan'];
                            $record->transaction_date = $data['transaction_date'];
                            $record->out += $data['jumlah'];
                            $record->save();
                        }),
                    Action::make('return_out')
                        ->label('Cancel Use')
                        ->icon('heroicon-o-arrow-uturn-right')
                        ->color('info')
                        ->form([
                            DatePicker::make('transaction_date')
                                ->label('Tanggal')
                                ->default(now())
                                ->required(),
                            TextInput::make('jumlah')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->maxValue(fn (\Illuminate\Database\Eloquent\Model $record) => max(0, $record->out - $record->return_out))
                                ->helperText(fn (\Illuminate\Database\Eloquent\Model $record) => 'Maksimal Cancel Use: ' . max(0, $record->out - $record->return_out))
                                ->hint('Batalkan Penggunaan (Stok kembali bertambah)'),
                            TextInput::make('keterangan')
                                ->default('Pembatalan pemakaian barang')
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            $record->keterangan = $data['keterangan'];
                            $record->transaction_date = $data['transaction_date'];
                            $record->return_out += $data['jumlah'];
                            $record->save();
                        }),
                ]),
            ])
            ->toolbarActions([
                Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        return Excel::download(new InventoriesExport, 'inventories-' . now()->format('Y-m-d') . '.xlsx');
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('reset_stock_bulk')
                        ->label('Jadikan Stok Awal')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Jadikan Stok Awal Massal')
                        ->modalDescription('Apakah Anda yakin ingin menjadikan stok akhir saat ini sebagai stok awal untuk SEMUA item yang dipilih? Nilai masuk, keluar, dan retur akan direset menjadi 0.')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $filename = 'inventory-reset-bulk-' . now()->format('Y-m-d-His') . '.xlsx';
                            
                            // Snapshot data
                            $dataSnapshot = $records->map(fn($r) => $r->toArray())->toArray();
                            
                            // Save History
                            \App\Models\ResetStockHistory::create([
                                'filename' => $filename,
                                'data_snapshot' => $dataSnapshot,
                                'reset_by' => auth()->user()->name ?? 'System',
                            ]);

                            $records->each(function ($record) {
                                $record->keterangan = 'Jadikan stok awal (Bulk)';
                                $record->begins = $record->ends_balance;
                                $record->in = 0;
                                $record->return_in = 0;
                                $record->out = 0;
                                $record->return_out = 0;
                                $record->save();
                            });

                            \Filament\Notifications\Notification::make()
                                ->title('Batch Stok berhasil direset')
                                ->body('Snapshot data telah disimpan di History Reset.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
