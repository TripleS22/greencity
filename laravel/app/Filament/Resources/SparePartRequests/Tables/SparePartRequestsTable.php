<?php

namespace App\Filament\Resources\SparePartRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SparePartRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mechanicAnalysis.unit_code')
                    ->label('Unit Code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('inventory.item_sparepart')
                    ->label('Spare Part')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'procurement' => 'info',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('admin_note')
                    ->label('Admin Note')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                \Filament\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update(['status' => 'approved'])),
                \Filament\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('admin_note')
                            ->label('Reason for Rejection')
                            ->required(),
                    ])
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record, array $data) => $record->update([
                        'status' => 'rejected',
                        'admin_note' => $data['admin_note'],
                    ])),
                \Filament\Actions\Action::make('schedule')
                    ->label('Schedule Procurement')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('admin_note')
                            ->label('Procurement Note')
                            ->placeholder('Expected date, supplier, etc.'),
                    ])
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record, array $data) => $record->update([
                        'status' => 'procurement',
                        'admin_note' => $data['admin_note'],
                    ])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
