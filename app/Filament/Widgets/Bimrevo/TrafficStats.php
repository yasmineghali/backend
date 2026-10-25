<?php

namespace App\Filament\Widgets\Bimrevo;

use App\Support\Bimrevo\Traffic;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TrafficStats extends StatsOverviewWidget
{
    // Only shown on the BIMRevo Traffic page, not on the main dashboard.
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'BIMRevo website traffic';

    protected ?string $description = 'Cookie-free and anonymous. Visitors are counted per day.';

    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $week = now()->subDays(6)->startOfDay();
        $month = now()->subDays(29)->startOfDay();

        $series = Traffic::daily(7);

        return [
            Stat::make('Views today', number_format(Traffic::views($today)))
                ->description(number_format(Traffic::visitors($today)).' visitors')
                ->chart($series['views'])
                ->color('primary'),
            Stat::make('Views, last 7 days', number_format(Traffic::views($week)))
                ->description(number_format(Traffic::visitors($week)).' visitors')
                ->chart($series['visitors'])
                ->color('info'),
            Stat::make('Views, last 30 days', number_format(Traffic::views($month)))
                ->description(number_format(Traffic::visitors($month)).' visitors')
                ->color('success'),
        ];
    }
}
