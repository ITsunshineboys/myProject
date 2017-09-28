<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/13 0013
 * Time: 下午 15:19
 */
namespace app\models;

use yii\db\ActiveRecord;

class CoefficientManagement extends ActiveRecord
{
    const TABLE_NAME = 'coefficient_management';
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'coefficient_management';
    }

    public function rules()
    {
        return [
            [['classify'],'string'],
            [['coefficient'],'number']
        ];
    }

    public static function findByAll($select = [],$where = [])
    {
        return self::find()
            ->asArray()
            ->select($select)
            ->where($where)
            ->all();
    }

    public static function findByInsert($rows)
    {
        $row = \Yii::$app->db->createCommand();
        return $row
            ->batchInsert(self::TABLE_NAME,['classify','coefficient'],$rows)
            ->execute();
    }
}
