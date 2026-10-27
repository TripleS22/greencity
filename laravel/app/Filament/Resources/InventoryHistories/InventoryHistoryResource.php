<?php

namespace App\Filament\Resources\InventoryHistories;

use App\Filament\Resources\InventoryHistories\Pages\CreateInventoryHistory;
use App\Filament\Resources\InventoryHistories\Pages\EditInventoryHistory;
use App\Filament\Resources\InventoryHistories\Pages\ListInventoryHistories;
use App\Filament\Resources\InventoryHistories\Pages\ViewInventoryHistory;
use App\Filament\Resources\InventoryHistories\Schemas\InventoryHistoryForm;
use App\Filament\Resources\InventoryHistories\Schemas\InventoryHistoryInfolist;
use App\Filament\Resources\InventoryHistories\Tables\InventoryHistoriesTable;
use App\Models\InventoryHistory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InventoryHistoryResource extends Resource
{
    protected static ?string $model = InventoryHistory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'InventoryHistory';

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return InventoryHistoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InventoryHistoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InventoryHistoriesTable::configure($table);
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
            'index' => ListInventoryHistories::route('/'),
            'create' => CreateInventoryHistory::route('/create'),
            'view' => ViewInventoryHistory::route('/{record}'),
            'edit' => EditInventoryHistory::route('/{record}/edit'),
        ];
    }
}
