<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/10/17
 * Time: 9:19 AM
 */

namespace app\services;

use Yii;
use yii\helpers\Json;
use app\models\GoodsCategory;
use app\models\User;

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
            // async
            case $events['async']:
                Yii::$app->on($events['async'], function () use ($data, $events) {
                    $func = 'fastcgi_finish_request';
                    if (function_exists($func)) {
                        $func();
                        session_write_close();
                    }
                    new self($data['event']['name'], $data['event']['data']);
                    Yii::$app->trigger($data['event']['name']);
                });
                break;

            // system error
            case $events['system']['error']:
                Yii::$app->on($events['system']['error'], function () use ($data, $events) {
                    StringService::writeLog($events['system']['error'], $data);
                });
                break;

            // register user
            case $events['user']['register']:
                Yii::$app->on($events['user']['register'], function () use ($data, $events) {
                    $username = StringService::getUniqueStringBySalt($data);
                    if (User::createHuanXinUser($username)) {
                        $user = User::find()->where(['mobile' => $data])->one();
                        if ($user) {
                            $user->username = $username;
                            if (!$user->save()) {
                                $update = 'update ' . User::tableName() . ' set username = ' . $username;
                                $where = ' where id = ' . $user->id . ';';
                                $data = [
                                    'sql' => $update . $where,
                                    'table' => User::tableName(),
                                ];
                                $event = $events['db']['failed'];
                                new self($event, $data);
                                Yii::$app->trigger($event);
                            }
                        } else {
                            $event = $events['system']['error'];
                            new self($event, 'cannot find user by mobile: ' . $data);
                            Yii::$app->trigger($event);
                        }
                    }
                });
                break;

            // call 3rd. service(create huanxin user) failed
            case $events['3rd']['failed']['createHuanxinUser']:
                Yii::$app->on($events['3rd']['failed']['createHuanxinUser'], function () use ($data, $events) {
                    StringService::writeLog($events['3rd']['failed']['createHuanxinUser'], $data);
                });
                break;

            // db failed
            case $events['db']['failed']:
                Yii::$app->on($events['db']['failed'], function () use ($data, $events) {
                    StringService::writeLog($data['table'], $data['sql']);
                });
                break;

            // user login
            case $events['user']['login']:
                Yii::$app->on($events['user']['login'], function () use ($data, $events) {
                    $newHuanXinPwd = User::generateHxPwd($data['mobile'], $data['registrationId']);
                    StringService::writeLog('hxpwd', $newHuanXinPwd);
                    if (User::resetHuanXinUserPwd($data['username'], $newHuanXinPwd)) {
                        $user = User::find()->where(['mobile' => $data['mobile']])->one();
                        if ($user) {
                            $user->hx_pwd_date = date('Ymd');
                            if (!$user->save()) {
                                $update = 'update ' . User::tableName() . ' set hx_pwd = ' . $newHuanXinPwd;
                                $where = ' where id = ' . $user->id . ';';
                                $data = [
                                    'sql' => $update . $where,
                                    'table' => User::tableName(),
                                ];
                                $event = $events['db']['failed'];
                                new self($event, $data);
                                Yii::$app->trigger($event);
                            }
                        } else {
                            $event = $events['system']['error'];
                            new self($event, 'cannot find user by mobile: ' . $data['mobile']);
                            Yii::$app->trigger($event);
                        }
                    }
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