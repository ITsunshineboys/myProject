<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/19 0019
 * Time: 下午 15:27
 */
namespace app\models;
use yii\db\ActiveRecord;

class PlasteringReconstruction extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'plastering_reconstruction';
    }

    public static function findById($id = 1)
    {
        $all = self::find()->asArray()->where(['decoration_list_id'=>$id])->all();
        return $all;
    }
}