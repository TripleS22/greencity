<?php

namespace App\Filament\Resources\DriverReports\Pages;

use App\Filament\Resources\DriverReports\DriverReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDriverReports extends ListRecords
{
    protected static string $resource = DriverReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
