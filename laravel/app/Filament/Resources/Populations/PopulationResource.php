<?php

namespace App\Filament\Resources\Populations;

use App\Filament\Resources\Populations\Pages\CreatePopulation;
use App\Filament\Resources\Populations\Pages\EditPopulation;
use App\Filament\Resources\Populations\Pages\ListPopulations;
use App\Filament\Resources\Populations\Pages\ViewPopulation;
use App\Filament\Resources\Populations\Schemas\PopulationForm;
use App\Filament\Resources\Populations\Schemas\PopulationInfolist;
use App\Filament\Resources\Populations\Tables\PopulationsTable;
use App\Models\Population;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PopulationResource extends Resource
{
    protected static ?string $model = Population::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Population';

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 99;

    public static function form(Schema $schema): Schema
    {
        return PopulationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PopulationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PopulationsTable::configure($table);
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
            'index' => ListPopulations::route('/'),
            'create' => CreatePopulation::route('/create'),
            'view' => ViewPopulation::route('/{record}'),
            'edit' => EditPopulation::route('/{record}/edit'),
        ];
    }
}
