<?php

namespace App\Console\Commands;

use App\Models\Bimrevo\PageView;
use Illuminate\Console\Command;

class PruneBimrevoAnalytics extends Command
{
    protected $signature = 'bimrevo:analytics-prune {--days= : Override the configured retention period}';

    protected $description = 'Delete BIMRevo page views older than the retention period';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('bimrevo.analytics.retention_days'));

        $deleted = PageView::where('viewed_at', '<', now()->subDays($days))->delete();

        $this->info("Deleted {$deleted} BIMRevo page views older than {$days} days.");

        return self::SUCCESS;
    }
}
