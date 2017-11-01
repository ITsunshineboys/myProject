<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/10/17
 * Time: 9:19 AM
 */

namespace app\services;

use Yii;
use app\models\GoodsCategory;
use app\models\User;

class EventHandleService
{
    /**
     * construct function
     *
     * @param mixed $data default null
     */
    public function __construct($data = null)
    {
        $events = Yii::$app->params['events'];

        Yii::$app->on($events['mall']['category']['updateBatch'], function () use ($data) {
            $cache = Yii::$app->cache;
            $keys = (array)$cache->get(GoodsCategory::CACHE_PREFIX_KEY_LIST);
            foreach ($keys as $key) {
                $key && Yii::$app->cache->delete($key);
            }
        });

        // supplier login
        Yii::$app->on($events['supplier']['login'], function () use ($data) {
//            echo 'hi ', $data, ', you are logged in at ', date('Y-m-d H:i:s');
        });

        // supplier logout
        Yii::$app->on($events['supplier']['logout'], function () use ($data) {
//            echo 'you are logged out at ', date('Y-m-d H:i:s');
        });

        // call 3rd. service(create huanxin user) failed
        Yii::$app->on($events['3rd']['failed']['createHuanxinUser'], function () use ($data, $events) {
            StringService::writeLog($events['3rd']['failed']['createHuanxinUser'], $data);
        });

        // db failed
        Yii::$app->on($events['db']['failed'], function () use ($data, $events) {
            StringService::writeLog($events['db']['failed'], $data['sql'], $data['table']);
        });
    }
}