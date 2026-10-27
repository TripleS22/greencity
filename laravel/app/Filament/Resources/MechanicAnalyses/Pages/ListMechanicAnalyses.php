<?php

namespace App\Filament\Resources\MechanicAnalyses\Pages;

use App\Filament\Resources\MechanicAnalyses\MechanicAnalysisResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMechanicAnalyses extends ListRecords
{
    protected static string $resource = MechanicAnalysisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
