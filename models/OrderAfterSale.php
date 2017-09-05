<?php

namespace app\models;
use Yii;
use yii\db\Exception;
use yii\db\ActiveRecord;

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

}
