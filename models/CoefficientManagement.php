<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/13 0013
 * Time: 下午 15:19
 */
namespace app\models;

use yii\db\ActiveRecord;

class CoefficientManagement extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'coefficient_management';
    }

    public static function findByAll()
    {
        return self::find()
            ->asArray()
            ->all();
    }
}
