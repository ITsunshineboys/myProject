<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/27 0027
 * Time: 下午 15:14
 */

namespace app\controllers;

use app\models\Worker;
use app\models\WorkerOrder;
use app\services\ExceptionHandleService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class WorkerManagementController extends Controller
{
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
                'only' => ['logout', 'about'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post',],
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

    public function actionHomepageList()
    {
        // 工人总数
        $worker_count = Worker::find()->count('id');
        //工人订单
        $worker_order = WorkerOrder::find()->groupBy('order_no')->count('id');
        //工人总金额
//        $worker_picer = WorkerOrder::
        var_dump($worker_order);exit;
    }

}