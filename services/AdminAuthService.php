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

        if (isset(Yii::$app->params['auth'][$action->id])
            && !in_array($user->login_role_id, Yii::$app->params['auth'][$action->id])
        ) {
            if ($this->denyCallback !== null) {
                call_user_func($this->denyCallback, null, $action);
            }

            return false;
        }

        return true;
    }
}