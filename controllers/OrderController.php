<?php

namespace app\controllers;

use app\models\Addressadd;
use app\models\GoodsOrder;
use app\models\Invoice;
use app\services\ExceptionHandleService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use Yii;


class OrderController extends Controller
{


    public function init(){
        parent::init();
    }


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
     * 线下店商城-添加收货地址
     * @return string
     *
     *                  地区区分--此方案保留
     *                   $index = strpos($region,"省")/3+1;
     *                   $index1 = strpos($region,"市")/3+1;
     *                   $index2 = strpos($region,"区")/3+1;
     *                   $citylen= $index1-$index;
     *                   $provincelen=$index-0;
     *                   $addresslen=$index2-$index1;
     *                   $city=mb_substr($region,$index, $citylen, 'utf-8');
     *                   $province=mb_substr($region,0, $provincelen, 'utf-8');
     *                   $address=mb_substr($region,$index1, $addresslen, 'utf-8');
     *                   $res= Yii::$app->db->createCommand()->insert('user_address', [
     *                   'mobile'    => $mobile,
     *                   'consignee' =>$consignee,
     *                   'city'      =>$city,
     *                   'province'  =>$province,
     *                   'address'   =>$address,
     *                   'district'  =>$district
     *                   ])->execute();
     *
     *生成收货地址唯一性的token
     *
     *  $region:详细地址   $district:地址
     */
    public function actionAdduseraddress()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $consignee = trim($request->post('consignee',''));
            $mobile= trim($request->post('mobile',''));
            $district=trim($request->post('district',''));
            $region=trim($request->post('region',''));
            $model = new Addressadd();
            $model->attributes = $request->post();
            if ($model->validate()) {
                $res=$model->insertaddress($mobile,$consignee,$region,$district);
               if ($res==true){
                   return Json::encode([
                       'code' => 200,
                       'msg' => '添加收货地址成功',
                       'data' => '添加收货地址成功'
                   ]);
               }else
               {
                   $code=500;
                   return Json::encode([
                       'code' => $code,
                       'msg' => Yii::$app->params['errorCodes'][$code],
                       'data' => '添加收货地址失败'
                   ]);
               }
            }else{
                    $errors = $model->errors;
                    $code=1000;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code],
                        'data' => $errors
                    ]);
            }
        }else {
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data' => 0
            ]);
        }
    }
    /**
     * 线下店商城-确认订单页面-获取收货地址
     *
     *
     */
    public function actionGetaddress(){
        $session = Yii::$app->session;
        $addresstoken=$session['addresstoken'];
        $model = new Addressadd();
        $user_address=$model->getaddress($addresstoken);
        if ($user_address){
            return Json::encode([
                'code' => 200,
                'msg'  => '成功',
                'data' => $user_address
            ]);
        }else{
            $code=404;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
    }
    /**
     *线下店商城-添加发票信息-
     */
    public function actionOrderinvoicelineadd(){
        $request = \Yii::$app->request->post();
        $invoice_type        = trim(htmlspecialchars($request['invoice_type']),' ');
        $invoice_header_type = trim(htmlspecialchars($request['invoice_header_type']),' ');
        $invoice_header      = trim(htmlspecialchars($request['invoice_header']),' ');
        $invoice_content     = trim(htmlspecialchars($request['invoice_content']),' ');
        $model = new Invoice();
        $res=$model->addinvoice($invoice_type,$invoice_header_type,$invoice_header,$invoice_content);
        if ($res['code']==200){
            return Json::encode([
                'code' => 200,
                'msg' => '添加发票成功',
                'data' => '添加发票成功'
            ]);
        }else{
            $code=$res['code'];
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
    }


    /**
     * 线下电商城-获取商品信息
     */
    public function actionGetgoodsdata(){

        $request = Yii::$app->request;
//        var_dump($request);exit;
        if ($request->isPost) {
            $goods_id=trim(htmlspecialchars($request->post('goods_id')),' ');
            $goods_num=trim(htmlspecialchars($request->post('goods_num')),' ');
            $goods_attr=trim(htmlspecialchars($request->post('goods_attr')),' ');
            $model=new GoodsOrder();
            $data=$model->getlinegoodsdata($goods_id,$goods_num,$goods_attr);
            if ($data){
                return Json::encode([
                    'code' => 200,
                    'msg'  => '成功',
                    'data' => $data
                ]);
            }
        }else{
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }

    }


    /**
     * 线下店商城-获取发票信息
     */

    public function  actionGetinvoicelinedata()
    {
        $session = Yii::$app->session;
        $invoicetoken=$session['invoicetoken'];
        $model = new Invoice();
        $data=$model->getlineinvoice($invoicetoken);
        if ($data){
            return Json::encode([
                'code' => 200,
                'msg'  => '成功',
                'data' => $data
            ]);
        }else{
            $code=1017;
            return Json::encode([
                'code' => $code,
                'msg'  => '无发票信息',
                'data' => null
            ]);
        }
    }




    public function  actionAsd()
    {
       echo  Yii::$app->districts['86'];
    }

}