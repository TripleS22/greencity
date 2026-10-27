<?php

namespace App\Exports;

use App\Models\DBR;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DBRsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function query()
    {
        return DBR::query()->with('population')->orderBy('date', 'asc');
    }

    public function headings(): array
    {
        return [
            'Created At',
            'Updated At',
            'Date',
            'Week',
            'EQP Type',
            'Unit Code',
            'Brand',
            'Model',
            'Shift Down',
            'Location Unit',
            'Category BD',
            'Date BD',
            'Time Start BD',
            'SMU Down',
            'Time Start',
            'Time Finish',
            'SMU Ready',
            'Duration BD',
            'Project',
            'Status Code',
            'MTC Type',
            'Status Breakdown',
            'Problem Reported',
            'Activity',
            'Unit Status',
            'Remarks',
            'Count MTC Type',
            'Task Down',
            'Nama Driver',
            'Nama Mechanic 1',
            'Nama Mechanic 2',
            'Components',
            'Total Sparepart',
            'Total Service',
            'Grand Total',
        ];
    }

    public function map($record): array
    {
        // Shift Down
        $shiftDown = '-';
        if ($record->time_start) {
            $hour = Carbon::parse($record->time_start)->format('H');
            $shiftDown = ($hour >= 17 || $hour <= 5) ? 'NS' : 'DS';
        }

        // MTC Type
        $mtcType = '';
        if (!empty($record->status_code) && $record->status_code !== '-') {
            $mtcType = match ($record->status_code) {
                'BS'    => 'Scheduled Breakdown',
                'B4'    => 'Abuse',
                'B9'    => 'Opportunity',
                default => 'Unscheduled Breakdown',
            };
        }

        // Status BD
        $statusBd = '';
        if (!empty($record->status_code) && $record->status_code !== '-') {
            $statusBd = match ($record->status_code) {
                'BS'  => 'BS (Breakdown Schedule)',
                'B0'  => 'BUS (On Progress)',
                'B1'  => 'BUS (Waiting Part)',
                'B2'  => 'BUS (Waiting Manpower)',
                'B3'  => 'BUS (Warranty Dealer)',
                'B4'  => 'ABS (Accident, Miss Opt, Miss Applicable, Miss Maintenance)',
                'B5'  => 'BUS (Breakdown Tire)',
                'B6'  => 'BUS (Out Site Repair, On Progress Repair External Vendor)',
                'B7'  => 'BUS (Waiting Tools, Support Lifting, Mobilization, dll)',
                'B8'  => 'Off Hire, Commissioning, Mobe Demob',
                'B9'  => 'BS (Wait Weather, Unit Stand By)',
                'OPP' => 'Schedule Maintenance',
                default => '',
            };
        }

        // Count MTC Type
        $countMtcType = 'Opportunity';
        if (!empty($record->status_code) && $record->status_code !== '-') {
            $countMtcType = match ($record->status_code) {
                'BS'    => 'Scheduled',
                'B4'    => 'Unscheduled',
                'B9'    => 'Opportunity',
                default => 'Unscheduled',
            };
        }

        // Task Down
        $taskDown = '-';
        if (!empty($record->status_code) && $record->status_code !== '-') {
            $taskDown = match ($record->status_code) {
                'B0', 'B1', 'B2', 'B3', 'B5', 'B6', 'B7' => 'BUS',
                'BS'  => 'BS',
                'B4'  => 'ABS',
                'B8'  => 'Off Hire',
                'B9'  => 'BS',
                'OPP' => 'Schedule Maintenance',
                default => '-',
            };
        }

        // Components
        $spareParts = $record->spare_parts ?? [];
        $components = !empty($spareParts)
            ? collect($spareParts)->pluck('component')->implode(', ')
            : ($record->component ?? '-');

        return [
            $record->created_at?->format('d/m/Y H:i') ?? '-',
            $record->updated_at?->format('d/m/Y H:i') ?? '-',
            $record->date?->format('d/m/Y') ?? '-',
            $record->week,
            $record->eqp_type,
            $record->unit_code,
            $record->brand,
            $record->model,
            $shiftDown,
            $record->location_unit,
            $record->category_bd,
            $record->date_bd?->format('d/m/Y') ?? '-',
            $record->time_start_bd,
            $record->smu_down,
            $record->time_start,
            $record->time_finish,
            $record->smu_ready,
            $record->duration_db,
            $record->population?->project ?? '-',
            $record->status_code,
            $mtcType,
            $statusBd,
            $record->problem_reported,
            $record->activity,
            $record->unit_status,
            $record->remarks,
            $countMtcType,
            $taskDown,
            $record->nama_driver,
            $record->nama_mechanic_satu,
            $record->nama_mechanic_dua,
            $components,
            $record->sales_sparepart,
            $record->sales_service,
            $record->total,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1565C0'],
                ],
            ],
        ];
    }
}
