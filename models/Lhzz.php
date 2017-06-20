<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class Lhzz extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'lhzz';
    }

    /**
     * Get lhzz instance by user model
     *
     * @param ActiveRecord $user user model
     * @return ActiveRecord
     */
    public static function findByUser(ActiveRecord $user)
    {
        return self::find()->where(['uid' => $user->id])->one();
    }
}