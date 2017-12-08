<?php

$config = require(__DIR__ . '/../config/web.php');

// comment out the following two lines when deployed to production
define('YII_DEBUG', $config['params']['online']['env'] == 'dev');
define('YII_ENV', $config['params']['online']['env']);

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

if (YII_DEBUG) {

    header('Access-Control-Allow-Origin: http://cross.local.com:808');
    header("Access-Control-Allow-Credentials: true");
}

(new yii\web\Application($config))->run();