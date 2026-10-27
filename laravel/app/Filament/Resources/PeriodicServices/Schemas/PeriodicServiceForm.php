<?php

namespace App\Filament\Resources\PeriodicServices\Schemas;

use App\Models\Population;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PeriodicServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('unit_code')
                ->label('Unit Code')
                ->options(fn() => Population::pluck('unit_code', 'unit_code')->toArray())
                ->searchable()
                ->required()
                ->unique(ignoreRecord: true)
                ->validationMessages([
                    'unique' => 'Unit ini sudah terdaftar dalam monitoring.',
                ]),
            DatePicker::make('last_service_date')
                ->label('Tanggal Service Terakhir'),
            TextInput::make('last_service_smu')
                ->label('SMU Saat Service Terakhir')
                ->numeric(),
            TextInput::make('service_interval_km')
                ->label('KM Service (Interval)')
                ->numeric()
                ->default(8000),
            TextInput::make('average_daily_ritase')
                ->label('Rata-rata Ritase per Hari')
                ->numeric()
                ->default(0),
        ]);
    }
}
