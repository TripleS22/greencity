<?php

namespace App\Filament\Resources\Populations\Pages;

use App\Filament\Resources\Populations\PopulationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPopulation extends EditRecord
{
    protected static string $resource = PopulationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
