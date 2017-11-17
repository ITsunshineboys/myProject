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
        'rank',
        'worker_kind_details'
    ];
    const LABOR_COST ='labor_cost';
    const SELECT_FIND = 'id,univalence,worker_kind';
    const LABOR_LEVEL = '白银';
    const WORKER_KIND_DETAILS = [
            'weak'=> '弱电',
            'strong' => '强电',
            'waterway'=>'水路'
        ];

    const WEAK_CURRENT_PRICE = 300;
    const WATERPROOF_PRICE = 350;
    const CARPENTRY_PRICE = 240;
    const PRICE_CONVERT = 100;
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'labor_cost';
    }

    public function rules()
    {
        return [
            ['univalence','integer']
        ];
    }

    /**
     * 根据地名查询
     * @param array $arr
     * @param string $jobs
     * @param string $rank
     * @return array|ActiveRecord[]
     */
    public static function univalence($arr,$jobs,$rank = self::LABOR_LEVEL)
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
    public static function profession($arr,$craft,$select = self::SELECT_FIND ,$rank = self::LABOR_LEVEL)
    {
        $labors = self::find()
            ->asArray()
            ->select($select)
            ->where(['and',['city_code' => $arr],['worker_kind'=>$craft],['rank'=>$rank]])
            ->one();

        $labors['univalence'] = $labors['univalence'] / self::PRICE_CONVERT;

        return $labors;
    }

    /**
     * labor const list
     * @return array|ActiveRecord[]
     */
    public static function LaborCostList($select = [] , $group = [])
    {
        return  self::find()
            ->distinct()
            ->select($select)
            ->groupBy($group)
            ->asArray()
            ->all();
    }


    public static function workerKind($select = [],$province,$city,$worker_kind)
    {
        return self::find()
            ->asArray()
            ->select($select)
            ->where(['and',['province_code'=>$province],['city_code'=>$city],['worker_kind'=>$worker_kind]])
            ->one();
    }
}

