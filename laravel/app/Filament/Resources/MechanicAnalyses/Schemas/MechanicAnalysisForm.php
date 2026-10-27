<?php

namespace App\Filament\Resources\MechanicAnalyses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MechanicAnalysisForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Report Information')
                    ->description('Data original dari laporan driver.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('unit_code')
                            ->disabled()
                            ->dehydrated(),
                        DatePicker::make('tanggal')
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('unit_driver_problem')
                            ->label('Masalah yang Dilaporkan')
                            ->columnSpanFull()
                            ->disabled()
                            ->dehydrated(),
                    ]),

                Section::make('Analysis Details')
                    ->description('Detail analisa dan penugasan mechanic.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('nama_mechanic_satu')
                            ->label('Mechanic Utama')
                            ->required(),
                        TextInput::make('nama_mechanic_dua')
                            ->label('Mechanic Pendamping'),
                        TextInput::make('smu_down')
                            ->label('SMU Down (Km Awal)')
                            ->required()
                            ->numeric(),
                        Select::make('location_unit')
                            ->label('Location Unit')
                            ->options([
                                'Sukamulya' => 'Sukamulya',
                                'Sadang' => 'Sadang',
                            ])
                            ->placeholder('Select Location'),
                        Select::make('status_analysis')
                            ->options([
                                'Pending' => 'Pending',
                                'In Progress' => 'In Progress',
                                'Completed' => 'Completed',
                            ])
                            ->default('Pending')
                            ->required(),
                        Textarea::make('hasil_analisis')
                            ->label('Hasil Analisis')
                            ->columnSpanFull(),
                        Textarea::make('saran_perbaikan')
                            ->label('Saran Perbaikan')
                            ->columnSpanFull(),
                    ]),

                Section::make('Spare Parts to Replace')
                    ->description('Pilih spare part yang perlu diganti dari inventory.')
                    ->schema([
                        \Filament\Forms\Components\Repeater::make('sparePartRequests')
                            ->relationship()
                            ->schema([
                                Select::make('inventory_id')
                                    ->relationship('inventory', 'item_sparepart')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Spare Part')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->item_sparepart} (Stock: {$record->ends_balance})")
                                    ->columnSpan(3),
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->step(1)
                                    ->default(1)
                                    ->required()
                                    ->label('Units')
                                    ->columnSpan(1),
                                Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'approved' => 'Approved',
                                        'rejected' => 'Rejected',
                                        'procurement' => 'Scheduled for Procurement',
                                    ])
                                    ->default('pending')
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(1),
                                TextInput::make('admin_note')
                                    ->label('Admin Note')
                                    ->disabled()
                                    ->placeholder('Waiting for admin...')
                                    ->columnSpanFull(),
                            ])
                            ->columns(5)
                            ->columnSpanFull()
                            ->itemLabel(function (array $state): ?string {
                                $item = \App\Models\Inventory::find($state['inventory_id']);
                                $name = $item?->item_sparepart ?? 'Spare Part';
                                $qty = $state['quantity'] ?? 0;
                                $status = ucfirst($state['status'] ?? 'pending');
                                return "{$name} ({$qty} units) - [{$status}]";
                            }),
                    ]),
            ]);
    }
}
