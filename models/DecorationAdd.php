<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31 0031
 * Time: 下午 15:37
 */
namespace app\models;

use yii\data\Pagination;
use yii\db\ActiveRecord;
use yii\db\Query;

class DecorationAdd extends ActiveRecord
{

    const PAGE_SIZE_DEFAULT = 12;
    const FIELDS_ADMIN =
        [
            'id',
            'province_code',
            'city_code',
            'one_materials',
            'two_materials',
            'three_materials',
            'correlation_message',
            'sku',
            'add_time',
        ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'decoration_add';
    }

//    public function rules()
//    {
//        return [
//            [['one_materials','two_materials','three_materials','correlation_message'],'string','max' => 100],
//            [['province_code','city_code','sku'],'number'],
//        ];
//    }

    /**
     * 防水查询
     * @param string $str
     * @param string $all_area
     * @return int|mixed
     */
    public static function AllArea($str,$all_area,$city='510100')
    {
        $add = self::find()
            ->asArray()
            ->where(['and',['project'=>$str],['district_code'=>$city]])
            ->all();
        $add_prices = [];
        foreach ($add as $one) {
            if($one['max_area'] >= $all_area && $one['min_area'] <= $all_area){
                $add_prices [] = $one;
            }
        }
        return $add_prices;
    }

    /**
     * series find all
     * @param $str
     * @param $all_series
     * @param string $city
     * @return array|ActiveRecord[]
     */
    public static function AllSeries($str,$all_series,$city= '510100')
    {
        return self::find()
            ->asArray()
            ->where(['and',['project'=>$str],['district_code'=>$city],['series_id'=>$all_series]])
            ->all();
    }

    /**
     * style find all
     * @param $str
     * @param $all_style
     * @param string $city
     * @return array|ActiveRecord[]
     */
    public static function AllStyle($str,$all_style,$city= '510100')
    {
        return self::find()
            ->asArray()
            ->where(['and',['project'=>$str],['district_code'=>$city],['style_id'=>$all_style]])
            ->all();
    }

    public static function findByAll($code,$style,$series,$area)
    {

//        $select ='decoration_add.id,decoration_add.c_id,decoration_add.sku,d.quantity';
        $data=self::find()
            ->asArray()
            ->where(['city_code'=>$code])
            ->all();

        foreach ($data as &$v){
            $quantity=DecorationMessage::find()
                ->select('max(quantity) as quantity')
                ->where(['decoration_add_id'=>$v['id']])
                ->andWhere(['or',['style_id'=>$style],['series_id'=>$series],['and',['<=','min_area',$area],['>=','max_area',$area]]])
                ->asArray()
                ->one();

//            $quantity=arsort($quantity);
//            $max=max($quantity);
            $v['quantity']=$quantity['quantity'];
        }
        return $data;

//        return self::find()
//            ->asArray()
//            ->select($select)
//            ->leftJoin('decoration_message as d','d.decoration_add_id = decoration_add.id')
//            ->where(['decoration_add.city_code'=>$code])
//            ->andWhere(['or',['d.style_id'=>$style],['d.series_id'=>$series],['and',['<=','d.min_area',$area],['>=','d.max_area',$area]]])
//            ->all();
    }


    /**
     * carpentry find all
     * @param $str
     * @param int $series
     * @param int $style
     * @return int|mixed
     */
    public static function CarpentryAddAll($str,$series = 1,$style = 1)
    {
        if($str){
            $add = self::find()
                ->asArray()
                ->where(['and',['project'=>$str],['series_id'=>$series],['style_id'=>$style]])
                ->all();
            $add_price = 0;
            foreach ($add as $one)
            {
                $add_price += $one['price'];
            }
        }
        return $add_price;
    }

    public static function pagination($where,$select,$page = 1, $size = self::PAGE_SIZE_DEFAULT)
    {
        $query = (new Query())
            ->from( 'decoration_add as da')
            ->leftJoin('goods_category as gc', 'gc.id = da.c_id')
            ->select($select)
            ->where($where)
            ->orderBy(['da.add_time' => SORT_ASC]);

        $count = $query->count();

        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        foreach ($arr as &$effect) {
            if(isset($effect['add_time'])){
                $effect['add_time']=date('Y-m-d H:i', $effect['add_time']);
            }
            $effect['three_materials']=$effect['title'];

        }

        return [
            'total' => (int)$count,
            'page'=>$page,
            'size'=>$size,
            'details' => $arr
        ];
    }

    public static function findByUpdate($sku, $id)
    {
        return \Yii::$app->db->createCommand()
            ->update(self::tableName(),[
                'sku'=>$sku,
            ],['id'=>$id])
            ->execute();
    }
}