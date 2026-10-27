<?php

namespace App\Filament\Widgets;

use App\Models\Population;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class StatusPercentageChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'PERCENTAGE STATUS UNIT';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $counts = Population::getStatusCounts();

        $total = $counts['total'] ?: 1; // avoid division by zero

        return [
            'datasets' => [
                [
                    'data' => [
                        round(($counts['rfu'] / $total) * 100, 1),
                        round(($counts['bd_major'] / $total) * 100, 1),
                        round(($counts['bd_minor'] / $total) * 100, 1),
                        round(($counts['other'] / $total) * 100, 1),
                    ],
                    'backgroundColor' => [
                        '#22c55e', // RFU - Green
                        '#ef4444', // BD Major - Red
                        '#f97316', // BD Minor - Orange
                        '#3b82f6', // Others - Blue
                    ],
                ],
            ],
            'labels' => ['RFU', 'BD MAJOR', 'BD MINOR', 'OTHERS'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
