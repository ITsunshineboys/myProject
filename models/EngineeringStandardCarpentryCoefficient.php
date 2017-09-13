<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/13 0013
 * Time: 下午 13:58
 */
namespace app\models;

use yii\db\ActiveRecord;

class EngineeringStandardCarpentryCoefficient extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'engineering_standard_carpentry_coefficient';
    }

    public static function findByAll()
    {
        return self::find()
            ->asArray()
            ->All();
    }

}
