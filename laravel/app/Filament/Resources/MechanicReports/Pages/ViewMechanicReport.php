<?php

namespace App\Filament\Resources\MechanicReports\Pages;

use App\Filament\Resources\MechanicReports\MechanicReportResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMechanicReport extends ViewRecord
{
    protected static string $resource = MechanicReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('approve')
                ->label('Setujui / Putuskan')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->hidden(fn ($record) => $record->status_approval === 'Approved')
                ->form([
                    \Filament\Forms\Components\Select::make('status_approval')
                        ->label('Status Approval')
                        ->options([
                            'Approved' => 'Approved',
                            'Rejected' => 'Rejected',
                        ])
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('admin_response')
                        ->label('Respon Admin')
                        ->placeholder('Berikan alasan atau instruksi tambahan...'),
                ])
                ->action(function ($record, array $data) {
                    $record->update($data);
                    \Filament\Notifications\Notification::make()
                        ->title('Laporan Diperbarui')
                        ->success()
                        ->send();
                }),
            EditAction::make(),
        ];
    }
}
