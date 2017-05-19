<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/19 0019
 * Time: 上午 11:15
 */
namespace app\models;
use yii\db\ActiveRecord;

class DecorationList extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'decoration_list';
    }

    public static function findByIds($series ='',$style = '')
    {
        if ($series && $style){
            $decoration_list = self::find()->where(['and',['series_id'=>$series],['style_id'=>$style]])->one();
        }
        return $decoration_list;
    }
}