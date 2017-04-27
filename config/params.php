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
];
