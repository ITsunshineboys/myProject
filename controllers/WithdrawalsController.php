<?php

namespace app\controllers;
use app\models\Alipay; 
use app\models\BankBranch;
use app\models\BankinfoLog;
use app\models\BankName;
use app\models\Goods;
use app\models\GoodsOrder;
use app\models\OrderGoods;
use app\models\OrderPlatForm;
use app\models\Role;
use app\models\Supplier;
use app\models\SupplierCashregister;
use app\models\User;
use app\models\Worker;
use app\models\UserAccessdetail;
use app\models\UserCashregister;
use app\models\UserBankInfo;
use app\models\UserFreezelist;
use app\models\UserRole;
use app\services\ModelService;
use app\services\SmValidationService;
use app\services\ExceptionHandleService;
use app\services\StringService;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\db\Exception;
use yii\web\ServerErrorHttpException;
use app\models\Wxpay;
use app\services\AuthService;
use Yii;
class WithdrawalsController extends Controller
{


    /**
     * Actions accessed by logged-in users
     */
    const ACCESS_LOGGED_IN_USER = [
        'find-balance',
        'check-isset-pay-pwd',
        'send-pay-code',
        'set-pay-pwd',
        'find-supplier-balance',
        'supplier-withdrawals-apply',
        'find-supplier-freeze-list',
        'supplier-access-detail',
        'find-supplier-access-detail-list',
        'check-cash-money',
        'check-supplier-pay-pwd',
        'set-bank-card',
        'find-bank-card',
        'find-balance',
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AuthService::className(),
                'denyCallback' => function ($rule, $action) {
                    $code = 403;
                    new ExceptionHandleService($code);
                    exit;
                },
                'only' => self::ACCESS_LOGGED_IN_USER,
                'rules' => [
                    [
                        'actions' => self::ACCESS_LOGGED_IN_USER,
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                ],
            ],
        ];
    }
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * 添加/修改银行卡信息
     * @return string
     */
    public function  actionSetBankCard()
    {
        $user= Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=Yii::$app->request;
        $bankname=trim($request->post('bankname',''));
        $bankcard=(int)trim($request->post('bankcard'));
        $username=trim($request->post('username',''));
        $position=trim($request->post('position',''));
        $bankbranch=trim($request->post('bankbranch',''));
        $role_id=trim($request->post('role_id',''));
        if (!$bankname || !$bankcard || !$username || !$position || !$bankbranch  ){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (!$role_id)
        {
            $role_id=$user->last_role_id_app;
        }
        $code=UserBankInfo::SetBankCard($bankname,$bankcard,$username,$position,$bankbranch,$role_id,$user);
        if ($code==200)
        {
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        }else
        {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }


   /**
     * 移动端查询银行卡信息
     * @return string
     */
   public function actionUserFindBankCard(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $role_id=$user->last_role_id_app;
        $role=Role::GetRoleByRoleId($role_id,$user);
        if (!$role)
        {
            $code=1010;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $UserBankInfo=(new Query())
            ->from(UserBankInfo::tableName().' as u')
            ->leftJoin(BankinfoLog::tableName().' as b','u.log_id=b.id')
            ->select('u.id,u.uid,u.role_id,u.selected,u.log_id,b.bankname,b.bankcard,b.bank_type,b.username,b.position,b.bankbranch,b.create_time')
            ->where(['u.uid'=>$user->id,'u.role_id'=>$role_id])
            ->all();

        if ($UserBankInfo)
        {
            foreach ($UserBankInfo as &$list)
            {
                $list['create_time']=date('Y-m-d H:i',$list['create_time']);
            }
            $data=$UserBankInfo;
        }else{
            $data=[];
        }
        $is_real=0;
        if ($user->legal_person)
        {
            $is_real=1;
        }
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>[
                'is_real'=>$is_real,
                'balance'=>GoodsOrder::switchMoney($role->availableamount*0.01),
                'list'=>$data
            ]
        ]);
    }

     /**
     * 商家后台-查询银行卡信息
     * @return string
     */
    public function actionFindBankCard(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $role_id=trim(Yii::$app->request->get('role_id',7));
//        $UserBankInfo=UserBankInfo::findByUidAndRole_id($user->id,$role_id);
        $UserBankInfo=(new Query())
            ->from(UserBankInfo::tableName().' as u')
            ->leftJoin(BankinfoLog::tableName().' as b','u.log_id=b.id')
            ->where(['u.uid'=>$user->id,'u.role_id'=>$role_id,'u.selected'=>1])
            ->one();
        if ($UserBankInfo)
        {
            $data=$UserBankInfo;
            $data['create_time']=date('Y-m-d H:i',$data['create_time']);
        }else{
            $data=[];
        }
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$data
        ]);
    }

    /**
     * 获取余额
     * @return string
    */
    public function actionFindBalance(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
//        $role_id=trim(Yii::$app->request->get('role_id',7));
        $role_id=$user->last_role_id_app;
        $data=UserBankInfo::findCapitalDetail($role_id,$user);
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$data
        ]);
    }

    /**
     * 判断是否设置过交易密码
     * @return string
     */
    public function actionCheckIssetPayPwd()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier=Supplier::find()
            ->select('pay_password')
            ->where(['uid'=>$user->id])
            ->one();
        if (!$supplier)
        {
            $code=1010;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $payPassword=$supplier->pay_password;
        $data['type']=$payPassword?'unfirst':'first';
        $data['key']=$payPassword? \Yii::$app->getSecurity()->generatePasswordHash(User::UNFIRST_SET_PAYPASSWORD.$user->id.date('Y-m-d',time())):\Yii::$app->getSecurity()->generatePasswordHash(User::FIRST_SET_PAYPASSWORD.$user->id.date('Y-m-d',time()));
         $data['mobile']=$user->mobile;
         $code=200;
         return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$data
         ]);
    }

    /**
     * 发送短信验证-商家后台重设支付密码
     * @return string
     */
    public function actionSendPayCode(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=[];
        $data['mobile']=$user->mobile;
        $data['type']='resetPayPassword';
        try {
            new SmValidationService($data);
        } catch (\InvalidArgumentException $e) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        } catch (ServerErrorHttpException $e) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }catch (\Exception $e) {
            $code = 1020;
            if ($code == $e->getCode()) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        $code=200;
        return Json::encode(
            [
                'code'=>$code,
                'msg'=>'ok'
            ]
        );
    }

    /**
     * 设置支付密码
     * @return string
     */
    public function  actionSetPayPwd(){
        $request=Yii::$app->request;
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier= Supplier::find()
            ->where(['uid'=>$user->id])
            ->one();
        if (!$supplier){
            $code=1034;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $key=trim($request->post('key',''));
        if (!$key){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=1000;
        if (Yii::$app->getSecurity()->validatePassword(User::FIRST_SET_PAYPASSWORD.$user->id.date('Y-m-d',time()), $key)==true){
            $pay_pwd_first=trim($request->post('pay_pwd_first',''));
            $pay_pwd_secend=trim($request->post('pay_pwd_secend',''));
            if (!User::CheckPaypwdFormat($pay_pwd_first) ||
                !User::CheckPaypwdFormat($pay_pwd_secend)){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            if ($pay_pwd_first !=  $pay_pwd_secend){
                $code=1053;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $tran = Yii::$app->db->beginTransaction();
            try{
                $psw = Yii::$app->getSecurity()->generatePasswordHash($pay_pwd_secend);
                $supplier->pay_password=$psw;
                $res=$supplier->save(false);
                if (!$res)
                {
                    $tran->rollBack();
                    $code=500;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $code=200;
                $tran->commit();
                return Json::encode([
                    'code' => $code,
                    'msg' => 'ok'
                ]);
            }catch (Exception $e){
                $tran->rollBack();
                $code=500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        if (Yii::$app->getSecurity()->validatePassword(User::UNFIRST_SET_PAYPASSWORD.$user->id.date('Y-m-d',time()), $key)==true)
        {
            $sms_code=trim($request->post('sms_code',''));
            $pay_pwd=trim($request->post('pay_pwd',''));
            $codeValidationRes = SmValidationService::validCode($user->mobile,$sms_code);
            if ($codeValidationRes !== true) {
                $code = is_int($codeValidationRes) ? $codeValidationRes : 1002;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            SmValidationService::deleteCode($user->mobile);
            if (!User::CheckPaypwdFormat($pay_pwd))
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $tran = Yii::$app->db->beginTransaction();
            try{
                $psw = Yii::$app->getSecurity()->generatePasswordHash($pay_pwd);
                $supplier->pay_password=$psw;
                $res=$supplier->save(false);
                if (!$res)
                {
                    $tran->rollBack();
                    $code=500;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $code=200;
                $tran->commit();
                return Json::encode([
                    'code' => $code,
                    'msg' => 'ok'
                ]);
            }catch (Exception $e){
                $tran->rollBack();
                $code=500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
    }



    /**
     * 获取可用余额-商家后台
     * @return string
     */
     public function actionFindSupplierBalance(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $m = Supplier::find()
            ->select('availableamount')
            ->where(['uid' => $user->id])
            ->one();
        if (!$m)
        {
            $code=1034;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }

        if (!$m->availableamount)
        {
            $money=0.00;
        }else{
            $money=sprintf('%.2f', (float)$m->availableamount*0.01);
        }
        if ($m->availableamount<0){
            $money=0.00;
        }
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$money
        ]);
    }

   /**
     * 商家提现申请
     * @return string
     */
    public function actionSupplierWithdrawalsApply(){
        $request=Yii::$app->request;
        $money=trim($request->post('money',''));
        $pay_password=trim($request->post('pay_pwd',''));
        if (!$money ||!$pay_password){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier=Supplier::find()
            ->where(['uid' => $user->id])
            ->one();
        if (!$supplier){
            $code=1034;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $availableamount =$supplier->availableamount;
        $pwd=$supplier->pay_password;
        if ($money*100>$availableamount){
            $code=1054;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (Yii::$app->getSecurity()->validatePassword($pay_password, $pwd)!=true){
            $code=1055;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $userBankInfo=UserBankInfo::find()
            ->where(['uid'=>$user->id,'role_id'=>6])
            ->andWhere(['selected'=>1])
            ->one();
        if (!$userBankInfo)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => '你尚未绑定银行卡'
            ]);
        }
        $role_number=$supplier->shop_no;
        $transaction_no=GoodsOrder::SetTransactionNo($role_number);
        $time=time();
        $tran = Yii::$app->db->beginTransaction();
        try{
            $UserCashRegister= new UserCashregister();
            $UserCashRegister->uid=$user->id;
            $UserCashRegister->role_id=6;
            $UserCashRegister->cash_money=$money*100;
            $UserCashRegister->apply_time=$time;
            $UserCashRegister->status=1; //1:提现中  2.已提现  3.驳回
            $UserCashRegister->transaction_no=$transaction_no;
            $UserCashRegister->bank_log_id=$userBankInfo->log_id;
            $res1=$UserCashRegister->save(false);
            if (!$res1)
            {
                $code=500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $supplier->availableamount=$supplier->availableamount-$money*100;
            $supplier->balance=$supplier->balance-$money*100;
            $res2=$supplier->save(false);
            if (!$res2)
            {
                $code=500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $UserAccessdetail=new UserAccessdetail();
            $UserAccessdetail->access_type=4;
            $UserAccessdetail->uid=$user->id;
            $UserAccessdetail->role_id=6;
            $UserAccessdetail->access_money=$money*100;
            $UserAccessdetail->create_time=$time;
            $UserAccessdetail->transaction_no=$transaction_no;
            if (!$UserAccessdetail->save(false))
            {
                $code=500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $code=200;
            $tran->commit();
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        }catch (Exception $e){
            $tran->rollBack();
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }


    /**
     * 商家获取已冻结资金列表
     * @return string
     */
    public function actionFindSupplierFreezeList(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier=Supplier::find()
            ->where(['uid'=>$user->id])
            ->one();
        if (!$supplier)
        {
            $code=1010;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $page=trim($request->get('page',1));
        $size=trim($request->get('size',UserFreezelist::PAGE_SIZE_DEFAULT));
        $timeType = trim($request->get('time_type', ''));
        $where="role_id=6   and status=0";
        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));
            if (($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime))
            )
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            if($startTime==$endTime){
                list($startTime, $endTime) =ModelService::timeDeal($startTime);
            }else{
                $endTime && $endTime .= ' 23:59:59';
            }
        }else{
            list($startTime, $endTime) = StringService::startEndDate($timeType);
        }
        if ($startTime) {
            $startTime = (int)strtotime($startTime);
            $startTime && $where .= " and create_time >= {$startTime}";
        }
        if ($endTime) {
            if ($timeType=='today')
            {
                $endTime = (int)(strtotime($endTime)+24*60*60);

            }else{
                $endTime = (int)strtotime($endTime);
            }
            $endTime && $where .= " and create_time <= {$endTime}";
        }
        $sort_time=trim($request->get('sort_time','2'));
        switch ($sort_time)
        {
            case 1:
                $sort='create_time asc';
                break;
            case 2:
                $sort='create_time desc';
                break;
        }
        $data=UserFreezelist::pagination($user->id,$where,[],$page,$size,$sort);
        return Json::encode([
            'code'=>200,
            'msg' =>'ok',
            'data' => $data
        ]);
    }



   /**商家获取收支明细列表
     * @return string
     */
    public function  actionFindSupplierAccessDetailList()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier=Supplier::find()->where(['uid'=>$user->id])->one();
        if (!$supplier)
        {
            $code=1010;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $page=trim($request->get('page',1));
        $size=trim($request->get('size',GoodsOrder::PAGE_SIZE_DEFAULT));
        $keyword = trim($request->get('keyword', ''));
        $timeType = trim($request->get('time_type', ''));
        $type=trim($request->get('type',''));
        if (array_key_exists($type,UserCashregister::ACCESS_TYPE_LIST))
        {
            $where="access_type={$type} and role_id=6 and uid={$user->id}";
        }else{
            $where="role_id=6 and uid={$user->id}";
        }
        if($keyword){
            $where .=" and   CONCAT(order_no,transaction_no) like '%{$keyword}%'";
//            $where .=" and order_no like '%{$keyword}%' or transaction_no like '%{$keyword}%'";
        }
        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));
            if (
                ($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime))
            )
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            if($startTime==$endTime){
                list($startTime, $endTime) =ModelService::timeDeal($startTime);
            }else{
                $endTime && $endTime .= ' 23:59:59';
            }
        }else{
            list($startTime, $endTime) = StringService::startEndDate($timeType);
        }
        if ($startTime) {
            $startTime = (int)strtotime($startTime);
            $startTime && $where .= " and create_time >= {$startTime}";
        }
        if ($endTime) {
            if ($timeType=='today')
            {
                $endTime = strtotime($endTime)+24*60*60;
            }else{
                $endTime = strtotime($endTime);
            }

            $endTime && $where .= " and create_time <= {$endTime}";
        }
        $sort_time=trim($request->get('sort_time','2'));
        switch ($sort_time)
        {
            case 1:
                $sort='create_time asc';
                break;
            case 2:
                $sort='create_time desc';
                break;
        }
        $data=UserAccessdetail::pagination($where,[],$page,$size,$sort);
        return Json::encode([
            'code'=>200,
            'msg' =>'ok',
            'data' => $data
        ]);
    }


    /**
     * 大后台收支明细
     * @return string
     */
    public  function actionAdminUserAccessDetail()
    {
//        $user = Yii::$app->user->identity;
//        if (!$user){
//            $code=1052;
//            return Json::encode([
//                'code' => $code,
//                'msg' => Yii::$app->params['errorCodes'][$code]
//            ]);
//        }
//        $supplier=Supplier::find()->where(['uid'=>$user->id])->one();
//        if (!$supplier)
//        {
//            $code=1010;
//            return Json::encode([
//                'code' => $code,
//                'msg' => Yii::$app->params['errorCodes'][$code]
//            ]);
//        }
            $request = Yii::$app->request;
            $transaction_no=$request->get('transaction_no');
            if (!$transaction_no)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        $accessDetail=UserAccessdetail::find()
            ->andWhere(['transaction_no'=>$transaction_no])
            ->asArray()
            ->one();
        if (!$accessDetail){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        switch ($accessDetail['access_type'])
        {
            case 1:
                $data=UserAccessdetail::findRechargeDetail($accessDetail);
                break;
            case 2:
                $type=UserAccessdetail::ACCESS_CODE_DEBIT;
                $data=UserAccessdetail::findAccessDetail($accessDetail,$type);
                break;
            case 6:
                $type='Goods';
                $data=UserAccessdetail::findAccessDetail($accessDetail,$type);;
                break;
            case 7:
//                $type=UserAccessdetail::ACCESS_CODE_PAYMENT_BUY;
                $data=UserAccessdetail::GetPaymentBuyDetail($transaction_no);
                break;
        }
        if (is_numeric($data))
        {
            $code=$data;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        return Json::encode([
            'code'=>200,
            'msg' =>'ok',
            'data' => $data
        ]);
    }

    /**
     * 商家收支明细详情
     * @return string
     */
    public function  actionSupplierAccessDetail()
    {
//        $user = Yii::$app->user->identity;
//        if (!$user){
//            $code=1052;
//            return Json::encode([
//                'code' => $code,
//                'msg' => Yii::$app->params['errorCodes'][$code]
//            ]);
//        }
//        $supplier=Supplier::find()->where(['uid'=>$user->id])->one();
//        if (!$supplier)
//        {
//            $code=1010;
//            return Json::encode([
//                'code' => $code,
//                'msg' => Yii::$app->params['errorCodes'][$code]
//            ]);
//        }
        $request = Yii::$app->request;
        $transaction_no=trim($request->post('transaction_no',''));
        if (!$transaction_no)
        {
            $transaction_no=$request->get('transaction_no');
            if (!$transaction_no)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        $accessDetail=UserAccessdetail::find()
//            ->where(['uid'=>$user->id])
//            ->andWhere(['role_id'=>6])
            ->andWhere(['transaction_no'=>$transaction_no])
            ->asArray()
            ->one();
            if (!$accessDetail){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $role_id=6;
            switch ($accessDetail['access_type'])
            {
                case 1:
                    $data=UserAccessdetail::findRechargeDetail($accessDetail);
                    break;
                case 2:
                    $type='Debit';
                    $data=UserAccessdetail::findAccessDetail($accessDetail,$type);
                    break;
                case 6:
                    $type='Goods';
                    $data=UserAccessdetail::findAccessDetail($accessDetail,$type);;
                    break;
            }
            if (is_numeric($data))
            {
                $code=$data;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            return Json::encode([
                'code'=>200,
                'msg' =>'ok',
                'data' => $data
            ]);
    }




    /**
     *查询提现金额
     * @return string
     */
    public  function  actionCheckCashMoney()
    {
        $user=Yii::$app->user->identity;
        if (!$user)
        {
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=Yii::$app->request;
        $supplier=Supplier::find()->where(['uid'=>$user->id])->one();
        if (!$supplier)
        {
            $code=403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $cash_moeny=trim($request->post('cash_money'));
        if (!$cash_moeny || !is_numeric($cash_moeny))
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if ($supplier->availableamount-$cash_moeny<0 )
        {
            $code=1033;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok'
        ]);
    }

        /**
         * 验证商家支付密码
         * @return string
         */
        public  function  actionCheckSupplierPayPwd()
        {
            $user=Yii::$app->user->identity;
            if (!$user)
            {
                $code=1052;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $request=Yii::$app->request;
            $supplier=Supplier::find()->where(['uid'=>$user->id])->one();
            $pay_pwd=trim($request->post('pay_pwd'));
            if (!$pay_pwd)
            {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }

            if (Yii::$app->getSecurity()->validatePassword($pay_pwd,$supplier->pay_password)==false){
                $code=1055;
                return Json::encode([
                    'code'=>$code,
                    'msg'=>Yii::$app->params['errorCodes'][$code]
                ]);
            }

            $code=200;
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        }

    
    /**
     * 移动端-用户充值-支付宝
     * @return string
     */
    public function actionAliPayRecharge()
    {
        $user=Yii::$app->user->identity;
        if (!$user)
        {
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=Yii::$app->request;
        $money=$request->post('money');
        if (!$money)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=Alipay::UserRecharge($money,$user);
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$data
        ]);
    }

    /**
     * 移动端-用户充值-微信
     * @return string
     */
    public function actionWxPayRecharge()
    {
        $user=Yii::$app->user->identity;
        if (!$user)
        {
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=Yii::$app->request;
        $money=$request->post('money');
        if (!$money)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=Wxpay::UserRecharge($money,$user);
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$data
        ]);
    }


    /**
     * 微信充值数据库操作
     * @return bool|string
     */
    public  function  actionWxRechargeDatabase()
    {
        //获取通知的数据
        $xml = file_get_contents("php://input");
        $data=json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA));
        $msg=Json::decode($data);
        if ($msg['result_code']=='SUCCESS'){
            $transaction_id=$msg['transaction_id'];
            $result = Wxpay::QueryApporder($transaction_id);
            if (!$result)
            {
                return false;
            }
            $arr= explode(',',base64_decode($msg['attach']));
            $role_id=$arr[0];
            $uid=$arr[1];
            $transaction_no=$arr[2];
            $access=UserAccessdetail::find()->where(['transaction_no'=>$transaction_no])->one();
            $user=User::find()->where(['id'=>$uid])->one();
            if (!$user)
            {
               return true;
            }
            if ($access)
            {
                return true;
            }
            $tran=Yii::$app->db->beginTransaction();
            try{
                $role=Role::GetRoleByRoleId($role_id,$user);
                $role->balance=$role->balance+$msg['total_fee'];
                $role->availableamount=$role->availableamount+$msg['total_fee'];
                $res1=$role->save(false);
                if (!$res1)
                {
                    $tran->rollBack();
                    $code=500;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $accessDetail=new UserAccessdetail();
                $accessDetail->uid=$uid;
                $accessDetail->role_id=$role_id;
                $accessDetail->access_type=1;
                $accessDetail->access_money=$msg['total_fee'];
                $accessDetail->create_time=time();
                $accessDetail->transaction_no=$transaction_no;
                $accessDetail->recharge_pay_type=2;
                $res2=$accessDetail->save(false);
                if (!$res2)
                {
                    $tran->rollBack();
                    $code=500;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $tran->commit();
                echo 'success';
            }catch (Exception $e)
            {
                $tran->rollBack();
                $code=500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
    }


     /**
     * 用户充值 支付宝 APP支付 异步返回
     * @return string
     */
    public  function  actionAliPayUserRechargeDatabase()
    {
        $post=Yii::$app->request->post();
        $model=new Alipay();
        $alipaySevice=$model->Alipaylinenotify();
        $result = $alipaySevice->check($post);
        if ($result){
            if ($post['trade_status'] == 'TRADE_SUCCESS'){
                $out_trade_no=$post['out_trade_no'];
                $total_amount=$post['total_amount'];
                $passback_params=explode(',',urldecode($post['passback_params']));
                $role_id=$passback_params[0];
                $uid=$passback_params[1];
                $access=UserAccessdetail::find()->where(['transaction_no'=>$out_trade_no])->one();
                $user=User::find()->where(['id'=>$uid])->one();
                if (!$user)
                {
                    echo "success";
                    exit;
                }
                if ($access)
                {
                    echo "success";
                    exit;
                }
                $tran=Yii::$app->db->beginTransaction();
                try{
                    $role=Role::GetRoleByRoleId($role_id,$user);
                    $role->balance=$role->balance+$total_amount*100;
                    $role->availableamount=$role->availableamount+$total_amount*100;
                    $res1=$role->save(false);
                    if (!$res1)
                    {
                        $tran->rollBack();
                        $code=500;
                        return Json::encode([
                            'code' => $code,
                            'msg' => Yii::$app->params['errorCodes'][$code]
                        ]);
                    }
                    $accessDetail=new UserAccessdetail();
                    $accessDetail->uid=$uid;
                    $accessDetail->role_id=$role_id;
                    $accessDetail->access_type=1;
                    $accessDetail->access_money=$total_amount*100;
                    $accessDetail->create_time=time();
                    $accessDetail->transaction_no=$out_trade_no;
                    $accessDetail->recharge_pay_type=1;
                    if (!$accessDetail->save(false))
                    {
                        $tran->rollBack();
                        $code=500;
                        return Json::encode([
                            'code' => $code,
                            'msg' => Yii::$app->params['errorCodes'][$code]
                        ]);
                    }
                    $tran->commit();
                    echo 'success';
                }catch (Exception $e)
                {
                    $tran->rollBack();
                    $code=500;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
            }
        }else{
            //验证失败
            echo "fail";    //请不要修改或删除
        }
    }



     /**解绑银行卡
     * @return string
     */
    public function  actionDelBankCard()
    {
        $user=Yii::$app->user->identity;
        if (!$user)
        {
            $code=403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=Yii::$app->request;
        $bank_id=$request->post('bank_id');
        if (!$bank_id)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $bank=UserBankInfo::find()->where(['id'=>$bank_id])->one();
        if (!$bank)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $tran=Yii::$app->db->beginTransaction();
        try{
            if ($bank->selected==1)
            {
                $res=$bank->delete();
                if (!$res)
                {
                    $code=500;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $bankInfo=UserBankInfo::find()
                    ->where(['uid'=>$user->id,'role_id'=>$user->last_role_id_app])
                    ->one();
                if ($bankInfo)
                {
                    $bankInfo->selected=1;
                    $res1=$bankInfo->save(false);
                    if (!$res1)
                    {
                        $code=500;
                        return Json::encode([
                            'code' => $code,
                            'msg' => Yii::$app->params['errorCodes'][$code]
                        ]);
                    }
                }

            }else{
                $res=$bank->delete();
                if (!$res)
                {
                    $code=500;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
            }
            $tran->commit();
            $code=200;
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        }catch (Exception $e)
        {
            $tran->rollBack();
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }

    }

    /**
     * 用户提现申请
     * @return string
     */
    public function actionUserWithdrawalsApply(){
        $request=Yii::$app->request;
        $money=trim($request->post('money',''));
        $pay_password=trim($request->post('pay_pwd',''));
        $bank_id=trim($request->post('bank_id',''));
        if (!$money ||!$pay_password  || !$bank_id){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $role_id=$user->last_role_id_app;
        $role=Role::GetRoleByRoleId($role_id,$user);
        if (!$role)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $availableamount =$role->availableamount;
        $pwd=$role->pay_password;
        if ($money*100>$availableamount){
            $code=1054;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (!$pwd)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => '您还未设置交易密码，请先去设置'
            ]);
        }
        if (!Yii::$app->getSecurity()->validatePassword($pay_password, $pwd)==true){
            $code=1055;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data'=>$user->mobile
            ]);
        }
        $userBankInfo=UserBankInfo::find()
            ->where(['id'=>$bank_id,'uid'=>$user->id])
            ->one();
        if (!$userBankInfo)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => '你尚未绑定银行卡'
            ]);
        }
        switch ($role_id)
        {
            case 2:
                $role_number=$role->worker_type_id;
                break;
            case 3:
                $role_number=$role->decoration_company_id;
                break;
            case 4:
                $role_number=$role->decoration_company_id;
                break;
            case 5:
                $role_number=$role->id;
                break;
            case 6:
                $role_number=$role->shop_no;
                break;
            case 7:
                $role_number=$role->aite_cube_no;
                break;
        }
        $transaction_no=GoodsOrder::SetTransactionNo($role_number);
        $time=time();
        $tran = Yii::$app->db->beginTransaction();
        try{
            $UserCashRegister= new UserCashregister();
            $UserCashRegister->uid=$user->id;
            $UserCashRegister->role_id=$role_id;
            $UserCashRegister->cash_money=$money*100;
            $UserCashRegister->apply_time=$time;
            $UserCashRegister->status=1; //1:提现中  2.已提现  3.驳回
            $UserCashRegister->transaction_no=$transaction_no;
            $UserCashRegister->bank_log_id=$userBankInfo->log_id;
            $res1=$UserCashRegister->save(false);
            if (!$res1)
            {
                $code=500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $role->availableamount=$role->availableamount-$money*100;
            $role->balance=$role->balance-$money*100;
            $res2=$role->save(false);
            if (!$res2)
            {
                $code=500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $UserAccessdetail=new UserAccessdetail();
            $UserAccessdetail->access_type=4;
            $UserAccessdetail->uid=$user->id;
            $UserAccessdetail->role_id=$role_id;
            $UserAccessdetail->access_money=$money*100;
            $UserAccessdetail->create_time=$time;
            $UserAccessdetail->transaction_no=$transaction_no;
            if (!$UserAccessdetail->save(false))
            {
                $code=500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $code=200;
            $tran->commit();
              return Json::encode([
                'code' => $code,
                'msg' => 'ok',
                'data'=>date('Y-m-d h:i',$time+21*60*60*3)
            ]);
        }catch (\Exception $e){
            $tran->rollBack();
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }



     /**
     * app端交易明细
     * @return string
     */
    public   function  actionAppTransactionDetail()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $page=$request->get('page','1');
        $size=$request->get('size',20);
        $role_id=$user->last_role_id_app;
        $where="uid={$user->id} and role_id = {$role_id}";
        $sort='create_time  desc';
        $paginationData = UserAccessdetail::pagination($where, [],$page,$size, $sort);
        $code=200;
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>$paginationData
        ]);
    }


    /**
     * App交易详情
     * @return string
     */
   public  function  actionAppTransactionDetailData()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $transaction_no=$request->get('transaction_no','1');
        if (!$transaction_no)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $access=UserAccessdetail::find()
            ->where(['transaction_no'=>$transaction_no])
            ->asArray()
            ->one();
        if (!$access)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if ($access['access_type']==1 || $access['access_type']==5 || $access['access_type']==6 )
        {
            //"1.充值 2.扣款 3.已提现 4.提现中  5.驳回 6.货款 7.使用"
            $access['access_money']=sprintf('%.2f',(float)$access['access_money']*0.01);
            $name="出账金额";
        }else{
            $access['access_money']=sprintf('%.2f',-$access['access_money']*0.01);
            $name="入账金额";
        }
        $type=UserAccessdetail::findAccessType($access['access_type']);
        $list[]=[
            'name'=>$name,
            'value'=>$access['access_money']
        ];
        $list[]=[
            'name'=>'类型',
            'value'=>$type
        ];
        switch ($access['access_type'])
        {
            case 1:
                switch ($access['recharge_pay_type'])
                {
                    case 1:
                        $list[]=[
                            'name'=>'充值方式',
                            'value'=>'支付宝支付'
                        ];
                        break;
                    case 2:
                        $list[]=[
                            'name'=>'充值方式',
                            'value'=>'微信支付'
                        ];
                        break;
                }

                $list[]=[
                    'name'=>'时间',
                    'value'=>$access['create_time']
                ];
                $list[]=[
                    'name'=>'交易单号',
                    'value'=>$access['transaction_no']
                ];
                break;
            case 2:

                //此处有改动未修复
                $list[]=[
                    'name'=>'充值方式',
                    'value'=>'支付宝支付'
                ];
                $list[]=[
                    'name'=>'时间',
                    'value'=>$access['create_time']
                ];
                $list[]=[
                    'name'=>'交易单号',
                    'value'=>$transaction_no
                ];
                break;
            case 3:
                $cashData=(new Query())
                    ->from(UserCashregister::tableName().' as c')
                    ->leftJoin(BankinfoLog::tableName().' as b','c.bank_log_id=b.id')
                    ->where(['c.transaction_no'=>$transaction_no])
                    ->one();
                $list[]=[
                    'name'=>'到账银行卡',
                    'value'=>$cashData['bankname']
                ];
                $list[]=[
                    'name'=>'申请时间',
                    'value'=>date('Y-m-d H:i',$cashData['apply_time'])
                ];
                $list[]=[
                    'name'=>'处理时间',
                    'value'=>date('Y-m-d H:i',$cashData['handle_time'])
                ];
                $list[]=[
                    'name'=>'交易单号',
                    'value'=>$transaction_no
                ];
                break;
            case 4:
                $cashData=(new Query())
                    ->from(UserCashregister::tableName().' as c')
                    ->leftJoin(BankinfoLog::tableName().' as b','c.bank_log_id=b.id')
                    ->where(['c.transaction_no'=>$transaction_no])
                    ->one();
                $list[]=[
                    'name'=>'到账银行卡',
                    'value'=>$cashData['bankname']
                ];
                $list[]=[
                    'name'=>'申请时间',
                    'value'=>date('Y-m-d H:i',$cashData['apply_time'])
                ];

                $list[]=[
                    'name'=>'交易单号',
                    'value'=>$transaction_no
                ];
                break;
            case 5:
                $cashData=(new Query())
                    ->from(UserCashregister::tableName().' as c')
                    ->leftJoin(BankinfoLog::tableName().' as b','c.bank_log_id=b.id')
                    ->where(['c.transaction_no'=>$transaction_no])
                    ->one();
                $list[]=[
                    'name'=>'到账银行卡',
                    'value'=>$cashData['bankname']
                ];
                $list[]=[
                    'name'=>'申请时间',
                    'value'=>date('Y-m-d H:i',$cashData['apply_time'])
                ];
                $list[]=[
                    'name'=>'处理时间',
                    'value'=>date('Y-m-d H:i',$cashData['handle_time'])
                ];
                $list[]=[
                    'name'=>'交易单号',
                    'value'=>$transaction_no
                ];
                $list[]=[
                    'name'=>'备注',
                    'value'=>$cashData['supplier_reason']
                ];
                break;
            case 6:
                $list[]=[
                    'name'=>'时间',
                    'value'=>$access['create_time']
                ];

                $list[]=[
                    'name'=>'交易单号',
                    'value'=>$transaction_no
                ];
                $list[]=[
                    'name'=>'商品订单号',
                    'value'=>$access['order_no']
                ];
                break;
            case 7:
                $accessList=UserAccessdetail::find()
                    ->where(['transaction_no'=>$transaction_no])
                    ->asArray()
                    ->all();
                if ($access['order_no']){
                    $goodsOrder=GoodsOrder::find()
                        ->select('pay_name')
                        ->where(['order_no'=>$access['order_no']])
                        ->one();
                    $pay_name=$goodsOrder->pay_name;
                    foreach ($accessList as &$list)
                    {
                        $orderGoods=OrderGoods::find()
                            ->select('goods_name')
                            ->where(['order_no'=>$list['order_no']])
                            ->asArray()
                            ->all();
                        foreach ($orderGoods as $orderGood)
                        {
                            $goods_name[]=   $orderGood['goods_name'];
                        }
                    }
                    if (count($goods_name)>1)
                    {
                        $title=$goods_name[0]."...";
                    }else{
                        $title=$goods_name[0];
                    }
                    $list[]=[
                        'name'=>'支付类型',
                        'value'=>$pay_name
                    ];
                    $list[]=[
                        'name'=>'商品名称',
                        'value'=>$title
                    ];
                    $list[]=[
                        'name'=>'时间',
                        'value'=>$access['create_time']
                    ];
                    $list[]=[
                        'name'=>'交易单号',
                        'value'=>$transaction_no
                    ];
                }
                break;
        }
        $code=200;
         return Json::encode([
            'code'=>$code,
           'msg'=>'ok',
            'data'=>[
                'type'=>'交易详情-'.$type,
                'list'=>$list
            ]
        ]);
    }

   /**
    * 获取身份证
     * @return string
     */
    public  function  actionFindIdCard()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=200;
        return Json::encode([
            'code'=>$code,
            'msg'=>'ok',
            'data'=>[
                'idcard'=>$user->identity_no,
                'realname'=>$user->legal_person
            ]
        ]);
    }


    /**
     * 获取支行
     * @return string
     */
    public function  actionGetBankBranch()
    {
        $request = Yii::$app->request;
        $bank_name=$request->post('bank_name');
        $city_id=$request->post('city_id');
        if (!$bank_name || !$city_id)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $bank=BankName::find()->where("  name  like '%{$bank_name}%'")->one();
        if (!$bank)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $bankBranch=BankBranch::find()
            ->where(['cityId'=>$city_id,'bankId'=>$bank->value])
            ->all();
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$bankBranch
        ]);
    }


    /**
     * 通过银行卡号获取银行卡信息
     * @return string
     */
    public  function  actionFindBankData()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $bank_card=Yii::$app->request->post('bank_card');
        if (!$bank_card)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $sendUrl = 'http://api.avatardata.cn/Bank/Query?key=fc79d5cf0de64f9bb60759045e5977d0&cardnum='.$bank_card;
        $content =Wxpay::curl($sendUrl,false,0);
        if($content){
            $result = json_decode($content,true);
            if ($result['error_code']==0)
            {
                $code=200;
                return Json::encode([
                    'code' => $code,
                    'msg' => 'ok',
                    'data'=>[
                        'bank_name'=>$result['result']['bankname'],
                        'cardtype'=>$result['result']['cardtype']
                    ]
                ]);
            }else{
                $code=1000;
                 return Json::encode([
                    'code' => $code,
                    'msg' => '银行卡格式不对'
                 ]);
            }
        }else{
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }

    }


    /**
     * @return string
     */
    public function  actionFindUser()
    {
        $user = User::find()->all();
        return Json::encode([$user]);
    }

    /**
     * 业主提现列表
     * @return string
     */
    public function  actionFindOwnerCashList()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return json_encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $type=trim(Yii::$app->request->get('type', ''));
        $timeType = trim(Yii::$app->request->get('time_type', ''));
        $keyword=Yii::$app->request->get('keyword');
        $where="c.role_id=".Supplier::ROLE_SUPPLIER;
        $code=1000;
        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));
            if (($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime))
            )
            {
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
            if($startTime==$endTime){
                list($startTime, $endTime) =ModelService::timeDeal($startTime);
            }else{
                $endTime && $endTime .= ' 23:59:59';
            }
        }else{
            list($startTime, $endTime) = StringService::startEndDate($timeType);
        }
        if ($startTime) {
            $startTime = strtotime($startTime);
            $startTime && $where .= " and c.apply_time >= {$startTime}";
        }
        if ($endTime) {
            $endTime = (int)(strtotime($endTime));
            $endTime && $where .= " and c.apply_time <= {$endTime}";
        }
        if ($keyword)
        {
            $where .=" and CONCAT(u.nickname,u.aite_cube_no) like '%{$keyword}%'";
        }
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', SupplierCashregister::PAGE_SIZE_DEFAULT);
        if ($type)
        {
            switch ($type)
            {
                case UserCashregister::CASH_STATUS_IN:
                    $where.="c.status=".UserCashregister::CASH_STATUS_IN;
                    break;
                case UserCashregister::CASH_STATUS_OVER:
                    $where.="c.status=".UserCashregister::CASH_STATUS_OVER;
                    break;
                case UserCashregister::CASH_STATUS_FAIL:
                    $where.="c.=".UserCashregister::CASH_STATUS_FAIL;
                    break;
            }
        }
        $data=UserCashregister::paginationByOwner($where,$page,$size,['c.id', 'c.apply_time', 'c.cash_money', 'c.real_money', 'c.handle_time', 'c.transaction_no', 'c.status' ]);
        if (is_numeric($data))
        {
            $code=$data;
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $code=200;
        return json_encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$data
        ]);

    }

    public  function actionFindUserAccessDetail()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return json_encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=UserAccessdetail::find()
            ->where(['uid'=>$user->id])
            ->andWhere(['role_id'=>$user->last_role_id_app])
            ->one();
        $code=200;
        return json_encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$data
        ]);
    }


    public  function  actionTest()
    {
        $user=User::find()
            ->where(['mobile'=>Yii::$app->request->post('mobile', '')])
            ->one();
        $user->balance+=10000000;
        $user->availableamount+=10000000;
        $user->save(false);
    }






}
