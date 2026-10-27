<?php

namespace App\Filament\Resources\InventoryHistories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InventoryHistoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Inventory Information')
                    ->schema([
                        TextEntry::make('inventory.item_sparepart')
                            ->label('Item Sparepart')
                            ->weight(\Filament\Support\Enums\FontWeight::Bold)
                            ->color('primary'),
                    ]),
                Section::make('Stock Movement')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('begins')
                                    ->numeric()
                                    ->label('Opening Stock'),
                                TextEntry::make('in')
                                    ->numeric()
                                    ->label('Stock In'),
                                TextEntry::make('out')
                                    ->numeric()
                                    ->label('Stock Out'),
                                TextEntry::make('ends_balance')
                                    ->numeric()
                                    ->label('Closing Balance'),
                            ]),
                    ]),
                Section::make('Financial Movement')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('price_per_unit')
                                    ->money('IDR')
                                    ->label('Price Unit'),
                                TextEntry::make('selling_price')
                                    ->money('IDR')
                                    ->label('Selling Price'),
                                TextEntry::make('total_purchase_price')
                                    ->money('IDR')
                                    ->label('Total Purchase'),
                                TextEntry::make('cogs')
                                    ->money('IDR')
                                    ->label('COGS'),
                                TextEntry::make('total_sales_price')
                                    ->money('IDR')
                                    ->label('Total Sales'),
                                TextEntry::make('income')
                                    ->money('IDR')
                                    ->label('Income')
                                    ->weight(\Filament\Support\Enums\FontWeight::Bold)
                                    ->color(fn ($record) => ($record->income ?? 0) >= 0 ? 'success' : 'danger'),
                            ]),
                    ]),
                Section::make('History Details')
                    ->schema([
                        TextEntry::make('keterangan')
                            ->columnSpanFull()
                            ->placeholder('No description provided'),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('transaction_date')
                                    ->date()
                                    ->label('Transaction Date'),
                                TextEntry::make('created_at')
                                    ->dateTime()
                                    ->label('Recorded At'),
                                TextEntry::make('updated_at')
                                    ->dateTime()
                                    ->label('Last Updated'),
                            ]),
                    ]),
            ]);
    }
}
