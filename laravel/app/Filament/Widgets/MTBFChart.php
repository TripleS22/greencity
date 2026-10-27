<?php

namespace App\Filament\Widgets;

use App\Models\DBR;
use App\Models\Population;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class MTBFChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'MEAN TIME BETWEEN FAILURE (HOURS)';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->startOfMonth()->toDateString();
        $endDate = $this->filters['endDate'] ?? now()->endOfMonth()->toDateString();
        
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $daysInRange = $start->diffInDays($end) + 1;
        if ($daysInRange <= 0) $daysInRange = 1;

        $models = DBR::whereBetween('date', [$startDate, $endDate])
            ->distinct()
            ->pluck('model')
            ->filter()
            ->toArray();

        if (empty($models)) $models = ['NO DATA'];

        $labels = array_merge($models, ['TOTAL']);
        $mtbfData = [];
        $planMtbfData = [];

        $totalDowntime = 0;
        $totalEvents = 0;
        $totalUnits = Population::count();
        $totalAvailableHours = $totalUnits * $daysInRange * 24;

        foreach ($models as $modelName) {
            $modelUnits = Population::where('model', $modelName)->count();
            if ($modelUnits == 0) $modelUnits = 1;

            $availableHours = $modelUnits * $daysInRange * 24;
            
            $events = DBR::where('model', $modelName)
                ->whereBetween('date', [$startDate, $endDate])
                ->count();
            
            $downtime = DBR::where('model', $modelName)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->sum('duration_hours');

            $mtbf = $events > 0 ? (($availableHours - $downtime) / $events) : ($availableHours);
            
            $mtbfData[] = round($mtbf, 1);
            $planMtbfData[] = 80; 
            
            $totalDowntime += $downtime;
            $totalEvents += $events;
        }

        $totalMtbf = $totalEvents > 0 ? (($totalAvailableHours - $totalDowntime) / $totalEvents) : $totalAvailableHours;
        $mtbfData[] = round($totalMtbf, 1);
        $planMtbfData[] = 80;

        return [
            'datasets' => [
                [
                    'label' => 'Plan MTBF',
                    'data' => $planMtbfData,
                    'backgroundColor' => '#a78bfa',
                ],
                [
                    'label' => 'MTBF',
                    'data' => $mtbfData,
                    'backgroundColor' => '#22c55e',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
