<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/13 0013
 * Time: 下午 15:34
 */
namespace app\models;

use yii\db\ActiveRecord;

class LaborCost extends ActiveRecord
{
    const FIELDS_ADMIN =[
        'id',
        'province_code',
        'city_code',
        'univalence',
        'worker_kind',
        'quantity',
        'unit',
        'rank',
        'worker_kind_details'
    ];


    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'labor_cost';
    }

    /**
     * 根据地名查询
     * @param array $arr
     * @param string $jobs
     * @param string $rank
     * @return array|ActiveRecord[]
     */
    public static function univalence($arr = [],$jobs= '',$rank = '白银')
    {
        if(!$arr == null && !$jobs == null) {
            $province = $arr['province'] ?: 510000;
            $city = $arr['city'] ?: 510100;

            $labors = self::find()
                ->asArray()
                ->where(['and', ['province_code' => $province], ['city_code' => $city], ['worker_kind' => $jobs],['rank'=>$rank]])
                ->all();
        }
        return $labors;
    }
}

