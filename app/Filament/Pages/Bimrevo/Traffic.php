<?php

namespace App\Filament\Pages\Bimrevo;

use App\Filament\Widgets\Bimrevo\DeviceBreakdown;
use App\Filament\Widgets\Bimrevo\TopPages;
use App\Filament\Widgets\Bimrevo\TopReferrers;
use App\Filament\Widgets\Bimrevo\TrafficStats;
use App\Filament\Widgets\Bimrevo\TrafficTrend;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Traffic extends Page
{
    protected static ?string $slug = 'bimrevo/traffic';

    protected static string|UnitEnum|null $navigationGroup = 'BIMRevo';

    protected static ?int $navigationSort = 30;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Traffic';

    protected static ?string $title = 'BIMRevo traffic';

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
