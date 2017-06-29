<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/23 0023
 * Time: 上午 11:48
 */
namespace app\controllers;
use app\models\LaborCost;
use app\services\ExceptionHandleService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;

class QuoteController extends Controller
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
                        'actions' => ['logout', 'about'],
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

    /**
     * 做工标准添加
     * @return string
     */
    public function actionLaborCostAdd()
    {
        $code = 1000;
//        $data = \Yii::$app->request->post();
        $data = [
        [
            'profession'=>'电工',
            'province'=>'510000',
            'city'=>'510100',
            'grade'=>'白银',
            'univalence'=>'300',
            'points'=>'5',
            'worker_kind_details' => '强电',
            'unit'=>'个'
        ],
        [
            'profession'=>'电工',
            'province'=>'510000',
            'city'=>'510100',
            'grade'=>'白银',
            'univalence'=>'300',
            'points'=>'6',
            'worker_kind_details' => '弱电',
            'unit'=>'个'
        ]
        ];
        $labor_cost =  new LaborCost;
        foreach ($data as $one_data)
        {
            $_labor_cost = clone $labor_cost;
            $_labor_cost->province_code = $one_data['province'];
            $_labor_cost->city_code = $one_data['city'];
            $_labor_cost->rank = $one_data['grade'];
            $_labor_cost->worker_kind = $one_data['profession'];
            $_labor_cost->univalence = $one_data['univalence'];
            $_labor_cost->quantity = $one_data['points'];
            $_labor_cost->worker_kind_details = $one_data['worker_kind_details'];
            $_labor_cost->unit = $one_data['unit'];
            $_labor_cost->setAttributes($one_data);
            if (!$_labor_cost->validate())
            {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$_labor_cost->save())
            {
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }
    }

    /*
     * 电工、油漆工、木工、泥瓦工、杂工做工标准修改
     */
    public function actionMultitermEdit()
    {
        $code = 1000;
//        $data = \Yii::$app->request->post();
        $data = [
            [
                'id'=>54,
                'profession'=>'电工',
                'univalence'=>'400',
                'points'=>'6',
                'worker_kind_details' => '强电'
            ],
            [
                'id'=>55,
                'univalence'=>'250',
                'points'=>'6',
                'worker_kind_details' => '弱电'
            ]
        ];
        $labor_cost = new LaborCost();
        foreach ($data as $one_data)
        {
            $_labor_cost = clone $labor_cost;
            $a = $_labor_cost->findOne($one_data['id']);
            $a->univalence = $one_data['univalence'];
            $a->quantity = $one_data['points'];
            $a->worker_kind_details = $one_data['worker_kind_details'];
            $a->setAttributes($one_data);
            if (!$a->validate())
            {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$a->save())
            {
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }
    }

    /**
     * 水路和防水做工标准修改
     * @return string
     */
    public function actionMonomialEdit()
    {
        $code = 1000;
//        $data = \Yii::$app->request->post();
        $data = [
                'id'=>56,
                'univalence'=>'400',
                'points'=>'6',
        ];
        $labor_cost = new LaborCost();
        $_labor_cost = $labor_cost->findOne($data['id']);
        $_labor_cost->univalence = $data['univalence'];
        $_labor_cost->quantity = $data['points'];
            if (!$_labor_cost->validate())
            {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$_labor_cost->save()) {
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
    }



}