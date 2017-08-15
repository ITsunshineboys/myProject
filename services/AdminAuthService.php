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

class AdminAuthService extends AccessControl
{
    public function beforeAction($action)
    {
        parent::beforeAction($action);

        $user = Yii::$app->user->identity;

        $path = Yii::$app->controller->id . '/' . $action->id;
        if (isset(Yii::$app->params['auth'][$path])) {
            if (!$user->checkAdminLogin()
                || !in_array($user->login_role_id, Yii::$app->params['auth'][$path])
            ) {
                $code = null;
                // todo: check 1023
                if ($this->denyCallback !== null) {
                    call_user_func($this->denyCallback, $code, $action);
                }

                return false;
            }
        }

        return true;
    }
}