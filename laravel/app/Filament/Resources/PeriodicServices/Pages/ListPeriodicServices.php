<?php

namespace App\Filament\Resources\PeriodicServices\Pages;

use App\Filament\Resources\PeriodicServices\PeriodicServiceMonitoringResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPeriodicServices extends ListRecords
{
    protected static string $resource = PeriodicServiceMonitoringResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
