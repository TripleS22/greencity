<?php

namespace App\Filament\Resources\PeriodicServices\Pages;

use App\Filament\Resources\PeriodicServices\PeriodicServiceMonitoringResource;
use Filament\Resources\Pages\EditRecord;

class EditPeriodicService extends EditRecord
{
    protected static string $resource = PeriodicServiceMonitoringResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
