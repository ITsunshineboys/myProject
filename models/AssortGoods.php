<?php
namespace app\models;

use yii\db\ActiveRecord;

class AssortGoods extends ActiveRecord
{
    const FIELDS_NAME = [
        'one_path',
        'two_path',
        'three_path',
        'add_path',
    ];
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'assort_goods';
    }

    public static function add($post)
    {
        $db= \Yii::$app->db;
        $res = $db
            ->createCommand()
            ->batchInsert(self::tableName(),self::FIELDS_NAME,$post)
            ->execute();
        return $res;
    }
}