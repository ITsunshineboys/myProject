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
    const TABLE_COLUMNS = "";
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

    public static function findByInsert($rows,$columns = [])
    {
        $row = \Yii::$app->db->createCommand();
        return $row->batchInsert(self::TABLE_NAME,$columns,$rows)->execute();
    }

}