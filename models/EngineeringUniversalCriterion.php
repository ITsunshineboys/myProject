<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/15 0015
 * Time: 上午 9:58
 */
namespace app\models;
use yii\db\ActiveRecord;

class
EngineeringUniversalCriterion extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'engineering_universal_criterion';
    }

    public static function findByAll($str)
    {
        return self::find()
            ->asArray()
            ->where(['project'=>$str])
            ->all();
    }

    public static function mudMakeArea($str,$area)
    {
        return self::find()
            ->asArray()
            ->where(['and',['project'=>$str],['project_particulars'=>$area]])
            ->one();
    }
}