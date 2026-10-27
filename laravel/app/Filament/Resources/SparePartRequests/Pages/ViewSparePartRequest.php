<?php

namespace App\Filament\Resources\SparePartRequests\Pages;

use App\Filament\Resources\SparePartRequests\SparePartRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSparePartRequest extends ViewRecord
{
    protected static string $resource = SparePartRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
