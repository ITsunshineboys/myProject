<?php

namespace app\models;

use app\services\ModelService;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "line_supplier_goods".
 *
 * @property integer $id
 * @property integer $line_supplier_id
 * @property integer $goods_id
 * @property integer $create_time
 * @property integer $status
 */
class LineSupplierGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'line_supplier_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['line_supplier_id', 'goods_id', 'create_time'], 'required'],
            [['line_supplier_id', 'goods_id', 'create_time', 'status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'line_supplier_id' => 'Line Supplier ID',
            'goods_id' => 'Goods ID',
            'create_time' => 'Create Time',
            'status' => 'Status',
        ];
    }


    /**
     * 添加线下体验店商品
     * @param $post
     * @return int
     */
    public  static  function  AddLineGoods($post)
    {
        $code=1000;
        $goods=Goods::find()->where(['sku'=>$post['sku']])->one();
        if (!$goods)
        {
            return $code;
        }
        $LineSupplier=LineSupplier::findOne($post['line_id']);
        if (!$LineSupplier)
        {
            return $code;
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            $LineGoods=new self();
            $LineGoods->line_supplier_id=$LineSupplier->id;
            $LineGoods->goods_id=$goods->id;
            $LineGoods->create_time=time();
            $LineGoods->status=1;
            if (!$LineGoods->validate())
            {
                $tran->rollBack();
                $code=500;
                return $code;
            }
            if (!$LineGoods->save())
            {
                $tran->rollBack();
                $code=500;
                return $code;
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (\Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }
    }


    /**
     * 编辑线下体验店商品
     * @param $post
     * @return int
     */
    public  static  function  UpLineGoods($post)
    {
        $code=1000;
        if (
            !array_key_exists('line_id',$post)
            ||!array_key_exists('sku',$post)
            ||!array_key_exists('status',$post)
        )
        {
            return $code;
        }
        if ((int)$post['status']!=1 && (int)$post['status']!=2)
        {
            return $code;
        }
        $goods=Goods::find()->where(['sku'=>$post['sku']])->one();
        if (!$goods)
        {
            return $code;
        }
        $LineSupplier=LineSupplierGoods::findOne($post['line_id']);
        if (!$LineSupplier)
        {
            return $code;
        }
        $LineSupplierGoods=LineSupplierGoods::find()
            ->where(['goods_id'=>$goods->id])
            ->one();
        if (!$LineSupplierGoods)
        {
            return $code;
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            $LineSupplierGoods->line_supplier_id=$LineSupplier->id;
            $LineSupplierGoods->goods_id=$goods->id;
            $LineSupplierGoods->create_time=time();
            $LineSupplierGoods->status=$post['status'];
            if (!$LineSupplierGoods->save(false))
            {
                $tran->rollBack();
                $code=500;
                return $code;
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (\Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }
    }



    /**
     * @param array $where
     * @param int $page
     * @param int $size
     * @return array
     */
    public static function pagination($where = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT)
    {
        $orderBy = 'LG.id DESC';
        $select ="LG.id as line_goods_id,LG.line_supplier_id,G.sku,G.title as goods_name,L.district_code,LG.status,S.shop_name as supplier_shop_name";
        $offset = ($page - 1) * $size;
        $List = (new Query())
            ->from(self::tableName().' as LG')
            ->leftJoin(LineSupplier::tableName().' as L','L.id=LG.line_supplier_id')
            ->leftJoin(Supplier::tableName().' as S','L.supplier_id=S.id')
            ->leftJoin(Goods::tableName().' as  G','LG.goods_id=G.id')
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->all();
        foreach ($List as &$list)
        {
            $list['line_shop_name']=self::GetLineShopNameByLineId($list['line_supplier_id']);
            $list['district']=LogisticsDistrict::GetLineDistrictByDistrictCode($list['district_code']);
            unset($list['line_supplier_id']);
            unset($list['district_code']);
        }
        $total=(new Query())
            ->from(self::tableName().' as LG')
            ->leftJoin(LineSupplier::tableName().' as L','L.id=LG.line_supplier_id')
            ->leftJoin(Supplier::tableName().' as S','L.supplier_id=S.id')
            ->leftJoin(Goods::tableName().' as  G','LG.goods_id=G.id')
            ->select($select)
            ->where($where)
            ->count();
        return ModelService::pageDeal($List, $total, $page, $size);
    }


    /**
     * 获取线下商品名称 by  line_id
     * @param $line_id
     * @return mixed|string
     */
    public  static  function  GetLineShopNameByLineId($line_id)
    {
            $LineSupplier=LineSupplier::findOne($line_id);
            if ($LineSupplier)
            {
                $supplier=Supplier::find()
                    ->select('shop_name')
                    ->where(['id'=>$LineSupplier->supplier_id])
                    ->one();
                if ($supplier)
                {
                    return $supplier->shop_name;
                }
            }
            return '';
    }

    /**
     * 切换线下体验店商品status
     * @param $post
     * @return int
     */
    public  static  function  SwitchLineSupplierGoodsStatus($post)
    {
        if (
            !array_key_exists('status',$post)
            ||!array_key_exists('line_goods_id',$post)
        )
        {
            $code=1000;
            return $code;
        }
        if ((int)$post['status']!=1 && (int)$post['status']!=2)
        {
            $code=1000;
            return $code;
        }
        $LineSupplierGoods=self::findOne($post['line_goods_id']);
        if (!$LineSupplierGoods)
        {
            $code=1000;
            return $code;
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            $LineSupplierGoods->status=(int)$post['status'];
            if (!$LineSupplierGoods->save(false))
            {
                $code=500;
                $tran->rollBack();
                return $code;
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (\Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }
    }

    /**
     * 删除线下体验店商品
     * @param $line_goods_id
     * @return int
     */
    public  static  function  DelLineSupplierGoods($line_goods_id)
    {
        $LineSupplierGoods=LineSupplierGoods::findOne($line_goods_id);
        if (!$LineSupplierGoods)
        {
            $code=1000;
            return $code;
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            $res1=$LineSupplierGoods->delete();
            if (!$res1)
            {
                $code=500;
                return $code;
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (\Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }
    }



}
