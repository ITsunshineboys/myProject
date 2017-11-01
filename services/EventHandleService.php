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

class EventHandleService
{
    /**
     * construct function
     *
     * @param string $event event name
     * @param mixed $data default null
     */
    public function __construct($event, $data = null)
    {
        $event = trim($event);
        if (!$event) {
            return;
        }

        $events = Yii::$app->params['events'];

        switch ($event) {
            // call 3rd. service(create huanxin user) failed
            case $events['3rd']['failed']['createHuanxinUser']:
                Yii::$app->on($events['3rd']['failed']['createHuanxinUser'], function () use ($data, $events) {
                    StringService::writeLog($events['3rd']['failed']['createHuanxinUser'], $data);
                });
                break;

            // db failed
            case $events['db']['failed']:
                Yii::$app->on($events['db']['failed'], function () use ($data, $events) {
                    StringService::writeLog($events['db']['failed'], $data['sql'], $data['table']);
                });
                break;

            // supplier login
            case $events['supplier']['login']:
                Yii::$app->on($events['supplier']['login'], function () use ($data) {
                });
                break;

            // supplier logout
            case $events['supplier']['logout']:
                Yii::$app->on($events['supplier']['logout'], function () use ($data) {
                });

            // update categories in batches
            case $events['mall']['category']['updateBatch']:
                Yii::$app->on($events['mall']['category']['updateBatch'], function () use ($data) {
                    $cache = Yii::$app->cache;
                    $keys = (array)$cache->get(GoodsCategory::CACHE_PREFIX_KEY_LIST);
                    foreach ($keys as $key) {
                        $key && Yii::$app->cache->delete($key);
                    }
                });
                break;
        }
    }
}