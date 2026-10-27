<?php

namespace App\Filament\Resources\MechanicReports\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MechanicReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Reference')
                    ->schema([
                        Select::make('mechanic_analysis_id')
                            ->relationship('mechanicAnalysis', 'id')
                            ->label('Related Analysis')
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $analysis = \App\Models\MechanicAnalysis::find($state);
                                    if ($analysis) {
                                        $set('unit_code', $analysis->unit_code);
                                        $set('activity', "Perbaikan berdasarkan analisis: " . substr($analysis->hasil_analisis, 0, 50));
                                    }
                                }
                            }),
                    ]),

                Section::make('Report Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('unit_code')
                            ->required(),
                        TextInput::make('activity')
                            ->required(),
                        TimePicker::make('time_start')
                            ->required(),
                        TimePicker::make('time_finish')
                            ->required(),
                        TextInput::make('smu_ready')
                            ->label('SMU Ready (Km Akhir)')
                            ->required()
                            ->numeric(),
                        FileUpload::make('documentation')
                            ->label('Documentation (Images/PDF)')
                            ->disk('public')
                            ->directory('mechanic-reports')
                            ->multiple()
                            ->reorderable()
                            ->columnSpanFull()
                            ->required(),
                    ]),

                Section::make('Admin Approval')
                    ->description('Hanya diisi oleh admin.')
                    ->columns(2)
                    ->schema([
                        Select::make('status_approval')
                            ->options([
                                'Pending' => 'Pending',
                                'Approved' => 'Approved',
                                'Rejected' => 'Rejected',
                            ])
                            ->default('Pending')
                            // ->disabled() // Typically only editable via the table action or by admins
                            ->dehydrated(),
                        Textarea::make('admin_response')
                            ->label('Respon Admin')
                            ->columnSpanFull()
                            // ->disabled()
                            ->dehydrated(),
                    ]),
            ]);
    }
}
