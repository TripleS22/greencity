<?php

namespace App\Filament\Resources\PeriodicServices;

use App\Filament\Resources\PeriodicServices\Pages;
use App\Models\PeriodicService;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use App\Filament\Resources\PeriodicServices\Schemas\PeriodicServiceForm;
use App\Filament\Resources\PeriodicServices\Tables\PeriodicServicesTable;
use BackedEnum;
// use UnitEnum;

class PeriodicServiceMonitoringResource extends Resource
{
    protected static ?string $model = PeriodicService::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Monitoring Periodik Service';

    protected static ?string $slug = 'monitoring-periodik-service';

    // protected static string|UnitEnum|null $navigationGroup = 'Maintenance';

    public static function form(Schema $schema): Schema
    {
        return PeriodicServiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PeriodicServicesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeriodicServices::route('/'),
            'create' => Pages\CreatePeriodicService::route('/create'),
            'edit' => Pages\EditPeriodicService::route('/{record}/edit'),
        ];
    }
}
