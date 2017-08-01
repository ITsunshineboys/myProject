<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/19 0019
 * Time: 上午 11:15
 */
namespace app\models;
use yii\db\ActiveRecord;

class DecorationList extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'decoration_list';
    }

    /**
     * find id
     * @param $id
     * @return mixed
     */
    public static function findById($id)
    {
        $decoration = self::find()->where(['effect_id'=>$id])->one();
        $decoration_list  = $decoration['id'];
        return $decoration_list;
    }
}