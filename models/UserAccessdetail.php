<?php

namespace app\models;

use app\services\StringService;
use Yii;
use app\services\ModelService;
/**
 * This is the model class for table "user_accessdetail".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $role_id
 * @property integer $access_type
 * @property string $access_money
 * @property integer $create_time
 * @property string $order_no
 * @property string $transaction_no
 * @property string $sku
 */
class UserAccessdetail extends \yii\db\ActiveRecord
{


    const PAGE_SIZE_DEFAULT=12;
    const FIELDS_EXTRA=[];
    const FIELDS_ADMIN = [
        'id' ,
        'uid',
        'role_id',
        'cash_money' ,
        'real_money' ,
        'apply_time',
        'handle_time',
        'status',
        'supplier_reason',
        'transaction_no',
        'order_no'
    ];

    const ACCESS_TYPE_RECHARGE=1;
    const ACCESS_TYPE_DEBIT=2;
    const ACCESS_TYPE_CASH=3;
    const ACCESS_TYPE_UNCASH_IN=4;
    const ACCESS_TYPE_DISAGREE=5;
    const ACCESS_TYPE_PAYMENT_GOODS=6;
    const ACCESS_TYPE_PAYMENT_BUY=7;
    const ACCESS_TYPE_REWARD_MONEY=8;
    const ACCESS_TYPE_PROJECTS_MONEY=9;
    const ACCESS_TYPE_PROJECTS_REFUND=10;
    const ACCESS_TYPE_REFUND=11;

    const ACCESS_TYPE_DESC_RECHARGE='充值';
    const ACCESS_TYPE_DESC_DEBIT='扣款';
    const ACCESS_TYPE_DESC_CASH='已提现';
    const ACCESS_TYPE_DESC_UNCASH_IN='提现中';
    const ACCESS_TYPE_DESC_DISAGREE='驳回';
    const ACCESS_TYPE_DESC_PAYMENT_GOODS='货款';
    const ACCESS_TYPE_DESC_PAYMENT_BUY='使用';
    //8.奖励金  9.工程款  10.工程退款
    const ACCESS_TYPE_DESC_REWARD_MONEY='奖励金';
    const ACCESS_TYPE_DESC_PROJECTS_MONEY='工程款';
    const ACCESS_TYPE_DESC_PROJECTS_REFUND='工程退款';
    const ACCESS_TYPE_DESC_REFUND='退款';

