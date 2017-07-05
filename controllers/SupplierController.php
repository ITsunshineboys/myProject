<?php

namespace app\controllers;

use app\services\ExceptionHandleService;
use app\models\Supplier;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use Yii;

class SupplierController extends Controller
{
    /**
     * Actions accessed by logged-in users
     */
    const ACCESS_LOGGED_IN_USER = [
        'certification',
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
                    'certification' => ['post',],
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
     * Supplier certification action(app)
     *
     * @return string
     */
    public function actionCertification()
    {
        $code = 1000;

        $user = Yii::$app->user->identity;

        if (Supplier::find()->where(['uid' => $user->id])->exists()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $code = Supplier::add($user, Yii::$app->request->post());
        if (200 != $code) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => $code,
            'msg' => 'OK',
        ]);
    }
}