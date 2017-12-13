<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/13 0013
 * Time: 下午 13:56
 */
namespace app\models;

use yii\db\ActiveRecord;

class EngineeringStandardCarpentryCraft extends ActiveRecord
{

    const UNIT = [
        1 => 'm',
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'engineering_standard_carpentry_craft';
    }

    public static function findByAll()
    {
        $row =  self::find()
            ->asArray()
            ->all();

        foreach ($row as &$one){
            $one['unit'] = self::UNIT[$one['unit']];
            $one['value'] = $one['value'] / 100;
        }

        return $row;
    }
}