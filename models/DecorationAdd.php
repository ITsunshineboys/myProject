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
    public static function findByAll($str = '',$all_area ='')
    {
        if($str)
        {
            $add = self::find()->where(['project'=>$str])->all();
            $add_price = 0;
            foreach ($add as $one)
            {
                if($one['max_area']>= $all_area && $one['min_area'] <= $all_area){
                    $add_price = $one['price'];
                }
            }
        }
        return $add_price;
    }

    public static function CarpentryAddAll($str = '',$series = '',$style = '')
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