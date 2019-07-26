<?php

/*
 * This file is part of the overtrue/laravel-wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

return [
    /*
     * 默认配置，将会合并到各模块中
     */
    'defaults' => [
        /*
         * 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
         */
        'response_type' => 'array',

        /*
         * 使用 Laravel 的缓存系统
         */
        'use_laravel_cache' => true,

        /*
         * 日志配置
         *
         * level: 日志级别，可选为：
         *                 debug/info/notice/warning/error/critical/alert/emergency
         * file：日志文件位置(绝对路径!!!)，要求可写权限
         */
        'log' => [
            'level' => env('WECHAT_LOG_LEVEL', 'debug'),
            'file' => env('WECHAT_LOG_FILE', storage_path('logs/wechat.log')),
        ],
    ],

    /*
     * 路由配置
     */
    'route' => [
        /*
         * 开放平台第三方平台路由配置
         */
        // 'open_platform' => [
        //     'uri' => 'serve',
        //     'action' => Overtrue\LaravelWeChat\Controllers\OpenPlatformController::class,
        //     'attributes' => [
        //         'prefix' => 'open-platform',
        //         'middleware' => null,
        //     ],
        // ],
    ],

    'official_account_no' => env('WECHAT_OFFICIAL_ACCOUNT_NO', 'art|music'), //可以使用的公众号【控制一些限制使用的公众号】

    /*
     * 公众号
     */
    'official_account' => [
        'art' => [
            'app_id' => env('WECHAT_OFFICIAL_ACCOUNT_ART_APPID', 'your-app-id'),         // AppID
            'secret' => env('WECHAT_OFFICIAL_ACCOUNT_ART_SECRET', 'your-app-secret'),    // AppSecret
            'token' => env('WECHAT_OFFICIAL_ACCOUNT_ART_TOKEN', 'your-token'),           // Token
            'aes_key' => env('WECHAT_OFFICIAL_ACCOUNT_ART_AES_KEY', ''),                 // EncodingAESKey

            /*
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址(如果使用中间件，则随便填写。。。)
             */
            'oauth' => [
                'scopes' => array_map('trim', explode(',', env('WECHAT_OFFICIAL_ACCOUNT_ART_OAUTH_SCOPES', 'snsapi_userinfo'))),
                'callback' => env('WECHAT_OFFICIAL_ACCOUNT_ART_OAUTH_CALLBACK', '/api/oauth/wechat/art/web-notify'),
            ],
        ],

        'music' => [
            'app_id' => env('WECHAT_OFFICIAL_ACCOUNT_MUSIC_APPID', 'your-app-id'),         // AppID
            'secret' => env('WECHAT_OFFICIAL_ACCOUNT_MUSIC_SECRET', 'your-app-secret'),    // AppSecret
            'token' => env('WECHAT_OFFICIAL_ACCOUNT_MUSIC_TOKEN', 'your-token'),           // Token
            'aes_key' => env('WECHAT_OFFICIAL_ACCOUNT_MUSIC_AES_KEY', ''),                 // EncodingAESKey

            /*
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址(如果使用中间件，则随便填写。。。)
             */
            'oauth' => [
                'scopes' => array_map('trim', explode(',', env('WECHAT_OFFICIAL_ACCOUNT_MUSIC_OAUTH_SCOPES', 'snsapi_userinfo'))),
                'callback' => env('WECHAT_OFFICIAL_ACCOUNT_MUSIC_OAUTH_CALLBACK', '/api/oauth/wechat/music/web-notify'),
            ],
        ],
    ],

    /*
     * 开放平台第三方平台
     */
    // 'open_platform' => [
    //     'default' => [
    //         'app_id'  => env('WECHAT_OPEN_PLATFORM_APPID', ''),
    //         'secret'  => env('WECHAT_OPEN_PLATFORM_SECRET', ''),
    //         'token'   => env('WECHAT_OPEN_PLATFORM_TOKEN', ''),
    //         'aes_key' => env('WECHAT_OPEN_PLATFORM_AES_KEY', ''),
    //     ],
    // ],

    /*
     * 小程序
     */
    'mini_program' => [
        'default' => [
            'app_id' => env('WECHAT_MINI_PROGRAM_APPID', ''),
            'secret' => env('WECHAT_MINI_PROGRAM_SECRET', ''),
            'token' => env('WECHAT_MINI_PROGRAM_TOKEN', ''),
            'aes_key' => env('WECHAT_MINI_PROGRAM_AES_KEY', ''),
        ],
    ],

    'payment_no' => env('WECHAT_PAYMENT_NO', 'art|music'), //可以使用的微信支付【控制一些限制使用的】

    /*
     * 微信支付
     */
    'payment' => [
        'art' => [
            'sandbox' => env('WECHAT_PAYMENT_ART_SANDBOX', false),
            'app_id' => env('WECHAT_PAYMENT_ART_APPID', ''),
            'mch_id' => env('WECHAT_PAYMENT_ART_MCH_ID', 'your-mch-id1'),
            'key' => env('WECHAT_PAYMENT_ART_KEY', 'key-for-signature'),
            'cert_path' => env('WECHAT_PAYMENT_ART_CERT_PATH', base_path('resources/cert') . '/apiclient_cert.pem'),    // XXX: 绝对路径！！！！
            'key_path' => env('WECHAT_PAYMENT_ART_KEY_PATH', base_path('resources/cert') . '/apiclient_key.pem'),      // XXX: 绝对路径！！！！
            'notify_url' => '/api/oauth/payments/wechat/art/notify',                                    // 默认支付结果通知地址
        ],
        'music' => [
            'sandbox' => env('WECHAT_PAYMENT_MUSIC_SANDBOX', false),
            'app_id' => env('WECHAT_PAYMENT_MUSIC_APPID', ''),
            'mch_id' => env('WECHAT_PAYMENT_MUSIC_MCH_ID', 'your-mch-id2'),
            'key' => env('WECHAT_PAYMENT_MUSIC_KEY', 'key-for-signature'),
            'cert_path' => env('WECHAT_PAYMENT_MUSIC_CERT_PATH', base_path('resources/cert') . '/apiclient_cert.pem'),   // XXX: 绝对路径！！！！
            'key_path' => env('WECHAT_PAYMENT_MUSIC_KEY_PATH', base_path('resources/cert') . '/apiclient_key.pem'),     // XXX: 绝对路径！！！！
            'notify_url' => '/api/oauth/payments/wechat/music/notify',                                   // 默认支付结果通知地址
        ],
    ],

    /*
     * 企业微信
     */
    // 'work' => [
    //     'default' => [
    //         'corp_id' => 'xxxxxxxxxxxxxxxxx',
    ///        'agent_id' => 100020,
    //         'secret'   => env('WECHAT_WORK_AGENT_CONTACTS_SECRET', ''),
    //          //...
    //      ],
    // ],

    'template' => [
        'art' => [
            'purchase_success' => [
                'id' => env('TPL_ART_PURCHASE_SUCCESS', 'PaPnK78XvPKyHQ5VUb2UBcvW8hlHgeFigyT9SJCc4m0'),
                'params' => ['first', 'keyword1', 'keyword2', 'keyword3', 'remark']
            ],
            'perfect_address' => [
                'id' => env('TPL_ART_PERFECT_ADDRESS', 'nc2Xn2X23PfsbeN4QRqyi2Bz87Tb6XLIVwveFbyOOew'),
                'params' => ['first', 'keyword1', 'keyword2', 'remark']
            ],
            'add_delivery' => [
                'id' => env('TPL_ART_PERFECT_ADDRESS', 'cn0QkmD-lWTy9ge_DU60q96jsRBCWxKjA7McsyNhROo'),
                'params' => ['first', 'keyword1', 'keyword2', 'keyword3', 'remark']
            ]
        ],
        'music' => [
            'purchase_success' => [
                'id' => env('TPL_MUSIC_PURCHASE_SUCCESS', 'LhaYqK4qQHh-Zv4PalTmrG2I74PNNzBl8Z5Jq7ZYmOY'),
                'params' => ['first', 'keyword1', 'keyword2', 'keyword3', 'remark']
            ],
            'perfect_address' => [
                'id' => env('TPL_ART_PERFECT_ADDRESS', 'PZnCBqjwXEUHBdgba1jcgncT6wKi1SVP_bfnjEIoXBQ'),
                'params' => ['first', 'keyword1', 'keyword2', 'remark']
            ],
            'add_delivery' => [
                'id' => env('TPL_ART_PERFECT_ADDRESS', '_1wrbYGAtsS8USF-cRJ50JvpcnvwtvhqVE_yHedrDcE'),
                'params' => ['first', 'keyword1', 'keyword2', 'keyword3', 'remark']
            ]
        ]
    ],

    'template_seed_mock' => [
        'is_mock' => env('TEMPLATE_SEED_MOCK', false),
        'art' => env('TEMPLATE_SEED_MOCK_ART_TAG', null),
        'music' => env('TEMPLATE_SEED_MOCK_MUSIC_TAG', null),
    ],
];
