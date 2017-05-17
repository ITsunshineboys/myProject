<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/17 0017
 * Time: 下午 17:29
 */
namespace app\models;
use yii\db\ActiveRecord;

class AppliancesAssort extends ActiveRecord
{
    public static function tableName()
    {
        return 'appliances_assort';
    }

    public function findById($id = '')
    {
        $arr_id = [];
        if(!$id == null){
            $array = self::find()->where(['decoration_list_id' => $id])->all();
            foreach ($array as $arr)
            {
                $arr_id[] = $arr['goods_id'];
            }
        }
        return $arr_id;
    }
}