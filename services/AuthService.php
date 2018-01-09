<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 6/3/17
 * Time: 3:46 PM
 */

namespace app\services;

use app\models\User;
use app\models\RolePermission;
use Yii;
use yii\filters\AccessControl;
use yii\log\Logger;

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
            if (!YII_DEBUG && User::checkKickedout()) {
                if ($this->denyCallback !== null) {
                    call_user_func($this->denyCallback, $kickedOutcode, $action);
                }
                return false;
            }

            $path = Yii::$app->controller->id . '/' . $action->id;
            if (in_array($path, RolePermission::allPermissions())) {
                if (!$user->checkAdminLogin()
                    || !in_array($path, RolePermission::rolePermissions($user->login_role_id))
                ) {
                    if ($this->denyCallback !== null) {
                        call_user_func($this->denyCallback, $denyCode, $action);
                    }
                    return false;
                }
            } else {
                if (!$user->checkLogin()) {
                    if ($this->denyCallback !== null) {
                        call_user_func($this->denyCallback, $denyCode, $action);
                    }
                    return false;
                }
            }

            return true;
        } else {
//            $code = User::checkKickedout() ? $kickedOutcode : $denyCode;
//            StringService::writeLog('test', $code, 'auth');
//            if (YII_DEBUG && $code == $kickedOutcode) {
//                return true;
//            }
//
//            if ($this->denyCallback !== null) {
//                call_user_func($this->denyCallback, $code, $action);
//            }
//            return false;
        }
    }
}