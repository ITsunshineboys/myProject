<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/27/17
 * Time: 2:08 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class UserStatus extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'user_status';
    }

    /**
     * Add user status by user, operator and/or remark
     *
     * @param User $user user
     * @param User|null $operator operator default null
     * @param string $remark remark default empty
     * @return bool
     */
    public static function addByUserAndOperator(User $user, User $operator = null, $remark = '')
    {
        $userStatus = new self;
        $userStatus->uid = $user->id;
        $userStatus->mobile = $user->mobile;
        $userStatus->create_time = time();
        $userStatus->remark = $remark;
        $user->deadtime == 0 && $userStatus->remark = $remark;
        $userStatus->status = $user->deadtime > 0 ? User::STATUS_OFFLINE : User::STATUS_ONLINE;
        if ($operator) {
            $lhzz = Lhzz::find()->where(['uid' => $operator->id])->one();
            if ($lhzz) {
                $userStatus->op_uid = $lhzz->id;
                $userStatus->op_username = $lhzz->nickname;
            }
        }

        return $userStatus->save();
    }
}