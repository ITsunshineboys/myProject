<?php

return [
    'class' => 'yii\db\Connection',

    // common configuration for masters
    'masterConfig' => [
        'username' => 'root',
        'password' => 'admin',
        'charset' => 'utf8',
        'attributes' => [
            // use a smaller connection timeout
            PDO::ATTR_TIMEOUT => 10,
        ],
    ],

    // list of master configurations
    'masters' => [
        ['dsn' => 'mysql:host=localhost;dbname=lhzz'],
//        ['dsn' => 'dsn for master server 2'],
    ],

    // common configuration for slaves
    'slaveConfig' => [
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'attributes' => [
            // use a smaller connection timeout
            PDO::ATTR_TIMEOUT => 10,
        ],
    ],

    // list of master configurations
    'slaves' => [
        ['dsn' => 'mysql:host=localhost;dbname=lhzz'],
//        ['dsn' => 'dsn for slave server 2'],
    ],
];
