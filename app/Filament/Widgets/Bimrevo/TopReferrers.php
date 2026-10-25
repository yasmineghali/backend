<?php

namespace App\Filament\Widgets\Bimrevo;

use App\Models\Bimrevo\PageView;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class TopReferrers extends TableWidget
{
    protected static bool $isDiscovered = false;

    protected static ?string $heading = 'Top referrers (30 days)';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PageView::query()
                    ->selectRaw('MIN(id) as id, referrer_host, COUNT(*) as views, COUNT(DISTINCT visitor_hash) as visitors')
                    ->where('viewed_at', '>=', now()->subDays(29)->startOfDay())
                    ->whereNotNull('referrer_host')
                    ->groupBy('referrer_host')
                    ->orderByDesc('views')
                    ->limit(10)
            )
            ->defaultKeySort(false)
            ->paginated(false)
            ->columns([
                TextColumn::make('referrer_host')->label('Source'),
                TextColumn::make('views')->label('Views')->alignEnd(),
                TextColumn::make('visitors')->label('Visitors')->alignEnd(),
            ]);
    }
}
