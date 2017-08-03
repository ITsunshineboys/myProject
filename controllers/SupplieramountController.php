<?php

namespace app\controllers;
use app\models\Supplieramountmanage;
use app\models\Supplier;
use app\models\GoodsOrder;
use app\services\SmValidationService;
use app\services\ExceptionHandleService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use Yii;
class SupplieramountController extends Controller
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
     * 添加银行卡
     * @return string
     */
    public function actionAddbankcard(){
        $request=Yii::$app->request;
        $bankname=trim(htmlspecialchars($request->post('bankname','')),'');
        $bankcard=trim(htmlspecialchars($request->post('bankcard','')),'');
        $username=trim(htmlspecialchars($request->post('username','')),'');
        $position=trim(htmlspecialchars($request->post('position','')),'');
        $bankbranch=trim(htmlspecialchars($request->post('bankbranch','')),'');
        $user = Yii::$app->user->identity;
        if (!$bankname || !$bankcard || !$username || !$position || !$bankbranch){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier_id = Supplier::find()->where(['uid' => $user->id])->one()['id'];
        $res=(new Supplieramountmanage())->Bankcardadd($supplier_id,$bankname,$bankcard,$username,$position,$bankbranch);
        if ($res){
            $code=200;
            return Json::encode([
                'code' => $code,
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
        $supplier_id = Supplier::find()->where(['uid' => $user->id])->one()['id'];
        $data=(new Supplieramountmanage())->Getbankcardinformation($supplier_id);
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data'=>$data
        ]);
    }

    /**
     * 重新绑定银行卡
     * @return string
     */
    public function actionBankcardupdate(){
        $request=Yii::$app->request;
        $bankname=trim(htmlspecialchars($request->post('bankname','')),'');
        $bankcard=trim(htmlspecialchars($request->post('bankcard','')),'');
        $username=trim(htmlspecialchars($request->post('username','')),'');
        $position=trim(htmlspecialchars($request->post('position','')),'');
        $bankbranch=trim(htmlspecialchars($request->post('bankbranch','')),'');
        $user = Yii::$app->user->identity;
        if (!$bankname || !$bankcard || !$username || !$position || !$bankbranch){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier_id = Supplier::find()->where(['uid' => $user->id])->one()['id'];
        $res=(new Supplieramountmanage())->Bankcardupdate($supplier_id,$bankname,$bankcard,$username,$position,$bankbranch);
        if ($res ==true){
            $code=200;
            return Json::encode([
                'code' => $code,
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
        $supplier_id = Supplier::find()->where(['uid' => $user->id])->one()['id'];
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
        $pay_password = Supplier::find()->select('pay_password')->where(['uid' => $user->id])->one()['pay_password'];
        if ($pay_password){
            $key='2';
            $data= Yii::$app->getSecurity()->generatePasswordHash($key);
        }else{
            $key='1';
            $data= Yii::$app->getSecurity()->generatePasswordHash($key);
        }
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data'=>$data
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
        $key=trim(htmlspecialchars($request->post('key','')),'');
        $newpassword=trim(htmlspecialchars($request->post('newpwd','')),'');
        $oldpassword=trim(htmlspecialchars($request->post('oldpwd','')),'');
        if (!$newpassword || !$oldpassword || !$key){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if ($newpassword !=$oldpassword){
            $code=1053;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $smscode=trim(htmlspecialchars($request->post('code','')),'');
        $psw = Yii::$app->getSecurity()->generatePasswordHash($newpassword);
        if (Yii::$app->getSecurity()->validatePassword('1', $key)==true) {
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
        } else if (Yii::$app->getSecurity()->validatePassword('2', $key)){
            $res=(new Supplieramountmanage())->Setpaypsw_sup_reset($psw,$supplier_id,$smscode);
            switch ($res){
                case 1:
                    return Json::encode([
                        'code' => 200,
                        'msg' => 'ok'
                    ]);
                    break;
                case 2:
                    $code=1052;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                    break;
                case 3:
                    $code=1051;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                    break;
                case 4:
                    $code=1002;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                    break;
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
        $money = Supplier::find()->select('availableamount')->where(['uid' => $user->id])->one()['availableamount']*0.01;
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
        $request = Yii::$app->request;
        $page=trim(htmlspecialchars($request->get('page','')),'');
        $pagesize=trim(htmlspecialchars($request->get('pagesize','')),'');
        $time_id=trim(htmlspecialchars($request->post('time_id','')),'');
        if (!$time_id){
            $time_id=0;
        }
        $time_start=trim(htmlspecialchars($request->post('time_start','')),'');
        $time_end=trim(htmlspecialchars($request->post('time_end','')),'');

        if ($time_id==5){
            if (!$time_start || !$time_end){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg'  => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        if (!$pagesize){
            $pagesize=15;
        }
        if (!$page){
            $page=1;
        }
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier=Supplier::find()->select('id')->where(['uid' => $user->id])->one();
        $data=(new Supplieramountmanage())->Getfreezelist($supplier['id'],$page,$pagesize,$time_id,$time_start,$time_end);
        return Json::encode([
            'code'=>200,
            'msg' =>'ok',
            'data' => $data
        ]);
    }
}
