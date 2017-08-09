<?php

namespace app\controllers;

use app\models\Distribution;
use app\models\LoginForm;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\helpers\Json;

use app\services\SmValidationService;

class DistributionController extends Controller
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
        'reset-nickname',
        'reset-signature',
        'reset-gender',
        'reset-birthday',
        'reset-district',
        'user-view',
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
                    'upload-delete' => ['post',],
                    'reset-nickname' => ['post',],
                    'reset-signature' => ['post',],
                    'reset-gender' => ['post',],
                    'reset-birthday' => ['post',],
                    'reset-district' => ['post',],
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
     * 登录状态判断并保存手机号
     * @return string
     */
    public function actionDistributionlogin_mobile(){
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $mobile=trim(htmlspecialchars($request->post('mobile','')),'');
        $user=Distribution::find()->where(['mobile'=>$mobile])->asArray()->one();
        $ismobile = preg_match('/^1[34578]\d{9}$/',$mobile);
        if ($ismobile!=1 || !$mobile){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
        $time=time();
        if ($session['distributionlastsendtime']){
            if (($time-$session['distributionlastsendtime'])<60){
                $code = 1020;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }else{
            $session['distributionlastsendtime']=$time;
        }
        if ($user){
            $data['mobile']=$mobile;
            $data['type']='register';
            $res=new SmValidationService($data);
            if ($res){
                $code=200;
                $session['distributionmobile']=$mobile;
                return Json::encode([
                    'code' => $code,
                    'msg' =>'ok'
                ]);
            }
        }else{
            $data['mobile']=$mobile;
            $data['type']='register';
            $res=new SmValidationService($data);
            if ($res){
                $code=200;
                $session['distributionmobile']=$mobile;
                return Json::encode([
                    'code' => $code,
                    'msg' =>'ok'
                ]);
            }
        }
    }

    /**
     * 登录
     * @return string
     */
    public function actionDistributionlogin(){
        $session = Yii::$app->session;
        $mobile=$session['distributionmobile'];
        $request = Yii::$app->request;
        $code=trim(htmlspecialchars($request->post('code','')),'');
        // if (!SmValidationService::validCode($mobile, $code)) {
        //     $code = 1002;
        //     return Json::encode([
        //         'code' => $code,
        //         'msg' => Yii::$app->params['errorCodes'][$code],
        //     ]);
        // }
        // SmValidationService::deleteCode($mobile);
        $time=time();
        $customer = new Distribution();
        $customer->mobile = $mobile;
        $customer->create_time = $time;
        $user=Distribution::find()->select('mobile')->where(['mobile'=>$mobile])->asArray()->one();
        if ($user){
            $session['distribution_token']=urlencode($mobile.'&'.$time);
        }else{
             $res=$customer->save();
            if ($res){
                $session['distribution_token']=urlencode($mobile.'&'.$time);
                $code=200;
                return Json::encode([
                    'code' => $code,
                    'msg' =>'ok'
                ]);
            }
        }
    }


    /**
     * 分销个人中心
     * @return string
     */
    public function actionDistributionusercenter(){
        $session = Yii::$app->session;
        $data=explode('&', urldecode($session['distribution_token']));
        $mobile=$data[0];
        $data=Distribution::Distributionusercenter($mobile);
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' =>'ok',
            'data'=>$data
        ]);
    }


    /**
     * 分销绑定手机号
     * @return string
     */
    public function actionDistributionbindingmobile(){
        $request = Yii::$app->request;
        $mobile=trim(htmlspecialchars($request->post('mobile','')),'');
        $session = Yii::$app->session;
        if (!$session['distribution_token']){
            $code= 403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        if (!$mobile || preg_match('/^1[34578]\d{9}$/',$mobile)!=1){
            $code= 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $user=Distribution::find()->where(['mobile'=>$mobile])->one();
        if (!$user){
             $code= 1010;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $id=Distribution::find()->where(['mobile'=>explode('&', urldecode($session['distribution_token']))[0]])->asArray()->one()['id'];
        $customer = Distribution::findOne($id);
        $customer->parent_id =$user['id'];
       $res= $customer->update();
       if ($res){
           return Json::encode([
               'code' => 200,
               'msg' => 'ok',
           ]);
       }
    }




}