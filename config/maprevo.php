<?php

return [

    'analytics' => [

        'enabled' => env('MAPREVO_ANALYTICS_ENABLED', true),

        // Raw page views older than this are deleted by `maprevo:analytics-prune` (13 months by default).
        'retention_days' => (int) env('MAPREVO_ANALYTICS_RETENTION_DAYS', 395),

        // Referrers from these hosts are internal navigation and are not recorded.
        'own_hosts' => array_filter(array_map('trim', explode(',', env('MAPREVO_ANALYTICS_OWN_HOSTS', 'maprevo.ch')))),

    ],

];
