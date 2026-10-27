<?php

namespace App\Filament\Resources\Inventories\Schemas;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class InventoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Item Information')
                    ->schema([
                        TextEntry::make('item_sparepart')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('pcs_ltl')
                            ->label('PCS/LTL'),
                        TextEntry::make('unit'),
                    ])->columns(3),

                Section::make('Vendor / Supplier')
                    ->schema([
                        TextEntry::make('vendor.name')
                            ->label('Nama Vendor')
                            ->placeholder('Belum diisi')
                            ->badge()
                            ->color('info'),
                        TextEntry::make('vendor.phone')
                            ->label('Telepon')
                            ->placeholder('-'),
                        TextEntry::make('vendor.contact_person')
                            ->label('Kontak Person')
                            ->placeholder('-'),
                        TextEntry::make('vendor.address')
                            ->label('Alamat')
                            ->placeholder('-'),
                    ])->columns(2),

                Section::make('Financial Details')
                    ->schema([
                        TextEntry::make('price_per_unit')
                            ->label('Price per unit')
                            ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ',')),
                        TextEntry::make('margin')
                            ->label('Margin')
                            ->suffix('%'),
                        TextEntry::make('selling_price')
                            ->label('Selling price')
                            ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                            ->weight(FontWeight::Bold)
                            ->color('success'),
                    ])->columns(3),

                Section::make('Stock Status')
                    ->schema([
                        TextEntry::make('begins')
                            ->label('Stock Awal')
                            ->formatStateUsing(fn ($state) => number_format($state, 0, '.', ',')),
                        TextEntry::make('in')
                            ->label('Stock Masuk')
                            ->formatStateUsing(fn ($state) => number_format($state, 0, '.', ',')),
                        TextEntry::make('return_in')
                            ->label('Retur Masuk')
                            ->formatStateUsing(fn ($state) => number_format($state, 0, '.', ','))
                            ->color('warning'),
                        TextEntry::make('out')
                            ->label('Stock Keluar')
                            ->formatStateUsing(fn ($state) => number_format($state, 0, '.', ',')),
                        TextEntry::make('return_out')
                            ->label('Cancel Use')
                            ->formatStateUsing(fn ($state) => number_format($state, 0, '.', ','))
                            ->color('info'),
                        TextEntry::make('ends_balance')
                            ->label('Stock Akhir')
                            ->formatStateUsing(fn ($state) => number_format($state, 0, '.', ','))
                            ->weight(FontWeight::Bold),

                        Actions::make([
                            ActionGroup::make([
                                Action::make('reset_stock')
                                    ->label('Jadikan Stok Awal')
                                    ->icon('heroicon-o-arrow-path')
                                    ->color('warning')
                                    ->requiresConfirmation()
                                    ->modalHeading('Jadikan Stok Awal')
                                    ->modalDescription('Apakah Anda yakin ingin menjadikan stok akhir saat ini sebagai stok awal? Nilai masuk, keluar, dan retur akan direset menjadi 0.')
                                    ->action(function ($record) {
                                        $record->keterangan = 'Jadikan stok awal';
                                        $record->begins = $record->ends_balance;
                                        $record->in = 0;
                                        $record->return_in = 0;
                                        $record->out = 0;
                                        $record->return_out = 0;
                                        $record->save();
                                    }),
                                Action::make('stock_in')
                                    ->label('Stock Masuk')
                                    ->icon('heroicon-o-plus-circle')
                                    ->color('success')
                                    ->form([
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
                                        $record->in += $data['jumlah'];
                                        $record->save();
                                    }),
                                Action::make('return_in')
                                    ->label('Retur Masuk')
                                    ->icon('heroicon-o-arrow-uturn-left')
                                    ->color('warning')
                                    ->form([
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
                                        $record->return_in += $data['jumlah'];
                                        $record->save();
                                    }),
                                Action::make('stock_out')
                                    ->label('Stock Keluar')
                                    ->icon('heroicon-o-minus-circle')
                                    ->color('danger')
                                    ->form([
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
                                        $record->out += $data['jumlah'];
                                        $record->save();
                                    }),
                                Action::make('return_out')
                                    ->label('Cancel Use')
                                    ->icon('heroicon-o-arrow-uturn-right')
                                    ->color('info')
                                    ->form([
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
                                        $record->return_out += $data['jumlah'];
                                        $record->save();
                                    }),
                            ])
                            ->label('Actions')
                            ->icon('heroicon-m-ellipsis-vertical')
                            ->color('gray')
                            ->button(),
                        ])->columnSpanFull(),
                    ])->columns(4),

                Section::make('Calculation Summary (Otomatis)')
                    ->description('Dihitung otomatis berdasarkan harga dan stok (Netto setelah retur).')
                    ->schema([
                        TextEntry::make('price_begins_balance')
                            ->label('Price Begins Balance')
                            ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                            ->weight(FontWeight::Bold)
                            ->helperText('Price Per Unit × Stock Awal'),
                        TextEntry::make('total_purchase_price')
                            ->label('Total Purchase Price')
                            ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                            ->helperText('Price Per Unit × (Stock Masuk − Retur Masuk)'),
                        TextEntry::make('cogs')
                            ->label('COGS')
                            ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                            ->color('danger')
                            ->helperText('Price Per Unit × (Stock Keluar − Retur Keluar)'),
                        TextEntry::make('total_sales_price')
                            ->label('Total Sales Price')
                            ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                            ->color('success')
                            ->helperText('Selling Price × (Stock Keluar − Retur Keluar)'),
                        TextEntry::make('income')
                            ->label('Income')
                            ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                            ->weight(FontWeight::Bold)
                            ->color(fn ($record) => ($record->income ?? 0) >= 0 ? 'success' : 'danger')
                            ->helperText('Total Sales Price − COGS'),
                        TextEntry::make('price_end_balance')
                            ->label('Price End Balance')
                            ->formatStateUsing(fn ($state) => 'IDR ' . number_format($state, 4, '.', ','))
                            ->weight(FontWeight::Bold)
                            ->helperText('Price Per Unit × Stock Akhir'),
                    ])->columns(3),
            ]);
    }
}
