<?php

namespace app\controllers;
use app\models\GoodsOrder;
use app\models\Role;
use app\models\Supplieramountmanage;
use app\models\Supplier;
use app\models\User;
use app\models\UserAccessdetail;
use app\models\UserCashregister;
use app\models\UserBankInfo;
use app\models\BankinfoLog;
use app\models\UserFreezelist;
use app\services\ModelService;
use app\services\SmValidationService;
use app\services\ExceptionHandleService;
use app\services\StringService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\db\Exception;
use yii\web\ServerErrorHttpException;
use Yii;
class WithdrawalsController extends Controller
{


    /**
     * Actions accessed by logged-in users
     */
    const ACCESS_LOGGED_IN_USER = [
        'logout',
        'roles',
        'reset-password',
        'roles-status',
        'time-types',
        'upload',
        'upload-delete',
        'review-statuses',
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
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
                    'logout' => ['post',],
                    'reset-password' => ['post',],
                    'upload' => ['post',],
                    'upload-delete' => ['post',]
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
        $bankcard=trim($request->post('bankcard',''));
        $username=trim($request->post('username',''));
        $position=trim($request->post('position',''));
        $bankbranch=trim($request->post('bankbranch',''));
        $role_id=trim($request->post('role_id',''));
        if (!$bankname || !$bankcard || !$username || !$position || !$bankbranch  || !$role_id){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
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
     * 查询银行卡信息
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
            ->where(['u.uid'=>$user->id,'u.role_id'=>$role_id])
            ->one();
        if ($UserBankInfo)
        {
            $data=$UserBankInfo->toArray();
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
        $supplier= Supplier::find()->where(['uid'=>$user->id])->one();
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
            if (!User::CheckPaypwdFormat($pay_pwd_first) || !User::CheckPaypwdFormat($pay_pwd_secend)){
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
            if (!SmValidationService::validCode($user->mobile,$sms_code)) {
                $code = 1002;
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
        return Json::encode([
            'code' => $code,
            'msg' => Yii::$app->params['errorCodes'][$code]
        ]);
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
        $m = Supplier::find()->select('availableamount')->where(['uid' => $user->id])->one()->availableamount;
        if (!$m)
        {
            $code=1034;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $money=sprintf('%.2f', (float)$m*0.01);
        if ($m<0){
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
            ->one();
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
            $UserCashRegister->status=1;
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
        $size=trim($request->get('size',UserFreezelist::PAGE_SIZE_DEFAULT));
        $timeType = trim($request->get('time_type', ''));
        $where="role_id=6 and uid={$user->id}  and status=0";
        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));
            if (($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime))
            ) {
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }else{
            list($startTime, $endTime) = StringService::startEndDate($timeType);
            $startTime = explode(' ', $startTime)[0];
            $endTime = explode(' ', $endTime)[0];
        }
        if ($startTime) {
            $startTime = (int)strtotime($startTime);
            $startTime && $where .= " and create_time >= {$startTime}";
        }
        if ($endTime) {
            $endTime = (int)strtotime($endTime);
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
        $data=UserFreezelist::pagination($where,[],$page,$size,$sort);
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
            $where .=" and order_no like '%{$keyword}%' or transaction_no like '%{$keyword}%'";
        }
        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));
            if (($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime))
            ){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }else{
            list($startTime, $endTime) = StringService::startEndDate($timeType);
            $startTime = explode(' ', $startTime)[0];
            $endTime = explode(' ', $endTime)[0];
        }
        if ($startTime) {
            $startTime = (int)strtotime($startTime);
            $startTime && $where .= " and create_time >= {$startTime}";
        }
        if ($endTime) {
            $endTime = (int)strtotime($endTime);
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
     * @return string
     */
    public function  actionSupplierAccessDetail()
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
        $transaction_no=trim($request->post('transaction_no',''));
        if (!$transaction_no)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $accessDetail=UserAccessdetail::find()
            ->where(['uid'=>$user->id])
            ->andWhere(['role_id'=>6])
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


     public  function  actionAddSupplier()
    {
        $mobile=Yii::$app->request->post('mobile','');
        if (!$mobile)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user=User::find()->where(['mobile'=>$mobile])->one();
        if (!$user)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier=Supplier::find()->asArray()->where(['uid'=>$user->id])->one();
        if (!$supplier)
        {
            $tran=Yii::$app->db->beginTransaction();
            try{
                $sss=Supplier::find()->where(['id'=>12])->one();
                $sss->uid=$user->id;
                if (!$sss->save(false))
                {
                    $code=500;
                    $tran->rollBack();
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $userRole=new UserRole();
                $userRole->user_id=$user->id;
                $userRole->role_id=6;
                $userRole->review_apply_time=time();
                $userRole->review_status=2;
                $userRole->reviewer_uid=7;
                if (!$userRole->save(false))
                {
                    $code=500;
                    $tran->rollBack();
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
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok'
        ]);
    }

}
