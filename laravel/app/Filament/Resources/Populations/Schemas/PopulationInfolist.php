<?php

namespace App\Filament\Resources\Populations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class PopulationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextEntry::make('unit_code')
                            ->weight(FontWeight::Bold),
                        TextEntry::make('unit_status')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'Ready' => 'success',
                                'Breakdown' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('active')
                            ->label('Active Status'),
                        TextEntry::make('loc_unit')
                            ->label('Location Unit'),
                    ])->columns(4),

                Section::make('Technical Details')
                    ->schema([
                        TextEntry::make('eqp_type')
                            ->label('Equipment Type'),
                        TextEntry::make('brand'),
                        TextEntry::make('model'),
                        TextEntry::make('eqp_class')
                            ->label('Equipment Class')
                            ->numeric(),
                        TextEntry::make('machine_s_n')
                            ->label('Machine Serial Number'),
                        TextEntry::make('engine_s_n')
                            ->label('Engine Serial Number'),
                    ])->columns(3),

                Section::make('Operational & Warranty')
                    ->schema([
                        TextEntry::make('activity'),
                        TextEntry::make('delivery_date')
                            ->date(),
                        TextEntry::make('warranty_status'),
                        TextEntry::make('year'),
                    ])->columns(2),

                Section::make('Ownership & Project')
                    ->schema([
                        TextEntry::make('project'),
                        TextEntry::make('owner'),
                        TextEntry::make('maintenance_by'),
                    ])->columns(3),

                Section::make('System Logs')
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ])->columns(2)
                    ->collapsed(),
            ]);
    }
}
