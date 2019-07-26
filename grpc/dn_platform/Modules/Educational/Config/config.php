<?php

return [
    'name' => 'Educational',
    'live' => [
        'app_id' => env('LIVE_APP_ID', ''),
        'app_certificate' => env('LIVE_APP_CERTIFICATE', ''),
        'channel_prefix' => env('LIVE_CHANNEL_PREFIX', 'DiEn_VideoRoom_'),
        'live_web_host' => env('LIVE_WEB_HOST', ''),
    ]
];
