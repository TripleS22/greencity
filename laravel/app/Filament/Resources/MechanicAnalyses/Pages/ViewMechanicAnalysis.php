<?php

namespace App\Filament\Resources\MechanicAnalyses\Pages;

use App\Filament\Resources\MechanicAnalyses\MechanicAnalysisResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMechanicAnalysis extends ViewRecord
{
    protected static string $resource = MechanicAnalysisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
