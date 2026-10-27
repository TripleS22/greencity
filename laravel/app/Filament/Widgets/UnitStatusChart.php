<?php

namespace App\Filament\Widgets;

use App\Models\Population;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class UnitStatusChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'UPDATE STATUS UNIT';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $counts = Population::getStatusCounts();

        return [
            'datasets' => [
                [
                    'label' => 'Units',
                    'data' => [
                        $counts['total'], 
                        $counts['rfu'], 
                        $counts['bd_major'], 
                        $counts['bd_minor'],
                    ],
                    'backgroundColor' => [
                        '#3b82f6', // POP - Blue
                        '#22c55e', // RFU - Green
                        '#ef4444', // BD Major - Red
                        '#f97316', // BD Minor - Orange
                    ],
                ],
            ],
            'labels' => ['POP', 'RFU', 'BD MAJOR', 'BD MINOR'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'cutout' => '60%',
        ];
    }
}
