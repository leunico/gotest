<?php

return [
    'name'   => 'Crm',
    'host'   => env('CRM_HOST', 'https://crmapi.dev.d-n-a.cn'),
    'server' => env('CRM_SERVER', 'https://crmapi.dev.d-n-a.cn/api'),

    'auth' => [
        'crm_id'     => env('CRM_ID', 'crmapi'),
        'crm_secret' => env('CRM_SECRET', '5wp5o83aTTj1XMUK'),
    ],
];
