<?php

namespace App\Filament\Widgets\Bimrevo;

use App\Support\Bimrevo\Traffic;
use Filament\Widgets\ChartWidget;

class DeviceBreakdown extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $heading = 'Devices (30 days)';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $data = Traffic::breakdown('device');

        return [
            'datasets' => [[
                'data' => array_values($data),
                'backgroundColor' => ['#0069B4', '#000675', '#6fb1e0'],
            ]],
            'labels' => array_map('ucfirst', array_keys($data)),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
