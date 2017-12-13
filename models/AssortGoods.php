<?php
namespace app\models;

use yii\db\ActiveRecord;

class AssortGoods extends ActiveRecord
{
    const FIELDS_NAME = [
        'category_id',
        'path',
        'pid',
        'title',
    ];

    const APP_FIELDS = ['title','category_id','pid','path'];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'assort_goods';
    }

    /**
     * assort goods add
     * @param $post
     * @return int
     */
    public static function add($add,$city)
    {
        $db= \Yii::$app->db;
        $res = $db
            ->createCommand()
            ->insert(self::tableName(),[
                'title'=>$add['title'],
                'category_id'=>$add['id'],
                'pid'=>$add['pid'],
                'path'=>$add['path'],
                'state'=>0,
                'quantity'=>0,
                'city_code'=>$city,
            ])
            ->execute();
        return $res;
    }

    public static function findByAll($select = [] , $where = [])
    {
        return self::find()
            ->asArray()
            ->select($select)
            ->where($where)
            ->all();
    }

    public static function findByInsert($post,$city)
    {
        $db = \Yii::$app->db;
        $res = $db
            ->createCommand()
            ->insert(self::tableName(),[
                'title'=> $post['title'],
                'category_id'=> $post['id'],
                'pid'=> $post['pid'],
                'path'=> $post['path'],
                'state'=> 1,
                'quantity'=> $post['quantity'],
                'city'=> $city,
            ])
            ->execute();
        return $res;
    }
}