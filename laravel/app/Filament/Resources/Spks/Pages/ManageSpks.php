<?php

namespace App\Filament\Resources\Spks\Pages;

use App\Filament\Resources\Spks\SpkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSpks extends ManageRecords
{
    protected static string $resource = SpkResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
