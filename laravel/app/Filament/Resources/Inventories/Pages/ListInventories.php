<?php

namespace App\Filament\Resources\Inventories\Pages;

use App\Filament\Resources\Inventories\InventoryResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInventories extends ListRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import_excel')
                ->label('Import dari Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->url(InventoryResource::getUrl('import')),
            CreateAction::make(),
        ];
    }
}
