<?php

namespace App\Filament\Resources\MechanicReports;

use App\Filament\Resources\MechanicReports\Pages\CreateMechanicReport;
use App\Filament\Resources\MechanicReports\Pages\EditMechanicReport;
use App\Filament\Resources\MechanicReports\Pages\ListMechanicReports;
use App\Filament\Resources\MechanicReports\Pages\ViewMechanicReport;
use App\Filament\Resources\MechanicReports\Schemas\MechanicReportForm;
use App\Filament\Resources\MechanicReports\Schemas\MechanicReportInfolist;
use App\Filament\Resources\MechanicReports\Tables\MechanicReportsTable;
use App\Models\MechanicReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MechanicReportResource extends Resource
{
    protected static ?string $model = MechanicReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'MechanicReport';

    protected static string|\UnitEnum|null $navigationGroup = 'Mechanic';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return MechanicReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MechanicReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MechanicReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMechanicReports::route('/'),
            'create' => CreateMechanicReport::route('/create'),
            'view' => ViewMechanicReport::route('/{record}'),
            'edit' => EditMechanicReport::route('/{record}/edit'),
        ];
    }
}
