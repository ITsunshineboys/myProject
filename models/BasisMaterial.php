<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/15 0015
 * Time: 下午 16:19
 */
namespace app\models;

use yii\db\ActiveRecord;

class BasisMaterial extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'basis_material';
    }

    public static function material($id = '')
    {
        $arr_id = [];
        if(!$id == null){
            $array = self::find()->where(['goods_id' => $id])->all();
            foreach ($array as $arr)
            {
                $arr_id[] = $arr['goods_id'];
            }
        }
       return $arr_id;
    }
}