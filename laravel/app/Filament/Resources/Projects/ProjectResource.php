<?php

namespace App\Filament\Resources\Projects;

use App\Models\Project;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Resources\Projects\Pages;
use Filament\Forms\Components\TextInput;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use BackedEnum;
use UnitEnum;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map';
    
    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $label = 'Project Distance';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Project Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Project Name')
                            ->required()
                            ->unique(Project::class, 'name', ignoreRecord: true),
                        TextInput::make('distance_km')
                            ->label('Distance (One Way)')
                            ->numeric()
                            ->required()
                            ->suffix('KM')
                            ->helperText('This distance will be multiplied by 2 and Ritase for accumulation.'),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Project Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('distance_km')
                    ->label('Distance (One Way)')
                    ->numeric()
                    ->suffix(' KM')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
