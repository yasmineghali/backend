<?php

return [

    'enabled' => env('ANALYTICS_ENABLED', true),

    // Raw page views older than this are deleted by `analytics:prune` (13 months by default).
    'retention_days' => (int) env('ANALYTICS_RETENTION_DAYS', 395),

    // Referrers from these hosts are internal navigation and are not recorded.
    'own_hosts' => array_filter(array_map('trim', explode(',', env('ANALYTICS_OWN_HOSTS', 'georevo.ch')))),

];
