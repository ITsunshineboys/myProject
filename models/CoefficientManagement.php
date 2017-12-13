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

//    public function rules()
//    {
//        return [
//            [['coefficient','category_id'],'number']
//        ];
//    }

    public static function findByAll($select = [],$where = [])
    {
        $row =  self::find()
            ->asArray()
            ->select($select)
            ->where($where)
            ->all();

        foreach ($row as &$one){
            $one['coefficient'] = $one['coefficient'] / 100;
        }

        return $row;
    }

    public static function findByInsert($rows,$city)
    {
        $row = \Yii::$app->db->createCommand();
        return $row
            ->insert(self::TABLE_NAME,[
                'category_id' => $rows['id'],
                'city_code' => $city,
                'coefficient' => $rows['value'] * 100,
            ])
            ->execute();
    }
}
