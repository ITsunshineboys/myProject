<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;
use yii\db\ActiveRecord;
use Yii;

class OrderGoods extends ActiveRecord
{

    const  PLATFORM_PRICE_DESC='平台价';
    const  SUPPLIER_PRICE_DESC='供应商采购价格';
    const  COMPANY_PRICE_DESC='装修公司采购价';
    const  MANAGER_PRICE_DESC='项目经理采购价';
    const  DESIGNER_PRICE_DESC='设计师采购价';
    const  WORKER_PRICE_DESC='工人采购价';

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'order_goodslist';
    }

    /**
    * @param $order_no
    * @param $sku
    * @return array|null|ActiveRecord
    */
    public static  function  FindByOrderNoAndSku($order_no,$sku){
        $data=self::find()
            ->where(['order_no'=>$order_no])
            ->andWhere(['sku'=>$sku])
            ->one();
        return $data;
    }

    /**
     * @param array $postData
     * @param $user
     * @return int
     */
    public static  function  UserConfirmReceipt($postData=[],$user)
    {
            if (
                !array_key_exists('order_no',$postData)
                || !array_key_exists('sku',$postData)
            ){
                $code=1000;
                return $code;
            }
            $GoodsOrder=GoodsOrder::FindByOrderNo($postData['order_no']);
            $OrderGoods=self::FindByOrderNoAndSku($postData['order_no'],$postData['sku']);
            if (!$OrderGoods
                || $user->id !=$GoodsOrder->user_id
                || $OrderGoods->shipping_status!=1)
            {
                $code=1034;
                return $code;
            }
            $supplier=Supplier::find()->where(['id'=>$GoodsOrder->supplier_id])->one();
            $role_number=$supplier->shop_no;
            $transaction_no=GoodsOrder::SetTransactionNo($role_number);
            $tran = Yii::$app->db->beginTransaction();
            try{
                $OrderGoods->shipping_status=2;
                $OrderGoods->order_status=1;
                $res=$OrderGoods->save(false);
                if (!$res)
                {
                    $tran->rollBack();
                    $code=500;
                    return $code;
                }
                $supplier_accessdetail=new UserAccessdetail();
                $supplier_accessdetail->uid=$supplier->uid;
                $supplier_accessdetail->role_id=Yii::$app->params['supplierRoleId'];
                $supplier_accessdetail->access_type=UserAccessdetail::ACCESS_TYPE_PAYMENT_GOODS;
                $supplier_accessdetail->access_money=($OrderGoods->freight+$OrderGoods->supplier_price*$OrderGoods->goods_number);
                $supplier_accessdetail->order_no=$postData['order_no'];
                $supplier_accessdetail->sku=$postData['sku'];
                $supplier_accessdetail->create_time=time();
                $supplier_accessdetail->transaction_no=$transaction_no;
                $res2=$supplier_accessdetail->save(false);
                if (!$res2)
                {
                    $tran->rollBack();
                    $code=500;
                    return $code;
                }
                $supplier->availableamount+=($OrderGoods->freight+$OrderGoods->supplier_price*$OrderGoods->goods_number);
                $supplier->balance+=$OrderGoods->freight+$OrderGoods->supplier_price*$OrderGoods->goods_number;
                $res3=$supplier->save(false);
                if (!$res3)
                {
                    $tran->rollBack();
                    $code=500;
                    return $code;
                }
                $express=Express::find()
                    ->where(
                        [
                            'order_no'=>$postData['order_no'],
                            'sku'=>$postData['sku']
                        ]
                    )
                    ->one();
                if ($express)
                {
                    $express->receive_time=time();
                    if (!$express->save(false))
                    {
                        $tran->rollBack();
                        $code=500;
                        return $code;
                    }
                }
                $code=UserNewsRecord::AddOrderNewRecord(User::findOne($supplier->uid),'订单已收货',Yii::$app->params['supplierRoleId'],"订单号{$postData['order_no']},商品名称{$OrderGoods->goods_name},货款已打至您的账户余额.",$postData['order_no'],$postData['sku'],GoodsOrder::STATUS_DESC_DETAILS);
                if ($code!=200)
                {
                    $code=500;
                    $tran->rollBack();
                    return $code;
                }
                $code=200;
                $tran->commit();
                return $code;
            }catch (\Exception $e)
            {
                $tran->rollBack();
                $code=500;
                return $code;
            }
    }

    /**
     * 添加样板间操作
     * @param $id
     * @return int
     */
    public  static  function  AddEffect($id)
    {
        $effect=Effect::findOne($id);
        if (!$effect)
        {
           return 200;
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            $earnest=EffectEarnest::find()
                ->where(['effect_id'=>$id])
                ->one();
            $earnest->status=1;
            if (!$earnest->save(false))
            {
                $tran->rollBack();
                return 500;
            }
            $time=(time()-60*60*6);
            $list=EffectEarnest::find()
                ->where("create_time<={$time}")
                ->andWhere(['status'=>0,'type'=>0,'item'=>0])
                ->all();
            if ($list)
            {
                foreach ($list as &$delList)
                {
                    $effect_id=$delList->effect_id;
                    $res=$delList->delete();
                    if (!$res)
                    {
                        $tran->rollBack();
                        return 500;
                    };
                    $effect=Effect::find()
                        ->where(['id'=>$effect_id])
                        ->one();
                    if ($effect)
                    {
                        $res1=$effect->delete();
                        if (!$res1)
                        {
                            $tran->rollBack();
                            return 500;
                        };
                    }
                    $effect_material=EffectMaterial::find()
                        ->where(['effect_id'=>$effect_id])
                        ->one();
                    if ($effect_material)
                    {
                        $res2=$effect_material->delete();
                        if (!$res2)
                        {
                            $tran->rollBack();
                            return 500;
                        };
                    }
                    $EffectPicture=EffectPicture::find()
                        ->where(['effect_id'=>$effect_id])
                        ->one();
                    if ($EffectPicture)
                    {
                        $res3=$EffectPicture->delete();
                        if (!$res3)
                        {
                            $tran->rollBack();
                            return 500;
                        };
                    }
                }
            }
            $tran->commit();
            return 200;
        }catch (\Exception $e){
            $tran->rollBack();
            return 500;
        }

    }




    
}