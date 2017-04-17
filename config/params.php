<?php

return [
    'adminEmail' => 'admin@example.com',
    'errorCodes' => [
        403 => 'Access forbidden',
        500 => 'Internal server error',
        1000 => 'Parameter error',
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
