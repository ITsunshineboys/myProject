<?php

namespace app\models;

use app\services\ModelService;
use Yii;
use yii\data\Pagination;
use yii\db\Query;

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


    const FIXED_GOODS_SEACRH=[
        'fg.id',
        'fg.two_cate_id',
        'fg.three_cate_id',
        'fg.start_time',
        'fg.end_time',
        'fg.status',
        'gc.title',
        'gc.parent_title'

    ];

    const FIXED_GOODS_STATUS = [
        0 => '未开始',
        1 => '已开始',
        2 => '已逾期',

    ];
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


    /**
     * 添加
     * @param $path
     * @param $sku
     * @param $start_time
     * @param $end_time
     * @param $city_code
     * @param $user_id
     * @return int
     */
    public static function add($path,$sku,$start_time,$end_time,$city_code,$user_id){
            if($start_time>$end_time){
                return 1000;
            }
            if($start_time && $end_time){
                $start_time=strtotime($start_time);
                $end_time=strtotime($end_time);
            }
            $goods = Goods::find()
                ->where(['sku'=>$sku])
                ->andWhere(['status'=>2])
                ->one();

            if (!$goods){
                $code = 1043;
                return $code;
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


    /**
     * 修改
     * @param $id
     * @param $sku
     * @param $start_time
     * @param $end_time
     * @param $user_id
     * @return int
     */
    public static function edit($id,$sku,$start_time,$end_time,$user_id){

        if($start_time>$end_time){
            return 1000;
        }
        if($start_time && $end_time){
            $start_time=strtotime($start_time);
            $end_time=strtotime($end_time);
        }

        $edit_fixed=FixedGrabbingGoods::find()->where(['id'=>$id])->one();

        $goods = Goods::find()
            ->where(['sku'=>$sku])
            ->andWhere(['status'=>2])
            ->one();
        if (!$goods || $goods->category_id!=$edit_fixed->three_cate_id){
            $code = 1043;
            return $code;
        }
        $edit_fixed->start_time=$start_time;
        $edit_fixed->end_time=$end_time;
        $edit_fixed->sku=$sku;
        $edit_fixed->operat_time=time();
        $edit_fixed->operator=User::find()->select('nickname')->where(['id'=>$user_id])->one()->nickname;
        $res=$edit_fixed->save();
        if(!$res){
            $code=500;
            return $code;
        }

        return 200;
    }


    /**
     * 商品详情
     * @param $sku
     * @return array|null|\yii\db\ActiveRecord
     */
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



    /**
     * 列表分页
     * @param array $where
     * @param array $select
     * @param int $page
     * @param int $size
     * @param string $orderBy
     * @return array
     */
    public static function pagination($where = [], $select = [], $page = 1, $size = ModelService::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC'){
        $query = (new Query())
            ->from( 'fixed_grabbing_goods as fg')
            ->leftJoin('goods_category as gc', 'gc.id = fg.three_cate_id')
            ->select($select)
            ->where($where);
        $count = $query->count();

        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        foreach ($arr as &$value){
            if($value['start_time']){
                $value['start_time']=date('Y.m.d',$value['start_time']);
                $value['end_time']=date('Y.m.d',$value['end_time']);
                $value['time']=$value['start_time'].'-'.$value['end_time'];
            }
            if(isset($value['status'])){
                $value['status']=self::FIXED_GOODS_STATUS[$value['status']];
            }

        }
        return [
            'total' => (int)$count,
            'page'=>$page,
            'size'=>$size,
            'list' => $arr
        ];
    }
}
