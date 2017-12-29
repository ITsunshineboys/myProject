<?php

namespace app\models;
use app\admin\controller\Users;
use app\services\ModelService;
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
    const RETURN_ON_LINE=1;
    const EXCHANGE_ON_LINE=2;
    const REPAIR_DOOR=3;
    const EXCHANGE_DOOR=4;
    const RETURN_DOOR=5;
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
    const AFTER_SALE_HANDLE_AGREE_DESC='同意';
    const AFTER_SALE_HANDLE_DISAGREE_DESC='驳回';
    const AFTER_SALE_HANDLE=[
        1=>self::AFTER_SALE_HANDLE_AGREE_DESC,
        2=>self::AFTER_SALE_HANDLE_DISAGREE_DESC
    ];

    const AFTER_SALE_UN_HANDLE='unhandle';
    const AFTER_SALE_USER_UN_SHIPPED='user_unshipped';
    const AFTER_SALE_SUPPLIER_UN_CONFIRM_RECEIVED='supplier_unconfirm_received';
    const AFTER_SALE_SUPPLIER_UN_SHIPPED='supplier_unshipped';
    const AFTER_SALE_USER_UN_CONFIRM_RECEIVED='user_unconfirm_received';
    const AFTER_SALE_SUPPLIER_UN_SEND='supplier_unsend';
    const AFTER_SALE_USER_UN_CONFIRM_EXCHANGE='user_unconfirm_exchange';
    const AFTER_SALE_SUPPLIER_UN_CONFIRM_RETURN_TO_DOOR='supplier_unconfirm_retunrn_to_door';
    const AFTER_SALE_SHIPPED='shipped';
    const AFTER_SALE_COUNT_DOWN='countdown';
    const AFTER_SALE_USER_UN_CONFIRM_REPAIR='user_unconfirm_repair';
    const AFTER_SALE_USER_UN_CONFIRM='unconfirm';
    const AFTER_STATUS_IN='in';
    const AFTER_STATUS_OVER='over';
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
        if(!array_key_exists('type', $postData)
            || !array_key_exists('description', $postData)
            || !array_key_exists('order_no', $postData)
            || !array_key_exists('sku', $postData)){
            $code=1000;
            return $code;
        }
        if (!array_key_exists($postData['type'], self::AFTER_SALE_SERVICES))
        {
            $code=1000;
            return $code;
        }
        $GoodsOrder=GoodsOrder::find()
            ->select('user_id,pay_status')
            ->where(['order_no'=>$postData['order_no']])
            ->one();
        if (!$GoodsOrder)
        {
            $code=1000;
            return $code;
        }
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
//        if ($goods){
//            $array=explode(',',$goods->after_sale_services);
//            $arr=[];
//            foreach ($array as $k =>$v){
//                $arr[]=self::GOODS_AFTER_SALE_SERVICES[$array[$k]];
//            }
//            if (!in_array(self::AFTER_SALE_SERVICES[$postData['type']],$arr)){
//                $code=1035;
//                return $code;
//            }
//        }
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
            $res2=$OrderGoods->save(false);
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
                    if (!$OrderAfterSaleImage->save(false)){
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


        $goodsOrder=GoodsOrder::find()
            ->select(['supplier_id'])
            ->where(['order_no'=>$postData['order_no']])
            ->one();
        if (!$goodsOrder){
            $code=1000;
            return $code;
        }
        if ($user->last_role_id_app==6)
        {
            $supplier=Supplier::find()
                ->select(['id'])
                ->where(['uid'=>$user->id])
                ->one();
            if (!$supplier)
            {
                $code=1010;
                return $code;
            }
            if ($goodsOrder->supplier_id!= $supplier->id){
                $code=1036;
                return $code;
            }
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
//        switch ($data['supplier_handle']){
//            case 0:
//                $data['supplier_handle']='未处理';
//                break;
//            case 1:
//                $data['supplier_handle']='同意';
//                break;
//            case 2:
//                $data['supplier_handle']='驳回';
//                break;
//        }
        return $data;
    }


    /**
     * 获取售后信息
     * @param $postData
     * @param $user
     * @return array|int|null|ActiveRecord
     */
    public  static function GetAfterSaleData($postData,$user)
    {
        if(!array_key_exists('order_no', $postData) || !array_key_exists('sku', $postData)){
            $code=1000;
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
        $data=self::find()
            ->where(['order_no'=>$postData['order_no'],'sku'=>$postData['sku']])
            ->select('id,type,description,create_time')
            ->asArray()
            ->one();
        //'1. 退货  2.换货  3.上门维修  4. 上门换货   5.上门退货  ',
        $data['type_id']=$data['type'];
        $data['type']=self::AFTER_SALE_SERVICES[$data['type']];
        $data['image']=OrderAfterSaleImage::find()
            ->where(['after_sale_id'=>$data['id']])
            ->select('image')
            ->asArray()
            ->all();
        if ($data['create_time'] !=0){
            $data['create_time']=date('Y-m-d H:i',$data['create_time']);
        }
//        switch ($data['supplier_handle']){
//            case 0:
//                $data['supplier_handle']='未处理';
//                break;
//            case 1:
//                $data['supplier_handle']='同意';
//                break;
//            case 2:
//                $data['supplier_handle']='驳回';
//                break;
//        }
        return $data;
    }

    /**
     * @param $postData
     * @return int|void
     */
    public static function SupplierAfterSaleHandle($postData)
    {
        if(
            !array_key_exists('order_no', $postData)
            || !array_key_exists('sku', $postData)
            || !array_key_exists('handle',$postData)){
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
        if ($after_sale->supplier_handle!=0)
        {
            $code=1032;
            return $code;
        }
        switch ($postData['handle']){
            case 2:
                if (!array_key_exists('reject_reason',$postData)){
                    $code=1000;
                    return $code;
                }
                $code=self::disagreeAfterSale($after_sale,$postData);
                break;
            case 1:
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
            $after_sale->complete_time=time();
            $res=$after_sale->save(false);
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
            $res=$after_sale->save(false);
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


    /**
     * @param $OrderAfterSale
     * @return array
     */
    public  static  function  findUnHandleAfterSale($OrderAfterSale)
    {
        $data[]=[
            'type'=>'发起售后',
            'value'=>self::AFTER_SALE_SERVICES[$OrderAfterSale->type],
            'time'=>date('Y-m-d H:i',$OrderAfterSale->create_time),
            'phone'=>'',
            'content'=>'',
            'number'=>'',
            'code'=>self::AFTER_SALE_UN_HANDLE,
            'status'=>'in'
        ];
         return [
            'data'=>$data,
            'platform'=>[]
        ];
    }


    /**
     * @param $OrderAfterSale
     * @return array|int
     */
    public static  function  findHandleAfterSaleDisagree($OrderAfterSale,$role)
    {
        $data[]=[
            'type'=>'发起售后',
            'value'=>self::AFTER_SALE_SERVICES[$OrderAfterSale->type],
            'time'=>date('Y-m-d H:i',$OrderAfterSale->create_time),
            'phone'=>'',
            'content'=>'',
            'number'=>'',
            'code'=>'',
            'status'=>''
        ];
        $data[]=[
            'type'=>'商家反馈',
            'value'=>self::AFTER_SALE_HANDLE[$OrderAfterSale->supplier_handle],
            'time'=>date('Y-m-d H:i',$OrderAfterSale->supplier_handle_time),
            'phone'=>'',
            'content'=>$OrderAfterSale->supplier_handle_reason,
            'number'=>'',
            'code'=>'',
            'status'=>''
        ];
        $OrderGoods=OrderGoods::find()
            ->where(['order_no'=>$OrderAfterSale->order_no,'sku'=>$OrderAfterSale->sku])
            ->one();
        if (!$OrderGoods->customer_service==2)
        {
            $tran = Yii::$app->db->beginTransaction();
            try{
                $OrderGoods->customer_service=2;
                $res=$OrderGoods->save(false);
                if (!$res){
                    $tran->rollBack();
                }
                $tran->commit();
            }catch (\Exception $e){
                $tran->rollBack();
                $code=500;
                return $code;
            }
        }

        $data[]=[
            'type'=>'售后完成',
            'value'=>'',
            'time'=>date('Y-m-d H:i',$OrderAfterSale->supplier_handle_time),
            'phone'=>'',
            'content'=>'',
            'number'=>'',
            'code'=>'',
            'status'=>'over'
        ];
        //通过售后的类型获取是否过该类型下的平台介入
        $handle=OrderPlatForm::GetAfterHandleType($OrderAfterSale->type);
        $PlatForm=OrderPlatForm::find()
            ->where(['order_no'=>$OrderAfterSale->order_no,'sku'=>$OrderAfterSale->sku])
            ->andWhere(['handle'=>$handle])
            ->one();
        if (!$PlatForm){
            return ['data'=>$data,'platform'=>[]];
        }

//        //判断是否是关闭了订单
//        $PlatFormCloseOrder=OrderPlatForm::find()
//            ->where(['order_no'=>$OrderAfterSale->order_no,'sku'=>$OrderAfterSale->sku])
//            ->andWhere(['handle'=>OrderPlatForm::PLATFORM_CLOSE_ORDER])
//            ->one();
//        if ($PlatFormCloseOrder)
//        {
//            $CloseOrderData[]=[
//                'type'=>'关闭订单',
//                'value'=>$PlatFormCloseOrder->reasons,
//                'time'=>date('Y-m-d H:i',$PlatFormCloseOrder->creat_time),
//                'phone'=>'',
//                'content'=>'',
//                'number'=>'',
//                'code'=>'',
//                'status'=>'over'
//            ];
//            return ['data'=>$data,'platform'=>$CloseOrderData];
//        }

        //判断是否是关闭了订单并退款
        $PlatFormRefund=OrderPlatForm::find()
            ->where(['order_no'=>$OrderAfterSale->order_no,'sku'=>$OrderAfterSale->sku])
            ->andWhere(['handle'=>OrderPlatForm::PLATFORM_REFUND])
            ->one();
        if ($PlatFormRefund)
        {
            $RefundData[]=[
                'type'=>'关闭订单,退款',
                'value'=>$PlatFormRefund->reasons,
                'time'=>date('Y-m-d H:i',$PlatFormRefund->creat_time),
                'phone'=>'',
                'content'=>'',
                'number'=>'',
                'code'=>'',
                'status'=>'over'
            ];
            return ['data'=>$data,'platform'=>$RefundData];
        }
        $OrderGoods=OrderGoods::find()
            ->where(['order_no'=>$OrderAfterSale->order_no,'sku'=>$OrderAfterSale->sku])
            ->one();
        if (!$OrderGoods->customer_service==1)
        {
            $tran = Yii::$app->db->beginTransaction();
            try{
                $OrderGoods->customer_service=1;
                $res=$OrderGoods->save(false);
                if (!$res){
                    $tran->rollBack();
                }
                $tran->commit();
            }catch (Exception $e){
                $tran->rollBack();
                $code=500;
                return $code;
            }
        }
        $res=[];
        switch ($PlatForm->handle)
        {
            case 3:
                $res=self::ReturnGoodsHandleDetail($res,$OrderAfterSale,$role,self::AFTER_SALE_HANDLE_DISAGREE);
                break;
            case 4:
                $res=self::ExchangeGoodsHandleDetail($res,$OrderAfterSale,$role,self::AFTER_SALE_HANDLE_DISAGREE);
                break;
            case 5:
                $res=self::RepairGoodsHandleDetail($res,$OrderAfterSale,$role,'repair',self::AFTER_SALE_HANDLE_DISAGREE);
                break;
            case 6:
                $res=self::ToDoorReturnGoodsHandleDetail($res,$OrderAfterSale,$role,self::AFTER_SALE_HANDLE_DISAGREE);
                break;
            case 7:
                $res=self::RepairGoodsHandleDetail($res,$OrderAfterSale,$role,'exchange',self::AFTER_SALE_HANDLE_DISAGREE);
                break;
        }
        return ['data'=>$data,'platform'=>$res];
    }

    /**
     * @param $OrderAfterSale
     * @return array
     */
    public  static  function  CheckIsCloseOrder($OrderAfterSale,$data,$stage)
    {
        $code=1000;
        switch ($stage)
        {
            case self::AFTER_SALE_USER_UN_SHIPPED:
                if (!$OrderAfterSale->buyer_express_id)
                {
                    $code=200;
                }
                break;
            case self::AFTER_SALE_SUPPLIER_UN_CONFIRM_RECEIVED:
                if (!$OrderAfterSale->supplier_express_confirm==1)
                {
                    $code=200;
                }
                break;
            case self::AFTER_SALE_SUPPLIER_UN_SHIPPED:
                if (!$OrderAfterSale->supplier_express_id)
                {
                    $code=200;
                }
                break;
            case self::AFTER_SALE_USER_UN_CONFIRM_RECEIVED:
                if (!$OrderAfterSale->buyer_express_confirm)
                {
                    $code=200;
                }
                break;
            case self::AFTER_SALE_SUPPLIER_UN_SEND:
                if (!$OrderAfterSale->supplier_send_man)
                {
                    $code=200;
                }
                break;
            case self::AFTER_SALE_USER_UN_CONFIRM_EXCHANGE:
                if ($OrderAfterSale->buyer_confirm!=1)
                {
                    $code=200;
                }

                break;
                //此处代码单词写错，不易更改
            case self::AFTER_SALE_SUPPLIER_UN_CONFIRM_RETURN_TO_DOOR:
                if (!$OrderAfterSale->supplier_confirm)
                {
                    $code=200;
                }
                break;
        }
        if ($code==200)
        {
            //判断是否是关闭了订单
            $PlatFormCloseOrder=OrderPlatForm::find()
                ->where(['order_no'=>$OrderAfterSale->order_no,'sku'=>$OrderAfterSale->sku])
                ->andWhere(['handle'=>OrderPlatForm::PLATFORM_CLOSE_ORDER])
                ->one();
            if ($PlatFormCloseOrder)
            {
                $data[]=[
                    'type'=>'关闭订单',
                    'value'=>$PlatFormCloseOrder->reasons,
                    'time'=>date('Y-m-d H:i',$PlatFormCloseOrder->creat_time),
                    'phone'=>'',
                    'content'=>'',
                    'number'=>'',
                    'code'=>'',
                    'status'=>self::AFTER_STATUS_IN
                ];
                $data[]=[
                    'type'=>'售后完成',
                    'value'=>'',
                    'time'=>date('Y-m-d H:i',$PlatFormCloseOrder->creat_time),
                    'phone'=>'',
                    'content'=>'',
                    'number'=>'',
                    'code'=>'',
                    'status'=>'over'
                ];
                return $data;
            }else{
                return 1000;
            }
        }else{
            return $code;
        }

    }

    /**
     * @param $OrderAfterSale
     * @return array
     */
    public  static  function  findHandleAfterSaleAgree($OrderAfterSale,$role)
    {
        $data[]=[
            'type'=>'发起售后',
            'value'=>self::AFTER_SALE_SERVICES[$OrderAfterSale->type],
            'time'=>date('Y-m-d H:i',$OrderAfterSale->create_time),
            'phone'=>'',
            'content'=>'',
            'number'=>'',
            'code'=>'',
            'status'=>''
        ];
        $data[]=[
            'type'=>'商家反馈',
            'value'=>self::AFTER_SALE_HANDLE[$OrderAfterSale->supplier_handle],
            'time'=>date('Y-m-d H:i',$OrderAfterSale->supplier_handle_time),
            'phone'=>'',
            'content'=>$OrderAfterSale->supplier_handle_reason,
            'number'=>'',
            'code'=>'',
            'status'=>''
        ];
        switch ($OrderAfterSale->type){
            case 1:
                $data=self::ReturnGoodsHandleDetail($data,$OrderAfterSale,$role,self::AFTER_SALE_HANDLE_AGREE);
                break;
            case 2:
                $data=self::ExchangeGoodsHandleDetail($data,$OrderAfterSale,$role,self::AFTER_SALE_HANDLE_AGREE);
                break;
            case 3:
                $data=self::RepairGoodsHandleDetail($data,$OrderAfterSale,$role,'repair',self::AFTER_SALE_HANDLE_AGREE);
                break;
            case 4:
                $data=self::RepairGoodsHandleDetail($data,$OrderAfterSale,$role,'exchange',self::AFTER_SALE_HANDLE_AGREE);
                break;
            case 5:
                $data=self::ToDoorReturnGoodsHandleDetail($data,$OrderAfterSale,$role,self::AFTER_SALE_HANDLE_AGREE);
                break;
        }
        return [
            'data'=>$data,
            'platform'=>[],
        ];
    }

    /**
     * @param $data
     * @param $OrderAfterSale
     * @return array|int
     */
    public static  function  ToDoorReturnGoodsHandleDetail($data,$OrderAfterSale,$role,$action)
    {
        if (!$OrderAfterSale->supplier_send_man){


            switch ($role)
            {
                case 'user':
                    $data[]=[
                        'type'=>'等待商家处理',
                        'value'=>'',
                        'time'=>'',
                        'phone'=>'',
                        'content'=>'',
                        'number'=>'',
                        'code'=>'',
                        'status'=>self::AFTER_STATUS_IN
                    ];
                    break;
                case 'supplier':
                    $data[]=[
                        'type'=>'等待商家处理',
                        'value'=>'',
                        'time'=>'',
                        'phone'=>'',
                        'content'=>'',
                        'number'=>'',
                        'code'=>self::AFTER_SALE_SUPPLIER_UN_SEND,
                        'status'=>self::AFTER_STATUS_IN
                    ];
                    break;
            }
            return $data;
        }

        if ($action==self::AFTER_SALE_HANDLE_DISAGREE)
        {
            $code=self::CheckIsCloseOrder($OrderAfterSale,$data,self::AFTER_SALE_SUPPLIER_UN_SEND);
            if (!is_numeric($code))
            {
                $data[]=$code;
                return $data;
            }
        }
        $data[]=[
            'type'=>'商家已派出工作人员',
            'value'=>'',
            'time'=>date('Y-m-d H:i',$OrderAfterSale->supplier_send_time),
            'phone'=>$OrderAfterSale->worker_name.' '.$OrderAfterSale->worker_mobile,
            'content'=>'',
            'number'=>'',
            'code'=>'',
            'status'=>''
        ];
        if (!$OrderAfterSale->supplier_confirm){
            switch ($role)
            {
                case 'user':
                    $data[]=[
                        'type'=>'商家待确认',
                        'value'=>'',
                        'time'=>'',
                        'phone'=>'',
                        'content'=>'',
                        'number'=>'',
                        'code'=>'',
                        'status'=>self::AFTER_STATUS_IN
                    ];
                    break;
                case 'supplier':
                    $data[]=[
                        'type'=>'商家待确认',
                        'value'=>'',
                        'time'=>'',
                        'phone'=>'',
                        'content'=>'',
                        'number'=>'',
                        'code'=>self::AFTER_SALE_SUPPLIER_UN_CONFIRM_RETURN_TO_DOOR,
                        'status'=>'in'
                    ];
                    break;
            }

            return $data;
        }

        if ($action==self::AFTER_SALE_HANDLE_DISAGREE)
        {
            $code=self::CheckIsCloseOrder($OrderAfterSale,$data,self::AFTER_SALE_SUPPLIER_UN_CONFIRM_RETURN_TO_DOOR);
            if (!is_numeric($code))
            {
                $data[]=$code;
                return $data;
            }
        }
        $data[]=[
            'type'=>'商家已确认',
            'value'=>'',
            'time'=>date('Y-m-d H:i',$OrderAfterSale->supplier_confirm_time),
            'phone'=>'',
            'content'=>'',
            'number'=>'',
            'code'=>'',
            'status'=>''
        ];
        $data[]=[
            'type'=>'退款结果',
            'value'=>'成功',
            'time'=>date('Y-m-d H:i',$OrderAfterSale->supplier_confirm_time),
            'phone'=>'',
            'content'=>'已退至顾客钱包',
            'number'=>'',
            'code'=>'',
            'status'=>''
        ];
        $data[]=[
            'type'=>'售后完成',
            'value'=>'',
            'time'=>date('Y-m-d H:i',$OrderAfterSale->supplier_confirm_time),
            'phone'=>'',
            'content'=>'',
            'number'=>'',
            'code'=>'',
            'status'=>'over'
        ];
        return $data;
    }

    /**
     * @param $data
     * @param $OrderAfterSale
     * @return array|int
     */
    public static function RepairGoodsHandleDetail($data,$OrderAfterSale,$role,$type,$action)
    {
        if (!$OrderAfterSale->supplier_send_man){
            switch ($role)
            {
                case 'user':
                    $data[]=[
                        'type'=>'等待商家处理',
                        'value' =>'',
                        'time'=>'',
                        'phone'=>'',
                        'content'=>'',
                        'number'=>'',
                        'code'=>'',
                        'status'=>'in'
                    ];
                    break;
                case 'supplier':
                    $data[]=[
                        'type'=>'等待商家处理',
                        'value' =>'',
                        'time'=>'',
                        'phone'=>'',
                        'content'=>'',
                        'number'=>'',
                        'code'=>self::AFTER_SALE_SUPPLIER_UN_SEND,
                        'status'=>'in'
                    ];
                    break;
            }

            return $data;
        }
        if ($action==self::AFTER_SALE_HANDLE_DISAGREE)
        {
            $code=self::CheckIsCloseOrder($OrderAfterSale,$data,self::AFTER_SALE_SUPPLIER_UN_SEND);
            if (!is_numeric($code))
            {
                $data[]=$code;
                return $data;
            }
        }

        if ($action==self::AFTER_SALE_HANDLE_DISAGREE)
        {
            $code=self::CheckIsCloseOrder($OrderAfterSale,$data,self::AFTER_SALE_SUPPLIER_UN_SEND);
            if (!is_numeric($code))
            {
                $data[]=$code;
                return $data;
            }
        }
        $data[]=[
            'type'=>'商家已派出工作人员',
            'value' =>'',
            'time'=>date('Y-m-d H:i',$OrderAfterSale->supplier_send_time),
            'phone'=>$OrderAfterSale->worker_name.' '.$OrderAfterSale->worker_mobile,
            'content'=>'',
            'number'=>'',
            'code'=>'',
            'status'=>''
        ];
        if ($OrderAfterSale->buyer_confirm!=1){
            switch ($role)
            {
                case 'user':
                    switch ($type)
                    {
                        case 'repair':
                            $data[]=[
                                'type'=>'顾客待确认',
                                'value' =>'',
                                'time'=>'',
                                'phone'=>'',
                                'content'=>'0',
                                'number'=>'',
                                'code'=>self::AFTER_SALE_USER_UN_CONFIRM_REPAIR,
                                'status'=>'in'
                            ];
                            break;
                        case 'exchange':
                            $data[]=[
                                'type'=>'顾客待确认',
                                'value' =>'',
                                'time'=>'',
                                'phone'=>'',
                                'content'=>'',
                                'number'=>'',
                                'code'=>self::AFTER_SALE_USER_UN_CONFIRM_EXCHANGE,
                                'status'=>'in'
                            ];
                            break;
                    }
                    break;
                case 'supplier':
                    //unconfirm ::未确认
                    $data[]=[
                        'type'=>'顾客待确认',
                        'value' =>'',
                        'time'=>'',
                        'phone'=>'',
                        'content'=>'0',
                        'number'=>'',
                        'code'=>self::AFTER_SALE_USER_UN_CONFIRM,
                        'status'=>'in'
                    ];
                    break;
            }
            return $data;
        }
        if ($action==self::AFTER_SALE_HANDLE_DISAGREE)
        {
            $code=self::CheckIsCloseOrder($OrderAfterSale,$data,self::AFTER_SALE_USER_UN_CONFIRM_EXCHANGE);
            if (!is_numeric($code))
            {
                $data[]=$code;
                return $data;
            }
        }
        if ($action==self::AFTER_SALE_HANDLE_DISAGREE)
        {
            $code=self::CheckIsCloseOrder($OrderAfterSale,$data,self::AFTER_SALE_USER_UN_CONFIRM_EXCHANGE);
            if (!is_numeric($code))
            {
                $data[]=$code;
                return $data;
            }
        }
        $data[]=[
            'type'=>'顾客已确认',
            'value' =>'',
            'time'=>date('Y-m-d H:i',$OrderAfterSale->buyer_confirm_time),
            'phone'=>'',
            'content'=>'',
            'number'=>'',
            'code'=>'',
            'status'=>''
        ];

        $OrderGoods=OrderGoods::find()->where(['order_no'=>$OrderAfterSale->order_no,'sku'=>$OrderAfterSale->sku])->one();
        if ($OrderGoods->customer_service!=2){
            $tran = Yii::$app->db->beginTransaction();
            try{

                $OrderGoods->customer_service=2;
                $res=$OrderGoods->save(false);
                if (!$res){
                    $tran->rollBack();
                }
                $tran->commit();
            }catch (Exception $e){
                $tran->rollBack();
                $code=500;
                return $code;
            }
        }
        $data[]=[
            'type'=>'售后完成',
            'value' =>'',
            'time'=>date('Y-m-d H:i',$OrderAfterSale->buyer_confirm_time),
            'phone'=>'',
            'content'=>'',
            'number'=>'',
            'code'=>'',
            'status'=>'over'
        ];
        return $data;
    }

//    public  static function  ReturnGoodsHandleDetailDisAgree($data,$OrderAfterSale,$role)
//    {
//        if (!$OrderAfterSale->buyer_express_id){
//            switch ($role)
//            {
//                case 'user':
//                    $data[]=[
//                        'type'=>'顾客待发货',
//                        'value' =>'',
//                        'time'=>'',
//                        'phone'=>'',
//                        'content'=>'',
//                        'number'=>'',
//                        'code'=>self::AFTER_SALE_USER_UN_SHIPPED,
//                        'status'=>'in'
//                    ];
//                    break;
//                case 'supplier':
//                    $data[]=[
//                        'type'=>'顾客待发货',
//                        'value' =>'',
//                        'time'=>'',
//                        'phone'=>'',
//                        'content'=>'',
//                        'number'=>'',
//                        'code'=>'',
//                        'status'=>'in'
//                    ];
//                    break;
//            }
//            return $data;
//        }
//        $buyer_express=Express::find()
//            ->where(['id'=>$OrderAfterSale->buyer_express_id])
//            ->one();
//        $data[]=[
//            'type'=>'顾客已发货',
//            'value' =>'快递',
//            'time'=>date('Y-m-d H:i',$buyer_express->create_time),
//            'phone'=>'',
//            'content'=>$buyer_express->waybillname,
//            'number'=>$buyer_express->waybillnumber,
//            'code'=>self::AFTER_SALE_SHIPPED,
//            'status'=>''
//        ];
//        $time=15*24*60*60+$buyer_express->create_time-time();
//        if ($time<0){
//            $tran = Yii::$app->db->beginTransaction();
//            try{
//                $OrderAfterSale->supplier_express_confirm=1;
//                $res1=$OrderAfterSale->save(false);
//                $buyer_express->receive_time=time();
//                $res2=$buyer_express->save(false);
//                if (!$res1 || !$res2){
//                    $code=500;
//                    return $code;
//                }
//                $tran->commit();
//            }catch (Exception $e){
//                $tran->rollBack();
//                $code=500;
//                return $code;
//            }
//        }
//        if ($OrderAfterSale->supplier_express_confirm!=1){
//
//            switch ($role)
//            {
//
//                case 'user':
//                    $data[]=[
//                        'type'=>'商家确认收货',
//                        'value' =>'剩余确认时间',
//                        'time'=>date('Y-m-d H:i',$buyer_express->create_time),
//                        'content'=>$time,
//                        'phone'=>'',
//                        'number'=>'',
//                        'code'=>'countdown',
//                        'status'=>'in'
//                    ];
//                    break;
//                case 'supplier':
//                    $data[]=[
//                        'type'=>'商家确认收货',
//                        'value' =>'剩余确认时间',
//                        'time'=>date('Y-m-d H:i',$buyer_express->create_time),
//                        'content'=>$time,
//                        'phone'=>'',
//                        'number'=>'',
//                        'code'=>self::AFTER_SALE_SUPPLIER_UN_CONFIRM_RECEIVED,
//                        'status'=>'in'
//                    ];
//                    break;
//            }
//            return $data;
//        }
//        $data[]=[
//            'type'=>'商家已收货',
//            'value' =>'',
//            'time'=>date('Y-m-d H:i',$buyer_express->receive_time),
//            'content'=>'',
//            'phone'=>'',
//            'number'=>'',
//            'code'=>'',
//            'status'=>''
//        ];
//        $OrderGoods=OrderGoods::find()
//            ->where(['order_no'=>$OrderAfterSale->order_no,'sku'=>$OrderAfterSale->sku])
//            ->one();
//        if ($OrderGoods->customer_service!=2){
//            $tran = Yii::$app->db->beginTransaction();
//            try{
//                $GoodsOrder=GoodsOrder::find()
//                    ->where(['order_no'=>$OrderAfterSale->order_no])
//                    ->one();
//                $user=User::find()
//                    ->where(['id'=>$GoodsOrder->user_id])
//                    ->one();
//                $user->balance=($user->balance+$OrderGoods->goods_price*$OrderGoods->goods_number);
//                $res=$user->save(false);
//                if (!$res){
//                    $tran->rollBack();
//                }
//                $supplier=Supplier::find()
//                    ->where(['id'=>$GoodsOrder->supplier_id])
//                    ->one();
//                $supplier->balance=($supplier->balance-$OrderGoods->supplier_price*$OrderGoods->goods_number);
//                $supplier->availableamount=$supplier->availableamount-$OrderGoods->supplier_price*$OrderGoods->goods_number;
//                $res2=$supplier->save(false);
//                if (!$res2){
//                    $tran->rollBack();
//                }
//                $role_number=$supplier->shop_no;
//                $transaction_no=GoodsOrder::SetTransactionNo($role_number);
//                $supplier_accessdetail=new UserAccessdetail();
//                $supplier_accessdetail->uid=$user->id;
//                $supplier_accessdetail->role_id=6;
//                $supplier_accessdetail->access_type=2;
//                $supplier_accessdetail->access_money=$OrderGoods->supplier_price*$OrderGoods->goods_number;
//                $supplier_accessdetail->order_no=$OrderGoods->order_no;
//                $supplier_accessdetail->sku=$OrderGoods->sku;
//                $supplier_accessdetail->create_time=time();
//                $supplier_accessdetail->transaction_no=$transaction_no;
//                $res3=$supplier_accessdetail->save(false);
//                if (!$res3){
//                    $tran->rollBack();
//                }
//                $OrderGoods->customer_service=2;
//                $res4=$OrderGoods->save(false);
//                if (!$res4){
//                    $tran->rollBack();
//                }
//                $tran->commit();
//            }catch (Exception $e){
//                $tran->rollBack();
//                $code=500;
//                return $code;
//            }
//        }
//        $data[]=[
//            'type'=>'退款结果',
//            'value' =>'成功',
//            'time'=>date('Y-m-d H:i',$buyer_express->receive_time),
//            'phone'=>'',
//            'content'=>'已退至顾客钱包',
//            'number'=>'',
//            'code'=>'',
//            'status'=>''
//        ];
//        $data[]=[
//            'type'=>'售后完成',
//            'value' =>'',
//            'time'=>date('Y-m-d H:i',$buyer_express->receive_time),
//            'phone'=>'',
//            'content'=>'已退至顾客钱包',
//            'number'=>'',
//            'code'=>'',
//            'status'=>'over'
//        ];
//        return $data;
//    }
    /**
     * 同意退货
     * @param $data
     * @param $OrderAfterSale
     * @return array|int
     */
    public  static  function  ReturnGoodsHandleDetail($data,$OrderAfterSale,$role,$action)
    {

        if (!$OrderAfterSale->buyer_express_id){
            switch ($role)
            {
                case 'user':
                    $data[]=[
                        'type'=>'顾客待发货',
                        'value' =>'',
                        'time'=>'',
                        'phone'=>'',
                        'content'=>'',
                        'number'=>'',
                        'code'=>self::AFTER_SALE_USER_UN_SHIPPED,
                        'status'=>self::AFTER_STATUS_IN
                    ];
                    break;
                case 'supplier':
                    $data[]=[
                        'type'=>'顾客待发货',
                        'value' =>'',
                        'time'=>'',
                        'phone'=>'',
                        'content'=>'',
                        'number'=>'',
                        'code'=>'',
                        'status'=>self::AFTER_STATUS_IN
                    ];
                    break;
            }
            return $data;
        }
        if ($action==self::AFTER_SALE_HANDLE_DISAGREE)
        {
            $code=self::CheckIsCloseOrder($OrderAfterSale,$data,self::AFTER_SALE_USER_UN_SHIPPED);
            if (!is_numeric($code))
            {
                $data[]=$code;
                return $data;
            }
        }

        $buyer_express=Express::find()
            ->where(['id'=>$OrderAfterSale->buyer_express_id])
            ->one();
        $data[]=[
            'type'=>'顾客已发货',
            'value' =>'快递',
            'time'=>date('Y-m-d H:i',$buyer_express->create_time),
            'phone'=>'',
            'content'=>$buyer_express->waybillname,
            'number'=>$buyer_express->waybillnumber,
            'code'=>self::AFTER_SALE_SHIPPED,
            'status'=>''
        ];
        $time=15*24*60*60+$buyer_express->create_time-time();
        if ($time<0){
            $code=self::AfterReturnGoodsAction($OrderAfterSale);
            if ($code!=200)
            {
                return $data;
            }
            $OrderAfterSale->supplier_express_confirm=1;
        }
        if ($OrderAfterSale->supplier_express_confirm!=1){

            switch ($role)
            {

                case 'user':
                    $data[]=[
                        'type'=>'商家确认收货',
                        'value' =>'剩余确认时间',
                        'time'=>date('Y-m-d H:i',$buyer_express->create_time),
                        'content'=>$time,
                        'phone'=>'',
                        'number'=>'',
                        'code'=>self::AFTER_SALE_COUNT_DOWN,
                        'status'=>'in'
                    ];
                    break;
                case 'supplier':
                    $data[]=[
                        'type'=>'商家确认收货',
                        'value' =>'剩余确认时间',
                        'time'=>date('Y-m-d H:i',$buyer_express->create_time),
                        'content'=>$time,
                        'phone'=>'',
                        'number'=>'',
                        'code'=>self::AFTER_SALE_SUPPLIER_UN_CONFIRM_RECEIVED,
                        'status'=>'in'
                    ];
                    break;
            }
            return $data;
        }

        if ($action==self::AFTER_SALE_HANDLE_DISAGREE)
        {
            $code=self::CheckIsCloseOrder($OrderAfterSale,$data,self::AFTER_SALE_SUPPLIER_UN_CONFIRM_RECEIVED);
            if (!is_numeric($code))
            {
                $data[]=$code;
                return $data;
            }
        }
        $data[]=[
            'type'=>'商家已收货',
            'value' =>'',
            'time'=>date('Y-m-d H:i',$buyer_express->receive_time),
            'content'=>'',
            'phone'=>'',
            'number'=>'',
            'code'=>'',
            'status'=>''
        ];
        $data[]=[
            'type'=>'退款结果',
            'value' =>'成功',
            'time'=>date('Y-m-d H:i',$buyer_express->receive_time),
            'phone'=>'',
            'content'=>'已退至顾客钱包',
            'number'=>'',
            'code'=>'',
            'status'=>''
        ];
        $data[]=[
            'type'=>'售后完成',
            'value' =>'',
            'time'=>date('Y-m-d H:i',$buyer_express->receive_time),
            'phone'=>'',
            'content'=>'已退至顾客钱包',
            'number'=>'',
            'code'=>'',
            'status'=>'over'
        ];
        return $data;
    }
    /**
     * @param $data
     * @param $OrderAfterSale
     * @return array|int
     */
    public static  function  ExchangeGoodsHandleDetail($data,$OrderAfterSale,$role,$action)
    {
        if (!$OrderAfterSale->buyer_express_id){
            switch ($role)
            {
                case 'user':
                    $data[]=[
                        'type'=>'顾客待发货',
                        'value' =>'',
                        'time'=>'',
                        'phone'=>'',
                        'content'=>'',
                        'number'=>'',
                        'code'=>self::AFTER_SALE_USER_UN_SHIPPED,
                        'status'=>'in'
                    ];
                    break;
                case 'supplier':
                    $data[]=[
                        'type'=>'顾客待发货',
                        'value' =>'',
                        'time'=>'',
                        'phone'=>'',
                        'content'=>'',
                        'number'=>'',
                        'code'=>'',
                        'status'=>'in'
                    ];
                    break;
            }
            return $data;
        }
        if ($action==self::AFTER_SALE_HANDLE_DISAGREE)
        {
            $code=self::CheckIsCloseOrder($OrderAfterSale,$data,self::AFTER_SALE_USER_UN_SHIPPED);
            if (!is_numeric($code))
            {
                $data[]=$code;
                return $data;
            }
        }
        $buyer_express=Express::find()
            ->where(['id'=>$OrderAfterSale->buyer_express_id])
            ->one();
        $data[]=[
            'type'=>'顾客已发货',
            'value'=>'快递',
            'time'=>date('Y-m-d H:i',$buyer_express->create_time),
            'phone'=>'',
            'content' =>$buyer_express->waybillname,
            'number'=>$buyer_express->waybillnumber,
            'code'=>self::AFTER_SALE_SHIPPED,
            'status'=>''
        ];
//            $time=15*24*60*60+$buyer_express->create_time-time();
            $time=2*60+$buyer_express->create_time-time();
            $tran = Yii::$app->db->beginTransaction();
            try{
                if ($time<0){
                    $OrderAfterSale->supplier_express_confirm=1;
                    $res1=$OrderAfterSale->save(false);
                    $buyer_express->receive_time=time();
                    $res2=$buyer_express->save(false);
                    if (!$res1 || !$res2){
                        $code=500;
                        return $code;
                    }
                }
                $tran->commit();
            }catch (Exception $e){
                $tran->rollBack();
                $code=500;
                return $code;
            }

            if ($action==self::AFTER_SALE_HANDLE_DISAGREE)
            {
                $code=self::CheckIsCloseOrder($OrderAfterSale,$data,self::AFTER_SALE_SUPPLIER_UN_CONFIRM_RECEIVED);
                if (!is_numeric($code))
                {
                    $data[]=$code;
                    return $data;
                }
            }

            if (!$OrderAfterSale->supplier_express_confirm){
                $day=floor($time/(24*60*60));
                $hour=floor(($time-$day*(24*60*60))/(60*60));
                $min=floor(($time-$day*(24*60*60)-$hour*3600)/60);
                $s=$time-$day*(24*60*60)-$hour*3600-$min*60;
                switch ($role)
                {
                    case 'user':
                        $data[]=[
                            'type'=>'商家待确认收货',
                            'value' =>'剩余确认时间',
                            'time'=>date('Y-m-d H:i',$buyer_express->create_time),
                            'phone'=>'',
                            'content'=>$time,
                            'number'=>'',
                            'code'=>self::AFTER_SALE_COUNT_DOWN,
                            'status'=>'in'
                        ];
                        break;
                    case 'supplier':
                        $data[]=[
                            'type'=>'商家待确认收货',
                            'value' =>'剩余确认时间',
                            'time'=>date('Y-m-d H:i',$buyer_express->create_time),
                            'phone'=>'',
                            'content'=>$time,
                            'number'=>'',
                            'code'=>self::AFTER_SALE_SUPPLIER_UN_CONFIRM_RECEIVED,
                            'status'=>'in'
                        ];
                        break;
                }

                return $data;
            }
            $data[]=[
                'type'=>'商家已收货',
                'value' =>'',
                'time'=>date('Y-m-d H:i',$buyer_express->receive_time),
                'phone'=>'',
                'content'=>'',
                'number'=>'',
                'code'=>'',
                'status'=>''
            ];
            if(!$OrderAfterSale->supplier_express_id){
                switch ($role)
                {
                    case 'user':
                        $data[]=[
                            'type'=>'商家待发货',
                            'value' =>'',
                            'time'=>'',
                            'phone'=>'',
                            'content'=>'',
                            'number'=>'',
                            'code'=>'',
                            'status'=>'in'
                        ];
                        break;
                    case 'supplier':
                        $data[]=[
                            'type'=>'商家待发货',
                            'value' =>'',
                            'time'=>'',
                            'phone'=>'',
                            'content'=>'',
                            'number'=>'',
                            'code'=>self::AFTER_SALE_SUPPLIER_UN_SHIPPED,
                            'status'=>'in'
                        ];
                        break;
                }
                return $data;
            }

        if ($action==self::AFTER_SALE_HANDLE_DISAGREE)
        {
            $code=self::CheckIsCloseOrder($OrderAfterSale,$data,self::AFTER_SALE_SUPPLIER_UN_SHIPPED);
            if (!is_numeric($code))
            {
                $data[]=$code;
                return $data;
            }
        }
        $supplier_express=Express::find()
            ->where(['id'=>$OrderAfterSale->supplier_express_id])
            ->one();
        $data[]=[
            'type'=>'商家已发货',
            'value' =>'快递',
            'time'=>date('Y-m-d H:i',$supplier_express->create_time),
            'phone'=>'',
            'content'=>$supplier_express->waybillname,
            'number'=>$supplier_express->waybillnumber,
            'code'=>self::AFTER_SALE_SHIPPED,
            'status'=>''
        ];

        $time=15*24*60*60+$supplier_express->create_time-time();
            $tran = Yii::$app->db->beginTransaction();
            try{
                if ($time<0){
                    $OrderAfterSale->buyer_express_confirm=1;
                    $res1=$OrderAfterSale->save(false);
                    $supplier_express->receive_time=time();
                    $res2=$supplier_express->save(false);
                    if (!$res1 || !$res2){
                        $tran->rollBack();
                        $code=500;
                        return $code;
                    }
                }
                $tran->commit();
            }catch (Exception $e){
                $tran->rollBack();
                $code=500;
                return $code;
            }


        if ($action==self::AFTER_SALE_HANDLE_DISAGREE)
        {
            $code=self::CheckIsCloseOrder($OrderAfterSale,$data,self::AFTER_SALE_USER_UN_CONFIRM_RECEIVED);
            if (!is_numeric($code))
            {
                $data[]=$code;
                return $data;
            }
        }
            if (!$OrderAfterSale->buyer_express_confirm){
                switch ($role)
                {
                    case 'user':
                        $data[]=[
                            'type'=>'顾客待确认收货',
                            'value' =>'剩余确认时间',
                            'time'=>date('Y-m-d H:i',$supplier_express->create_time),
                            'phone'=>'',
                            'content'=>$time,
                            'number'=>'',
                            'code'=>self::AFTER_SALE_USER_UN_CONFIRM_RECEIVED,
                            'status'=>'in'
                        ];
                        break;
                    case 'supplier':
                        $data[]=[
                            'type'=>'顾客待确认收货',
                            'value' =>'剩余确认时间',
                            'time'=>date('Y-m-d H:i',$supplier_express->create_time),
                            'phone'=>'',
                            'content'=>$time,
                            'number'=>'',
                            'code'=>self::AFTER_SALE_COUNT_DOWN,
                            'status'=>'in'
                        ];
                        break;
                }
                return $data;
            }
        $data[]=[
            'type'=>'顾客已收货',
            'value' =>'',
            'time'=>date('Y-m-d H:i',$supplier_express->receive_time),
            'phone'=>'',
            'content'=>'',
            'number'=>'',
            'code'=>'',
            'status'=>''
        ];
        $OrderGoods=OrderGoods::find()->where(['order_no'=>$OrderAfterSale->order_no,'sku'=>$OrderAfterSale->sku])->one();
        if ($OrderGoods->customer_service!=2){
            $tran = Yii::$app->db->beginTransaction();
            try{

                $OrderGoods->customer_service=2;
                $res=$OrderGoods->save(false);
                if (!$res){
                    $tran->rollBack();
                }
                $tran->commit();
            }catch (Exception $e){
                $tran->rollBack();
                $code=500;
                return $code;
            }
        }
        $data[]=[
            'type'=>'售后完成',
            'value' =>'',
            'time'=>date('Y-m-d H:i',$supplier_express->receive_time),
            'phone'=>'',
            'content'=>'',
            'number'=>'',
            'code'=>'',
            'status'=>'over'
        ];
        return $data;
    }

    /**
     *
     * @param $OrderAfterSale
     * @return int
     */
    public  static  function  SupplierSendMan($OrderAfterSale,$worker_mobile,$worker_name)
    {
        $tran = Yii::$app->db->beginTransaction();
        try{
            $OrderAfterSale->supplier_send_man=1;
            $OrderAfterSale->worker_name=$worker_name;
            $OrderAfterSale->worker_mobile=$worker_mobile;
            $OrderAfterSale->supplier_send_time=time();
            $res=$OrderAfterSale->save(false);
            if (!$res){
                $tran->rollBack();
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
     * 收货退货操作
     * @param $OrderAfterSale
     * @return int
     */
    public  static  function  AfterReturnGoodsAction($OrderAfterSale)
    {
        $tran = Yii::$app->db->beginTransaction();
        try{

            $OrderAfterSale->supplier_express_confirm=1;
            $express=Express::findOne($OrderAfterSale->buyer_express_id);
            $express->receive_time=time();
            if (!$express->save(false))
            {
                $tran->rollBack();
            }
            $OrderGoods=OrderGoods::find()
                ->where(['order_no'=>$OrderAfterSale->order_no,'sku'=>$OrderAfterSale->sku])
                ->one();
            $GoodsOrder=GoodsOrder::find()
                ->where(['order_no'=>$OrderAfterSale->order_no])
                ->one();
            if (!$GoodsOrder || !$OrderGoods)
            {
                $tran->rollBack();
                $code=1000;
                return $code;
            }
            if ($GoodsOrder->role_id==7) {
                $userRole = User::findOne($GoodsOrder->user_id);
            } else {
                $userRole = Role::CheckUserRole($GoodsOrder->role_id)
                    ->where(['uid' =>$GoodsOrder->user_id])
                    ->one();
            }
            $refund_money=$OrderGoods->supplier_price*$OrderGoods->goods_number;
            $return_money=$OrderGoods->goods_price*$OrderGoods->goods_number;
            $userRole->balance+=$return_money;
            $userRole->availableamount+=$return_money;
            $res=$userRole->save(false);
            if (!$res){
                $tran->rollBack();
            }
            switch ($GoodsOrder->role_id)
            {
                case 2:
                    $user_role_number=$userRole->worker_type_id;
                    break;
                case 3:
                    $user_role_number=$userRole->decoration_company_id;
                    break;
                case 4:
                    $user_role_number=$userRole->decoration_company_id;
                    break;
                case 5:
                    $user_role_number=$userRole->id;
                    break;
                case 6:
                    $user_role_number=$userRole->shop_no;
                    break;
                case 7:
                    $user_role_number=$userRole->aite_cube_no;
                    break;
            }
            $user_transaction_no=GoodsOrder::SetTransactionNo($user_role_number);
            $user_access_detail=new UserAccessdetail();
            if ($GoodsOrder->role_id=7)
            {
                $user_access_detail->uid=$userRole->id;
            }else
            {
                $user_access_detail->uid=$userRole->role_id;
            }
            $user_access_detail->role_id=$GoodsOrder->role_id;
            $user_access_detail->access_type=11;
            $user_access_detail->access_money=$return_money;
            $user_access_detail->order_no=$OrderGoods->order_no;
            $user_access_detail->sku=$OrderGoods->sku;
            $user_access_detail->create_time=time();
            $user_access_detail->transaction_no=$user_transaction_no;
            if (!$user_access_detail->save(false))
            {
                $tran->rollBack();
                $code=1000;
                return $code;
            }
            $supplier=Supplier::find()
                ->where(['id'=>$GoodsOrder->supplier_id])
                ->one();
            $supplier->balance=($supplier->balance-$refund_money);
            $supplier->availableamount=$supplier->availableamount-$refund_money;
            if (!$supplier->save(false)){
                $tran->rollBack();
            }
            $role_number=$supplier->shop_no;
            $transaction_no=GoodsOrder::SetTransactionNo($role_number);
            $supplier_accessdetail=new UserAccessdetail();
            $supplier_accessdetail->uid=$supplier->uid;
            $supplier_accessdetail->role_id=6;
            $supplier_accessdetail->access_type=2;
            $supplier_accessdetail->access_money=$refund_money;
            $supplier_accessdetail->order_no=$OrderGoods->order_no;
            $supplier_accessdetail->sku=$OrderGoods->sku;
            $supplier_accessdetail->create_time=time();
            $supplier_accessdetail->transaction_no=$transaction_no;
            $res3=$supplier_accessdetail->save(false);
            if (!$res3){
                $tran->rollBack();
            }
            $OrderGoods->customer_service=2;
            $res4=$OrderGoods->save(false);
            if (!$res4){
                $tran->rollBack();
            }
            $res=$OrderAfterSale->save(false);
            if (!$res){
                $tran->rollBack();
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (Exception $e){
            $tran->rollBack();
            $code=1000;
            return $code;
        }
    }

    /**
     * 上门退货操作
     * @param $OrderAfterSale
     * @return int
     */
    public  static  function  AfterReturnToDoorGoodsAction($OrderAfterSale)
    {
        $tran = Yii::$app->db->beginTransaction();
        try{
            $OrderAfterSale->supplier_express_confirm=1;
            $OrderGoods=OrderGoods::find()
                ->where(['order_no'=>$OrderAfterSale->order_no,'sku'=>$OrderAfterSale->sku])
                ->one();
            $GoodsOrder=GoodsOrder::find()
                ->where(['order_no'=>$OrderAfterSale->order_no])
                ->one();
            if (!$GoodsOrder || !$OrderGoods)
            {
                $tran->rollBack();
                $code=1000;
                return $code;
            }
            if ($GoodsOrder->role_id==7) {
                $userRole = User::findOne($GoodsOrder->user_id);
            } else {
                $userRole = Role::CheckUserRole($GoodsOrder->role_id)
                    ->where(['uid' =>$GoodsOrder->user_id])
                    ->one();
            }
            $refund_money=$OrderGoods->supplier_price*$OrderGoods->goods_number;
            $return_money=$OrderGoods->goods_price*$OrderGoods->goods_number;
            $userRole->balance+=$return_money;
            $userRole->availableamount+=$return_money;
            $res=$userRole->save(false);
            if (!$res){
                $tran->rollBack();
            }
            switch ($GoodsOrder->role_id)
            {
                case 2:
                    $user_role_number=$userRole->worker_type_id;
                    break;
                case 3:
                    $user_role_number=$userRole->decoration_company_id;
                    break;
                case 4:
                    $user_role_number=$userRole->decoration_company_id;
                    break;
                case 5:
                    $user_role_number=$userRole->id;
                    break;
                case 6:
                    $user_role_number=$userRole->shop_no;
                    break;
                case 7:
                    $user_role_number=$userRole->aite_cube_no;
                    break;
            }
            $user_transaction_no=GoodsOrder::SetTransactionNo($user_role_number);
            $user_access_detail=new UserAccessdetail();
            if ($GoodsOrder->role_id=7)
            {
                $user_access_detail->uid=$userRole->id;
            }else
            {
                $user_access_detail->uid=$userRole->role_id;
            }
            $user_access_detail->role_id=$GoodsOrder->role_id;
            $user_access_detail->access_type=11;
            $user_access_detail->access_money=$return_money;
            $user_access_detail->order_no=$OrderGoods->order_no;
            $user_access_detail->sku=$OrderGoods->sku;
            $user_access_detail->create_time=time();
            $user_access_detail->transaction_no=$user_transaction_no;
            if (!$user_access_detail->save(false))
            {
                $tran->rollBack();
                $code=1000;
                return $code;
            }
            $supplier=Supplier::find()
                ->where(['id'=>$GoodsOrder->supplier_id])
                ->one();
            $supplier->balance=($supplier->balance-$refund_money);
            $supplier->availableamount=$supplier->availableamount-$refund_money;
            if (!$supplier->save(false)){
                $tran->rollBack();
            }
            $role_number=$supplier->shop_no;
            $transaction_no=GoodsOrder::SetTransactionNo($role_number);
            $supplier_accessdetail=new UserAccessdetail();
            $supplier_accessdetail->uid=$supplier->uid;
            $supplier_accessdetail->role_id=6;
            $supplier_accessdetail->access_type=2;
            $supplier_accessdetail->access_money=$refund_money;
            $supplier_accessdetail->order_no=$OrderGoods->order_no;
            $supplier_accessdetail->sku=$OrderGoods->sku;
            $supplier_accessdetail->create_time=time();
            $supplier_accessdetail->transaction_no=$transaction_no;
            $res3=$supplier_accessdetail->save(false);
            if (!$res3){
                $tran->rollBack();
            }
            $OrderGoods->customer_service=2;
            $res4=$OrderGoods->save(false);
            if (!$res4){
                $tran->rollBack();
            }
            $res=$OrderAfterSale->save(false);
            if (!$res){
                $tran->rollBack();
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (Exception $e){
            $tran->rollBack();
            $code=1000;
            return $code;
        }
    }

    /**
     * @param $OrderAfterSale
     * @return int
     */
    public static  function  SupplierConfirm($OrderAfterSale,$type)
    {
        $tran = Yii::$app->db->beginTransaction();
        try{
            if ($type=='received')
            {
                if ($OrderAfterSale->type==1)
                {
                    $code=self::AfterReturnGoodsAction($OrderAfterSale);
                    if ($code!=200)
                    {
                        $tran->rollBack();
                        $code=1000;
                        return $code;
                    }
                }
                if ($OrderAfterSale->type==2)
                {
                    $OrderAfterSale->supplier_express_confirm=1;
                    $express=Express::findOne($OrderAfterSale->buyer_express_id);
                    $express->receive_time=time();
                    if (!$express->save(false))
                    {
                        $tran->rollBack();
                    }
                }
            }else{
                $OrderAfterSale->supplier_confirm=1;
                $OrderAfterSale->supplier_confirm_time=time();
                if ($OrderAfterSale->type==5)
                {
                    $code=self::AfterReturnToDoorGoodsAction($OrderAfterSale);
                    if ($code!=200)
                    {
                        $tran->rollBack();
                        $code=1000;
                        return $code;
                    }
                }
            }
            $res=$OrderAfterSale->save(false);
            if (!$res){
                $tran->rollBack();
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (Exception $e){
            $tran->rollBack();
            $code=1000;
            return $code;
        }
    }
    /**
     * 用户确认售后
     * @param $OrderAfterSale
     * @return int
     */
    public static  function  userConfirm($OrderAfterSale,$type)
    {
        $tran = Yii::$app->db->beginTransaction();
        try{
            if ($type=='received')
            {
                $OrderAfterSale->buyer_express_confirm=1;
                $express=Express::findOne($OrderAfterSale->supplier_express_id);
                $express->receive_time=time();
                if (!$express->save(false))
                {
                    $tran->rollBack();
                }
            }else{
                $OrderAfterSale->buyer_confirm=1;
                $OrderAfterSale->worker_name='';
                $OrderAfterSale->worker_mobile='';
                $OrderAfterSale->buyer_confirm_time=time();
            }

            $res=$OrderAfterSale->save(false);
            if (!$res){
                $tran->rollBack();
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
     * 关闭订单操作
     * @param $order_no
     * @param $sku
     * @param $reason
     * @return int
     */
    public  static  function  CloseOrder($order_no,$sku,$reason)
    {
        //关闭订单操作
        $OrderGoods=OrderGoods::FindByOrderNoAndSku($order_no,$sku);

        $tran = Yii::$app->db->beginTransaction();
        try{

            switch ($OrderGoods->order_status)
            {
                case 0:
                    $OrderGoods->order_status=2;
                    if (!$OrderGoods->save(false)){
                        $tran->rollBack();
                    }
                    break;
                case 1:
                    $OrderGoods->customer_service=2;
                    if (!$OrderGoods->save(false)){
                        $tran->rollBack();
                    }
                    break;
                case 2:
                    $tran->rollBack();
                    return 1000;
                    break;
            }
            $orderPlatForm=OrderPlatForm::find()
                ->where(['order_no'=>$order_no])
                ->andWhere(['sku'=>$sku])
                ->andWhere(['handle'=>OrderPlatForm::PLATFORM_CLOSE_ORDER])
                ->one();
            if ($orderPlatForm)
            {
                $tran->rollBack();
                $code=1000;
                return $code;
            }

            $time=time();
            $OrderPlatForm=new OrderPlatForm;
            $OrderPlatForm->order_no=$order_no;
            $OrderPlatForm->sku=$sku;
            $OrderPlatForm->handle=OrderPlatForm::PLATFORM_CLOSE_ORDER;
            $OrderPlatForm->reasons=$reason;
            $OrderPlatForm->creat_time=$time;
            $OrderPlatForm->refund_result=2;
            $OrderPlatForm->refund_time=$time;
            $res=$OrderPlatForm->save(false);
            if (!$res){
                $code=1000;
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
}
