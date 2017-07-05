<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/17 0017
 * Time: 下午 17:37
 */
namespace app\models;
use yii\db\ActiveRecord;

class FixationFurniture extends ActiveRecord
{
    public static function tableName()
    {
        return 'fixation_furniture';
    }

    public static function findById($arr = [])
    {
        if(!$arr == null){
            $id = $arr['effect_id'];
            $province = $arr['province'] ?: 510000;
            $city = $arr['city'] ?: 510100;
            $series_id = $arr['series'];
            $style_id = $arr['style'];
            $fixation_furniture = self::find()->asArray()->where(['and',['effect_id'=>$id],['city'=>$city],['province'=>$province],['series_id'=>$series_id],['style_id'=>$style_id]])->all();

        }
        return $fixation_furniture;
    }
}