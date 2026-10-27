<?php

namespace App\Filament\Exports;

use App\Models\DBR;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class DBRExporter extends Exporter
{
    protected static ?string $model = DBR::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('date'),
            ExportColumn::make('week'),
            ExportColumn::make('eqp_type'),
            ExportColumn::make('unit_code'),
            ExportColumn::make('brand'),
            ExportColumn::make('model'),
            ExportColumn::make('shift_down')
                ->state(function ($record) {
                    if (!$record->time_start) {
                        return '-';
                    }
                    $hour = \Illuminate\Support\Carbon::parse($record->time_start)->format('H');
                    if ($hour >= 17 || $hour <= 5) {
                        return 'NS';
                    }
                    return 'DS';
                }),
            ExportColumn::make('location_unit'),
            ExportColumn::make('category_bd'),
            ExportColumn::make('date_bd'),
            ExportColumn::make('time_start_bd'),
            ExportColumn::make('smu_down'),
            ExportColumn::make('time_start'),
            ExportColumn::make('time_finish'),
            ExportColumn::make('smu_ready'),
            ExportColumn::make('duration_db')->label('Duration BD'),
            ExportColumn::make('population.project')->label('Project'),
            ExportColumn::make('status_code'),
            ExportColumn::make('mtc_type')
                ->label('MTC Type')
                ->state(function ($record) {
                    if (empty($record->status_code) || $record->status_code === '-') {
                        return '';
                    }
                    return match ($record->status_code) {
                        'BS' => 'Scheduled Breakdown',
                        'B4' => 'Abuse',
                        'B9' => 'Opportunity',
                        default => 'Unscheduled Breakdown',
                    };
                }),
            ExportColumn::make('status_bd')
                ->label('Status Breakdown')
                ->state(function ($record) {
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
                }),
            ExportColumn::make('problem_reported'),
            ExportColumn::make('activity'),
            ExportColumn::make('unit_status'),
            ExportColumn::make('remarks'),
            ExportColumn::make('count_mtc_type')
                ->label('Count MTC Type')
                ->state(function ($record) {
                    if (empty($record->status_code) || $record->status_code === '-') {
                        return 'Opportunity';
                    }
                    return match ($record->status_code) {
                        'BS' => 'Scheduled',
                        'B4' => 'Unscheduled', // Abuse is usually unscheduled
                        'B9' => 'Opportunity',
                        default => 'Unscheduled',
                    };
                }),
            ExportColumn::make('task_down')
                ->label('Task Down')
                ->state(function ($record) {
                    if (empty($record->status_code) || $record->status_code === '-') {
                        return '-';
                    }
                    return match ($record->status_code) {
                        'B0', 'B1', 'B2', 'B3', 'B5', 'B6', 'B7' => 'BUS',
                        'BS' => 'BS',
                        'B4' => 'ABS',
                        'B8' => 'Off Hire',
                        'B9' => 'BS',
                        'OPP' => 'Schedule Maintenance',
                        default => '-',
                    };
                }),
            ExportColumn::make('nama_driver'),
            ExportColumn::make('nama_mechanic_satu'),
            ExportColumn::make('nama_mechanic_dua'),
            ExportColumn::make('component')
                ->label('Components')
                ->state(function ($record) {
                    $spareParts = $record->spare_parts ?? [];
                    if (empty($spareParts)) {
                        return $record->component ?? '-';
                    }
                    return collect($spareParts)->pluck('component')->implode(', ');
                }),
            ExportColumn::make('sales_sparepart')->label('Total Sparepart'),
            ExportColumn::make('sales_service')->label('Total Service'),
            ExportColumn::make('total')->label('Grand Total'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your d b r export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
