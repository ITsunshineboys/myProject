<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31 0031
 * Time: 下午 15:37
 */
namespace app\models;

use yii\db\ActiveRecord;

class DecorationAdd extends ActiveRecord
{

    const FIELDS_ADMIN =
        [
            'id',
            'project',
            'min_area',
            'max_area',
            'material',
            'quantity',
            'sku',
            'series_id',
            'style_id',
            'district_code'
        ];
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'decoration_add';
    }

    /**
     * 防水查询
     * @param string $str
     * @param string $all_area
     * @return int|mixed
     */
    public static function AllArea($str = '',$all_area ='',$city= '510100')
    {
        if($str)
        {
            $add = self::find()->asArray()->where(['and',['project'=>$str],['district_code'=>$city]])->all();
            $add_prices = [];
            foreach ($add as $one)
            {
                if($one['max_area'] >= $all_area && $one['min_area'] <= $all_area){
                    $add_prices [] = $one;
                }
            }
        }
        return $add_prices;
    }

    public static function AllSeries($str = '',$all_series ='',$city= '510100')
    {
        if ($str && $all_series)
        {
            $add = self::find()->asArray()->where(['and',['project'=>$str],['district_code'=>$city],['series_id'=>$all_series]])->all();
        }
        return $add;
    }

    public static function AllStyle($str = '',$all_style ='',$city= '510100')
    {
        if ($str && $all_style)
        {
            $add = self::find()->asArray()->where(['and',['project'=>$str],['district_code'=>$city],['style_id'=>$all_style]])->all();
        }
        return $add;
    }

    public static function CarpentryAddAll($str = '',$series = 1,$style = 1)
    {
        if($str){
            $add = self::find()->where(['and',['project'=>$str],['series_id'=>$series],['style_id'=>$style]])->all();
            $add_price = 0;
            foreach ($add as $one)
            {
                $add_price += $one['price'];
            }
        }
        return $add_price;
    }
}