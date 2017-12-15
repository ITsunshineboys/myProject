<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/25 0025
 * Time: 下午 15:58
 */
namespace app\models;

use yii\db\ActiveRecord;

class DecorationMessage extends ActiveRecord
{
    const TABLE_NAME = 'decoration_message';
    const FIELDS_ADMIN = [
            'decoration_add_id',
            'quantity',
            'style_id',
            'series_id',
            'min_area',
            'max_area',
        ];
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'decoration_message';
    }

    public static function findById($select = [] , $where = [])
    {
        return self::find()
            ->asArray()
            ->select($select)
            ->where($where)
            ->all();
    }

    public static function findByInsert($rows,$columns)
    {
        $row = \Yii::$app->db->createCommand();
        return $row->batchInsert(self::TABLE_NAME,$columns,$rows)->execute();
    }

    public static function findByUpdate($row,$id)
    {

        $rows=self::find()->where(['decoration_add_id'=>$id])->one();
        $rows->quantity=(int)$row;
        $res=$rows->save(false);
//        $rows = \Yii::$app->db->createCommand();
//        $res= $rows->update(self::TABLE_NAME,[
//            'quantity' => $row,
//        ],['id'=>$id])->execute();
        return $res;
    }
}