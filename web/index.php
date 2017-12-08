<?php

$config = require(__DIR__ . '/../config/web.php');

// comment out the following two lines when deployed to production
define('YII_DEBUG', $config['params']['online']['env'] == 'dev');
define('YII_ENV', $config['params']['online']['env']);

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

if (YII_DEBUG) {


    $origin = isset('http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"])?'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"] : 'http://cross.local.com:808';

    $allow_origin = array(
        'http://cross.local.com:808',
        'http://localhost:808'
    );

    if(in_array($origin, $allow_origin)){
        header('Access-Control-Allow-Origin:'.$origin);
        header("Access-Control-Allow-Credentials: true");
    }
}


(new yii\web\Application($config))->run();