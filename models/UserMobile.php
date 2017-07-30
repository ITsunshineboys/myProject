<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/27/17
 * Time: 2:08 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class UserMobile extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'user_mobile';
    }

    /**
     * Add user mobile by user and operator
     *
     * @param User $user user
     * @param User|null $operator operator default null
     * @return bool
     */
    public static function addByUserAndOperator(User $user, User $operator = null)
    {
        $userMobile = new self;
        $userMobile->uid = $user->id;
        $userMobile->mobile = $user->mobile;
        $userMobile->create_time = time();
        if ($operator) {
            $lhzz = Lhzz::find()->where(['uid' => $operator->id])->one();
            if ($lhzz) {
                $userMobile->op_uid = $lhzz->id;
                $userMobile->op_username = $lhzz->nickname;
            }
        }

        return $userMobile->save();
    }
}