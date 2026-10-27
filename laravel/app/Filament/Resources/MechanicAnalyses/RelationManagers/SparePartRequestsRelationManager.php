<?php

namespace App\Filament\Resources\MechanicAnalyses\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\CreateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class SparePartRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'sparePartRequests';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('inventory_id')
                    ->relationship('inventory', 'item_sparepart')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Spare Part'),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->default(1)
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'procurement' => 'Scheduled for Procurement',
                    ])
                    ->default('pending')
                    ->required(),
                Forms\Components\Textarea::make('admin_note')
                    ->label('Admin Note')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
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
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update(['status' => 'approved'])),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('admin_note')
                            ->label('Reason for Rejection')
                            ->required(),
                    ])
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record, array $data) => $record->update([
                        'status' => 'rejected',
                        'admin_note' => $data['admin_note'],
                    ])),
                Action::make('schedule')
                    ->label('Schedule Procurement')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->form([
                        Forms\Components\Textarea::make('admin_note')
                            ->label('Procurement Note')
                            ->placeholder('Expected date, supplier, etc.'),
                    ])
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record, array $data) => $record->update([
                        'status' => 'procurement',
                        'admin_note' => $data['admin_note'],
                    ])),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
