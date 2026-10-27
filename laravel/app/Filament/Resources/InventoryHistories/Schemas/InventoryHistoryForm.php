<?php

namespace App\Filament\Resources\InventoryHistories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InventoryHistoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Inventory Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                \Filament\Forms\Components\Select::make('inventory_id')
                                    ->relationship('inventory', 'item_sparepart')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Item Sparepart'),
                                \Filament\Forms\Components\DatePicker::make('transaction_date')
                                    ->label('Tanggal Transaksi')
                                    ->default(now())
                                    ->required(),
                            ]),
                    ]),
                Section::make('Stock Details')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('begins')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Qty'),
                                TextInput::make('in')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Qty'),
                                TextInput::make('out')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Qty'),
                                TextInput::make('ends_balance')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Qty'),
                            ]),
                    ]),
                Section::make('Financial Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('price_per_unit')->numeric()->step(0.0001)->prefix('Rp')->label('Price Unit'),
                                TextInput::make('selling_price')->numeric()->step(0.0001)->prefix('Rp')->label('Selling Price'),
                                TextInput::make('total_purchase_price')->numeric()->step(0.0001)->prefix('Rp')->label('Total Purchase'),
                                TextInput::make('cogs')->numeric()->step(0.0001)->prefix('Rp')->label('COGS'),
                                TextInput::make('total_sales_price')->numeric()->step(0.0001)->prefix('Rp')->label('Total Sales'),
                                TextInput::make('income')->numeric()->step(0.0001)->prefix('Rp')->label('Income'),
                            ]),
                    ]),
                Section::make('Additional Information')
                    ->schema([
                        \Filament\Forms\Components\Textarea::make('keterangan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
