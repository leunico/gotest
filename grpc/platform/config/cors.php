<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    */

    'supportsCredentials' => true,
    'allowedOrigins' => [
        'http://localhost'
    ],
    'allowedOriginsPatterns' => [
        '/localhost(:\d+)?/',
        '/\w+(\.(dev|staging|local))?\.codepku\.com(:\d+)?/'
    ],
    'allowedHeaders' => ['*'],
    'allowedMethods' => ['*'],
    'exposedHeaders' => [],
    'maxAge' => 0
];
