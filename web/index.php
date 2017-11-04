<?php

$config = require(__DIR__ . '/../config/web.php');

// comment out the following two lines when deployed to production
define('YII_DEBUG', $config['params']['online']['env'] == 'dev');
define('YII_ENV', $config['params']['online']['env']);

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

//$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
//
//$allow_origin = array(
//    'http://cross.local.com:808',
//    'http://cross.local.com'
//);
//
//if (in_array($origin, $allow_origin)) {
//    header('Access-Control-Allow-Origin:' . $origin);
//}
header('Access-Control-Allow-Origin:*');
header("Access-Control-Allow-Credentials: true");

// if (YII_DEBUG) {
//     header('Access-Control-Allow-Origin: http://cross.local.com:808');
//     header("Access-Control-Allow-Credentials: true");
// //    header('Access-Control-Allow-Origin:' . apache_request_headers()['Origin']);
// }

(new yii\web\Application($config))->run();
