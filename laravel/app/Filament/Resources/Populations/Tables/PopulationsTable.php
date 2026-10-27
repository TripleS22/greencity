<?php

namespace App\Filament\Resources\Populations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PopulationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('unit_code')
                    ->searchable(),
                TextColumn::make('unit_status')
                    ->searchable(),
                TextColumn::make('eqp_type')
                    ->searchable(),
                TextColumn::make('model')
                    ->searchable(),
                TextColumn::make('brand')
                    ->searchable(),
                TextColumn::make('eqp_class')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('machine_s_n')
                    ->searchable(),
                TextColumn::make('engine_s_n')
                    ->searchable(),
                TextColumn::make('activity')
                    ->searchable(),
                TextColumn::make('delivery_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('warranty_status')
                    ->searchable(),
                TextColumn::make('year'),
                TextColumn::make('project')
                    ->searchable(),
                TextColumn::make('owner')
                    ->searchable(),
                TextColumn::make('maintenance_by')
                    ->searchable(),
                TextColumn::make('loc_unit')
                    ->searchable(),
                TextColumn::make('active')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
