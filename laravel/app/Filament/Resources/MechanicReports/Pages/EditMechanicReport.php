<?php

namespace App\Filament\Resources\MechanicReports\Pages;

use App\Filament\Resources\MechanicReports\MechanicReportResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMechanicReport extends EditRecord
{
    protected static string $resource = MechanicReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
