<?php

namespace App\Filament\Resources\PeriodicServices\Pages;

use App\Filament\Resources\PeriodicServices\PeriodicServiceMonitoringResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePeriodicService extends CreateRecord
{
    protected static string $resource = PeriodicServiceMonitoringResource::class;
}
