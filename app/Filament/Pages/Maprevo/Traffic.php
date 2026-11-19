<?php

namespace App\Filament\Pages\Maprevo;

use App\Filament\Widgets\Maprevo\DeviceBreakdown;
use App\Filament\Widgets\Maprevo\TopPages;
use App\Filament\Widgets\Maprevo\TopReferrers;
use App\Filament\Widgets\Maprevo\TrafficStats;
use App\Filament\Widgets\Maprevo\TrafficTrend;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Traffic extends Page
{
    protected static ?string $slug = 'maprevo/traffic';

    protected static string|UnitEnum|null $navigationGroup = 'MapRevo';

    protected static ?int $navigationSort = 60;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Traffic';

    protected static ?string $title = 'MapRevo traffic';

    /** @return array<class-string> */
    protected function getHeaderWidgets(): array
    {
        return [
            TrafficStats::class,
            TrafficTrend::class,
            DeviceBreakdown::class,
            TopPages::class,
            TopReferrers::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 2;
    }
}
