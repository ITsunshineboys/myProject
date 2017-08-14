<?php


namespace app\models;
use yii;
use yii\db\ActiveRecord;
use yii\data\Pagination;
use app\models\LogisticsDistrict;
use app\services\StringService;
use app\services\SmValidationService;
use yii\db\Query;

const  SUP_BANK_CARD='supplier_bankinformation';
const  SUPPLIER='supplier';
const  SUP_FREELIST='supplier_freezelist';
const  SUP_CASHREGISTER='supplier_cashregister';

class Supplieramountmanage extends  ActiveRecord
{


    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'supplier_bankinformation';
    }

    /**
     * 添加银行卡-商家
     * @param $supplier_id
     * @param $bankname
     * @param $bankcard
     * @param $username
     * @param $position
     * @param $bankbranch
     * @return bool|int
     */
    public function Bankcardadd($supplier_id,$bankname,$bankcard,$username,$position,$bankbranch){
        //查询
        $data=$this->GetBankcard($supplier_id);
        if ($data){
            $res=Yii::$app->db->createCommand()->update(SUP_BANK_CARD, ['bankname'   =>$bankname, 'bankcard'   =>$bankcard, 'username'   =>$username, 'position'   =>$position, 'bankbranch' =>$bankbranch],'supplier_id='.$supplier_id)->execute();
            return $res;
        }else{
            $time=time();
            $res=Yii::$app->db->createCommand()->insert(SUP_BANK_CARD,[
                'supplier_id'=>$supplier_id,
                'bankname'   =>$bankname,
                'bankcard'   =>$bankcard,
                'username'   =>$username,
                'position'   =>$position,
                'bankbranch' =>$bankbranch,
                'create_time'=>$time
            ])->execute();
            return $res;
        }
    }

    /**
     * 查询银行卡
     * @param $supplier_id
     * @return array|null
     */
    public function Getbankcardinformation($supplier_id){
        $data=$this->GetBankcard($supplier_id);
        if ($data){
            return $data;
        }else{
            return null;
        }
    }

    /**
     * 修改银行卡信息
     * @param $supplier_id
     * @param $bankname
     * @param $bankcard
     * @param $username
     * @param $position
     * @param $bankbranch
     * @return bool|int
     */
    public function Bankcardupdate($supplier_id,$bankname,$bankcard,$username,$position,$bankbranch)
    {
        //查询
        $data=$this->GetBankcard($supplier_id);
        if ($data){
            $res=Yii::$app->db->createCommand()->update(SUP_BANK_CARD, ['bankname'   =>$bankname, 'bankcard'   =>$bankcard, 'username'   =>$username, 'position'   =>$position, 'bankbranch' =>$bankbranch],'supplier_id='.$supplier_id)->execute();
            return $res;
        }else{
            return false;
        }
    }

    /**
     * 商家后台，获取商家余额，可用余额
     * @param $supplier_id
     * @return array|null
     */
    public function Getbalance($supplier_id){
        $data=(new \yii\db\Query())->from(SUPPLIER.' as a')->leftJoin(SUP_FREELIST.' as b','a.id=b.supplier_id')->select('a.type_shop,a.status,a.shop_no,a.balance,a.availableamount,b.freeze_money')->where(['a.id'=>$supplier_id])->all();
        if (!$data){
            return null;
        }
        $output=array();
        $a=0;
        foreach($data AS $k=>$v){
            $a+= $data[$k]['freeze_money'];
            $output['balance']= sprintf('%.2f', (float)$data[$k]['freeze_money']*0.01);

            $output['availableamount']= sprintf('%.2f', (float)$data[$k]['availableamount']*0.01);
        }
        $user = Yii::$app->user->identity;
        $cashregister=(new \yii\db\Query())->from(SUP_CASHREGISTER)->select('cash_money')->where(['supplier_id'=>$supplier_id,'status'=>3])->all();
        $cash_money=0;
        foreach ($cashregister AS $k=>$v){
            $cash_money+=$cashregister[$k]['cash_money'];
        }
        $output['cash_money']=sprintf('%.2f', (float)$cash_money*0.01);
        $output['freeze_money']=sprintf('%.2f', (float)$a*0.01);
        return $output;
    }


    /**
     * 验证是否设置支付密码
     * @param $supplier_id
     * @return int
     */
    public function Issetpaypsw($supplier_id){
        $data=(new \yii\db\Query())->from(SUPPLIER)->select('pay_password')->where(['id'=>$supplier_id])->one();
        if ($data['pay_password']){
            return 1;
        }else{
            return 2;
        }
    }


    /**
     * @param $psw
     * @param $supplier_id
     * @return bool
     */
    public function  Setpaypsw($psw,$supplier_id){

        $res=Yii::$app->db->createCommand()->update(SUPPLIER, ['pay_password' =>$psw],'id='.$supplier_id)->execute();
        if($res){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 商家提现申请
     * @param $money
     * @return bool
     */
    public function Supplierwithdrawalsapply($money){
        $user = Yii::$app->user->identity;
        $supplier=Supplier::find()->select('id,shop_no,availableamount,balance')->where(['uid' => $user->id])->one();
        $supplier_id=$supplier['id'];
        $time=time();
        $month=date('m',$time);
        $day=date('d',$time);
        $rand=rand(10000,99999);
        $transaction_no=$month.$day.$supplier['shop_no'].$rand;
        $res=Yii::$app->db->createCommand()->insert(SUP_CASHREGISTER,[
            'supplier_id'=>$supplier_id,
            'cash_money'=>$money*100,
            'status'    =>1,
            'transaction_no'=>$transaction_no,
            'apply_time'=>$time
        ])->execute();
        $availableamount=$supplier['availableamount']-$money*100;
        $balance=$supplier['balance']-$money*100;
        if ($availableamount<0){
            return false;
        }
        $re=Yii::$app->db->createCommand()->update(SUPPLIER, ['availableamount'=>$availableamount,'balance'=>$balance],'id='.$supplier_id)->execute();
        if ($res && $re){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 查询商家已冻结余额
     * @param $supplier_id
     * @return array|bool|null
     */
    public function Getfreezelist($supplier_id,$page,$page_size,$time_type,$time_start,$time_end){
        $array=(new Query())->from(SUP_FREELIST)->where(['supplier_id'=>$supplier_id]);
        $time_area = self::timeDeal($time_type, $time_start, $time_end);
        $time_start = $time_area[0];
        $time_end = $time_area[1];
        if ($time_start && $time_end && $time_end > $time_start) {
            $array->andWhere(['>', 'create_time', $time_start])
                ->andWhere(['<', 'create_time', $time_end]);
        }
        $count = $array->count();
        $pagination = new Pagination(['totalCount' =>$count,'pageSize' => $page_size,'pageSizeParam'=>false]);
        $arr=$array->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        foreach ($arr as $k=>$v){
            $arr[$k]['create_time']=date('Y-m-d H:i',$arr[$k]['create_time']);
            $arr[$k]['freeze_money']=sprintf('%.2f', (float)$arr[$k]['freeze_money']*0.01);
        }
        $data=$this->page($count,$page_size,$page,$arr);
        return $data;
    }

    /**
     * 收支明细列表
     * @param $supplier_id
     * @param $page
     * @param $page_size
     * @param $time_type
     * @param $time_start
     * @param $time_end
     * @param $access_type
     * @param $search
     * @param $order_type
     * @return array
     */
    public function Accessdetails($supplier_id,$page,$page_size,$time_type,$time_start,$time_end,$access_type,$search,$order_type){

        $array=(new Query())->from('supplier_accessdetail')->where(['supplier_id'=>$supplier_id]);
        $time_area = self::timeDeal($time_type, $time_start, $time_end);
        $time_start = $time_area[0];
        $time_end = $time_area[1];
        if ($time_start && $time_end && $time_end > $time_start) {
            $array->andWhere(['>', 'create_time', $time_start])
                ->andWhere(['<', 'create_time', $time_end]);
        }
        if ($search) {
            $array->andFilterWhere(['like', 'order_no', $search])
                ->orFilterWhere(['like', 'transaction_no', $search]);
        }
        if ($order_type==2){
            $orderby='create_time desc';
        }else {
            $orderby='create_time asc';
        }
        if ($access_type!=0 && $access_type){
            $array->andWhere(['=', 'access_type',$access_type]);
        }
        $count = $array->count();
        $pagination = new Pagination(['totalCount' =>$count,'pageSize' => $page_size,'pageSizeParam'=>false]);
        $arr=$array->orderby($orderby)->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        foreach ($arr as $k=>$v){
            $arr[$k]['create_time']=date('Y-m-d H:i',$arr[$k]['create_time']);
            $arr[$k]['access_money']=sprintf('%.2f', (float)$arr[$k]['access_money']*0.01);
            switch ($arr[$k]['access_type']){
                case 1:
                    $arr[$k]['access_type']='货款';
                    break;
                case 2:
                    $arr[$k]['access_type']='提现失败';
                    break;
                case 3:
                    $arr[$k]['access_type']='充值';
                    break;
                case 4:
                    $arr[$k]['access_type']='扣款';
                    break;
            }
        }
        $data=$this->page($count,$page_size,$page,$arr);
        return $data;

    }

     public function Accessinformation($transaction_no){
         $arr=(new Query())->from('supplier_accessdetail')->where(['transaction_no'=>$transaction_no])->one();
         switch ($arr['access_type']){
             case 1:
                 $getorderlist  = (new Query())->from('goods_order AS a')->leftJoin('order_goodslist AS b','b.order_no = a.order_no')->leftJoin('user_address as c','a.address_id=c.id')->leftJoin('express as d','d.order_no=a.order_no')->where(['a.order_no'=> $arr['order_no']])->select('b.goods_price,b.goods_name,b.sku,b.goods_number,a.pay_name,b.shipping_type,c.district,c.region,a.create_time,a.paytime,d.waybillname,d.waybillnumber')->all();
                 foreach ($getorderlist as $k =>$v){
                     $getorderlist[$k]['goods_price']=sprintf('%.2f', (float)$getorderlist[$k]['goods_price']*0.01);
                     $getorderlist[$k]['create_time']=date('Y-m-d H:i',$getorderlist[$k]['create_time']);
                     $getorderlist[$k]['paytime']=date('Y-m-d H:i',$getorderlist[$k]['paytime']);
                     if ($getorderlist[$k]['shipping_type']==1){
                         $getorderlist[$k]['shipping_way']='送货上门';
                     }else{
                         $getorderlist[$k]['shipping_way']=$getorderlist[$k]['waybillname'].'('.$getorderlist[$k]['number'].')';
                     }
                     $getorderlist[$k]['get_address']=(new LogisticsDistrict())->getdistrict($getorderlist[$k]['district']).$getorderlist[$k]['region'];
                     unset($getorderlist[$k]['shipping_type']);
                     unset($getorderlist[$k]['district']);
                     unset($getorderlist[$k]['region']);

                 }
                 $data=$getorderlist;
                 break;
             case 2:
                 $getcashlist  = (new Query())->from('supplier_cashregister AS a')->leftJoin('supplier_bankinformation as b','a.supplier_id=b.supplier_id')->select('a.cash_money,a.apply_time,a.status,a.handle_time,a.real_money,a.supplier_reason,b.bankcard')->where(['a.transaction_no'=>$arr['transaction_no']])->one();
                 foreach ($getcashlist  as $k =>$v){
                     $getcashlist[$k]['apply_time']=date('Y-m-d H:i',$getcashlist[$k]['apply_time']);
                     $getcashlist[$k]['handle_time']=date('Y-m-d H:i',$getcashlist[$k]['handle_time']);
                     $getcashlist[$k]['cash_money']=sprintf('%.2f', (float)$getcashlist[$k]['cash_money']*0.01);
                     $getcashlist[$k]['real_money']=sprintf('%.2f', (float)$getcashlist[$k]['real_money']*0.01);
                     $getcashlist[$k]['status']='提现失败';
                 }
                 $data=$getcashlist;
                 break;
             case 3:
                 $data=array();
                 break;
             case 4:
                 $data=array();
                 break;
         }
         return $data;
     }
    /**
     * 查询银行卡信息
     * @param $supplier_id
     * @return array
     */
    private function GetBankcard($supplier_id){
        $data=(new \yii\db\Query())->from(SUP_BANK_CARD)->where(['supplier_id'=>$supplier_id])->one();
        return $data;
    }

    private  function page($count,$pagesize,$page,$arr){
        $totalpage=ceil($count/$pagesize);
            if ($page>$totalpage){
                $sd= array(
                    'list'=>'',
                    'totalpage'=>$totalpage,
                    'count'=>$count,
                    'page'=>$page
            );
        }else{
            $sd=array(
                'list'=>$arr,
                'totalpage'=>$totalpage,
                'count'=>$count,
                'page'=>$page
            );
        }
        return $sd;
    }
    /**
     * 得到开始和结束时间
     * @param $time_type
     * @param $time_start
     * @param $time_end
     * @return array
     */
    private function timeDeal($time_type, $time_start, $time_end)
    {
        if ($time_type == 'custom' && $time_start && $time_end) {
            $time_start = strtotime($time_start);
            $time_end = strtotime($time_end);
        } else {
            $time_area = StringService::startEndDate($time_type, 1);
            $time_start = $time_area[0];
            $time_end = $time_area[1];
        }
        return [$time_start, $time_end];
    }
}