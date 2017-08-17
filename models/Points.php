<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/18 0018
 * Time: 上午 11:32
 */
namespace app\models;

use yii\db\ActiveRecord;

class Points extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'points';
    }

    /**
     * 弱电点位
     * @param $id
     * @return int|mixed
     */
    public static function weakPoints($id)
    {
        $sql = "place,weak_current_points";
        $all = self::find()->select($sql)->where(['effect_id'=>$id])->all();
        $weak_current = 0;
        foreach ($all as $number)
        {
            $weak_current += $number['weak_current_points'];
        }
        return $weak_current;
    }

    /**
     * 水路点位
     * @param $id
     * @return int|mixed
     */
    public static function waterwayPoints($id)
    {
        $sql = "place,waterway_points";
        $all = self::find()->select($sql)->where(['effect_id'=>$id])->all();
        $waterway_points = 0;
        foreach ($all as $number)
        {
            $waterway_points += $number['waterway_points'];
        }
        return $waterway_points;
    }

    public static function strongPoints($id)
    {
        $strong_id = [];
        $all = self::find()->where(['effect_id'=>$id])->all();
        foreach ($all as $one) {
            $strong_id [] = $one['id'];
        }

        return $strong_id;
    }

    public static function strongPointsAll($id)
    {
        $all = self::find()
            ->asArray()
            ->where(['effect_id'=>$id])
            ->all();
        return $all;
    }
}