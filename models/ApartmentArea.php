<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/23 0023
 * Time: 下午 15:02
 */

namespace app\models;

use yii\db\ActiveRecord;

class ApartmentArea extends ActiveRecord
{

    const PAGE_SIZE_DEFAULT = 12;
    const FIELDS_NAME = [
        'province_code',
        'city_code',
        'min_area',
        'max_area',
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'apartment_area';
    }

    public static function findByAll($select)
    {
        return self::find()
            ->asArray()
            ->select($select)
            ->all();
    }

    public static function findCondition($select=[],$where = [])
    {
        return self::find()
            ->asArray()
            ->select($select)
            ->where($where)
            ->all();
    }

    public static function findInset($rows)
    {
        return \Yii::$app->db
            ->createCommand()
            ->insert(self::tableName(),[
                'points_id'=>$rows['add_id'],
                'min_area'=>$rows['min_area'],
                'max_area'=>$rows['max_area'],
            ])
            ->execute();
    }

    public static function findUpdate($rows)
    {
        return \Yii::$app->db
            ->createCommand()
            ->update(self::tableName(),[
                'min_area'=>$rows['min_area'],
                'max_area'=>$rows['max_area'],
            ],['id'=>$rows['edit_id']])
            ->execute();
    }
}