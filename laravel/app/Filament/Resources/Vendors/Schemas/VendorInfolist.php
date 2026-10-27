<?php

namespace App\Filament\Resources\Vendors\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VendorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Vendor')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Vendor'),
                        TextEntry::make('phone')
                            ->label('Telepon')
                            ->placeholder('-'),
                        TextEntry::make('email')
                            ->label('Email')
                            ->placeholder('-'),
                        TextEntry::make('contact_person')
                            ->label('Kontak Person')
                            ->placeholder('-'),
                        TextEntry::make('address')
                            ->label('Alamat')
                            ->placeholder('-'),
                        TextEntry::make('notes')
                            ->label('Catatan')
                            ->placeholder('-'),
                        TextEntry::make('inventories_count')
                            ->label('Jumlah Item Inventory')
                            ->state(fn ($record) => $record->inventories()->count() . ' item'),
                    ])->columns(2),
            ]);
    }
}
