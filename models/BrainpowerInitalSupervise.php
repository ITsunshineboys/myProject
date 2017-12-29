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
    const STATUS_OPEN = 1;
    const FIELDS_NAME = [
        'province',
        'province_code',
        'city',
        'city_code',
        'district',
        'district_code',
        'street',
        'toponymy',
        'image',
        'add_time',
        'sort',
        'house_type_name',
        'recommend_name',
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'brainpower_inital_supervise';
    }

//    public function rules()
//    {
//        return  [
//            ['province','province_code','city','city_code','district','district_code','street','toponymy','image','add_time','sort','house_type_name','recommend_name','required'],
//            ['sort','integer']
//        ];
//    }

    public static function findByCode($province,$city)
    {
        $res = self::find()
            ->asArray()
            ->select([])
            ->orderBy(['sort'=>SORT_ASC])
            ->where(['and',['province_code'=>$province],['city_code'=>$city]])
            ->all();
        foreach ($res as &$list) {
            if(isset($list['add_time'])){
                $list['add_time']=date('Y-m-d H:i', $list['add_time']);
            }

        }
        return $res;
    }

    public static function codeStatus($city)
    {  // ['status'=>1]
        $ros = self::find()
            ->asArray()
            ->where([['city_code'=>$city]])
            ->all();
        return $ros;
    }
}
