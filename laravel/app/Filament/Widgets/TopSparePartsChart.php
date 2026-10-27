<?php

namespace App\Filament\Widgets;

use App\Models\DBR;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class TopSparePartsChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'SPARE PARTS MOST USED (BY QTY)';
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = 2; // Make it wider

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->startOfMonth()->toDateString();
        $endDate = $this->filters['endDate'] ?? now()->endOfMonth()->toDateString();

        $dbrs = DBR::whereBetween('date', [$startDate, $endDate])->get();

        $usage = [];

        foreach ($dbrs as $dbr) {
            $spareParts = $dbr->spare_parts;
            if (is_array($spareParts)) {
                foreach ($spareParts as $part) {
                    $name = $part['component'] ?? 'Unknown';
                    $qty = floatval($part['qty'] ?? 0);
                    
                    if (!isset($usage[$name])) {
                        $usage[$name] = 0;
                    }
                    $usage[$name] += $qty;
                }
            }
        }

        // Sort by usage descending
        arsort($usage);

        // Take top 10
        $topUsage = array_slice($usage, 0, 10, true);

        return [
            'datasets' => [
                [
                    'label' => 'Total Quantity Used',
                    'data' => array_values($topUsage),
                    'backgroundColor' => '#f59e0b', // Amber/Yellow
                ],
            ],
            'labels' => array_keys($topUsage),
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
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
