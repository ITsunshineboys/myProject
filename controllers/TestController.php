<?php

namespace app\controllers;

use app\models\Carousel;
use app\models\Goods;
use app\models\GoodsOrder;
use app\models\OrderGoods;
use app\models\OrderPlatForm;
use app\models\ShippingCart;
use app\models\Supplier;
use app\models\User;
use app\models\UserRole;
use app\services\ExceptionHandleService;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;

class TestController extends Controller
{
    /**
     * Actions accessed by logged-in users
     */
    const ACCESS_LOGGED_IN_USER = [
        'cache-delete',
        'cache-delete-all',
//        'reset-mobile-pwd',
        'goods-qr-gen',
        'register-user',
        'upload',
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
                    'cache-delete' => ['post',],
                    'cache-delete-all' => ['post',],
                    'reset-mobile-pwd' => ['post',],
                    'register-user' => ['post',],
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
     * Delete cache action.
     *
     * @return string
     */
    public function actionCacheDelete()
    {
        $key = trim(Yii::$app->request->post('key', ''));
        return Yii::$app->cache->delete($key);
    }

    /**
     * Delete all cache action.
     *
     * @return string
     */
    public function actionCacheDeleteAll()
    {
        return Yii::$app->cache->flush();
    }

    /**
     * Reset user's new mobile and new password
     *
     * @return bool
     */
    public function actionResetMobilePwd()
    {
        $mobile = Yii::$app->request->post('mobile');
        $newMobile = Yii::$app->request->post('new_mobile');
        $pwd = Yii::$app->request->post('pwd');
        return User::resetMobileAndPwdByMobile($mobile, $newMobile, $pwd);
    }

    /**
     * Generate goods qr code image
     */
    public function actionGoodsQrGen()
    {
        $id = (int)Yii::$app->request->get('id', 0);
        if ($id > 0) {
            $goods = Goods::findOne($id);
            $goods && $goods->generateQrCodeImage();
        }
    }

    /**
     * Register user
     *
     * @return string
     */
    public function actionRegisterUser()
    {
        $res = User::register(Yii::$app->request->post(), false);
        echo is_array($res) ? 'ok' : 'failed';
        Yii::$app->trigger(Yii::$app->params['events']['async']);
    }

    /**
     * Upload test
     *
     * @return string
     */
    public function actionUpload()
    {
        return $this->render('upload');
    }

    /**
     * Login test
     *
     * @return string
     */
    public function actionLogin()
    {
        return $this->render('login');
    }

    /**
     * Test
     */
    public function actionTest()
    {
        $users = \Yii::$app->user->identity;
        $user=User::findOne($users->id);
      $users->icon=13;
      $users->save(false);
    }

    /**
     * Test wxa
     */
    public function actionWx()
    {
        return $this->render('wx');
    }


    public  function  actionReturnPost()
    {
        $data =Yii::$app->request->post();
        echo json_encode($data);
    }


    public  function  actionUpData()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $lists=ShippingCart::find()
            ->where(['uid'=>$user->id,'role_id'=>$user->last_role_id_app])
            ->all();
        foreach ($lists as &$list)
        {
            $list->delete();
        }
    }

    public  function  actionDelInvalidData()
    {
        $GoodsOrder=GoodsOrder::find()->all();
        foreach ($GoodsOrder as &$list)
        {
            $supplier=Supplier::findOne($list->supplier_id);
            if (!$supplier)
            {
                $OrderGoods=OrderGoods::find()->where(['order_no'=>$list->order_no])->all();
                foreach ($OrderGoods as &$orderGoods)
                {
                    $res1=$orderGoods->delete();
                    if (!$res1)
                    {
                        echo 2;
                    }
                }
                $res=$list->delete();
                if (!$res)
                {
                    echo 2;
                }
            }
        }
        echo 1;
    }


    /**
     * @return string
     */
    public  function  actionBalanceAdd()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user=User::findOne($user->id);
        $user->balance=100000000;
        $user->availableamount=100000000;
        $user->save(false);
    }

    /**
     * @return string
     */
    public  function  actionBalanceDelete()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user=User::findOne($user->id);
        $supplier=Supplier::find()->where(['uid'=>$user->id])->one();
        $supplier->balance=0;
        $supplier->availableamount=0;
        $supplier->save(false);
    }

    /**
     * 获取支付测试数据
     * @return string
     */
    public function actionAliPayGetNotify(){
        $data=(new Query())->from('alipayreturntest')->all();
        return Json::encode([
            'code' => 200,
            'msg'  => 'ok',
            'data' => $data
        ]);
    }

    /**
     * 测试接口
     * @return int
     */
    public  function  actionPlatformUp()
    {
        $request    = Yii::$app->request;
        $order_no   = trim($request->post('order_no',''));
        $sku        = trim($request->post('sku',''));
        $handle_type= trim($request->post('handle_type',''));

        $OrderPlatForm=OrderPlatForm::find()
            ->where(['order_no'=>$order_no])
            ->andWhere(['sku'=>$sku])
            ->one();
        $OrderPlatForm->handle=$handle_type;
        $res=$OrderPlatForm->save(false);
        if (!$res){
            $code=500;
            return $code;
        }
    }

    public  function  actionTestData()
    {
        $arr[]=[
               "uid"=> "2",
               'role_id'=>'7',
               'id'=>'56',
               'content'=>'uploads/2017/12/16/1513412632.jpg'
           ] ;
        $arr[]=[
            "uid"=> "8",
            'role_id'=>'6',
            'id'=>'79',
            'content'=>'uploads/2017/12/16/1513412632.jpg'
        ] ;
        $arr[]=[
            "uid"=> "8",
            'role_id'=>'7',
            'id'=>'111',
            'content'=>'uploads/2017/12/16/1513412632.jpg'
        ] ;
        $arr[]=[
            "uid"=> "11",
            'role_id'=>'7',
            'id'=>'60',
            'content'=>'uploads/2017/12/16/1513412632.jpg'
        ] ;
        $arr[]=[
            "uid"=> "40",
            'role_id'=>'7',
            'id'=>'27',
            'content'=>'uploads/2017/12/16/1513412632.jpg'
        ] ;
        $arr[]=[
            "uid"=> "8",
            'role_id'=>'6',
            'id'=>'77',
            'content'=>'uploads/2017/12/16/1513412632.jpg'
        ] ;
        $arr[]=[
            "uid"=> "8",
            'role_id'=>'7',
            'id'=>'76',
            'content'=>'uploads/2017/12/16/1513412632.jpg'
        ];
//        foreach ($arr as $k =>$v)
//        {
//            $a[$k]=0;
//              foreach ( $arr as $key =>$val)
//              {
//                if ($arr[$k]['uid']==$arr[$key]['uid']
//                    && $arr[$k]['role_id']==$arr[$key]['role_id']
//                    && $arr[$k]['id']<$arr[$key]['id']
//                )
//                {
//                    $a[$k]=1;
//                }
//              }
//              if ($a[$k]==1)
//              {
//                  unset($arr[$k]);
//              }
//        }
//        var_dump($arr);die;
        $filtered = array_filter($arr, function($item){

            return $item['id'] !== '76';
        });
        print_r($filtered);
    }
}
