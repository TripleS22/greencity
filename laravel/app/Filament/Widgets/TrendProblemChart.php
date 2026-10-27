<?php

namespace App\Filament\Widgets;

use App\Models\DBR;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class TrendProblemChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Trend Problem By Event & By Duration';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->startOfMonth()->toDateString();
        $endDate = $this->filters['endDate'] ?? now()->endOfMonth()->toDateString();
        
        $problems = DBR::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('problem_reported, count(*) as event_count')
            ->groupBy('problem_reported')
            ->orderByDesc('event_count')
            ->limit(5)
            ->get();

        $labels = [];
        $eventData = [];
        $durationData = [];

        foreach ($problems as $problem) {
            $labels[] = $problem->problem_reported ?: 'Unknown';
            $eventData[] = $problem->event_count;
            
            $duration = DBR::where('problem_reported', $problem->problem_reported)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->sum('duration_hours');
                
            $durationData[] = round($duration, 1);
        }

        if (empty($labels)) {
            $labels = ['NO DATA'];
            $eventData = [0];
            $durationData = [0];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Duration (Hours)',
                    'data' => $durationData,
                    'backgroundColor' => '#a78bfa',
                ],
                [
                    'label' => 'Event Count',
                    'data' => $eventData,
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

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
