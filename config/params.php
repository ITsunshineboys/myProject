<?php

return [
    'adminEmail' => 'admin@example.com',
    'offsetAiteCubeNo' => 10000,
    'ownerRoleId' => 7,
    'errorCodes' => [
        403 => '没有权限',
        500 => '接口服务器异常',
        1000 => '请求的参数不正确',
        1001 => '用户名/密码不正确',
        1002 => '验证码不正确',
        1003 => '请先停用再删除',
        1004 => '上传文件大小不能超过5M',
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
    'uploadPublic' => [
        'directory' => 'web/uploads',
        'maxSize' => 5 * 1024 * 1024 , // 5M
    ],
    'sm' => [
        'appKey' => '23532528',
        'appSecret' => 'f589df8c27f634a94df0b908da7339f7',
        'interval' => 60, // 1m
        'validationCode' => [
            'expire' => 600, // 10m
            'rule' => 'fourDigits',
        ],
        'maxSendNumPerDay' => 5,
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
    'timeTypes' => [
        'all' => '全部时间',
        'today' => '今天',
        'week' => '本周',
        'month' => '本月',
        'year' => '本年',
        'custom' => '自定义',
    ],
];
