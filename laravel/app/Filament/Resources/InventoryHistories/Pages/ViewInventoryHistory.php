<?php

namespace App\Filament\Resources\InventoryHistories\Pages;

use App\Filament\Resources\InventoryHistories\InventoryHistoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInventoryHistory extends ViewRecord
{
    protected static string $resource = InventoryHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
