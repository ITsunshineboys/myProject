<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/13 0013
 * Time: 下午 17:23
 */
namespace app\models;
use yii\db\ActiveRecord;

class PointsTotal extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'points_total';
    }

    public static function findByAll($AllId)
    {
        $id = [];
        foreach ($AllId as $oneId)
        {
            $id [] = intval($oneId['id']);
        }
        $all = self::find()
            ->asArray()
            ->where(['in','place_id',$id])
            ->all();
        return $all;
    }
}