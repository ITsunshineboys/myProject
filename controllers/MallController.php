<?php

namespace app\controllers;

use app\models\GoodsBrand;
use app\models\GoodsRecommend;
use app\models\GoodsCategory;
use app\models\Goods;
use app\models\GoodsRecommendViewLog;
use app\models\Supplier;
use app\services\ExceptionHandleService;
use app\services\StringService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Controller;

class MallController extends Controller
{
    /**
     * Actions accessed by logged-in users
     */
    const ACCESS_LOGGED_IN_USER = [
        'recommend-admin-index',
        'recommend-disable-batch',
        'recommend-delete-batch',
        'recommend-delete',
        'recommend-status-toggle',
        'recommend-history',
        'recommend-second-admin',
        'recommend-by-sku',
        'recommend-add',
        'recommend-edit',
        'recommend-sort',
        'recommend-click-record',
        'carousel-admin',
        'review-supplier-category',
        'categories-admin',
        'category-admin',
        'category-status-toggle',
        'category-disable-batch',
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
                    'toggle-banner-status' => ['post',],
                    'delete-banner' => ['post',],
                    'recommend-add' => ['post',],
                    'recommend-edit' => ['post',],
                    'recommend-sort' => ['post',],
                    'recommend-delete-batch' => ['post',],
                    'recommend-delete' => ['post',],
                    'recommend-status-toggle' => ['post',],
                    'recommend-click-record' => ['post',],
                    'recommend-disable-batch' => ['post',],
                    'review-supplier-category' => ['post',],
                    'category-add' => ['post',],
                    'category-status-toggle' => ['post',],
                    'category-disable-batch' => ['post',],
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
                'recommend_first' => GoodsRecommend::first(),
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
                'recommend_second' => GoodsRecommend::second($page, $size),
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
        $categories = GoodsCategory::categoriesByPid(GoodsCategory::APP_FIELDS, $pid);
        $pid == 0 && array_unshift($categories, GoodsCategory::forAll());
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'categories' => $categories
            ],
        ]);
    }

    /**
     * Get goods categories action(admin).
     *
     * @return string
     */
    public function actionCategoriesAdmin()
    {
        $pid = (int)Yii::$app->request->get('pid', 0);
        $categories = GoodsCategory::categoriesByPid(GoodsCategory::APP_FIELDS, $pid);

        $user = Yii::$app->user->identity;
        if ($user->login_role_id == Yii::$app->params['supplierRoleId'] && $pid > 0) {
            array_unshift($categories, GoodsCategory::forCurrent());
        } elseif ($user->login_role_id == Yii::$app->params['lhzzRoleId']) {
            array_unshift($categories, GoodsCategory::forCurrent());
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'categories' => $categories
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
     * Toggle recommend status action.
     *
     * @return string
     */
    public function actionRecommendStatusToggle()
    {
        $id = (int)Yii::$app->request->post('id', 0);

        $code = 1000;

        if (!$id) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $recommend = GoodsRecommend::findOne($id);
        if (!$recommend) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($recommend->status == GoodsRecommend::STATUS_ONLINE) {
            $recommend->status = GoodsRecommend::STATUS_OFFLINE;
        } else {
            $recommend->status = GoodsRecommend::STATUS_ONLINE;
        }

        if (!$recommend->save()) {
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
     * Delete recommend record action.
     *
     * @return string
     */
    public function actionRecommendDelete()
    {
        $id = (int)Yii::$app->request->post('id', 0);

        $code = 1000;

        if (!$id) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $recommend = GoodsRecommend::findOne($id);
        if (!$recommend) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($recommend->delete_time > 0) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($recommend->status != GoodsRecommend::STATUS_OFFLINE) {
            $code = 1003;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $recommend->delete_time = time();
        if (!$recommend->save()) {
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
     * Delete recommend records in batches action.
     *
     * @return string
     */
    public function actionRecommendDeleteBatch()
    {
        $ids = trim(Yii::$app->request->post('ids', ''));
        $ids = trim($ids, ',');

        $code = 1000;

        if (!$ids) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $canDelete = GoodsRecommend::canDelete($ids);
        if (false === $canDelete) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        } elseif (-1 === $canDelete) {
            $code = 1003;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = 'id in(' . $ids . ')';
        if (!GoodsRecommend::updateAll([
            'delete_time' => time()
        ], $where)
        ) {
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
     * Disable recommend records in batches action.
     *
     * @return string
     */
    public function actionRecommendDisableBatch()
    {
        $ids = trim(Yii::$app->request->post('ids', ''));
        $ids = trim($ids, ',');

        $code = 1000;

        if (!$ids) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $canDisable = GoodsRecommend::canDisable($ids);
        if (!$canDisable) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = 'id in(' . $ids . ')';
        if (!GoodsRecommend::updateAll([
            'status' => GoodsRecommend::STATUS_OFFLINE,
        ], $where)
        ) {
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
     * Recommend history action(admin)
     *
     * @return string
     */
    public function actionRecommendHistory()
    {
        $code = 1000;

        $timeType = trim(Yii::$app->request->get('time_type', ''));
        if (!$timeType || !in_array($timeType, array_keys(Yii::$app->params['timeTypes']))) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $type = (int)Yii::$app->request->get('type', GoodsRecommend::RECOMMEND_GOODS_TYPE_CAROUSEL);
        if (!in_array($type, GoodsRecommend::$types)) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = 'delete_time > 0 and type = ' . $type;

        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));

            if (($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime))
            ) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            $endTime && $endTime .= ' 23:59:59';
        } else {
            list($startTime, $endTime) = StringService::startEndDate($timeType);
        }

        if ($startTime) {
            $startTime = strtotime($startTime);
            $startTime && $where .= " and create_time >= {$startTime}";
        }
        if ($endTime) {
            $endTime = strtotime($endTime);
            $endTime && $where .= " and create_time <= {$endTime}";
        }

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', GoodsRecommend::PAGE_SIZE_DEFAULT);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'recommend_history' => [
                    'total' => (int)GoodsRecommend::find()->where($where)->asArray()->count(),
                    'details' => GoodsRecommend::pagination($where, GoodsRecommend::$adminFields, $page, $size)
                ]
            ],
        ]);
    }

    /**
     * Recommend index action(admin)
     *
     * @return string
     */
    public function actionRecommendAdminIndex()
    {
        $type = (int)Yii::$app->request->get('type', GoodsRecommend::RECOMMEND_GOODS_TYPE_CAROUSEL);

        if (!in_array($type, GoodsRecommend::$types)) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = 'delete_time = 0 and type = ' . $type;

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'recommend_admin_index' => [
                    'details' => GoodsRecommend::pagination($where, GoodsRecommend::$adminFields, 1, GoodsRecommend::PAGE_SIZE_DEFAULT_ADMIN_INDEX)
                ]
            ],
        ]);
    }

    /**
     * Get recommend by sku action
     *
     * @return string
     */
    public function actionRecommendBySku()
    {
        $code = 1000;

        $sku = (int)Yii::$app->request->get('sku', '');

        if (!$sku) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $ret = [
            'code' => 200,
            'msg' => 'OK',
            'data' => [],
        ];

        $goods = Goods::findBySku($sku, ['id', 'title', 'subtitle', 'platform_price']);
        if ($goods) {
            $ret['data'] = [
                'detail' => [
                    'title' => $goods->title,
                    'subtitle' => $goods->subtitle,
                    'platform_price' => $goods->platform_price,
                    'url' => Url::to([Goods::GOODS_DETAIL_URL_PREFIX . $goods->id], true),
                ],
            ];
        }

        return Json::encode($ret);
    }

    /**
     * Add recommend action
     *
     * @return string
     */
    public function actionRecommendAdd()
    {
        $recommend = new GoodsRecommend;
        $recommend->attributes = Yii::$app->request->post();

        $code = 1000;

        if (!$recommend->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($recommend->sku) {
            $goods = Goods::find()->where(['sku' => $recommend->sku])->one();
            $supplier = Supplier::findOne($goods->supplier_id);
            $recommend->supplier_id = $supplier->id;
            $recommend->supplier_name = $supplier->nickname;
            $recommend->url = Goods::GOODS_DETAIL_URL_PREFIX . $goods->id;
        }

        if (!$recommend->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
        ]);
    }

    /**
     * Edit recommend action
     *
     * @return string
     */
    public function actionRecommendEdit()
    {
        $code = 1000;

        $id = (int)Yii::$app->request->post('id', 0);
        $recommend = GoodsRecommend::findOne($id);
        if (!$recommend) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $postData = Yii::$app->request->post();
        if (isset($postData['id'])) {
            unset($postData['id']);
        }
        if (isset($postData['supplier_id'])) {
            unset($postData['supplier_id']);
        }
        if (isset($postData['supplier_name'])) {
            unset($postData['supplier_name']);
        }
        if (isset($postData['url'])) {
            unset($postData['url']);
        }
        $recommend->attributes = $postData;

        if (!empty($postData['sku'])) {
            $goods = Goods::find()->where(['sku' => $recommend->sku])->one();
            $supplier = Supplier::findOne($goods->supplier_id);
            $recommend->supplier_id = $supplier->id;
            $recommend->supplier_name = $supplier->nickname;
            $recommend->url = Goods::GOODS_DETAIL_URL_PREFIX . $goods->id;
        }

        if (!$recommend->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$recommend->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
        ]);
    }

    /**
     * Sort recommend action
     *
     * @return string
     */
    public function actionRecommendSort()
    {
        $ids = trim(Yii::$app->request->post('ids', ''));
        $ids = trim($ids, ',');

        $idArr = explode(',', $ids);
        $code = GoodsRecommend::sort($idArr);

        return Json::encode([
            'code' => 200,
            'msg' => 200 == $code ? 'OK' : Yii::$app->params['errorCodes'][$code],
        ]);
    }

    /**
     * Log recommend click action
     *
     * @return string
     */
    public function actionRecommendClickRecord()
    {
        $code = 1000;

        $recommendViewLog = new GoodsRecommendViewLog;
        $recommendViewLog->attributes = Yii::$app->request->post();
        $recommendViewLog->ip = ip2long(Yii::$app->request->userIP);

        if (!$recommendViewLog->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$recommendViewLog->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
        ]);
    }

    /**
     * Review supplier category action.
     *
     * @return string
     */
    public function actionReviewSupplierCategory()
    {
        $user = Yii::$app->user->identity;
        if (!$user || $user->login_role_id != Yii::$app->params['lhzzRoleId']) {
            $code = 403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $code = 1000;

        $id = (int)Yii::$app->request->post('id', 0);
        $goodsCategory = GoodsCategory::findOne($id);
        if (!$goodsCategory) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $goodsCategory->reason = trim(Yii::$app->request->post('reason', ''));
        $goodsCategory->review_status = (int)Yii::$app->request->post('review_status');
        $goodsCategory->scenario = GoodsCategory::SCENARIO_REVIEW;
        if (!$goodsCategory->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$goodsCategory->save()) {
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
     * Add category action
     *
     * @return string
     */
    public function actionCategoryAdd()
    {
        $category = new GoodsCategory;
        $category->attributes = Yii::$app->request->post();

        $code = 1000;

        if (!$category->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$category->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
        ]);
    }

    /**
     * Toggle category status action.
     *
     * @return string
     */
    public function actionCategoryStatusToggle()
    {
        $id = (int)Yii::$app->request->post('id', 0);

        $code = 1000;

        if (!$id) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $model = GoodsCategory::findOne($id);
        if (!$model) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $now = time();
        if ($model->deleted == GoodsCategory::STATUS_ONLINE) {
            $model->deleted = GoodsCategory::STATUS_OFFLINE;
            $model->online_time = $now;
        } else {
            $model->deleted = GoodsCategory::STATUS_ONLINE;
            $model->offline_time = $now;
        }

        if (!$model->save()) {
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
     * Disable category records in batches action.
     *
     * @return string
     */
    public function actionCategoryDisableBatch()
    {
        $ids = trim(Yii::$app->request->post('ids', ''));
        $ids = trim($ids, ',');

        $code = 1000;

        if (!$ids) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $canDisable = GoodsCategory::canDisable($ids);
        if (!$canDisable) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = 'id in(' . $ids . ')';
        if (!GoodsCategory::updateAll([
            'deleted' => GoodsCategory::STATUS_ONLINE,
            'offline_time' => time()
        ], $where)
        ) {
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
     * Admin category list action
     *
     * @return string
     */
    public function actionCategoryListAdmin()
    {// todo
        $code = 1000;

        $timeType = trim(Yii::$app->request->get('time_type', ''));
        if (!$timeType || !in_array($timeType, array_keys(Yii::$app->params['timeTypes']))) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $type = (int)Yii::$app->request->get('type', GoodsRecommend::RECOMMEND_GOODS_TYPE_CAROUSEL);
        if (!in_array($type, GoodsRecommend::$types)) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = 'delete_time > 0 and type = ' . $type;

        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));

            if (($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime))
            ) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            $endTime && $endTime .= ' 23:59:59';
        } else {
            list($startTime, $endTime) = StringService::startEndDate($timeType);
        }

        if ($startTime) {
            $startTime = strtotime($startTime);
            $startTime && $where .= " and create_time >= {$startTime}";
        }
        if ($endTime) {
            $endTime = strtotime($endTime);
            $endTime && $where .= " and create_time <= {$endTime}";
        }

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', GoodsRecommend::PAGE_SIZE_DEFAULT);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'recommend_history' => [
                    'total' => (int)GoodsRecommend::find()->where($where)->asArray()->count(),
                    'details' => GoodsRecommend::pagination($where, GoodsRecommend::$adminFields, $page, $size)
                ]
            ],
        ]);
    }
}