<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 6/3/17
 * Time: 3:46 PM
 */

namespace app\services;

use app\models\User;
use Yii;
use yii\filters\AccessControl;

class AuthService extends AccessControl
{
    public function beforeAction($action)
    {
        $denyCode = 403;
        $kickedOutcode = 1023;

        $user = Yii::$app->user->identity;
        if (!$user) {
            if ($this->denyCallback !== null) {
                call_user_func($this->denyCallback, $denyCode, $action);
            }
            return false;
        }

        if (!empty(Yii::$app->session[User::LOGIN_ORIGIN_ADMIN])
            || !empty(Yii::$app->session[User::LOGIN_ORIGIN_APP])
        ) {
            $path = Yii::$app->controller->id . '/' . $action->id;
            if (isset(Yii::$app->params['auth'][$path])) {
                if (!$user->checkAdminLogin()
                    || !in_array($user->login_role_id, Yii::$app->params['auth'][$path])
                ) {
                    if ($this->denyCallback !== null) {
                        call_user_func($this->denyCallback, $denyCode, $action);
                    }

                    return false;
                }
            }

            if (!empty(Yii::$app->session[User::LOGIN_ORIGIN_APP])) {
                if (!$user->checkLogin()) {
                    if ($this->denyCallback !== null) {
                        call_user_func($this->denyCallback, $denyCode, $action);
                    }

                    return false;
                }
            }

            return true;
        } else {
            $code = User::checkKickedout() ? $kickedOutcode : $denyCode;
            if (YII_DEBUG && $code == $kickedOutcode) {
                return true;
            }

            if ($this->denyCallback !== null) {
                call_user_func($this->denyCallback, $code, $action);
            }
            return false;
        }
    }
}