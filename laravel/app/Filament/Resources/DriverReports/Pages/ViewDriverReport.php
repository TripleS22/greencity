<?php

namespace App\Filament\Resources\DriverReports\Pages;

use App\Filament\Resources\DriverReports\DriverReportResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDriverReport extends ViewRecord
{
    protected static string $resource = DriverReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
