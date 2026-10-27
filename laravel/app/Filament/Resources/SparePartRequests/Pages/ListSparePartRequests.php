<?php

namespace App\Filament\Resources\SparePartRequests\Pages;

use App\Filament\Resources\SparePartRequests\SparePartRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSparePartRequests extends ListRecords
{
    protected static string $resource = SparePartRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
