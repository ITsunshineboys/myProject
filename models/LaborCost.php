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
    const WORKER_KIND_DETAILS = [
            'weak'=> '弱电',
            'strong' => '强电',
            'waterway'=>'水路'
        ];

    const UNIT = [
      1 => '元/天',
    ];
//    const WEAK_CURRENT_PRICE = 300;
//    const WATERPROOF_PRICE = 350;
//    const CARPENTRY_PRICE = 240;
//    const PRICE_CONVERT = 100;
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
     * 根据工种id查询
     * @param $city
     * @param $id
     * @param string $rank
     * @return array|null|ActiveRecord
     */
    public static function profession($city,$id,$rank = 1)
    {
        $select = self::SELECT_FIND;
        $labors = self::find()
            ->asArray()
            ->select($select)
            ->where(['city_code' => $city])
            ->andWhere(['worker_kind_id'=>$id])
            ->andWhere(['rank'=>$rank])
            ->one();

        $labors['univalence'] = $labors['univalence'] / 100;

        return $labors;
    }

    /**
     * labor const list
     * @return array|ActiveRecord[]
     */
    public static function LaborCostList($select,$where)
    {
        return  self::find()
//            ->distinct()
            ->select($select)
            ->where($where)
//            ->groupBy('worker_kind')
            ->asArray()
            ->all();
    }


    public static function workerKind($id,$city_code,$province_code)
    {

        $row =  self::find()
            ->asArray()
//            ->select($select)
            ->where(['worker_kind_id'=>$id,'city_code'=>$city_code])
            ->one();

        if($row==null){
            $row['city'] = District::findByCode($city_code)['name'];
            $row['province'] = District::findByCode($province_code)['name'];
            $row['location']=$row['province'].'-'.$row['city'];
            $row['worker_kind']=WorkerType::gettype($id);
            $row['worker_id']= $id;
            $row['city_code']=$city_code;
            $row['province_code']=$province_code;
//            $row['city']='巴中';
//            $row['province']='四川省';
            $row['univalence']='';
            $row['unit']=self::UNIT[1];

        }else{
//            $row['city']='成都';
//            $row['province']='四川省';
            $row['univalence'] = $row['univalence'] / 100;
            $row['worker_kind']=WorkerType::gettype($row['worker_kind_id']);
            $row['unit'] = self::UNIT[$row['unit']];
            $row['city'] = District::findByCode($row['city_code'])['name'];
            $row['province'] = District::findByCode($row['province_code'])['name'];
            $row['location']=$row['province'].'-'.$row['city'];
        }


        unset($row['worker_kind_id']);
        return $row;


    }
}

