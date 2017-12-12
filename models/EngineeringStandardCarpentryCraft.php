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
    const FIELDS_ADMIN =[
        'id',
        'title',
        'value',
    ];

    const UNIT = [
        1 => '长度',
        2 => '宽度',
        3 => '根',
        4 => '张',
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