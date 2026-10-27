<?php

namespace App\Filament\Resources\ResetStockHistories\Pages;

use App\Filament\Resources\ResetStockHistories\ResetStockHistoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageResetStockHistories extends ManageRecords
{
    protected static string $resource = ResetStockHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
