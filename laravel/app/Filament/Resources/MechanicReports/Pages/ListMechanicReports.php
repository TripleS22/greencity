<?php

namespace App\Filament\Resources\MechanicReports\Pages;

use App\Filament\Resources\MechanicReports\MechanicReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMechanicReports extends ListRecords
{
    protected static string $resource = MechanicReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
