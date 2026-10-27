<?php

namespace App\Filament\Widgets;

use App\Models\DBR;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class MTTRChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'MEAN TIME TO REPAIR (HOURS)';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->startOfMonth()->toDateString();
        $endDate = $this->filters['endDate'] ?? now()->endOfMonth()->toDateString();
        
        $models = DBR::whereBetween('date', [$startDate, $endDate])
            ->distinct()
            ->pluck('model')
            ->filter()
            ->toArray();

        if (empty($models)) $models = ['NO DATA'];

        $labels = array_merge($models, ['TOTAL']);
        $mttrData = [];
        $planMttrData = [];

        $totalDowntime = 0;
        $totalEvents = 0;

        foreach ($models as $modelName) {
            $events = DBR::where('model', $modelName)
                ->whereBetween('date', [$startDate, $endDate])
                ->count();
            
            $downtime = DBR::where('model', $modelName)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->sum('duration_hours');

            $mttr = $events > 0 ? ($downtime / $events) : 0;
            
            $mttrData[] = round($mttr, 2);
            $planMttrData[] = 3.0; 
            
            $totalDowntime += $downtime;
            $totalEvents += $events;
        }

        $totalMttr = $totalEvents > 0 ? ($totalDowntime / $totalEvents) : 0;
        $mttrData[] = round($totalMttr, 2);
        $planMttrData[] = 3.0;

        return [
            'datasets' => [
                [
                    'label' => 'Plan MTTR',
                    'data' => $planMttrData,
                    'backgroundColor' => '#a78bfa',
                ],
                [
                    'label' => 'MTTR',
                    'data' => $mttrData,
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