    const ACCESS_CODE_DEBIT='Debit';
    const ACCESS_CODE_PAYMENT_BUY='Payment_buy';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_accessdetail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'role_id', 'access_type', 'access_money', 'create_time'], 'integer'],
            [['order_no', 'transaction_no','sku'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '用户id',
            'role_id' => '角色id',
            'access_type' => '1.充值 2.扣款 3.已提现 4.提现中  5.驳回 6.货款',
            'access_money' => '收支金额',
            'create_time' => '创建时间',
            'order_no' => '订单号',
            'transaction_no' => '交易单号',
            'sku' => '商品编号',
        ];
    }

     /**
     * @param array $where
     * @param array $select
     * @param int $page
     * @param int $size
     * @param string $orderBy
     * @return array
     */
   public  static  function  pagination($where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC')
    {
        $select = array_diff($select, self::FIELDS_EXTRA);
//        $select ="transaction_no,uid,role_id,access_type,access_money,create_time,order_no,sku";
        $offset = ($page - 1) * $size;
        $Accessdetaillist=self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->groupBy('transaction_no')
            ->asArray()
            ->all();
         foreach ($Accessdetaillist as &$list) {
             $list['access_type']=self::findAccessType($list['access_type']);
             if ($list['access_type']==self::ACCESS_TYPE_DESC_PAYMENT_BUY)
             {
                    $accessList=self::find()
                         ->where(['transaction_no'=>$list['transaction_no']])
                         ->asArray()
                         ->all();
                     $list['access_money']=0;
                     foreach ($accessList as &$aList)
                     {
                         $list['access_money']+=$aList['access_money'];
                     }
             }
             if ($list['access_type']==self::ACCESS_TYPE_DESC_DEBIT
                ||$list['access_type']==self::ACCESS_TYPE_DESC_CASH
                ||$list['access_type']==self::ACCESS_TYPE_DESC_PAYMENT_BUY
                ||$list['access_type']==self::ACCESS_TYPE_DESC_UNCASH_IN
             )
            {
                $list['access_money']=sprintf('%.2f',-$list['access_money']*0.01);
            }else{
                $list['access_money']=sprintf('%.2f',$list['access_money']*0.01);
            }
            $list['create_time']=date('Y-m-d H:i',$list['create_time']);
        }
        $total=self::find()
            ->select($select)
            ->where($where)
            ->groupBy('transaction_no')
            ->asArray()
            ->count();
        if ($total>0)
        {
            return ModelService::pageDeal($Accessdetaillist, $total, $page, $size);
        }else{
            return array(
                'list' => [],
                'total_page' => 0,
                'count' => 0,
                'page' => 0
            );
        }

    }

        /**
     * @param $access_type
     * @return string
     */
   public static  function  findAccessType($access_type)
    {
        //1.充值 2.扣款 3.已提现 4.提现中  5.驳回 6.货款  7.使用  8.奖励金  9.工程款  10.工程退款
        switch ($access_type)
        {
            case 1:
                $type=self::ACCESS_TYPE_DESC_RECHARGE;
                break;
            case 2:
                $type=self::ACCESS_TYPE_DESC_DEBIT;
                break;
            case 3:
                $type=self::ACCESS_TYPE_DESC_CASH;
                break;
            case 4:
                $type=self::ACCESS_TYPE_DESC_UNCASH_IN;
                break;
            case 5:
                $type=self::ACCESS_TYPE_DESC_DISAGREE;
                break;
            case 6:
                $type=self::ACCESS_TYPE_DESC_PAYMENT_GOODS;
                break;
            case 7:
                $type=self::ACCESS_TYPE_DESC_PAYMENT_BUY;
                break;
            case 8:
                $type=self::ACCESS_TYPE_DESC_REWARD_MONEY;
                break;
            case 9:
                $type=self::ACCESS_TYPE_DESC_PROJECTS_MONEY;
                break;
            case 10:
                $type=self::ACCESS_TYPE_DESC_PROJECTS_REFUND;
                break;
            case self::ACCESS_TYPE_REFUND:
                $type=self::ACCESS_TYPE_DESC_REFUND;
                break;
        }
        return $type;
    }

    /**
     * 充值类收支明细
     * @param array $accessDetail
     * @return array
     */
    public  static  function  findRechargeDetail($accessDetail=[])
    {
        $data[]=[
            'name'=>'充值金额',
            'value'=>$accessDetail['access_money']
        ];
        switch ($accessDetail['recharge_pay_type'])
        {
            case 1:
                $data[]=[
                    'name'=>'充值类型',
                    'value'=>'支付宝'
                ];
                break;
            case 2:
                $data[]=[
                    'name'=>'充值类型',
                    'value'=>'微信支付'
                ];
                break;
        }

        $data[]=[
            'name'=>'充值时间',
            'value'=>date('Y-m-d H:i',$accessDetail['create_time'])
        ];
        $data[]=[
            'name'=>'交易单号',
            'value'=>$accessDetail['transaction_no']
        ];
        return $data;
    }

    /**
     * @param array $accessDetail
     * @return int
     */
    public static  function findAccessDetail($accessDetail=[],$type)
    {

        $GoodsOrder=GoodsOrder::findByOrderNo($accessDetail['order_no'])->toArray();
        $OrderGoods=OrderGoods::find()
            ->where(['order_no'=>$accessDetail['order_no']])
            ->andWhere(['sku'=>$accessDetail['sku']])
            ->asArray()
            ->one();
            if (!$GoodsOrder || !$OrderGoods)
            {
                $code=1000;
                return $code;
            }

            $data[]=[
                'name'=>'商品名称',
                'value'=>$OrderGoods['goods_name']
            ];

            switch ($type){
                case self::ACCESS_CODE_DEBIT:
                    $data[]=[
                        'name'=>'扣款金额',
                        'value'=> StringService::formatPrice($accessDetail['access_money']*0.01)
                    ];
                    break;
                case 'Goods':
                    $data[]=[
                        'name'=>'货款金额',
                        'value'=> StringService::formatPrice($OrderGoods['supplier_price']*0.01*$OrderGoods['goods_number'])
                    ];
                    $data[]=[
                        'name'=>"运费金额",
                        'value'=> StringService::formatPrice($OrderGoods['freight']*0.01)
                    ];
                    break;
//                case self::ACCESS_CODE_PAYMENT_BUY:
//                    switch ($role_id){
//                        case 7:
//
//                            break;
//                    }
//                    break;
            }

            $data[]=[
                'name'=>'商品编号',
                'value'=>$OrderGoods['sku']
            ];
            $data[]=[
                'name'=>'订单号',
                'value'=>$GoodsOrder['order_no']
            ];
            $data[]=[
                'name'=>'交易单号',
                'value'=>$accessDetail['transaction_no']
            ];
            $data[]=[
                'name'=>'下单时间',
                'value'=>date('Y-m-d H:i',$GoodsOrder['create_time'])
            ];
            $data[]=[
                'name'=>'付款方式',
                'value'=>$GoodsOrder['pay_name']
            ];
            $data[]=[
                'name'=>'付款时间',
                'value'=>date('Y-m-d H:i',$GoodsOrder['paytime'])
            ];
            $data[]=[
                'name'=>'扣款时间',
                'value'=>date('Y-m-d H:i',$accessDetail['create_time'])
            ];
            switch ($OrderGoods['shipping_type'])
            {
                case 0:
                    $express=Express::find()
                        ->where(['order_no'=>$accessDetail['order_no']])
                        ->andWhere(['sku'=>$accessDetail['sku']])
                        ->one();
                    if ($express)
                    {
                        $shippingType=$express->waybillname.'('.$express->waybillnumber.')';
                    }else{
                        $shippingType='';
                    }
                    break;
                case 1:
                    $shippingType='送货上门';
                    break;
            }
            $data[]=[
                'name'=>'配送方式',
                'value'=>$shippingType
            ];
            $data[]=[
                'name'=>'收货地址',
                'value'=>LogisticsDistrict::getdistrict($GoodsOrder['district_code']).$GoodsOrder['region']
            ];
           if ($type=='Goods')
           {
               $data[]=[
                   'name'=>'到账账户',
                   'value'=>'账户余额'
               ];
           }
            return $data;
    }


    /**
     * 退款收支详情
     * @param $transaction_no
     * @return array
     */
    public  static  function  findRefundDetail($transaction_no)
    {
        $access=self::find()
            ->where(['transaction_no'=>$transaction_no])
            ->asArray()
            ->one();
        $OrderGoods=OrderGoods::FindByOrderNoAndSku($access['order_no'],$access['sku'],'goods_name');
        $GoodsOrder=GoodsOrder::FindByOrderNo($access['order_no'],'create_time,pay_name,paytime');
        $list[]=[
            'name'=>'商品名称',
            'value'=>$OrderGoods->goods_name
        ];
        $list[]=[
            'name'=>'退款金额',
            'value'=>StringService::formatPrice($access['access_money']*0.01)
        ];
//        $refund_bank=BankinfoLog::findOne($access['refund_bank_log_id']);
//        $list[]=[
//            'name'=>'到账银行卡',
//            'value'=>$refund_bank->bankname
//        ];
        $list[]=[
            'name'=>'商品订单号',
            'value'=>$access['order_no']
        ];
        $list[]=[
            'name'=>'交易单号',
            'value'=>$transaction_no
        ];
        $list[]=[
            'name'=>'下单时间',
            'value'=>$GoodsOrder->create_time
        ];
        $list[]=[
            'name'=>'付款方式',
            'value'=>$GoodsOrder->pay_name
        ];
        $list[]=[
            'name'=>'付款时间',
            'value'=>date('Y-m-d H:i',$GoodsOrder->paytime)
        ];
        $list[]=[
            'name'=>'退款时间',
            'value'=>date('Y-m-d H:i',$access['create_time'])
        ];
        return $list;
    }
    /**
     * @param $transaction_no
     * @return array
     */
    public  static  function  GetPaymentBuyDetail($transaction_no)
    {
        $accessDetailList=self::find()
            ->where(['transaction_no'=>$transaction_no])
            ->all();

        $goods_name='';
        $amount_order=0;
        $sku=[];
        $order_no=[];
        $add_time='';
        $pay_time='';
        $pay_name='';
        foreach ($accessDetailList as &$List)
        {
            $OrderGoods=OrderGoods::find()
                ->where(['order_no'=>$List->order_no])
                ->all();
            foreach ($OrderGoods as &$orderGood)
            {
                $sku[]=$orderGood->sku;
            }
            $GoodsOrder=GoodsOrder::FindByOrderNo($List->order_no);
            $order_no[]=$GoodsOrder->order_no;
            $amount_order+=$GoodsOrder->amount_order;
            $goods_name=$OrderGoods[0]->goods_name;
        }
        if (count($accessDetailList)>0)
        {
            if (count($sku)>2)
            {
                $sku=$sku[0].','.$sku[1].','.$sku[2].',...';
            }elseif(count($sku)==2){
                $sku=$sku[0].','.$sku[1];
            }else
            {
                $sku=$sku[0];
            }
            if (count($order_no)>2)
            {
                $order_no=$order_no[0].','.$order_no[1].','.$order_no[2].',...';
            }elseif(count($sku)==2){
                $order_no=$order_no[0].','.$order_no[1];
            }else
            {
                $order_no=$order_no[0];
            }
            if (count($accessDetailList)>1 )
            {
                $goods_name=$goods_name.',...';
            }
            $add_time=date('Y-m-d H:i',$GoodsOrder->create_time);
            $pay_time=date('Y-m-d H:i',$accessDetailList[0]->create_time);
            $pay_name=$GoodsOrder->pay_name;
        }

        $data[]=[
            'name'=>'商品名称',
            'value'=>$goods_name,
        ];
        $data[]=[
            'name'=>'结算金额',
            'value'=> StringService::formatPrice($amount_order*0.01),
        ];
        $data[]=[
            'name'=>'商品编号',
            'value'=>$sku,
        ];
        $data[]=[
            'name'=>'订单号',
            'value'=>$order_no,
        ];
        $data[]=[
            'name'=>'下单时间',
            'value'=>$add_time,
        ];
        $data[]=[
            'name'=>'付款方式',
            'value'=>$pay_name,
        ];
        $data[]=[
            'name'=>'付款时间',
            'value'=>$pay_time,
        ];

        return $data;
    }



}
