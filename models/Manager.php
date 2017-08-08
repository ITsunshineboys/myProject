<?php

namespace app\models;

use yii\db\ActiveRecord;

class Manager extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'project_manager';
    }

    /**
     * Get total number of managers
     *
     * @return int
     */
    public static function totalNumber()
    {
        return self::find()->count();
    }
}
