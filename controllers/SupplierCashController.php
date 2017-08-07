<?php

namespace app\controllers;


use app\models\Supplier;
use app\models\Supplieramountmanage;
use app\models\SupplierCashManager;
use app\services\ExceptionHandleService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;

class SupplierCashController extends Controller
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
     * 获取商家现金流列表
     * @return mixed
     */
    public function actionGetCashList()
    {
        $user = \Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = \Yii::$app->request;
        $page = (int)trim(htmlspecialchars($request->get('page', 1)), '');
        $page_size = (int)trim(htmlspecialchars($request->get('page_size', 15)), '');
        $time_id = (int)trim(htmlspecialchars($request->post('time_id', 0)), '');
        $time_start = trim(htmlspecialchars($request->post('time_start', '')), '');
        $time_end = trim(htmlspecialchars($request->post('time_end', '')), '');
        if ($time_id == 5) {
            if (!$time_start || !$time_end) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        $supplier = Supplier::find()->select('id')->where(['uid' => $user->id])->one();
        $data = (new SupplierCashManager())->GetCashList($supplier['id'], $page, $page_size, $time_id, $time_start, $time_end);
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    /**
     * 获取单条现金流
     * @return mixed
     */
    public function actionGetCash()
    {
        $user = \Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = \Yii::$app->request;
        $cash_id = (int)trim(htmlspecialchars($request->post('cash_id', '')), '');
        if (!$cash_id) {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => \Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
        $supplier = Supplier::find()->select('id')->where(['uid' => $user->id])->one();
        $data = (new SupplierCashManager())->GetCash($supplier['id'], $cash_id);
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }


}