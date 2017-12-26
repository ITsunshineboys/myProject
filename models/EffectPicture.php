<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/9 0009
 * Time: 上午 11:44
 */
namespace app\models;

use yii\db\ActiveRecord;

class EffectPicture extends ActiveRecord
{
    const SUP_BANK_CARD = 'effect_picture';
    const FIELDS_ADMIN = [
        'id',
        'effect_id',
        'effect_images',
        'images_user',
        'series_id',
        'style_id',
    ];
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'effect_picture';
    }

    public static  function plotAdd($effect_id,$effect_images,$series_id,$style_id,$images_user)
    {
        $res = \Yii::$app->db->createCommand()->insert(self::SUP_BANK_CARD,[
            'effect_id'      => $effect_id,
            'effect_images'  => $effect_images,
            'images_user'    => $images_user,
            'series_id'      => $series_id,
            'style_id'       => $style_id,
        ])->execute();

        return $res;
    }

    public static  function plotEdit($id,$effect_images,$series_id,$style_id,$images_user)
    {
        $res = \Yii::$app->db->createCommand()->update(self::SUP_BANK_CARD,[
            'effect_images'  => $effect_images,
            'images_user'    => $images_user,
            'series_id'      => $series_id,
            'style_id'       => $style_id,
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
            ->where("effect_id in $ids")
            ->all();
    }
}