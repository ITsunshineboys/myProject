<?php

namespace app\models;
use Yii;
use yii\db\Exception;
use yii\db\ActiveRecord;
use yii\db\Query;

class OrderAfterSale extends ActiveRecord
{

    const AFTER_SALE_SERVICES=[
        1=> '退货',
        2=> '换货' ,
        3=> '上门维修',
        4=> '上门换货',
        5=> '上门退货',
    ];

    const GOODS_AFTER_SALE_SERVICES = [
        0=>'提供发票',
        1=> '上门安装',
        2=>'上门维修',
        3=>'上门退货',
        4=>'上门换货',
        5=>'退货',
        6=>'换货',
    ];
    const AFTER_SALE_HANDLE_AGREE='agree';
    const AFTER_SALE_HANDLE_DISAGREE='disagree';
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'order_after_sale';
    }


    /**
     * user apply After Sale
     * @param $postData
     * @param $user
     * @return int
     */
    public  static  function UserApplyAfterSale($postData,$user,$uploadsData)
    {
        if(!array_key_exists('type', $postData) || !array_key_exists('description', $postData) || !array_key_exists('order_no', $postData) || !array_key_exists('sku', $postData)){
            $code=1000;
            return $code;
        }
        $GoodsOrder=GoodsOrder::find()
            ->select('user_id,pay_status')
            ->where(['order_no'=>$postData['order_no']])
            ->one();
        if ($GoodsOrder->user_id!=$user->id){
            $code=1034;
            return $code;
        }
        $OrderGoods=OrderGoods::find()
            ->where(['order_no'=>$postData['order_no'],'sku'=>$postData['sku']])
            ->one();
        if (!$OrderGoods){
            $code=1000;
            return $code;
        }
        if ($OrderGoods->order_status !=1 || $GoodsOrder->pay_status !=1 ){
            $code=1036;
            return $code;
        }
        $goods=Goods::find()
            ->select('after_sale_services')
            ->where(['sku'=>$postData['sku']])
            ->one();
        if ($goods){
            $array=explode(',',$goods->after_sale_services);
            $arr=[];
            foreach ($array as $k =>$v){
                $arr[]=self::GOODS_AFTER_SALE_SERVICES[$array[$k]];
            }
            if (!in_array(self::AFTER_SALE_SERVICES[$postData['type']],$arr)){
                $code=1035;
                return $code;
            }
        }
        $CheckIsAfter=self::find()
            ->where(['order_no'=>$postData['order_no'],'sku'=>$postData['sku']])
            ->one();
        if ($CheckIsAfter){
            $code=1031;
            return $code;
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            $OrderAfterSale=new self;
            $OrderAfterSale->order_no=$postData['order_no'];
            $OrderAfterSale->sku=$postData['sku'];
            $OrderAfterSale->description=$postData['description'];
            $OrderAfterSale->type=$postData['type'];
            $OrderAfterSale->create_time=time();
            $res=$OrderAfterSale->save();
            if (!$res)
            {
                $tran->rollBack();
                $code=500;
                return $code;
            }
            $OrderGoods->customer_service=1;
            $res2=$OrderGoods->save();
            if (!$res2)
            {
                $tran->rollBack();
                $code=500;
                return $code;
            }
            if (is_array($uploadsData)){
                foreach ($uploadsData as &$uploads){
                    $OrderAfterSaleImage=new OrderAfterSaleImage();
                    $OrderAfterSaleImage->after_sale_id=$OrderAfterSale->id;
                    $OrderAfterSaleImage->image=$uploads;
                    if (!$OrderAfterSaleImage->save()){
                        $tran->rollBack();
                        $code=500;
                        return $code;
                    };
                }
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }
    }


    /**
     * @param $postData
     * @param $user
     * @return array|int|null|ActiveRecord
     */
    public  static function FindAfterSaleData($postData,$user)
    {
        if(!array_key_exists('order_no', $postData) || !array_key_exists('sku', $postData)){
            $code=1000;
            return $code;
        }
        $supplier=Supplier::find()
            ->select(['id'])
            ->where(['uid'=>$user->id])
            ->one();
        if (!$supplier)
        {
            $code=1010;
            return $code;
        }
        $goodsOrder=GoodsOrder::find()
            ->select(['supplier_id'])
            ->where(['order_no'=>$postData['order_no']])
            ->one();
        if (!$goodsOrder){
            $code=1000;
            return $code;
        }
        if ($goodsOrder->supplier_id!= $supplier->id){
            $code=1036;
            return $code;
        }
        $data=self::find()
            ->where(['order_no'=>$postData['order_no'],'sku'=>$postData['sku']])
            ->asArray()
            ->one();
        $data['image']=OrderAfterSaleImage::find()
            ->where(['after_sale_id'=>$data['id']])
            ->asArray()
            ->all();
        if ($data['create_time'] !=0){
            $data['create_time']=date('Y-m-d H:i',$data['create_time']);
        }
        if ($data['supplier_handle_time']!=0){
            $data['supplier_handle_time']=date('Y-m-d H:i',$data['supplier_handle_time']);
        }
        switch ($data['supplier_handle']){
            case 0:
                $data['supplier_handle']='未处理';
                break;
            case 1:
                $data['supplier_handle']='同意';
                break;
            case 2:
                $data['supplier_handle']='驳回';
                break;
        }
        return $data;
    }


    /**
     * @param $postData
     * @return int|void
     */
    public static function SupplierAfterSaleHandle($postData)
    {
        if(!array_key_exists('order_no', $postData) || !array_key_exists('sku', $postData) || !array_key_exists('handle',$postData)){
            $code=1000;
            return $code;
        }
        $after_sale=self::find()
            ->where(['order_no'=>$postData['order_no'],'sku'=>$postData['sku']])
            ->one();
        if (!$after_sale){
            $code=1000;
            return $code;
        }
        switch ($postData['handle']){
            case self::AFTER_SALE_HANDLE_DISAGREE:
                if (!array_key_exists('reject_reason',$postData)){
                    $code=1000;
                    return $code;
                }
                $code=self::disagreeAfterSale($after_sale,$postData);
                break;
            case self::AFTER_SALE_HANDLE_AGREE:
                $code=self::agreeAfterSale($after_sale,$postData);
                break;
        }
        return $code;
    }

    /**
     * @param $after_sale
     * @param $postData
     * @return int
     */
    public static  function disagreeAfterSale($after_sale,$postData)
    {
        $tran = Yii::$app->db->beginTransaction();
        try{
            $after_sale->supplier_handle=2;
            $after_sale->supplier_handle_reason=$postData['reject_reason'];
            $after_sale->supplier_handle_time=time();
            $res=$after_sale->save();
            if (!$res){
                $tran->rollBack();
                $code=500;
                return $code;
            }
            $OrderGoods=OrderGoods::find()
                ->where(['order_no'=>$postData['order_no'],'sku'=>$postData['sku']])
                ->one();
            if (!$OrderGoods){
                $tran->rollBack();
                $code=1036;
                return $code;
            }
            $OrderGoods->customer_service=2;
            $res2=$OrderGoods->save();
            if (!$res2){
                $tran->rollBack();
                $code=500;
                return $code;
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }
    }

    /**
     * @param $after_sale
     * @param $postData
     * @return int
     */
    public  static  function agreeAfterSale($after_sale,$postData)
    {
        $tran = Yii::$app->db->beginTransaction();
        try{
            $after_sale->supplier_handle=1;
            $after_sale->supplier_handle_time=time();
            $res=$after_sale->save();
            if (!$res){
                $tran->rollBack();
                $code=500;
                return $code;
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }
    }
}
