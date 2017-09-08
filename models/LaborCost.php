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
    const LABOR_COST ='labor_cost';

    const UNIT = [
        'day' => '天',
        'square' => 'M2',
        'number'=>'个'
    ];

    const WORKER_KIND_DETAILS = [
            'weak'=> '弱电',
            'strong' => '强电'
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
    public static function univalence($arr,$jobs,$rank = '白银')
    {
        $labors = self::find()
            ->asArray()
            ->where(['and',['province_code' => $arr['province']],['city_code' => $arr['city']],['worker_kind' => $jobs],['rank'=>$rank]])
            ->all();
        return $labors;
    }

    /**
     * 根据工种类型查询
     * @param $arr
     * @param $craft
     * @param string $rank
     * @return array|null|ActiveRecord
     */
    public static function profession($arr,$craft,$rank = '白银')
    {
        $labors = self::find()
            ->asArray()
            ->where(['and',['city_code' => $arr['city']],['worker_kind_details'=>$craft],['rank'=>$rank]])
            ->one();
        return $labors;
    }

    /**
     * labor const list
     * @return array|ActiveRecord[]
     */
    public static function LaborCostList()
    {
        return  self::find()
            ->distinct()
            ->select('worker_kind,rank')
            ->asArray()
            ->all();

    }

    public static function weakAdd($worker_kind,$province_code,$city_code,$rank,$univalence,$weak_quantity,$strong_quantity)
    {
        $labor_const = \Yii::$app->db;
        // 弱电添加
        $labor_const_add [] = $labor_const
            ->createCommand()
            ->insert(self::LABOR_COST,['province_code'=>$province_code,'city_code'=>$city_code,'univalence'=>$univalence,'worker_kind'=>$worker_kind,'quantity'=>$weak_quantity,'unit'=>self::UNIT['number'],'rank'=>$rank,'worker_kind_details'=>self::WORKER_KIND_DETAILS['weak']])
            ->execute();

        $labor_const_add [] = $labor_const
            ->createCommand()
            ->insert(self::LABOR_COST,['province_code'=>$province_code,'city_code'=>$city_code,'univalence'=>$univalence,'worker_kind'=>$worker_kind,'quantity'=>$strong_quantity,'unit'=>self::UNIT['number'],'rank'=>$rank,'worker_kind_details'=>self::WORKER_KIND_DETAILS['strong']])
            ->execute();

        return $labor_const_add;
    }
}

