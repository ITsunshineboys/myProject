<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/16 0016
 * Time: 下午 16:42
 */
namespace app\models;

use yii\db\ActiveRecord;

class IntelligenceAssort extends ActiveRecord
{
    public static function tableName()
    {
        return 'intelligence_assort';
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