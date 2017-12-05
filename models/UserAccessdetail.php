<?php

namespace app\models;

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
    const ACCESS_TYPE_DESC_RECHARGE='充值';
    const ACCESS_TYPE_DESC_DEBIT='扣款';
    const ACCESS_TYPE_DESC_CASH='已提现';
    const ACCESS_TYPE_DESC_UNCASH_IN='提现中';
    const ACCESS_TYPE_DESC_DISAGREE='驳回';
    const ACCESS_TYPE_DESC_PAYMENT_GOODS='货款';
    const ACCESS_TYPE_DESC_PAYMENT_BUY='使用';
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
            [['order_no', 'transaction_no'], 'string', 'max' => 50],
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
        //1.充值 2.扣款 3.已提现 4.提现中  5.驳回 6.货款  7.使用
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
        $data[]=[
            'name'=>'充值类型',
            'value'=>'微信支付'
        ];
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
                case 'Debit':
                    $data[]=[
                        'name'=>'扣款金额',
                        'value'=>GoodsOrder::switchMoney($accessDetail['access_money']*0.01)
                    ];
                    break;
                case 'Goods':
                    $data[]=[
                        'name'=>'货款金额',
                        'value'=>GoodsOrder::switchMoney($OrderGoods['supplier_price']*0.01*$OrderGoods['goods_number'])
                    ];
                    $data[]=[
                        'name'=>"运费金额",
                        'value'=>GoodsOrder::switchMoney($OrderGoods['freight']*0.01)
                    ];
                    break;
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
}
