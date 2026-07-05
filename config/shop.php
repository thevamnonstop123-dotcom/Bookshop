<?php

return [
    'low_stock_threshold' => (int) env('LOW_STOCK_THRESHOLD', 5),
    'dashboard_cache_ttl' => (int) env('DASHBOARD_CACHE_TTL', 300),
    'realtime_polling_interval' => (int) env('REALTIME_POLLING_INTERVAL', 30000),
];
