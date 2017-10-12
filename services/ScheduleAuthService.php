<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 6/3/17
 * Time: 3:46 PM
 */

namespace app\services;

use Yii;
use yii\filters\AccessControl;

class ScheduleAuthService extends AccessControl
{
    const DURATION_TOKEN = 7200;

    public function beforeAction($action)
    {
        if (self::getToken() != Yii::$app->request->post('token')) {
            $denyCode = 403;
            if ($this->denyCallback !== null) {
                call_user_func($this->denyCallback, $denyCode, $action);
            }

            return false;
        }

        return true;
    }

    /**
     * Get token
     *
     * @return string
     */
    public static function getToken()
    {
        $cacheKeyToken = 'token_' . self::className();
        $cache = Yii::$app->cache;
        $token = $cache->get($cacheKeyToken);
        if (!$token) {
            $token = uniqid(time());
            $cache->set($cacheKeyToken, $token, self::DURATION_TOKEN);
        }
        return $token;
    }
}