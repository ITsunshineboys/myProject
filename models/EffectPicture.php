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
    const FIELDS_ADMIN = [
        'id',
        'effect_id',
        'house_pictrue',
        'vr_pictrue',
        'images_user',
    ];
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'effect_picture';
    }

    public static  function plotAdd($post)
    {
        if ($post)
        {
            return 11;exit;
        }
    }
}