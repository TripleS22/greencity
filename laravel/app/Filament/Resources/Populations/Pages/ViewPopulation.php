<?php

namespace App\Filament\Resources\Populations\Pages;

use App\Filament\Resources\Populations\PopulationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPopulation extends ViewRecord
{
    protected static string $resource = PopulationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
