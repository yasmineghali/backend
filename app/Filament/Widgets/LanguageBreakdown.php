<?php

namespace App\Filament\Widgets;

use App\Support\Traffic;
use Filament\Widgets\ChartWidget;

class LanguageBreakdown extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Languages (30 days)';

    protected ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $data = Traffic::breakdown('locale');

        return [
            'datasets' => [[
                'data' => array_values($data),
                'backgroundColor' => ['#1953c8', '#f2c230', '#6f9bff', '#9ca3af'],
            ]],
            'labels' => array_map('strtoupper', array_keys($data)),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
