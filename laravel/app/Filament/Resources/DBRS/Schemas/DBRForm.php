<?php

namespace App\Filament\Resources\DBRS\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DBRForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Dasar')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        DatePicker::make('date')
                            ->label('Report Date')
                            ->live()
                            ->afterStateUpdated(function ($get, $set) {
                                self::updateDuration($get, $set);
                                if ($date = $get('date')) {
                                    $set('week', \Illuminate\Support\Carbon::parse($date)->weekOfYear);
                                }
                            }),
                        TextInput::make('week')
                            ->required()
                            ->numeric()
                            ->readOnly(),
                        Select::make('unit_status')
                            ->options([
                                \App\Models\DBR::STATUS_BREAKDOWN => \App\Models\DBR::STATUS_BREAKDOWN,
                                \App\Models\DBR::STATUS_RFU => \App\Models\DBR::STATUS_RFU,
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $record) {
                                $status = trim(strtolower($state ?? ''));
                                // Handle record as DBR model instance or null depending on context
                                $isApproved = false;
                                if ($record) {
                                    $isApproved = $record->mechanicReport?->status_approval === 'Approved';
                                }

                                if ($status === strtolower(\App\Models\DBR::STATUS_BREAKDOWN) && !$isApproved) {
                                    $set('remarks', 'Continue');
                                } elseif ($status === strtolower(\App\Models\DBR::STATUS_RFU) || $isApproved) {
                                    $set('remarks', null);
                                }
                            }),
                        TextInput::make('remarks')
                            ->readOnly(),
                    ]),

                Section::make('Informasi Unit')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        Select::make('unit_code')
                            ->label('Unit Code')
                            ->options(\App\Models\Population::pluck('unit_code', 'unit_code'))
                            ->required()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $unit = \App\Models\Population::where('unit_code', $state)->first();
                                    if ($unit) {
                                        $set('eqp_type', $unit->eqp_type);
                                        $set('brand', $unit->brand);
                                        $set('model', $unit->model);
                                        $set('location_unit', $unit->loc_unit);
                                        $set('project', $unit->project);
                                    }
                                }
                            }),
                        TextInput::make('eqp_type')
                            ->required()
                            ->readOnly(),
                        TextInput::make('brand')
                            ->required()
                            ->readOnly(),
                        TextInput::make('model')
                            ->required()
                            ->readOnly(),
                        TextInput::make('location_unit')
                            ->required()
                            ->readOnly(),
                        TextInput::make('project')
                            ->required()
                            ->readOnly(),
                    ]),

                Section::make('Detail Breakdown & Aktivitas')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        DatePicker::make('date_bd')
                            ->label('Date Breakdown')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($get, $set) => self::updateDuration($get, $set)),
                        TimePicker::make('time_start_bd')
                            ->label('Time Start Breakdown')
                            ->required()
                            ->live(),
                        TextInput::make('smu_down')
                            ->label('SMU Down (Km Awal)')
                            ->required()
                            ->numeric(),
                        
                        TimePicker::make('time_start')
                            ->label('Work Start')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($get, $set) => self::updateDuration($get, $set)),
                        TimePicker::make('time_finish')
                            ->label('Work Finish')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($get, $set) => self::updateDuration($get, $set)),
                        TextInput::make('smu_ready')
                            ->label('SMU Ready (Km Akhir)')
                            ->required()
                            ->numeric(),

                        TextInput::make('shift_down')
                            ->required()
                            ->readOnly(),
                        TextInput::make('duration_db')
                            ->label('Duration BD')
                            ->readOnly()
                            ->afterStateHydrated(function ($set, $record) {
                                if ($record) {
                                    $set('duration_db', $record->duration_db);
                                }
                            }),
                    ]),

                Section::make('Kategorisasi & Status')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('category_bd')
                            ->options([
                                'BD Minor' => 'BD Minor',
                                'BD Major' => 'BD Major',
                            ])
                            ->required(),
                        Select::make('status_code')
                            ->options([
                                'BS' => 'BS', 'B0' => 'B0', 'B1' => 'B1', 'B2' => 'B2', 
                                'B3' => 'B3', 'B4' => 'B4', 'B5' => 'B5', 'B6' => 'B6', 
                                'B7' => 'B7', 'B8' => 'B8', 'B9' => 'B9', 'OPP' => 'OPP',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if (!$state) return;
                                $set('mtc_type', match ($state) {
                                    'BS' => 'Scheduled Breakdown',
                                    'B4' => 'Abuse',
                                    'B9' => 'Opportunity',
                                    default => 'Unscheduled Breakdown',
                                });
                                $set('status_bd', match ($state) {
                                    'BS' => 'BS (Breakdown Schedule)',
                                    'B0' => 'BUS (On Progress)',
                                    'B1' => 'BUS (Waiting Part)',
                                    'B2' => 'BUS (Waiting Manpower)',
                                    'B3' => 'BUS (Warranty Dealer)',
                                    'B4' => 'ABS (Accident, Miss Opt, Miss Applicable, Miss Maintenance)',
                                    'B5' => 'BUS (Breakdown Tire)',
                                    'B6' => 'BUS (Out Site Repair, On Progress Repair External Vendor)',
                                    'B7' => 'BUS (Waiting Tools, Support Lifting, Mobilization, dll)',
                                    'B8' => 'Off Hire, Commissioning, Mobe Demob',
                                    'B9' => 'BS (Wait Weather, Unit Stand By)',
                                    'OPP' => 'Schedule Maintenance',
                                    default => '',
                                });
                                $set('count_mtc_type', match ($state) {
                                    'BS' => 'Scheduled',
                                    'B4' => 'Unscheduled',
                                    'B9' => 'Opportunity',
                                    default => 'Unscheduled',
                                });
                                $set('task_down', match ($state) {
                                    'B0', 'B1', 'B2', 'B3', 'B5', 'B6', 'B7' => 'BUS',
                                    'BS' => 'BS',
                                    'B4' => 'ABS',
                                    'B8' => 'Off Hire',
                                    'B9' => 'BS',
                                    'OPP' => 'Schedule Maintenance',
                                    default => '-',
                                });
                            }),
                        TextInput::make('mtc_type')->required()->readOnly(),
                        TextInput::make('status_bd')->required()->readOnly(),
                        TextInput::make('count_mtc_type')->required()->readOnly(),
                        TextInput::make('task_down')->required()->readOnly(),
                        TextInput::make('problem_reported'),
                        TextInput::make('activity'),
                    ]),

                Section::make('Personel')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextInput::make('nama_driver')->label('Driver Name'),
                        TextInput::make('nama_mechanic_satu')->label('Lead Mechanic'),
                        TextInput::make('nama_mechanic_dua')->label('Assistant Mechanic'),
                    ]),

                Section::make('Suku Cadang & Finansial')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('spare_parts')
                            ->label('Spare Parts Used')
                            ->schema([
                                Select::make('component')
                                    ->label('Spare Part')
                                    ->options(\App\Models\Inventory::pluck('item_sparepart', 'item_sparepart'))
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $inventory = \App\Models\Inventory::where('item_sparepart', $state)->first();
                                            if ($inventory) {
                                                $set('unit', $inventory->unit);
                                                $set('purchase_price', $inventory->price_per_unit);
                                                $set('price_per_pcs', $inventory->selling_price);
                                                $set('qty', 1);
                                                $set('amount', $inventory->selling_price);
                                                $set('total', $inventory->selling_price);
                                            }
                                        }
                                    }),
                                TextInput::make('qty')
                                    ->numeric()
                                    ->step(1)
                                    ->default(1)
                                    ->live()
                                    ->afterStateUpdated(fn ($state, $set, $get) => $set('amount', $state * ($get('price_per_pcs') ?? 0))),
                                TextInput::make('unit')->readOnly(),
                                TextInput::make('purchase_price')->numeric()->prefix('Rp')->readOnly(),
                                TextInput::make('price_per_pcs')->label('Selling Price')->numeric()->prefix('Rp')->live()
                                    ->afterStateUpdated(fn ($state, $set, $get) => $set('amount', $state * ($get('qty') ?? 0))),
                                TextInput::make('amount')->numeric()->prefix('Rp')->readOnly(),
                                TextInput::make('service_charge')->numeric()->prefix('Rp')->default(0)->live()
                                    ->afterStateUpdated(fn ($state, $set, $get) => $set('total', $state + ($get('amount') ?? 0))),
                                TextInput::make('total')->numeric()->prefix('Rp')->readOnly(),
                            ])
                            ->columns(4)
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $spareParts = collect($state)->filter(fn($item) => is_array($item));
                                $set('sales_sparepart', $spareParts->sum(fn($item) => (float) ($item['amount'] ?? 0)));
                                $set('sales_service', $spareParts->sum(fn($item) => (float) ($item['service_charge'] ?? 0)));
                                $set('total', $spareParts->sum(fn($item) => (float) ($item['total'] ?? 0)));
                            }),

                        Grid::make(3)
                            ->schema([
                                Placeholder::make('sales_sparepart_display')
                                    ->label('Penjualan Sparepart')
                                    ->content(fn ($get) => new \Illuminate\Support\HtmlString(
                                        \Illuminate\Support\Facades\Blade::render(
                                            '<x-filament::badge color="info" size="lg">{{ $value }}</x-filament::badge>',
                                            ['value' => 'Rp ' . number_format((float) ($get('sales_sparepart') ?? 0), 0, ',', '.')]
                                        )
                                    )),

                                Placeholder::make('sales_service_display')
                                    ->label('Penjualan Service')
                                    ->content(fn ($get) => new \Illuminate\Support\HtmlString(
                                        \Illuminate\Support\Facades\Blade::render(
                                            '<x-filament::badge color="success" size="lg">{{ $value }}</x-filament::badge>',
                                            ['value' => 'Rp ' . number_format((float) ($get('sales_service') ?? 0), 0, ',', '.')]
                                        )
                                    )),

                                Placeholder::make('total_display')
                                    ->label('Total Keseluruhan')
                                    ->content(fn ($get) => new \Illuminate\Support\HtmlString(
                                        \Illuminate\Support\Facades\Blade::render(
                                            '<x-filament::badge color="warning" size="lg">{{ $value }}</x-filament::badge>',
                                            ['value' => 'Rp ' . number_format((float) ($get('total') ?? 0), 0, ',', '.')]
                                        )
                                    )),

                                Hidden::make('sales_sparepart'),
                                Hidden::make('sales_service'),
                                Hidden::make('total'),
                            ]),
                    ]),
            ]);
    }

    protected static function updateDuration($get, $set)
    {
        $dateBd = $get('date_bd');
        $timeStart = $get('time_start_bd'); // Updated to use time_start_bd for duration or keep as is?
        // Let's stick to the previous logic but ensure fields are correct
        $timeStartField = $get('time_start');
        $dateReady = $get('date');
        $timeFinish = $get('time_finish');

        if (!$dateBd || !$timeStartField || !$dateReady || !$timeFinish) {
            return;
        }

        try {
            $start = \Illuminate\Support\Carbon::parse($dateBd . ' ' . $timeStartField);
            $finish = \Illuminate\Support\Carbon::parse($dateReady . ' ' . $timeFinish);

            if ($finish->lt($start)) {
                $set('duration_db', '-');
                return;
            }

            $diff = $start->diff($finish);
            $parts = [];
            if ($diff->d > 0) $parts[] = $diff->d . ' Hari';
            if ($diff->h > 0) $parts[] = $diff->h . ' Jam';
            if ($diff->i > 0) $parts[] = $diff->i . ' Menit';
            
            $set('duration_db', empty($parts) ? '0 Menit' : implode(' ', $parts));
        } catch (\Exception $e) {
            $set('duration_db', '-');
        }

        if ($timeStartField) {
            try {
                $hour = (int)\Illuminate\Support\Carbon::parse($timeStartField)->format('H');
                $set('shift_down', ($hour >= 17 || $hour <= 5) ? 'NS' : 'DS');
            } catch (\Exception $e) {}
        }
    }
}
