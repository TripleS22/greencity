<?php

namespace App\Filament\Resources\Populations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PopulationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('unit_code')
                            ->required()
                            ->placeholder('e.g. DT-001'),
                        \Filament\Forms\Components\Select::make('unit_status')
                            ->options([
                                \App\Models\DBR::STATUS_BREAKDOWN => \App\Models\DBR::STATUS_BREAKDOWN,
                                \App\Models\DBR::STATUS_RFU => \App\Models\DBR::STATUS_RFU,
                            ])
                            ->placeholder('Select Status'),
                        TextInput::make('active')
                            ->placeholder('e.g. Yes / No'),
                        TextInput::make('loc_unit')
                            ->label('Location Unit'),
                    ])->columns(2),

                Section::make('Technical Details')
                    ->schema([
                        TextInput::make('eqp_type')
                            ->label('Equipment Type'),
                        TextInput::make('brand'),
                        TextInput::make('model'),
                        TextInput::make('eqp_class')
                            ->label('Equipment Class')
                            ->numeric(),
                        TextInput::make('machine_s_n')
                            ->label('Machine Serial Number'),
                        TextInput::make('engine_s_n')
                            ->label('Engine Serial Number'),
                    ])->columns(3),

                Section::make('Operational & Warranty')
                    ->schema([
                        TextInput::make('activity'),
                        DatePicker::make('delivery_date'),
                        TextInput::make('warranty_status'),
                        TextInput::make('year')
                            ->placeholder('e.g. 2024'),
                    ])->columns(2),

                Section::make('Ownership & Project')
                    ->schema([
                        TextInput::make('project'),
                        TextInput::make('owner'),
                        TextInput::make('maintenance_by'),
                    ])->columns(3),
            ]);
    }
}
