<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/18 0018
 * Time: 下午 18:02
 */
namespace app\models;

use yii\db\ActiveRecord;

class WorksData extends ActiveRecord
{
    const  SUP_BANK_CARD ='works_data';
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'works_data';
    }

    public static function plotAdd($effect_id,$goods_first,$goods_second,$goods_three,$goods_code,$goods_quantity,$three_category_id)
    {
        $res = \Yii::$app->db->createCommand()->insert(self::SUP_BANK_CARD,[
            'effect_id'=>$effect_id,
            'goods_first'=>$goods_first,
            'goods_second'=>$goods_second,
            'goods_three'=>$goods_three,
            'goods_code'=>$goods_code,
            'goods_quantity'=>$goods_quantity,
            'three_category_id' => $three_category_id
        ])->execute();

        return $res;
    }

    public static function plotEdit($id,$goods_first,$goods_second,$goods_three,$goods_code,$goods_quantity,$three_category_id)
    {
        $res = \Yii::$app->db->createCommand()->update(self::SUP_BANK_CARD,[
            'goods_first'=>$goods_first,
            'goods_second'=>$goods_second,
            'goods_three'=>$goods_three,
            'goods_code'=>$goods_code,
            'goods_quantity'=>$goods_quantity,
            'three_category_id' =>$three_category_id
        ],['id'=>$id])->execute();

        return $res;
    }

    public static function findById($id)
    {
        return self::find()
            ->asArray()
            ->where(['effect_id'=>$id])
            ->all();
    }

    public static function findByIds($ids)
    {
        return self::find()
            ->asArray()
            ->where(['in','effect_id',$ids])
            ->all();
    }
}