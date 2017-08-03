<?php


namespace app\models;
use yii;
use yii\db\ActiveRecord;
use yii\data\Pagination;
use app\models\LogisticsDistrict;
use app\services\StringService;
use app\services\SmValidationService;

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
            return false;
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
            $output['balance']=$data[$k]['freeze_money']*0.01;
            $output['availableamount']=$data[$k]['availableamount']*0.01;
        }
        $user = Yii::$app->user->identity;
        $cashregister=(new \yii\db\Query())->from(SUP_CASHREGISTER)->select('cash_money')->where(['supplier_id'=>$supplier_id,'status'=>3])->all();
        $cash_money=0;
        foreach ($cashregister AS $k=>$v){
            $cash_money+=$cashregister[$k]['cash_money'];
        }
        $output['cash_money']=$cash_money*0.01;
        $output['freeze_money']=$a*0.01;
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
     * 商家重设支付密码-不是第一次
     * 1:成功  2：用户不存在 3：失败 4：短信验证失败
     * @param $psw
     * @param $supplier_id
     * @param $code
     * @return int
     */
    public function  Setpaypsw_sup_reset($psw,$supplier_id,$code){
        $user = Yii::$app->user->identity;
        if (!$user){
            return 2;
        }
        $mobile=user::find()->select('mobile')->where(['id'=>$user->id])->one()['mobile'];
        //验证短信验证码是否正确
        $re=SmValidationService::validCode($mobile,$code);
        if ($re==true){
            $res=Yii::$app->db->createCommand()->update(SUPPLIER, ['pay_password' =>$psw],'id='.$supplier_id)->execute();
            if($res){
                return 1;
            }else{
                return 3;
            }
        }else{
            return 4;
        }
    }

    /**
     * 商家提现申请
     * @param $money
     * @return bool
     */
    public function Supplierwithdrawalsapply($money){
        $user = Yii::$app->user->identity;
        $supplier=Supplier::find()->select('id,shop_no,availableamount')->where(['uid' => $user->id])->one();
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
        if ($availableamount<0){
            return false;
        }
        $re=Yii::$app->db->createCommand()->update(SUPPLIER, ['availableamount'=>$availableamount],'id='.$supplier_id)->execute();
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
    public function Getfreezelist($supplier_id,$page,$pagesize,$time_id,$time_start,$time_end){
        if ($time_id==0){
            $array=(new \yii\db\Query())->from(SUP_FREELIST)->where(['supplier_id'=>$supplier_id]);
        }else{
            $array=(new \yii\db\Query())->from(SUP_FREELIST)->where(['supplier_id'=>$supplier_id])->andwhere($this->Timehandle($time_id,$time_start,$time_end));
        }
        $count = $array->count();
        $pagination = new Pagination(['totalCount' =>$count,'pageSize' => $pagesize,'pageSizeParam'=>false]);
        $arr=$array->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        foreach ($arr as $k=>$v){
            $arr[$k]['create_time']=date('Y-m-d H:i',$arr[$k]['create_time']);
        }
        $data=$this->page($count,$pagesize,$page,$arr);
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

    private  function Timehandle($time_id,$time_start,$time_end){
        if ($time_id==0){
            return null;
        }else if ($time_id==1){
            $data='DATE(FROM_UNIXTIME(create_time))=CURDATE()';
            return $data;
        }else if ($time_id==2){
            $data="DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= DATE(FROM_UNIXTIME(create_time))";
            return $data;
        }else if ($time_id==3){
            $data="DATE_SUB(CURDATE(), INTERVAL 1 MONTH) <= DATE(FROM_UNIXTIME(create_time))";
            return $data;
        }else if($time_id==4){
            $data="DATE_SUB(CURDATE(), INTERVAL 365 DAY) <= DATE(FROM_UNIXTIME(create_time))";
            return $data;
        }else if ($time_id==5){
            $data="create_time>='".strtotime($time_start)."' and create_time<= '".strtotime($time_end)."'";
            return $data;
        }
    }
    private  function page($count,$pagesize,$page,$arr){
        $totalpage=ceil($count/$pagesize);
        if ($page>$totalpage){
            $sd= array(
                'freezelist'=>'',
                'totalpage'=>$totalpage,
                'count'=>$count,
                'page'=>$page
            );
        }else{
            $sd=array(
                'freezelist'=>$arr,
                'totalpage'=>$totalpage,
                'count'=>$count,
                'page'=>$page
            );
        }
        return $sd;
    }


}