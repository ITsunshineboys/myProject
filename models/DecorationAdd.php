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

    public static function findByAll($select = [] , $where = [])
    {
        return self::find()
            ->asArray()
            ->select($select)
            ->where($where)
            ->leftJoin('decoration_message as d','d.decoration_add_id = decoration_add.id')
            ->groupBy('decoration_add.three_materials')
            ->all();
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
        $offset = ($page - 1) * $size;
        $List = self::find()
            ->select($select)
            ->where($where)
            ->orderBy(['add_time' => SORT_ASC])
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();

        foreach ($List as &$effect) {
            if(isset($effect['add_time'])){
                $effect['add_time']=date('Y-m-d H:i', $effect['add_time']);
            }
        }

        return [
            'total' => (int)self::find()->where($where)->asArray()->count(),
            'page'=>$page,
            'size'=>$size,
            'details' => $List
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