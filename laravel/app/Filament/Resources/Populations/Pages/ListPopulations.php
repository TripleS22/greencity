<?php

namespace App\Filament\Resources\Populations\Pages;

use App\Filament\Resources\Populations\PopulationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPopulations extends ListRecords
{
    protected static string $resource = PopulationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
