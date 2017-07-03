<?php

namespace app\controllers;

use app\models\Carousel;
use app\models\GoodsRecommend;
use app\models\GoodsCategory;
use app\models\User;
use app\services\ExceptionHandleService;
use app\services\StringService;
use Yii;
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
        'reset-mobile-pwd',
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
}