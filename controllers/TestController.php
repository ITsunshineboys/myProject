<?php

namespace app\controllers;

use app\models\Carousel;
use app\models\Goods;
use app\models\User;
use app\services\ExceptionHandleService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
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
        return time() . 'a';
    }
}
