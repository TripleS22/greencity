<?php

namespace App\Filament\Resources\DBRS\Pages;

use App\Filament\Resources\DBRS\DBRResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDBR extends EditRecord
{
    protected static string $resource = DBRResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
