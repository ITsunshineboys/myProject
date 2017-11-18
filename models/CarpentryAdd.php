<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/5 0005
 * Time: 上午 11:52
 */
namespace app\models;
use yii\db\ActiveRecord;

class CarpentryAdd extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'carpentry_add';
    }

    public static function findByStipulate($series = '',$style = '',$province = '四川',$city = '成都')
    {
        $inquire_result = [];
        $carpentry_add_all = self::find()->where(['and',['province'=>$province],['city'=>$city],['series_id'=>$series],['style_id'=>$style]])->all();
        foreach ($carpentry_add_all as $carpentry_add_one)
        {
            if($carpentry_add_one['project'] == '造型长度'){
                $inquire_result ['modelling_length']  = $carpentry_add_one['standard'];
            }elseif ($carpentry_add_one['project'] == '平顶面积') {
                $inquire_result ['flat_area'] = $carpentry_add_one['standard'];
            }
        }
        return $inquire_result;
    }
}