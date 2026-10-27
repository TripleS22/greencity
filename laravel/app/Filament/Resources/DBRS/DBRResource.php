<?php

namespace App\Filament\Resources\DBRS;

use App\Filament\Resources\DBRS\Pages\CreateDBR;
use App\Filament\Resources\DBRS\Pages\EditDBR;
use App\Filament\Resources\DBRS\Pages\ListDBRS;
use App\Filament\Resources\DBRS\Pages\ViewDBR;
use App\Filament\Resources\DBRS\Schemas\DBRForm;
use App\Filament\Resources\DBRS\Schemas\DBRInfolist;
use App\Filament\Resources\DBRS\Tables\DBRSTable;
use App\Models\DBR;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DBRResource extends Resource
{
    protected static ?string $model = DBR::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'DBR';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return DBRForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DBRInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DBRSTable::configure($table);
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
            'index' => ListDBRS::route('/'),
            'create' => CreateDBR::route('/create'),
            'view' => ViewDBR::route('/{record}'),
            'edit' => EditDBR::route('/{record}/edit'),
        ];
    }
}
