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

        if (!YII_DEBUG && $user->isKickedout()) {
            if ($this->denyCallback !== null) {
                call_user_func($this->denyCallback, $kickedOutcode, $action);
            }
            return false;
        }

        if (!empty(Yii::$app->session[User::LOGIN_ORIGIN_APP])) {
            if (!$user->checkLogin()) {
                if ($this->denyCallback !== null) {
                    call_user_func($this->denyCallback, $denyCode, $action);
                }
                return false;
            }
            return true;
        }

        if (!empty(Yii::$app->session[User::LOGIN_ORIGIN_ADMIN])) {
            if (!$user->checkAdminLogin()) {
                if ($this->denyCallback !== null) {
                    call_user_func($this->denyCallback, $denyCode, $action);
                }
                return false;
            }

            if (RolePermission::isAdminApi(Yii::$app->controller->id, $action->id)) {
                if (!RolePermission::hasPermission(Yii::$app->controller->id, $action->id, $user->login_role_id)) {
                    if ($this->denyCallback !== null) {
                        call_user_func($this->denyCallback, $denyCode, $action);
                    }
                    return false;
                }
            }

            return true;
        }

        if ($this->denyCallback !== null) {
            call_user_func($this->denyCallback, $denyCode, $action);
        }        
        return false;
    }
}