<?php

namespace app\models;

use yii\db\ActiveRecord;

class Worker extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'worker';
    }

    /**
     * Get total number of workers
     *
     * @return int
     */
    public static function totalNumber()
    {
        return (int)self::find()->count();
    }
}
