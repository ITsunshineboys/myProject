<?php

return [
    'adminEmail' => 'admin@example.com',
    'offset_aite_cube_no' => 10000,
    'owner_role_id' => 7,
    'errorCodes' => [
        403 => '没有权限',
        500 => '接口服务器异常',
        1000 => '请求的参数不正确',
        1001 => '用户名/密码不正确',
        1002 => '验证码不正确',
    ],
    'events' => [
        'supplier' => [
            'login' => 'supplier_login',
            'logout' => 'supplier_logout',
        ],
    ],
    'download' => [
        'directory' => 'down',
        'enableBuffering' => 'yes',
        'rate' => 102400, // 速度限制 Byte/s
    ],
    'upload' => [
        'directory' => 'uploads',
    ],
    'sm' => [
        'appKey' => '23532528',
        'appSecret' => 'f589df8c27f634a94df0b908da7339f7',
        'interval' => 60, // 1m
        'validationCode' => [
            'expire' => 600, // 10m
            'rule' => 'fourDigits',
        ],
        'signName' => '领航装饰设计',
        'register' => [
            'templateId' => 'SMS_26070129',
            'signName' => '用户注册验证码',
        ],
        'resetPassword' => [
            'templateId' => 'SMS_26070127',
            'signName' => '修改密码验证码',
        ],
        'forgetPassword' => [
            'templateId' => 'SMS_63905689',
            'signName' => '忘记密码验证码',
        ],
    ],
];
