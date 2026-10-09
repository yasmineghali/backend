<?php

namespace App\Filament\Widgets;

use App\Support\Traffic;
use Filament\Widgets\ChartWidget;

class DeviceBreakdown extends ChartWidget
{
    protected static ?int $sort = 4;

    protected ?string $heading = 'Devices (30 days)';

    protected ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $data = Traffic::breakdown('device');

        return [
            'datasets' => [[
                'data' => array_values($data),
                'backgroundColor' => ['#1953c8', '#f2c230', '#6f9bff'],
            ]],
            'labels' => array_map('ucfirst', array_keys($data)),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
