<?php

namespace App\Console\Commands;

use App\Models\PageView;
use Illuminate\Console\Command;

class PruneAnalytics extends Command
{
    protected $signature = 'analytics:prune {--days= : Override the configured retention period}';

    protected $description = 'Delete page views older than the retention period';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('analytics.retention_days'));

        $deleted = PageView::where('viewed_at', '<', now()->subDays($days))->delete();

        $this->info("Deleted {$deleted} page views older than {$days} days.");

        return self::SUCCESS;
    }
}
