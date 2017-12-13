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
    const FIELDS_ADMIN =[
        'id',
        'project',
        'value',
        'coefficient',
        'series_or_style',
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'engineering_standard_carpentry_coefficient';
    }

    public static function findByAll($where)
    {
        $row =  self::find()
            ->asArray()
            ->select('id,project,value,coefficient,series_or_style')
            ->where($where)
            ->All();

        foreach ($row as &$one){
            $one['value'] = $one['value'] / 100;
        }

        return $row;
    }

}
