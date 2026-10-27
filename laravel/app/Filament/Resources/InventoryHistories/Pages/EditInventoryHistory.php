<?php

namespace App\Filament\Resources\InventoryHistories\Pages;

use App\Filament\Resources\InventoryHistories\InventoryHistoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditInventoryHistory extends EditRecord
{
    protected static string $resource = InventoryHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
