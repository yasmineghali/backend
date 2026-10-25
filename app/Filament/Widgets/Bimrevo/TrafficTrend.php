<?php

namespace App\Filament\Widgets\Bimrevo;

use App\Support\Bimrevo\Traffic;
use Filament\Widgets\ChartWidget;

class TrafficTrend extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Trend';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    public ?string $filter = '30';

    protected function getFilters(): ?array
    {
        return [
            '7' => 'Last 7 days',
            '30' => 'Last 30 days',
            '90' => 'Last 90 days',
        ];
    }

    protected function getData(): array
    {
        $series = Traffic::daily((int) $this->filter);

        return [
            'datasets' => [
                [
                    'label' => 'Views',
                    'data' => $series['views'],
                    'borderColor' => '#0069B4',
                    'backgroundColor' => 'rgba(0, 105, 180, 0.12)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Visitors',
                    'data' => $series['visitors'],
                    'borderColor' => '#000675',
                    'backgroundColor' => 'rgba(0, 6, 117, 0.10)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $series['labels'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
