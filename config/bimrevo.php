<?php

return [

    'analytics' => [

        'enabled' => env('BIMREVO_ANALYTICS_ENABLED', true),

        // Raw page views older than this are deleted by `bimrevo:analytics-prune` (13 months by default).
        'retention_days' => (int) env('BIMREVO_ANALYTICS_RETENTION_DAYS', 395),

        // Referrers from these hosts are internal navigation and are not recorded.
        'own_hosts' => array_filter(array_map('trim', explode(',', env('BIMREVO_ANALYTICS_OWN_HOSTS', 'bimrevo.ch')))),

    ],

];
