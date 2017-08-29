<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29 0029
 * Time: 上午 11:14
 */
namespace app\models;

use yii\db\ActiveRecord;

class BrainpowerInitalSupervise extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'brainpower_inital_supervise';
    }

    public static function findByCode($province_code,$city_code)
    {
        $res = self::find()
            ->asArray()
            ->select('recommend_name,add_time,toponymy,district')
            ->orderBy(['sort'=>SORT_ASC])
            ->where(['and',['province_code'=>$province_code],['city_code'=>$city_code]])
            ->all();
        foreach ($res as &$list) {
            if(isset($list['add_time'])){
                $list['add_time']=date('Y-m-d H:i', $list['add_time']);
            }

        }
        return $res;
    }
}
