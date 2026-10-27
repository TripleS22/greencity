<?php

namespace App\Filament\Resources\DriverReports\Tables;

use App\Models\MechanicAnalysis;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DriverReportsTable
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
                TextColumn::make('report_role')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn ($state) => $state === 'Driver' ? 'info' : 'success')
                    ->sortable(),
                TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('unit_code')
                    ->searchable(),
                TextColumn::make('nama_pelapor')
                    ->searchable(),
                TextColumn::make('nama_driver')
                    ->searchable(),

                TextColumn::make('unit_driver_problem')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('setujui')
                    ->label('Setujui')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => !$record->is_approved)
                    ->action(function ($record) {
                        MechanicAnalysis::create([
                            'tanggal' => $record->tanggal,
                            'unit_code' => $record->unit_code,
                            'nama_driver' => $record->nama_driver,
                            'unit_driver_problem' => $record->unit_driver_problem,
                            'status_analysis' => 'Pending',
                        ]);

                        $record->update(['is_approved' => true]);

                        Notification::make()
                            ->title('Laporan Disetujui')
                            ->body('Data telah dikirim ke Mechanic Analis.')
                            ->success()
                            ->send();
                    }),
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
