<?php

namespace App\Filament\Resources\MechanicAnalyses\Pages;

use App\Filament\Resources\MechanicAnalyses\MechanicAnalysisResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMechanicAnalysis extends EditRecord
{
    protected static string $resource = MechanicAnalysisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
