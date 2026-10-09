<?php

namespace App\Support;

use App\Models\PageView;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Visitor hashes rotate daily, so "visitors" over a range is the sum of each
 * day's distinct visitors (a returning person on another day counts again).
 */
class Traffic
{
    public static function views(CarbonInterface $from, ?CarbonInterface $to = null): int
    {
        return PageView::where('viewed_at', '>=', $from)
            ->when($to, fn ($q) => $q->where('viewed_at', '<', $to))
            ->count();
    }

    public static function visitors(CarbonInterface $from, ?CarbonInterface $to = null): int
    {
        $sub = PageView::query()
            ->selectRaw('DISTINCT DATE(viewed_at) as d, visitor_hash')
            ->where('viewed_at', '>=', $from)
            ->when($to, fn ($q) => $q->where('viewed_at', '<', $to));

        return DB::query()->fromSub($sub, 't')->count();
    }

    /** @return array{labels: list<string>, views: list<int>, visitors: list<int>} */
    public static function daily(int $days): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $rows = PageView::query()
            ->selectRaw('DATE(viewed_at) as d, COUNT(*) as views, COUNT(DISTINCT visitor_hash) as visitors')
            ->where('viewed_at', '>=', $start)
            ->groupBy('d')
            ->get()
            ->keyBy('d');

        $labels = $views = $visitors = [];
        for ($i = 0; $i < $days; $i++) {
            $day = $start->copy()->addDays($i);
            $row = $rows->get($day->toDateString());
            $labels[] = $day->format('d.m');
            $views[] = (int) ($row->views ?? 0);
            $visitors[] = (int) ($row->visitors ?? 0);
        }

        return compact('labels', 'views', 'visitors');
    }

    /** @return array<string, int> value => views, for the last $days days */
    public static function breakdown(string $column, int $days = 30): array
    {
        return PageView::where('viewed_at', '>=', now()->subDays($days)->startOfDay())
            ->selectRaw("{$column} as k, COUNT(*) as c")
            ->groupBy('k')
            ->orderByDesc('c')
            ->pluck('c', 'k')
            ->map(fn ($c) => (int) $c)
            ->all();
    }
}
