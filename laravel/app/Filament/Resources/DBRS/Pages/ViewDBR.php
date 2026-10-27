<?php

namespace App\Filament\Resources\DBRS\Pages;

use App\Filament\Resources\DBRS\DBRResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDBR extends ViewRecord
{
    protected static string $resource = DBRResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
