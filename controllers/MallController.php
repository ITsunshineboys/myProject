<?php

namespace app\controllers;

use app\models\Carousel;
use app\models\GoodsRecommend;
use app\models\GoodsCategory;
use app\models\Goods;
use app\services\ExceptionHandleService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;

class MallController extends Controller
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
                'only' => [''],
                'rules' => [
                    [
                        'actions' => [''],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
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
     * Carousel action.
     *
     * @return string
     */
    public function actionCarousel()
    {
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'carousel' => Carousel::carousel(),
            ],
        ]);
    }

    /**
     * Recommend goods for type first action.
     *
     * @return string
     */
    public function actionRecommendFirst()
    {
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'recommend-first' => GoodsRecommend::first(),
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
        $getData = Yii::$app->request->get();
        $page = (int)($getData['page'] ?? 1);
        $size = (int)($getData['size'] ?? GoodsRecommend::PAGE_SIZE_DEFAULT);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'recommend-second' => GoodsRecommend::second($page, $size),
            ],
        ]);
    }

    /**
     * Get goods categories action.
     *
     * @return string
     */
    public function actionCategories()
    {
        $pid = (int)Yii::$app->request->get('pid', 0);
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'categories' => GoodsCategory::categoriesByPid(['id', 'title', 'icon'], $pid)
            ],
        ]);
    }

    /**
     * Get category goods action.
     *
     * @return string
     */
    public function actionCategoryGoods()
    {
        $categoryId = (int)Yii::$app->request->get('category_id', 0);
        $code = 1000;
        if (!$categoryId) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $orderBy = trim(Yii::$app->request->get('order_by', ''));
        $orderByArr = [];
        if ($orderBy) {
            if (stripos($orderBy, Goods::ORDERBY_SEPARATOR) === false) {
                $orderByArr[$orderBy] = SORT_DESC;
            } else {
                list($field, $direction) = explode(Goods::ORDERBY_SEPARATOR, $orderBy);
                if ($field) {
                    $orderByArr[$field] = !empty($direction) ? (int)$direction : SORT_DESC;
                }
            }
        }

        $select = ['id', 'title', 'subtitle', 'platform_price', 'comment_number', 'favourable_comment_rate'];
        $categoryGoods = $orderByArr ? Goods::findByCategoryId($categoryId, $select, $orderByArr) : Goods::findByCategoryId($categoryId, $select);
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'category_goods' => $categoryGoods,
            ],
        ]);
    }
}