<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fixed_grabbing_goods".
 *
 * @property integer $id
 * @property integer $first_cate_id
 * @property integer $two_cate_id
 * @property integer $three_cate_id
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $status
 * @property string $sku
 * @property integer $operat_time
 * @property string $operator
 * @property integer $city_code
 */
class FixedGrabbingGoods extends \yii\db\ActiveRecord
{
    const FIXED_GOODS_SELET=[
        'goods.id',
        'goods.series_id',
        'goods.supplier_id',
        'goods.brand_id',
        'goods.title',
        's.shop_name',
        'b.name as brand_name',
        'se.series',


    ];
    const STATUS=1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fixed_grabbing_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_cate_id', 'two_cate_id', 'three_cate_id', 'sku', 'operat_time', 'city_code'], 'required'],
            [['first_cate_id', 'two_cate_id', 'three_cate_id', 'start_time', 'end_time', 'status', 'sku', 'operat_time', 'city_code'], 'integer'],
            [['operator'], 'string', 'max' => 100],
        ];
    }



    public static function add($path,$sku,$start_time,$end_time,$city_code,$user_id){
            if($start_time && $end_time){
                $start_time=strtotime($start_time);
                $end_time=strtotime($end_time);
            }

            $FixedGoods=new FixedGrabbingGoods();
            $FixedGoods->city_code=$city_code;
            $FixedGoods->sku=$sku;
            $FixedGoods->start_time=$start_time;
            $FixedGoods->end_time=$end_time;
            $FixedGoods->first_cate_id=$path[0];
            $FixedGoods->two_cate_id=$path[1];
            $FixedGoods->three_cate_id=$path[2];
            $FixedGoods->operat_time=time();
            $FixedGoods->status=self::STATUS;
            $FixedGoods->operator=User::find()->select('nickname')->where(['id'=>$user_id])->one()->nickname;

            $res=$FixedGoods->save();
            if(!$res){
                $code=500;
                return $code;
            }

            return 200;
    }

    public static function goodsview($sku){

        $goods_data=Goods::find()
            ->select(self::FIXED_GOODS_SELET)
            ->where(['sku'=>$sku])
            ->leftJoin('goods_brand as b','goods.brand_id=b.id')
            ->leftJoin('supplier as s','goods.supplier_id=s.id')
            ->leftJoin('series as se','goods.series_id=se.id')
            ->asArray()
            ->one();

        $styleIds = GoodsStyle::styleIdsByGoodsId($goods_data['id']);
        $goods_data['style']=join('、', Style::findNames(['in', 'id', $styleIds]));
        $goods_data['goods_attr']=GoodsAttr::frontDetailsByGoodsId($goods_data['id']);

        return $goods_data;


    }
}
