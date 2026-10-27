<?php

namespace App\Filament\Resources\SparePartRequests;

use App\Filament\Resources\SparePartRequests\Pages\CreateSparePartRequest;
use App\Filament\Resources\SparePartRequests\Pages\EditSparePartRequest;
use App\Filament\Resources\SparePartRequests\Pages\ListSparePartRequests;
use App\Filament\Resources\SparePartRequests\Pages\ViewSparePartRequest;
use App\Filament\Resources\SparePartRequests\Schemas\SparePartRequestForm;
use App\Filament\Resources\SparePartRequests\Schemas\SparePartRequestInfolist;
use App\Filament\Resources\SparePartRequests\Tables\SparePartRequestsTable;
use App\Models\SparePartRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SparePartRequestResource extends Resource
{
    protected static ?string $model = SparePartRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'SparePartRequest';

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return SparePartRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SparePartRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SparePartRequestsTable::configure($table);
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
            'index' => ListSparePartRequests::route('/'),
            'create' => CreateSparePartRequest::route('/create'),
            'view' => ViewSparePartRequest::route('/{record}'),
            'edit' => EditSparePartRequest::route('/{record}/edit'),
        ];
    }
}
