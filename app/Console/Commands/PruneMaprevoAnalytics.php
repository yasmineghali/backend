<?php

namespace App\Console\Commands;

use App\Models\Maprevo\PageView;
use Illuminate\Console\Command;

class PruneMaprevoAnalytics extends Command
{
    protected $signature = 'maprevo:analytics-prune {--days= : Override the configured retention period}';

    protected $description = 'Delete MapRevo page views older than the retention period';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('maprevo.analytics.retention_days'));

        $deleted = PageView::where('viewed_at', '<', now()->subDays($days))->delete();

        $this->info("Deleted {$deleted} MapRevo page views older than {$days} days.");

        return self::SUCCESS;
    }
}
