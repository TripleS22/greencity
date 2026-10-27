<?php

namespace App\Filament\Widgets;

use App\Models\DBR;
use App\Models\Population;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class PhysicalAvailabilityChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'PHYSICAL AVAILABILITY MACHINE';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->startOfMonth()->toDateString();
        $endDate = $this->filters['endDate'] ?? now()->endOfMonth()->toDateString();
        
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $daysInRange = $start->diffInDays($end) + 1;
        if ($daysInRange <= 0) $daysInRange = 1;

        // Group by Model
        $models = DBR::whereBetween('date', [$startDate, $endDate])
            ->distinct()
            ->pluck('model')
            ->filter()
            ->toArray();

        if (empty($models)) {
            $models = ['NO DATA'];
        }

        $labels = array_merge($models, ['TOTAL']);
        $actualData = [];
        $planData = [];
        $achievementData = [];

        $totalDowntime = 0;
        $totalUnits = Population::count();
        $totalAvailableHours = $totalUnits * $daysInRange * 24;

        foreach ($models as $modelName) {
            $modelUnits = Population::where('model', $modelName)->count();
            if ($modelUnits == 0) $modelUnits = 1;

            $availableHours = $modelUnits * $daysInRange * 24;
            
            $downtime = DBR::where('model', $modelName)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->sum('duration_hours');

            $pa = (($availableHours - $downtime) / $availableHours) * 100;
            
            $actualData[] = round($pa, 1);
            $planData[] = 85; 
            $achievementData[] = round(($pa / 85) * 100, 1);
            
            $totalDowntime += $downtime;
        }

        if ($totalAvailableHours > 0) {
            $totalPa = (($totalAvailableHours - $totalDowntime) / $totalAvailableHours) * 100;
            $actualData[] = round($totalPa, 1);
            $planData[] = 85;
            $achievementData[] = round(($totalPa / 85) * 100, 1);
        } else {
            $actualData[] = 0;
            $planData[] = 85;
            $achievementData[] = 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Plan PA',
                    'data' => $planData,
                    'backgroundColor' => '#a78bfa',
                    'borderColor' => '#a78bfa',
                    'type' => 'bar',
                ],
                [
                    'label' => 'Actual PA',
                    'data' => $actualData,
                    'backgroundColor' => '#22c55e',
                    'borderColor' => '#22c55e',
                    'type' => 'bar',
                ],
                [
                    'label' => 'Achievement PA',
                    'data' => $achievementData,
                    'borderColor' => '#eab308',
                    'backgroundColor' => '#eab308',
                    'type' => 'line',
                    'fill' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'min' => 0,
                    'max' => 140,
                    'ticks' => [
                        'callback' => "function(value) { return value + '%' }",
                    ],
                ],
            ],
        ];
    }
}
