<?php

namespace app\controllers;

use app\models\GoodsBrand;
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
                'only' => ['toggle-banner-status', 'delete-banner', 'banner-history', 'recommend-second-admin', 'carousel-admin'],
                'rules' => [
                    [
                        'actions' => ['toggle-banner-status', 'delete-banner', 'banner-history', 'recommend-second-admin', 'carousel-admin'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'toggle-banner-status' => ['post',],
                    'delete-banner' => ['post',],
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
                'carousel' => GoodsRecommend::carousel(),
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
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', GoodsRecommend::PAGE_SIZE_DEFAULT);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'recommend-second' => GoodsRecommend::second($page, $size),
            ],
        ]);
    }

    /**
     * Recommend goods for type second action(admin).
     *
     * @return string
     */
    public function actionRecommendSecondAdmin()
    {
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'recommend-second-admin' => GoodsRecommend::find()->select([])->where(['type' => GoodsRecommend::RECOMMEND_GOODS_TYPE_SECOND, 'delete_time' => 0])->asArray()->all()
            ],
        ]);
    }

    /**
     * Get carousel action(admin).
     *
     * @return string
     */
    public function actionCarouselAdmin()
    {
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'carousel-admin' => GoodsRecommend::find()->select([])->where(['type' => GoodsRecommend::RECOMMEND_GOODS_TYPE_CAROUSEL, 'delete_time' => 0])->asArray()->all()
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

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', Goods::PAGE_SIZE_DEFAULT);
        $select = ['id', 'title', 'subtitle', 'platform_price', 'comment_number', 'favourable_comment_rate', 'image1'];
        $categoryGoods = $orderByArr ? Goods::findByCategoryId($categoryId, $select, $page, $size, $orderByArr) : Goods::findByCategoryId($categoryId, $select, $page, $size);
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'category_goods' => $categoryGoods,
            ],
        ]);
    }

    /**
     * Search brands action.
     *
     * @return string
     */
    public function actionSearch()
    {
        $brands = [];

        $keyword = trim(Yii::$app->request->get('keyword', ''));
        if ($keyword) {
            $brands = GoodsBrand::findByName($keyword, ['id', 'name', 'logo']);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'brands' => $brands,
            ],
        ]);
    }

    /**
     * Brand goods action.
     *
     * @return string
     */
    public function actionBrandGoods()
    {
        $brandId = (int)Yii::$app->request->get('brand_id', 0);
        $code = 1000;
        if (!$brandId) {
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

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', Goods::PAGE_SIZE_DEFAULT);
        $select = ['id', 'title', 'subtitle', 'platform_price', 'comment_number', 'favourable_comment_rate', 'image1'];
        $goods = $orderByArr ? Goods::findByBrandId($brandId, $select, $page, $size, $orderByArr) : Goods::findByBrandId($brandId, $select, $page, $size);
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'brand_goods' => $goods,
            ],
        ]);
    }

    /**
     * Toggle banner status action.
     *
     * @return string
     */
    public function actionToggleBannerStatus()
    {
        $bannerId = (int)Yii::$app->request->post('banner_id', 0);

        $code = 1000;

        if (!$bannerId) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $banner = GoodsRecommend::findOne($bannerId);
        if (!$banner) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($banner->status == GoodsRecommend::STATUS_ONLINE) {
            $banner->status = GoodsRecommend::STATUS_OFFLINE;
        } else {
            $banner->status = GoodsRecommend::STATUS_ONLINE;
        }

        if (!$banner->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK'
        ]);
    }

    /**
     * Delete banner action.
     *
     * @return string
     */
    public function actionDeleteBanner()
    {
        $bannerId = (int)Yii::$app->request->post('banner_id', 0);

        $code = 1000;

        if (!$bannerId) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $banner = GoodsRecommend::findOne($bannerId);
        if (!$banner) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($banner->status != GoodsRecommend::STATUS_OFFLINE) {
            $code = 1003;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $banner->delete_time = time();
        if (!$banner->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK'
        ]);
    }

    public function actionBannerHistory()
    {
        $startTime = (int)Yii::$app->request->get('start_time', 0);
        $endTime = (int)Yii::$app->request->get('end_time', 0);
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', Goods::PAGE_SIZE_DEFAULT);

        $where = 'delete_time > 0';
        if ($startTime) {
            $where .= " and create_time >= {$startTime}";
        }
        if ($endTime) {
            $where .= " and create_time <= {$endTime}";
        }

        $select = ['id', 'sku', 'title', 'from_type', 'viewed_number', 'status', 'create_time', 'image'];

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'banner-history' => [
                    'total' => (int)GoodsRecommend::find()->where($where)->asArray()->count(),
                    'details' => GoodsRecommend::history(0, 0, $select, $page, $size)
                ]
            ],
        ]);
    }
}