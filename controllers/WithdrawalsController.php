<?php

namespace app\controllers;
use app\models\Supplieramountmanage;
use app\models\Supplier;
use app\models\User;
use app\models\UserAccessdetail;
use app\models\UserCashregister;
use app\models\UserBankInfo;
use app\services\SmValidationService;
use app\services\ExceptionHandleService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
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
     * 查询商家银行卡信息
     *
     * @return string
     */
    public function actionGetbankcard(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user_id=$user->id;
        $data=(new Supplieramountmanage())->Getbankcardinformation($user_id);
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$data
        ]);
    }



    /**
     * 获取商家后台商家资金
     * @return string
     */
    public function actionGetbalance(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier_id = Supplier::find()->where(['uid' => $user->id])->one()->id;
        $data=(new Supplieramountmanage())->Getbalance($supplier_id);
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$data
        ]);
    }

    /**
     * 验证是否是第一次进入
     * @return string
     */
    public function  actionIssetpaypsw(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $pay_password = Supplier::find()
            ->select('pay_password')
            ->asArray()
            ->where(['uid' => $user->id])
            ->one()['pay_password'];
        if ($pay_password){
            $key=2;
            $data= Yii::$app->getSecurity()->generatePasswordHash('unfirstsetpaypassword');
            $val='不是第一次';
        }else{
            $key=1;
            $data= Yii::$app->getSecurity()->generatePasswordHash('firstsetpaypassword');
            $val='第一次';
        }
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data'=>[
                'type'=>$data,
                'value'=>$val,
                'key'=>$key,
            ]
        ]);
    }

    /**
     * 发送短信验证-商家后台重设支付密码
     * @return string
     */
    public function actionSendpaycode(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user=user::find()->select('mobile')->where(['id'=>$user->id])->one();
        $data=array();
        $data['mobile']=$user['mobile'];
        $data['type']='resetPassword';
        $res=new SmValidationService($data);
//        $res=SmValidationService::sendNum($data['mobile'],$data['type']);
        if ($res){
            $code=200;
            return Json::encode([
                'code' => $code,
                'msg' =>'ok'
            ]);
        }
    }
    /**
     * 设置支付密码
     * @return string
     */
    public function  actionSetpaypsw(){
        $request=Yii::$app->request;
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier_id = Supplier::find()->where(['uid' => $user->id])->one()['id'];
        $type=trim(htmlspecialchars($request->post('type','')),'');
        if (!$type){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (Yii::$app->getSecurity()->validatePassword('firstsetpaypassword', $type)==true) {
            $onepwd=trim(htmlspecialchars($request->post('onepwd','')),'');
            $twopwd=trim(htmlspecialchars($request->post('twopwd','')),'');
            $psw = Yii::$app->getSecurity()->generatePasswordHash($twopwd);
            if (!$twopwd || !$onepwd){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            if ($onepwd !=$twopwd){
                $code=1053;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $res=(new Supplieramountmanage())->Setpaypsw($psw,$supplier_id);
            if ($res==true){
                return Json::encode([
                    'code' => 200,
                    'msg' => 'ok'
                ]);
            }else{
                $code=1051;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        } else if (Yii::$app->getSecurity()->validatePassword('unfirstsetpaypassword', $type)==true){
            $smscode=trim(htmlspecialchars($request->post('smscode','')),'');
            $mobile=User::find()->select('mobile')->where(['id' => $user->id])->one()['mobile'];
            $password=trim(htmlspecialchars($request->post('password','')),'');
            if (!$smscode || !$password){
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            $psw = Yii::$app->getSecurity()->generatePasswordHash($password);
            if (!SmValidationService::validCode($mobile,$smscode)) {
                $code = 1002;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            SmValidationService::deleteCode($mobile);
            $res=(new Supplieramountmanage())->Setpaypsw($psw,$supplier_id);
            if ($res==true){
                return Json::encode([
                    'code' => 200,
                    'msg' => 'ok'
                ]);
            }else{
                $code=1051;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
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
     * 获取可用余额-商家后台
     * @return string
     */
    public function actionGetsupplierbalance(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $m = Supplier::find()->select('availableamount')->where(['uid' => $user->id])->one()['availableamount']*0.01;
        $money=sprintf('%.2f', (float)$m);
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
    public function actionSupplierwithdrawalsapply(){
        $request=Yii::$app->request;
        $money=trim(htmlspecialchars($request->post('money','')),'');
        $pay_password=trim(htmlspecialchars($request->post('pay_password','')),'');
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
        $supplier=Supplier::find()->select('availableamount,pay_password')->where(['uid' => $user->id])->one();
        $availableamount =$supplier['availableamount']*0.01;
        $pwd=$supplier['pay_password'];
        if ($money>$availableamount){
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
        $res=(new Supplieramountmanage())->Supplierwithdrawalsapply($money);
        if ($res){
            return Json::encode([
                'code' => 200,
                'msg' => 'ok'
            ]);
        }else{
            $code=1051;
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
    public function actionGetfreezelist(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $page = (int)trim(htmlspecialchars($request->get('page', 1)), '');
        $page_size = (int)trim(htmlspecialchars($request->get('page_size', 15)), '');
        $time_type = trim(htmlspecialchars($request->post('time_type', 'all')), '');
        $time_start = trim(htmlspecialchars($request->post('time_start', '')), '');
        $time_end = trim(htmlspecialchars($request->post('time_end', '')), '');
        if (!$page_size){
            $page_size=15;
        }
        if (!$page){
            $page=1;
        }
        if ($time_type == 'custom') {
            if (!$time_start || !$time_end) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        $supplier=Supplier::find()->select('id')->where(['uid' => $user->id])->one();
        $data=(new Supplieramountmanage())->Getfreezelist($supplier['id'],$page,$page_size,$time_type,$time_start,$time_end);
        return Json::encode([
            'code'=>200,
            'msg' =>'ok',
            'data' => $data
        ]);
    }

    /**
     * 收支明细列表
     * @return string
     */
    public function actionAccessdetaillist(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $page = (int)trim(htmlspecialchars($request->get('page', 1)), '');
        $page_size = (int)trim(htmlspecialchars($request->get('page_size', 15)), '');
        $time_type = trim(htmlspecialchars($request->post('time_type', 'all')), '');
        $time_start = trim(htmlspecialchars($request->post('time_start', '')), '');
        $time_end = trim(htmlspecialchars($request->post('time_end', '')), '');
        if (!$page_size){
            $page_size=15;
        }
        if (!$page){
            $page=1;
        }
        if ($time_type == 'custom') {
            if (!$time_start || !$time_end) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        $access_type = trim(htmlspecialchars($request->post('access_type', '')), '');
        $search= trim(htmlspecialchars($request->post('search', '')), '');
        $order_type=trim(htmlspecialchars($request->post('order_type', '')), '');
        $supplier=Supplier::find()->select('id')->where(['uid' => $user->id])->one();
        $data=(new Supplieramountmanage())->Accessdetails($supplier['id'],$page,$page_size,$time_type,$time_start,$time_end,$access_type,$search,$order_type);
        return Json::encode([
            'code'=>200,
            'msg' =>'ok',
            'data' => $data
        ]);
    }


    public function actionAccessdetail(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $transaction_no = (int)trim(htmlspecialchars($request->post('transaction_no' )), '');
        $data=(new Supplieramountmanage())->Accessinformation($transaction_no);

    }


}
