<?php

namespace App\Filament\Resources\DBRS\Pages;

use App\Filament\Resources\DBRS\DBRResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDBRS extends ListRecords
{
    protected static string $resource = DBRResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
