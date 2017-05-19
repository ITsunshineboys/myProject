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
     * @param $id
     * @return int|mixed
     */
    public static function weakLocation($id)
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
}