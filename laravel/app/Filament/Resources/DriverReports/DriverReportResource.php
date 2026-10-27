<?php

namespace App\Filament\Resources\DriverReports;

use App\Filament\Resources\DriverReports\Pages\CreateDriverReport;
use App\Filament\Resources\DriverReports\Pages\EditDriverReport;
use App\Filament\Resources\DriverReports\Pages\ListDriverReports;
use App\Filament\Resources\DriverReports\Pages\ViewDriverReport;
use App\Filament\Resources\DriverReports\Schemas\DriverReportForm;
use App\Filament\Resources\DriverReports\Schemas\DriverReportInfolist;
use App\Filament\Resources\DriverReports\Tables\DriverReportsTable;
use App\Models\DriverReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DriverReportResource extends Resource
{
    protected static ?string $model = DriverReport::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $navigationLabel = 'Reports'; // Laporan Masuk
    
    protected static ?string $modelLabel = 'Laporan Masuk';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|\UnitEnum|null $navigationGroup = 'Mechanic';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return DriverReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DriverReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DriverReportsTable::configure($table);
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
            'index' => ListDriverReports::route('/'),
            'create' => CreateDriverReport::route('/create'),
            'view' => ViewDriverReport::route('/{record}'),
            'edit' => EditDriverReport::route('/{record}/edit'),
        ];
    }
}
