<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model'   => App\User::class,
        'key'     => env('STRIPE_KEY'),
        'secret'  => env('STRIPE_SECRET'),
        'webhook' => [
            'secret'    => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'weixinweb' => [
        'client_id' => env('WEIXINWEB_KEY', 'wx672d72141f65b24d'),
        'client_secret' => env('WEIXINWEB_SECRET'),
        'redirect' => env('WEIXINWEB_REDIRECT_URI')
    ],

    'weixin' => [
        'client_id' => env('WEIXIN_KEY'),
        'client_secret' => env('WEIXIN_SECRET'),
        'redirect' => env('WEIXIN_REDIRECT_URI')
    ],


    'login_redirect_url' => env('LOGIN_REDIRECT_URL', null),

    'allow_origin_urls' => env('ALLOW_ORIGIN_URLS', ''),
    'allow_any_cors' => env('ALLOW_ANY_CORS', false),
    'auth'              => [
        'platform_id'     => env('PLATFORM_ID', 'platform'),
        'platform_secret' => env('PLATFORM_SECRET', 'zlFuEYiGPKEA8wSx')
    ],

    'sms_throttle' => env('SMS_THROTTLE', '1'), // todo一分钟一次的意思,如果是1，1后面的1就是超时后的等待时间
    'success_status'  => env('HTTP_SUCCESS_STATUS', '1,OK'),
    'study_domain' => env('STUDY_DOMAIN', 'https://study.d-n-a.cn'),
    'marketing_domain' => env('MARKETING_DOMAIN', 'https://services.d-n-a.cn'),
    'default_headimgurl' => env('DEFAULT_HEADIMGURL', 'https://dn-platform-1253386414.file.myqcloud.com/default_avatar/20181129/1.png'),
];
