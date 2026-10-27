<?php

namespace App\Filament\Resources\DBRS\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DBRsExport;

class DBRSTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date')
                    ->date()
                    ->sortable()
                    ->extraHeaderAttributes([
                        'style' => 'position: sticky; left: 0; min-width: 120px; z-index: 10;',
                        'data-sticky' => true,
                    ])
                    ->extraCellAttributes([
                        'style' => 'position: sticky; left: 0; min-width: 120px; z-index: 9;',
                        'data-sticky' => true,
                    ]),
                TextColumn::make('week')
                    ->numeric()
                    ->sortable()
                    ->extraHeaderAttributes([
                        'style' => 'position: sticky; left: 120px; min-width: 80px; z-index: 10;',
                        'data-sticky' => true,
                    ])
                    ->extraCellAttributes([
                        'style' => 'position: sticky; left: 120px; min-width: 80px; z-index: 9;',
                        'data-sticky' => true,
                    ]),
                TextColumn::make('eqp_type')
                    ->searchable()
                    ->extraHeaderAttributes([
                        'style' => 'position: sticky; left: 200px; min-width: 120px; z-index: 10;',
                        'data-sticky' => true,
                    ])
                    ->extraCellAttributes([
                        'style' => 'position: sticky; left: 200px; min-width: 120px; z-index: 9;',
                        'data-sticky' => true,
                    ]),
                TextColumn::make('unit_code')
                    ->searchable()
                    ->extraHeaderAttributes([
                        'style' => 'position: sticky; left: 320px; min-width: 130px; z-index: 10; box-shadow: 4px 0 6px -2px rgba(0,0,0,0.15);',
                        'data-sticky' => true,
                    ])
                    ->extraCellAttributes([
                        'style' => 'position: sticky; left: 320px; min-width: 130px; z-index: 9; box-shadow: 4px 0 6px -2px rgba(0,0,0,0.15);',
                        'data-sticky' => true,
                    ]),
                TextColumn::make('brand')
                    ->searchable(),
                TextColumn::make('model')
                    ->searchable(),
                TextColumn::make('shift_down')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'NS' => 'success',
                        'DS' => 'warning',
                        default => 'gray',
                    })
                    ->getStateUsing(function ($record) {
                        if (!$record->time_start) {
                            return '-';
                        }
                        
                        $hour = \Illuminate\Support\Carbon::parse($record->time_start)->format('H');
                        
                        if ($hour >= 17 || $hour <= 5) {
                            return 'NS';
                        }
                        
                        return 'DS';
                    }),
                TextColumn::make('location_unit')
                    ->searchable(),
                SelectColumn::make('category_bd')
                    ->options([
                        'BD Minor' => 'BD Minor',
                        'BD Major' => 'BD Major',
                    ])
                    ->searchable(),
                TextColumn::make('date_bd')
                    ->date()
                    ->sortable(),
                TextColumn::make('time_start_bd')
                    ->time()
                    ->sortable(),
                TextColumn::make('smu_down')
                    ->searchable(),
                TextColumn::make('time_start')
                    ->time()
                    ->sortable(),
                TextColumn::make('time_finish')
                    ->time()
                    ->sortable(),
                TextColumn::make('smu_ready')
                    ->searchable(),
                TextColumn::make('duration_db')
                    ->label('Duration BD')
                    ->sortable(),
                TextColumn::make('population.project')
                    ->label('Project')
                    ->searchable(),
                SelectColumn::make('status_code')
                    ->options([
                        'BS' => 'BS',
                        'B0' => 'B0',
                        'B1' => 'B1',
                        'B2' => 'B2',
                        'B3' => 'B3',
                        'B4' => 'B4',
                        'B5' => 'B5',
                        'B6' => 'B6',
                        'B7' => 'B7',
                        'B8' => 'B8',
                        'B9' => 'B9',
                        'OPP' => 'OPP',
                    ])
                    ->searchable(),
                TextColumn::make('mtc_type')
                    ->label('MTC Type')
                    ->getStateUsing(function ($record) {
                        if (empty($record->status_code) || $record->status_code === '-') {
                            return '';
                        }
                        
                        return match ($record->status_code) {
                            'BS' => 'Scheduled Breakdown',
                            'B4' => 'Abuse',
                            'B9' => 'Opportunity',
                            default => 'Unscheduled Breakdown',
                        };
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Scheduled Breakdown' => 'info',
                        'Abuse' => 'danger',
                        'Opportunity' => 'success',
                        'Unscheduled Breakdown' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('status_bd')
                    ->label('Status Breakdown')
                    ->getStateUsing(function ($record) {
                        if (empty($record->status_code) || $record->status_code === '-') {
                            return '';
                        }
                        
                        return match ($record->status_code) {
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
                        };
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_starts_with($state, 'BS') => 'info',
                        str_starts_with($state, 'BUS') => 'warning',
                        str_starts_with($state, 'ABS') => 'danger',
                        str_starts_with($state, 'Off Hire') => 'gray',
                        $state === 'Schedule Maintenance' => 'success',
                        default => 'gray',
                    })
                    ->wrap()
                    ->searchable(),
                TextColumn::make('problem_reported')
                    ->searchable(),
                TextColumn::make('activity')
                    ->searchable(),
                SelectColumn::make('unit_status')
                    ->options([
                        \App\Models\DBR::STATUS_BREAKDOWN => \App\Models\DBR::STATUS_BREAKDOWN,
                        \App\Models\DBR::STATUS_RFU => \App\Models\DBR::STATUS_RFU,
                    ])
                    ->afterStateUpdated(function ($state, $record) {
                        $status = trim(strtolower($state ?? ''));
                        
                        $isApproved = false;
                        if ($record->mechanic_analysis_id) {
                            $isApproved = \App\Models\MechanicReport::where('mechanic_analysis_id', $record->mechanic_analysis_id)
                                ->where('status_approval', 'Approved')
                                ->exists();
                        }

                        if ($status === strtolower(\App\Models\DBR::STATUS_BREAKDOWN) && !$isApproved) {
                            $record->update(['remarks' => 'Continue']);
                        } elseif ($status === strtolower(\App\Models\DBR::STATUS_RFU) || $isApproved) {
                            $record->update(['remarks' => null]);
                        }
                    })
                    ->searchable(),
                TextColumn::make('remarks')
                    ->badge()
                    ->color(fn ($state) => $state === 'Continue' ? 'danger' : 'gray')
                    ->searchable(),
                TextColumn::make('count_mtc_type')
                    ->label('Count MTC Type')
                    ->getStateUsing(function ($record) {
                        if (empty($record->status_code) || $record->status_code === '-') {
                            return 'Opportunity';
                        }
                        
                        return match ($record->status_code) {
                            'BS' => 'Scheduled',
                            'B4' => 'Unscheduled', // Abuse is usually unscheduled
                            'B9' => 'Opportunity',
                            default => 'Unscheduled',
                        };
                    })
                    ->searchable(),
                TextColumn::make('task_down')
                    ->label('Task Down')
                    ->getStateUsing(function ($record) {
                        if (empty($record->status_code) || $record->status_code === '-') {
                            return '-';
                        }

                        // Map status_code to their prefix labels as per status_bd logic
                        return match ($record->status_code) {
                            'B0', 'B1', 'B2', 'B3', 'B5', 'B6', 'B7' => 'BUS',
                            'BS' => 'BS',
                            'B4' => 'ABS',
                            'B8' => 'Off Hire',
                            'B9' => 'BS',
                            'OPP' => 'Schedule Maintenance',
                            default => '-',
                        };
                    })
                    ->searchable(),
                TextColumn::make('nama_driver')
                    ->searchable(),
                TextColumn::make('nama_mechanic_satu')
                    ->searchable(),
                TextColumn::make('nama_mechanic_dua')
                    ->searchable(),
                TextColumn::make('component')
                    ->label('Components')
                    ->getStateUsing(function ($record) {
                        $spareParts = $record->spare_parts ?? [];
                        if (empty($spareParts)) {
                            return $record->component ?? '-';
                        }
                        
                        return collect($spareParts)->pluck('component')->implode(', ');
                    })
                    ->wrap()
                    ->searchable(),
                TextColumn::make('sales_sparepart')
                    ->label('Total Sparepart')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('sales_service')
                    ->label('Total Service')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('total')
                    ->label('Grand Total')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                Action::make('export_dbr')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $fileName = 'dbr-export-' . now()->format('Y-m-d') . '.xlsx';
                        return Excel::download(new DBRsExport(), $fileName);
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
