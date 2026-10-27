<?php

namespace App\Filament\Resources\SparePartRequests\Pages;

use App\Filament\Resources\SparePartRequests\SparePartRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSparePartRequest extends EditRecord
{
    protected static string $resource = SparePartRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
