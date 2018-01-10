<?php

$config = require(__DIR__ . '/../config/web.php');

// comment out the following two lines when deployed to production
define('YII_DEBUG', $config['params']['online']['env'] == 'dev');
define('YII_ENV', $config['params']['online']['env']);

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

//if (YII_DEBUG)
//{
    $origin = isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : 'http://cross.local.com:808';

    $allow_origin = array(
        'http://cross.local.com:808',
        'http://localhost:8080',
        'http://0.0.0.0:8080'
    );

    if(in_array($origin, $allow_origin)){
        header('Access-Control-Allow-Origin:'.$origin);
        header("Access-Control-Allow-Credentials: true");
    }
//}


(new yii\web\Application($config))->run();