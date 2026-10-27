<?php

namespace App\Filament\Resources\InventoryHistories\Pages;

use App\Filament\Resources\InventoryHistories\InventoryHistoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInventoryHistories extends ListRecords
{
    protected static string $resource = InventoryHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
