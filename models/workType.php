<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/31 0031
 * Time: 上午 11:19
 */
namespace app\models;
use yii\db\ActiveRecord;

class workType extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'work_type';
    }

    public static function findByCount()
    {
        $sql = 'SELECT worker_name,establish_time,COUNT(worker_name) as worker_name_quantity,COUNT(rank_name) as rank_name_quantity FROM work_type GROUP BY worker_name';
        return \Yii::$app->db
            ->createCommand($sql)
            ->queryAll();
    }
}