<?php

return [
    'code_min' => env('YUNPIAN_CODE_MIN', 100000),
    'code_max' => env('YUNPIAN_CODE_MAX', 999999),
    'code_ttl' => env('YUNPIAN_CODE_TTL', 120), // 单位：秒
    'apikey' => env('YUNPIAN_API_KEY', 'd251d465dd4167a928fe31aface69ea4'),
    'template_code' => [
        'art' => env('YUNPIAN_TEMPLATE_CODE_ART', '【迪恩艺术编程】您的验证码是%s。如非本人操作，请忽略本短信'),
        'music' => env('YUNPIAN_TEMPLATE_CODE_MUSIC', '【迪恩数字音乐】您的验证码是%s。如非本人操作，请忽略本短信'),
    ],
    'template_hello' => env('YUNPIAN_TEMPLATE_HELLO', '【编玩边学】恭喜您成功领取课程，请务必添加老师微信：大家好 进行后续操作。编玩边学官网：www.codepku.com'),
    'mock_send' => env('YUNPIAN_MOCK_SEND', false) //模拟发送
];
