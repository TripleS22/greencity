<?php

namespace App\Filament\Resources\Vendors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VendorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Vendor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('contact_person')
                    ->label('Kontak Person')
                    ->placeholder('-'),
                TextColumn::make('inventories_count')
                    ->label('Jumlah Item')
                    ->counts('inventories')
                    ->sortable(),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->placeholder('-')
                    ->limit(40),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
