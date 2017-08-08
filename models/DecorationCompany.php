<?php

namespace app\models;

use yii\db\ActiveRecord;

class DecorationCompany extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'decoration_company';
    }

    /**
     * Get total number of decoration companies
     *
     * @return int
     */
    public static function totalNumber()
    {
        return self::find()->count();
    }
}
