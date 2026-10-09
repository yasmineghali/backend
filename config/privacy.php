<?php

return [

    // Contact requests are deleted after this many days (policy: two years).
    'contact_retention_days' => (int) env('CONTACT_RETENTION_DAYS', 730),

    // IP address and user agent on a contact request are erased after this many days.
    'contact_ip_days' => (int) env('CONTACT_IP_DAYS', 30),

];
