<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/18 0018
 * Time: 下午 14:17
 */
namespace app\models;

use yii\db\ActiveRecord;

class PointsDetails extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'points_details';
    }

    public static function AllQuantity($allId = [])
    {
        $all_id = [];
        foreach ($allId as $all)
        {
            $all_id [] = $all['id'];
        }
        $sql = "points_quantity";
        $all_quantity = self::find()->select($sql)->where(['in','place_id',$all_id])->all();
        $powerful_points = 0;
        foreach ($all_quantity as $quantity)
        {
            $powerful_points += $quantity['points_quantity'];
        }
        return $powerful_points;
    }
}