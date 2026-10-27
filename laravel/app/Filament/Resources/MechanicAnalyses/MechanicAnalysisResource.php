<?php

namespace App\Filament\Resources\MechanicAnalyses;

use App\Filament\Resources\MechanicAnalyses\Pages\CreateMechanicAnalysis;
use App\Filament\Resources\MechanicAnalyses\Pages\EditMechanicAnalysis;
use App\Filament\Resources\MechanicAnalyses\Pages\ListMechanicAnalyses;
use App\Filament\Resources\MechanicAnalyses\Pages\ViewMechanicAnalysis;
use App\Filament\Resources\MechanicAnalyses\RelationManagers\SparePartRequestsRelationManager;
use App\Filament\Resources\MechanicAnalyses\Schemas\MechanicAnalysisForm;
use App\Filament\Resources\MechanicAnalyses\Schemas\MechanicAnalysisInfolist;
use App\Filament\Resources\MechanicAnalyses\Tables\MechanicAnalysesTable;
use App\Models\MechanicAnalysis;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MechanicAnalysisResource extends Resource
{
    protected static ?string $model = MechanicAnalysis::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'MechanicAnalysis';

    protected static string|\UnitEnum|null $navigationGroup = 'Mechanic';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return MechanicAnalysisForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MechanicAnalysisInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MechanicAnalysesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SparePartRequestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMechanicAnalyses::route('/'),
            'create' => CreateMechanicAnalysis::route('/create'),
            'view' => ViewMechanicAnalysis::route('/{record}'),
            'edit' => EditMechanicAnalysis::route('/{record}/edit'),
        ];
    }
}
