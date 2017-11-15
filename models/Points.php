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
    const TABLE_NAME = 'points';

    const WEAK_CURRENT_POINTS = 3;
    const STRONG_CURRENT_POINTS = 48;
    const WATERWAY_POINTS = 6;
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'points';
    }

    public function rules()
    {
        return [
            [['title'],'string'],
            [['pid','level'],'number']
        ];
    }

    public static function findByPid($select =[],$where = [])
    {
        return self::find()
            ->asArray()
            ->select($select)
            ->where($where)
            ->all();
    }

    public static function findByOne($select =[],$where = [])
    {
        return self::find()
            ->asArray()
            ->select($select)
            ->where($where)
            ->one();
    }

    public static function findByInsert($rows)
    {
        $row = \Yii::$app->db->createCommand();
        return $row->insert(self::TABLE_NAME,[
            'count' => $rows['count'],
            'title' => $rows['title'],
            'pid'   => $rows['id'],
            'level' => 3,
            'differentiate' => 1,
        ])->execute();
    }

    public static function findByUpdate($rows,$id,$title)
    {
        $row = \Yii::$app->db->createCommand();
        return $row->update(self::TABLE_NAME,[
            'title'=>$title,
            'count'=>$rows
            ],['id' => $id])->execute();
    }
}