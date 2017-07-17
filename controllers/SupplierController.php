<?php

namespace app\controllers;

use app\services\ExceptionHandleService;
use app\models\Supplier;
use app\models\Goods;
use app\models\GoodsRecommendSupplier;
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
        'certification-view',
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

    /**
     * Certification view action
     *
     * @return string
     */
    public function actionCertificationView()
    {
        $user = Yii::$app->user->identity;
        $supplier = Supplier::find()->where(['uid' => $user->id])->one();
        $supplier->status = Supplier::STATUS_APPROVED;

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'certification-view' => $supplier->viewCertification(),
            ],
        ]);
    }

    /**
     * View supplier action
     *
     * @return string
     */
    public function actionView()
    {
        $code = 1000;

        $id = (int)Yii::$app->request->get('id', 0);
        if ($id <= 0) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $supplier = Supplier::findOne($id);

        if (!$supplier) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'supplier-view' => $supplier->view(),
            ],
        ]);
    }

    /**
     * Get supplier goods action.
     *
     * @return string
     */
    public function actionGoods()
    {
        $code = 1000;

        $supplierId = (int)Yii::$app->request->get('supplier_id', 0);
        if (!$supplierId) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $sort = Yii::$app->request->get('sort', []);
        if ($sort) {
            foreach ($sort as &$v) {
                if (stripos($v, 'sold_number') !== false) {
                    $v = 'sold_number:' . SORT_DESC;
                    break;
                }
            }

            $model = new Goods;
            $orderBy = $sort ? ModelService::sortFields($model, $sort) : ModelService::sortFields($model);
            if ($orderBy === false) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }

        $ret = [
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'supplier_goods' => [],
            ],
        ];

        $where = "supplier_id = {$supplierId} and status = " . Goods::STATUS_ONLINE;

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', Goods::PAGE_SIZE_DEFAULT);
        $select = Goods::CATEGORY_GOODS_APP;

        $supplierGoods = $sort
            ? Goods::pagination($where, $select, $page, $size, $orderBy)
            : Goods::pagination($where, $select, $page, $size);
        $ret['data']['supplier_goods'] = $supplierGoods;
        return Json::encode($ret);
    }

    /**
     * Supplier carousel action.
     *
     * @return string
     */
    public function actionCarousel()
    {
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'carousel' => GoodsRecommendSupplier::carousel(),
            ],
        ]);
    }

    /**
     * Recommend goods for type second action.
     *
     * @return string
     */
    public function actionRecommendSecond()
    {
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', GoodsRecommendSupplier::PAGE_SIZE_DEFAULT);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'recommend_second' => GoodsRecommendSupplier::second(null, $page, $size),
            ],
        ]);
    }
}