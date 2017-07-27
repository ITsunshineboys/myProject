<?php

namespace app\models;

use yii\db\ActiveRecord;

class District extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'district';
    }

    /**
     * Check if valid district code
     *
     * @param int $code district code
     * @return bool
     */
    public static function checkDistrict($code)
    {
        return self::find()->where(['id' => $code])->exists();
    }
}
