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

    public static function material($decoration_list_id='',$decoration_id = '')
    {
        $arr_id = [];
        if(!$decoration_id == null){
            $array = self::find()->where(['and',['decoration_list_id'=>$decoration_list_id],['basis_decoration_id' => $decoration_id]])->all();
            foreach ($array as $arr)
            {
                $arr_id[] = $arr['goods_id'];
            }
        }
       return $arr_id;
    }
}