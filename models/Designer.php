<?php

namespace app\models;

use yii\db\ActiveRecord;

class Designer extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'designer';
    }

    /**
     * Get total number of designers
     *
     * @return int
     */
    public static function totalNumber()
    {
        return self::find()->count();
    }
}
