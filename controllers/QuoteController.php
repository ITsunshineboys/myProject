<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/23 0023
 * Time: 上午 11:48
 */
namespace app\controllers;
use app\models\DecorationAdd;
use app\models\EngineeringStandardCraft;
use app\models\Goods;
use app\models\GoodsAttr;
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
            ],
            [
                'id'=>55,
                'univalence'=>'250',
                'points'=>'6',
            ]
        ];
        $labor_cost = new LaborCost();
        foreach ($data as $one_data)
        {
            $_labor_cost = clone $labor_cost;
            $a = $_labor_cost->findOne($one_data['id']);
            $a->univalence = $one_data['univalence'];
            $a->quantity = $one_data['points'];
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
//        $post = \Yii::$app->request->post();
//        $data = Json::decode($post);
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

    /**
     * 工程标准工艺标准修改
     * @return string
     */
    public function actionCraftEdit()
    {
        $code = 1000;
//        $data = \Yii::$app->request->post();
        $data = [
            [
                'id'=>1,
                'material'=>'11'
            ],
            [
                'id'=>2,
                'material'=>'15'
             ]
        ];
        $engineering_standard_craft = new EngineeringStandardCraft();
        foreach ($data as $one_data)
        {
            $craft = clone $engineering_standard_craft;
            $_craft = $craft->findOne($one_data['id']);
            $_craft->material = $one_data['material'];
            $_craft->setAttributes($one_data);
            if (!$_craft->validate())
            {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$_craft->save())
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
     * 工程标准添加材料项
     * @return string
     */
    public function actionMaterialAddInquire()
    {
//        $post = \Yii::$app->request->post();
//        $data = Json::encode($post);
        $data = '河沙';
        $goods = Goods::newMaterialAdd(3,$data);
        $goods_attr = GoodsAttr::findByGoodsId($goods['id']);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'goods' => [
                   'goods'=> $goods,
                   'goods_attr'=> $goods_attr,
                ],
            ],
        ]);
    }

    /**
     * 工程标准添加材料项
     * @return string
     */
    public function actionMaterialAdd()
    {
        $code = 1000;
//        $post = \Yii::$app->request->post();
//        $data = Json::decode($post);
        $data = [
           [
                'project' => '电工',
                'material'=>'石灰粉',
//                'series_id'=>'11',
                'min_area'=>'60',
                'max_area'=>'69',
                'quantity'=>'1',
                'sku'=>'123',
                'district_code'=>'510100',
           ],
           [
               'project' => '电工',
               'material'=>'石灰粉',
//               'series_id'=>'12',
               'min_area'=>'70',
               'max_area'=>'79',
               'quantity'=>'3',
               'sku'=>'51231',
               'district_code'=>'510100',
           ]
        ];
        $decoration_add = new DecorationAdd();
        foreach ($data as $one_data)
        {
            $add = clone $decoration_add;
            if (array_key_exists('series_id',$one_data) == true)
            {
                $add->series_id = $one_data['series_id'];
            }
            if (array_key_exists('style_id',$one_data) == true)
            {
                $add->style_id = $one_data['style_id'];
            }
            if (array_key_exists('min_area',$one_data) == true)
            {
                $add->min_area = $one_data['min_area'];
                $add->max_area = $one_data['max_area'];
            }
            $add->project = $one_data['project'];
            $add->material = $one_data['material'];
            $add->sku = $one_data['sku'];
            $add->district_code = $one_data['district_code'];
            $add->quantity = $one_data['quantity'];
            $add->setAttributes($one_data);
            if (!$add->validate())
            {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$add->save())
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
     * 工程标准修改材料项
     * @return string
     */
    public function actionMaterialEdit()
    {
        $code = 1000;
//        $post = \Yii::$app->request->post();
//        $data = Json::decode($post);
        $data = [
            [
                'id'=>27,
                'project' => '电工',
                'material'=>'石灰粉',
//                'series_id'=>'11',
                'min_area'=>'60',
                'max_area'=>'69',
                'quantity'=>'1',
                'sku'=>'112233',
                'district_code'=>'510100',
            ],
            [
                'id'=>28,
                'project' => '电工',
                'material'=>'石灰粉',
//               'series_id'=>'12',
                'min_area'=>'70',
                'max_area'=>'79',
                'quantity'=>'3',
                'sku'=>'332211',
                'district_code'=>'510100',
            ]
        ];
        $decoration_add = new DecorationAdd();
        foreach ($data as $one_data)
        {
            $add = clone $decoration_add;
            $_add = $add->findOne($one_data['id']);
            if (array_key_exists('series_id',$one_data) == true)
            {
                $_add->series_id = $one_data['series_id'];
            }
            if (array_key_exists('style_id',$one_data) == true)
            {
                $_add->style_id = $one_data['style_id'];
            }
            if (array_key_exists('min_area',$one_data) == true)
            {
                $_add->min_area = $one_data['min_area'];
                $_add->max_area = $one_data['max_area'];
            }
            $_add->project = $one_data['project'];
            $_add->material = $one_data['material'];
            $_add->sku = $one_data['sku'];
            $_add->district_code = $one_data['district_code'];
            $_add->quantity = $one_data['quantity'];
            $_add->setAttributes($one_data);
            if (!$_add->validate())
            {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$_add->save())
            {
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }
    }

}