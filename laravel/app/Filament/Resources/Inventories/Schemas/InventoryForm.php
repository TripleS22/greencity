<?php

namespace App\Filament\Resources\Inventories\Schemas;

use App\Models\Vendor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InventoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Item Details')
                    ->schema([
                        TextInput::make('item_sparepart')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('pcs_ltl')
                            ->label('PCS/LTL')
                            ->maxLength(255),
                        TextInput::make('unit')
                            ->maxLength(255),
                    ])->columns(3),

                Section::make('Vendor')
                    ->schema([
                        Select::make('vendor_id')
                            ->label('Vendor / Supplier')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Vendor')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label('Telepon')
                                    ->maxLength(50),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(255),
                                TextInput::make('contact_person')
                                    ->label('Kontak Person')
                                    ->maxLength(255),
                                TextInput::make('address')
                                    ->label('Alamat')
                                    ->maxLength(255),
                                TextInput::make('notes')
                                    ->label('Catatan'),
                            ])
                            ->placeholder('Pilih vendor...'),
                    ]),

                Section::make('Pricing')
                    ->schema([
                        TextInput::make('price_per_unit')
                            ->numeric()
                            ->step(0.0001)
                            ->prefix('Rp')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $margin = (float) $get('margin') / 100;
                                $set('selling_price', $state * (1 + $margin));
                            }),
                        TextInput::make('margin')
                            ->label('Margin (%)')
                            ->numeric()
                            ->step(0.1)
                            ->suffix('%')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $price = (float) $get('price_per_unit');
                                $margin = (float) $state / 100;
                                $set('selling_price', $price * (1 + $margin));
                            }),
                        TextInput::make('service_charge')
                            ->label('Service Charge (Jasa)')
                            ->numeric()
                            ->step(0.0001)
                            ->prefix('Rp')
                            ->default(0),
                        TextInput::make('selling_price')
                            ->numeric()
                            ->step(0.0001)
                            ->prefix('Rp')
                            ->readOnly(),
                    ])->columns(3),

                Section::make('Stock Balance')
                    ->schema([
                        TextInput::make('begins')
                            ->label('Stock Awal')
                            ->numeric()
                            ->step(1)
                            ->default(0),
                        TextInput::make('in')
                            ->label('Stock Masuk')
                            ->numeric()
                            ->step(1)
                            ->reactive()
                            ->default(0),
                        TextInput::make('return_in')
                            ->label('Retur Masuk')
                            ->numeric()
                            ->step(1)
                            ->default(0)
                            ->maxValue(fn ($get) => (int) $get('in'))
                            ->helperText(fn ($get) => 'Maksimal Retur: ' . (int) $get('in')),
                        TextInput::make('out')
                            ->label('Stock Keluar')
                            ->numeric()
                            ->step(1)
                            ->reactive()
                            ->default(0),
                        TextInput::make('return_out')
                            ->label('Cancel Use')
                            ->numeric()
                            ->step(1)
                            ->default(0)
                            ->maxValue(fn ($get) => (int) $get('out'))
                            ->helperText(fn ($get) => 'Maksimal Cancel Use: ' . (int) $get('out')),
                        TextInput::make('ends_balance')
                            ->label('Stock Akhir')
                            ->numeric()
                            ->step(1)
                            ->default(0),
                    ])->columns(3),

                Section::make('Summary (Otomatis)')
                    ->description('Field ini dihitung otomatis berdasarkan harga dan stok.')
                    ->schema([
                        TextInput::make('total_purchase_price')
                            ->label('Total Purchase Price')
                            ->numeric()->step(0.0001)->prefix('Rp')->readOnly()
                            ->helperText('= Price Per Unit × Stock Masuk'),
                        TextInput::make('cogs')
                            ->label('COGS')
                            ->numeric()->step(0.0001)->prefix('Rp')->readOnly()
                            ->helperText('= Price Per Unit × Stock Keluar'),
                        TextInput::make('total_sales_price')
                            ->label('Total Sales Price')
                            ->numeric()->step(0.0001)->prefix('Rp')->readOnly()
                            ->helperText('= Selling Price × Stock Keluar'),
                        TextInput::make('income')
                            ->label('Income')
                            ->numeric()->step(0.0001)->prefix('Rp')->readOnly()
                            ->helperText('= Total Sales Price − COGS'),
                        TextInput::make('price_end_balance')
                            ->label('Price End Balance')
                            ->numeric()->step(0.01)->prefix('Rp')->readOnly()
                            ->helperText('= Price Per Unit × Stock Akhir'),
                    ])->columns(3),
            ]);
    }
}
