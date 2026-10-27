<?php

namespace App\Filament\Resources\DriverReports\Pages;

use App\Filament\Resources\DriverReports\DriverReportResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDriverReport extends EditRecord
{
    protected static string $resource = DriverReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
