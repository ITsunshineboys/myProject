<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/5 0005
 * Time: 上午 11:52
 */
namespace app\models;
use yii\db\ActiveRecord;

class CarpentryAdd extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'carpentry_add';
    }

    public static function findById($id,$code)
    {
        $select = 'carpentry_add.standard,wt.worker_name';
        $rows = self::find()
            ->select($select)
            ->leftJoin('worker_type as wt','wt.id = carpentry_add.worker_type_id')
            ->where(['in','worker_type_id',$id])
            ->andWhere(['city_code'=>$code])
            ->asArray()
            ->all();

        return $rows;
    }
}