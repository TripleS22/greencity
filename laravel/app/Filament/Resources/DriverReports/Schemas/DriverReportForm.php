<?php

namespace App\Filament\Resources\DriverReports\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DriverReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('report_role')
                    ->label('Kategori Pelapor')
                    ->options([
                        'Driver' => 'Driver',
                        'Asmen/Manager' => 'Asmen / Manager Operasional',
                    ])
                    ->default('Driver')
                    ->required()
                    ->native(false),
                DatePicker::make('tanggal')
                    ->required(),
                Select::make('unit_code')
                    ->relationship('unit', 'unit_code')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('nama_driver')
                    ->required(),
                TextInput::make('nama_pelapor')
                    ->required(),

                Textarea::make('unit_driver_problem')
                    ->required(),
            ]);
    }
}
