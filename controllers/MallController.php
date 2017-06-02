<?php

namespace app\controllers;

use app\models\BrandCategory;
use app\models\GoodsBrand;
use app\models\GoodsRecommend;
use app\models\GoodsCategory;
use app\models\Goods;
use app\models\GoodsRecommendViewLog;
use app\models\Supplier;
use app\models\Lhzz;
use app\services\ExceptionHandleService;
use app\services\StringService;
use app\services\ModelService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
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
        'category-enable-batch',
        'category-list-admin',
        'categories-manage-admin',
        'category-add',
        'category-edit',
        'category-offline-reason-reset',
        'category-review-list',
        'brand-add',
        'brand-review',
        'brand-edit',
        'brand-offline-reason-reset',
        'brand-status-toggle',
        'brand-disable-batch',
        'brand-enable-batch',
        'brand-review-list',
        'brand-list-admin',
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
                    'category-edit' => ['post',],
                    'category-status-toggle' => ['post',],
                    'category-disable-batch' => ['post',],
                    'category-enable-batch' => ['post',],
                    'category-offline-reason-reset' => ['post',],
                    'brand-add' => ['post',],
                    'brand-review' => ['post',],
                    'brand-edit' => ['post',],
                    'brand-offline-reason-reset' => ['post',],
                    'brand-status-toggle' => ['post',],
                    'brand-disable-batch' => ['post',],
                    'brand-enable-batch' => ['post',],
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
     * Get goods categories action(lhzz admin).
     *
     * @return string
     */
    public function actionCategoriesManageAdmin()
    {
        $pid = (int)Yii::$app->request->get('pid', 0);
        $categories = GoodsCategory::categoriesByPid(GoodsCategory::APP_FIELDS, $pid);

        $user = Yii::$app->user->identity;
        if ($user->login_role_id == Yii::$app->params['lhzzRoleId']) {
            array_unshift($categories, GoodsCategory::forAll2());
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
        $select = Goods::CATEGORY_GOODS_APP;
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

        $ret = [
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'recommend_history' => [
                    'total' => 0,
                    'details' => []
                ]
            ],
        ];

        $districtCode = (int)Yii::$app->request->get('district_code', 0);
        if (!StringService::checkDistrict($districtCode)) {
            return Json::encode($ret);
        }

        $where = 'delete_time = 0 and type = ' . $type . ' and district_code = ' . $districtCode;

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

        $ret['data']['recommend_history']['total'] = (int)GoodsRecommend::find()->where($where)->asArray()->count();
        $ret['data']['recommend_history']['details'] = GoodsRecommend::pagination($where, GoodsRecommend::$adminFields, $page, $size);
        return Json::encode($ret);
    }

    /**
     * Recommend index action(admin)
     *
     * @return string
     */
    public function actionRecommendAdminIndex()
    {
        $type = (int)Yii::$app->request->get('type', GoodsRecommend::RECOMMEND_GOODS_TYPE_CAROUSEL);
        $districtCode = (int)Yii::$app->request->get('district_code', 0);

        if (!in_array($type, GoodsRecommend::$types)) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $ret = [
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'recommend_admin_index' => [
                    'details' => []
                ]
            ],
        ];

        if (!StringService::checkDistrict($districtCode)) {
            return Json::encode($ret);
        }

        $where = 'delete_time = 0 and type = ' . $type . ' and district_code = ' . $districtCode;

        $ret['data']['recommend_admin_index']['details'] = GoodsRecommend::pagination($where, GoodsRecommend::$adminFields, 1, GoodsRecommend::PAGE_SIZE_DEFAULT_ADMIN_INDEX);
        return Json::encode($ret);
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
            'data' => [
                'detail' => [],
            ],
        ];

        $goods = Goods::findBySku($sku, ['id', 'title', 'subtitle', 'platform_price']);
        if ($goods) {
            $ret['data'] = [
                'detail' => [
                    'title' => $goods->title,
                    'subtitle' => $goods->subtitle,
                    'platform_price' => $goods->platform_price,
//                    'url' => Url::to([Goods::GOODS_DETAIL_URL_PREFIX . $goods->id], true),
                    'url' => Goods::GOODS_DETAIL_URL_PREFIX . $goods->id,
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
        $recommend->district_code = trim(Yii::$app->request->post('district_code', ''));

        $code = 1000;

        $recommend->scenario = GoodsRecommend::SCENARIO_ADD;
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

//        if ($goodsCategory->review_status == GoodsCategory::REVIEW_STATUS_APPROVE) {
//            new EventHandleService();
//            Yii::$app->trigger(Yii::$app->params['events']['mall']['category']['updateBatch']);
//        }

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

        $category->scenario = GoodsCategory::SCENARIO_ADD;
        if (!$category->validate()) {
            if ($category->title && isset($category->errors['title'])) {
                $code = 1006;
            }

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
     * Edit category action
     *
     * @return string
     */
    public function actionCategoryEdit()
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
        $category = GoodsCategory::findOne($id);
        if (!$category) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $category->title = trim(Yii::$app->request->post('title', ''));
        $category->icon = trim(Yii::$app->request->post('icon', ''));
        $category->description = trim(Yii::$app->request->post('description', ''));
        $pid = (int)Yii::$app->request->post('pid', '');
        $category->setLevelPath($pid);
        $category->pid = $pid;

        $category->scenario = GoodsCategory::SCENARIO_EDIT;
        if (!$category->validate()) {
            if ($category->title && isset($category->errors['title'])) {
                $code = 1006;
            }

            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $checkSameLevelResult = $category->checkSameLevelByPid($pid);
        if ($checkSameLevelResult != 200) {
            return Json::encode([
                'code' => $checkSameLevelResult,
                'msg' => Yii::$app->params['errorCodes'][$checkSameLevelResult],
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
        $user = Yii::$app->user->identity;
        if (!$user || $user->login_role_id != Yii::$app->params['lhzzRoleId']) {
            $code = 403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

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
        $lhzz = Lhzz::find()->where(['uid' => $user->id])->one();
        if ($model->deleted == GoodsCategory::STATUS_ONLINE) {
            $model->deleted = GoodsCategory::STATUS_OFFLINE;
            $model->online_time = $now;
            $model->online_person = $lhzz->nickname;
        } else {
            $model->deleted = GoodsCategory::STATUS_ONLINE;
            $model->offline_time = $now;
            $model->offline_reason = Yii::$app->request->post('offline_reason', '');
            $model->offline_person = $lhzz->nickname;
        }

        $model->scenario = GoodsCategory::SCENARIO_TOGGLE_STATUS;
        if (!$model->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$model->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($model->deleted == GoodsCategory::STATUS_ONLINE) {
            if ($model->level == GoodsCategory::LEVEL3) {
                $categoryIds = [$model->id];
            } else {
                $categoryIds = GoodsCategory::level23Ids($model->id);
                GoodsCategory::disableByIds($categoryIds);
            }
            Goods::disableGoodsByCategoryIds($categoryIds);
        }

//        new EventHandleService();
//        Yii::$app->trigger(Yii::$app->params['events']['mall']['category']['updateBatch']);

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
        $user = Yii::$app->user->identity;
        if (!$user || $user->login_role_id != Yii::$app->params['lhzzRoleId']) {
            $code = 403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $ids = trim(Yii::$app->request->post('ids', ''));
        $ids = trim($ids, ',');
        $idsArr = explode(',', $ids);

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
            'offline_time' => time(),
            'offline_reason' => Yii::$app->request->post('offline_reason', ''),
            'offline_person' => Lhzz::find()->where(['uid' => $user->id])->one()->nickname
        ], $where)
        ) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $categoryIds = array_unique(array_merge($idsArr, GoodsCategory::level23IdsByPids($idsArr)));
        GoodsCategory::disableByIds($categoryIds);
        Goods::disableGoodsByCategoryIds($categoryIds);

//        new EventHandleService();
//        Yii::$app->trigger(Yii::$app->params['events']['mall']['category']['updateBatch']);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK'
        ]);
    }

    /**
     * Enable category records in batches action.
     *
     * @return string
     */
    public function actionCategoryEnableBatch()
    {
        $user = Yii::$app->user->identity;
        if (!$user || $user->login_role_id != Yii::$app->params['lhzzRoleId']) {
            $code = 403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $ids = trim(Yii::$app->request->post('ids', ''));
        $ids = trim($ids, ',');

        $code = 1000;

        if (!$ids) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $canEnable = GoodsCategory::canEnable($ids);
        if (!$canEnable) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = 'id in(' . $ids . ')';
        if (!GoodsCategory::updateAll([
            'deleted' => GoodsCategory::STATUS_OFFLINE,
            'online_time' => time(),
            'online_person' => Lhzz::find()->where(['uid' => $user->id])->one()->nickname
        ], $where)
        ) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

//        new EventHandleService();
//        Yii::$app->trigger(Yii::$app->params['events']['mall']['category']['updateBatch']);

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
    {
        $code = 1000;

        $user = Yii::$app->user->identity;
        if (!$user) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $sort = Yii::$app->request->get('sort', []);
        $model = new GoodsCategory;
        $orderBy = $sort ? ModelService::sortFields($model, $sort) : ModelService::sortFields($model);
        if ($orderBy === false) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($user->login_role_id == Yii::$app->params['supplierRoleId']) {
            $supplier = Supplier::find()->where(['uid' => $user->id])->one();
            if (!$supplier) {
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            $where = "supplier_id = {$supplier->id}";
        } else {
            $status = (int)Yii::$app->request->get('status', GoodsCategory::STATUS_ONLINE);
            if (!in_array($status, array_keys(GoodsCategory::$statuses))) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            $deleted = 1 - $status;
            $where = 'review_status = ' . GoodsCategory::REVIEW_STATUS_APPROVE;
            $where .= " and supplier_id = 0 and deleted = {$deleted}";

            $pid = (int)Yii::$app->request->get('pid', 0);
            if ($pid > 0) {
                $ids = GoodsCategory::level23Ids($pid);
                if (!$ids) {
                    $where .= ' and 0';
                } else {
                    $where .= ' and id in (' . implode(',', $ids) . ')';
                }
            }
        }

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', GoodsCategory::PAGE_SIZE_DEFAULT);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'category_list_admin' => [
                    'total' => (int)GoodsCategory::find()->where($where)->asArray()->count(),
                    'details' => GoodsCategory::pagination($where, GoodsCategory::$adminFields, $page, $size, $orderBy)
                ]
            ],
        ]);
    }

    /**
     * Category review list action
     *
     * @return string
     */
    public function actionCategoryReviewList()
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

        $sort = Yii::$app->request->get('sort', '');
        $orderBy = ModelService::sortFields(new GoodsCategory, $sort);
        if ($orderBy === false) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $reviewStatus = (int)Yii::$app->request->get('review_status', GoodsCategory::REVIEW_STATUS_NOT_REVIEWED);
        if (!in_array($reviewStatus, array_keys(Yii::$app->params['reviewStatuses']))) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = "review_status = {$reviewStatus}";

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

        if ($startTime) {
            $startTime = strtotime($startTime);
            $startTime && $where .= " and create_time >= {$startTime}";
        }
        if ($endTime) {
            $endTime = strtotime($endTime);
            $endTime && $where .= " and create_time <= {$endTime}";
        }

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', GoodsCategory::PAGE_SIZE_DEFAULT);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'category_review_list' => [
                    'total' => (int)GoodsCategory::find()->where($where)->asArray()->count(),
                    'details' => GoodsCategory::pagination($where, GoodsCategory::$adminFields, $page, $size, ['level' => SORT_ASC])
                ]
            ],
        ]);
    }

    /**
     * Add brand action
     *
     * @return string
     */
    public function actionBrandAdd()
    {
        $code = 1000;

        $brand = new GoodsBrand;
        $brand->attributes = Yii::$app->request->post();
        $categoryIds = trim(Yii::$app->request->post('category_ids', ''));
        $categoryIds = trim($categoryIds, ',');
        if (!$categoryIds) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $brand->scenario = GoodsBrand::SCENARIO_ADD;
        if (!$brand->validate()) {
            if ($brand->name && isset($brand->errors['name'])) {
                $code = 1007;
            }

            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $transaction = Yii::$app->db->beginTransaction();

        if (!$brand->save()) {
            $transaction->rollBack();

            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $categoryIdsArr = explode(',', $categoryIds);
        foreach ($categoryIdsArr as $categoryId) {
            $category = GoodsCategory::findOne($categoryId);
            if (!$category) {
                $transaction->rollBack();

                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            $brandCategory = new BrandCategory;
            $brandCategory->brand_id = $brand->id;
            $brandCategory->category_id = $categoryId;
            list($rootCategoryId, $parentCategoryId, $categoryId) = explode(',', $category->path);
            $brandCategory->category_id_level1 = $rootCategoryId;
            $brandCategory->category_id_level2 = $parentCategoryId;

            $brandCategory->scenario = BrandCategory::SCENARIO_ADD;
            if (!$brandCategory->validate()) {
                $transaction->rollBack();

                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$brandCategory->save()) {
                $transaction->rollBack();

                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }

        $transaction->commit();

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
        ]);
    }

    /**
     * Review brand action
     *
     * @return string
     */
    public function actionBrandReview()
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
        $brand = GoodsBrand::findOne($id);
        if (!$brand) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $brand->review_status = (int)Yii::$app->request->post('review_status', 0);
        $brand->reason = trim(Yii::$app->request->post('reason', ''));

        $brand->scenario = GoodsBrand::SCENARIO_REVIEW;
        if (!$brand->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$brand->save()) {
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
     * Edit brand action
     *
     * @return string
     */
    public function actionBrandEdit()
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
        $brand = GoodsBrand::findOne($id);
        if (!$brand) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $categoryIds = trim(Yii::$app->request->post('category_ids', ''));
        $categoryIds = trim($categoryIds, ',');
        if (!$categoryIds) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($brand->status == GoodsBrand::STATUS_OFFLINE) {
            $brand->offline_reason = trim(Yii::$app->request->post('offline_reason', ''));
        }

        $brand->name = trim(Yii::$app->request->post('name', ''));
        $brand->certificate = trim(Yii::$app->request->post('certificate', ''));
        $brand->logo = trim(Yii::$app->request->post('logo', ''));

        $brand->scenario = GoodsBrand::SCENARIO_EDIT;
        if (!$brand->validate()) {
            if ($brand->name && isset($brand->errors['name'])) {
                $code = 1007;
            }

            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $transaction = Yii::$app->db->beginTransaction();

        if (!$brand->save()) {
            $transaction->rollBack();

            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $categoryIdsArr = explode(',', $categoryIds);
        $categoryIdsArrOld = BrandCategory::categoryIdsByBrandId($brand->id);
        if (count(array_diff($categoryIdsArrOld, $categoryIdsArr)) != 0
            || count(array_diff($categoryIdsArr, $categoryIdsArrOld)) != 0
        ) {
            $deletedNum = BrandCategory::deleteAll(
                [
                    'brand_id' => $brand->id,
                ]
            );

            if ($deletedNum == 0) {
                $transaction->rollBack();

                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            foreach ($categoryIdsArr as $categoryId) {
                $brandCategory = new BrandCategory;
                $brandCategory->brand_id = $brand->id;
                $brandCategory->category_id = $categoryId;

                $brandCategory->scenario = BrandCategory::SCENARIO_ADD;
                if (!$brandCategory->validate()) {
                    $transaction->rollBack();

                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code],
                    ]);
                }

                if (!$brandCategory->save()) {
                    $transaction->rollBack();

                    $code = 500;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code],
                    ]);
                }
            }
        }

        $transaction->commit();

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
        ]);
    }

    /**
     * Reset brand offline reason action
     *
     * @return string
     */
    public function actionBrandOfflineReasonReset()
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
        $brand = GoodsBrand::findOne($id);
        if (!$brand) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $brand->offline_reason = trim(Yii::$app->request->post('offline_reason', ''));

        $brand->scenario = GoodsBrand::SCENARIO_RESET_OFFLINE_REASON;
        if (!$brand->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$brand->save()) {
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
     * Reset category offline reason action
     *
     * @return string
     */
    public function actionCategoryOfflineReasonReset()
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
        $category = GoodsCategory::findOne($id);
        if (!$category) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $category->offline_reason = trim(Yii::$app->request->post('offline_reason', ''));

        $category->scenario = GoodsCategory::SCENARIO_RESET_OFFLINE_REASON;
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
     * Toggle brand status action.
     *
     * @return string
     */
    public function actionBrandStatusToggle()
    {
        $user = Yii::$app->user->identity;
        if (!$user || $user->login_role_id != Yii::$app->params['lhzzRoleId']) {
            $code = 403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $id = (int)Yii::$app->request->post('id', 0);

        $code = 1000;

        if (!$id) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $model = GoodsBrand::findOne($id);
        if (!$model) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $now = time();
        $lhzz = Lhzz::find()->where(['uid' => $user->id])->one();
        if ($model->status == GoodsBrand::STATUS_OFFLINE) {
            $model->status = GoodsBrand::STATUS_ONLINE;
            $model->online_time = $now;
            $model->online_person = $lhzz->nickname;
        } else {
            $model->status = GoodsBrand::STATUS_OFFLINE;
            $model->offline_time = $now;
            $model->offline_reason = Yii::$app->request->post('offline_reason', '');
            $model->offline_person = $lhzz->nickname;
        }

        $model->scenario = GoodsBrand::SCENARIO_TOGGLE_STATUS;
        if (!$model->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$model->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($model->status == GoodsBrand::STATUS_OFFLINE) {
            Goods::disableGoodsByBrandId($model->id);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK'
        ]);
    }

    /**
     * Disable brand records in batches action.
     *
     * @return string
     */
    public function actionBrandDisableBatch()
    {
        $user = Yii::$app->user->identity;
        if (!$user || $user->login_role_id != Yii::$app->params['lhzzRoleId']) {
            $code = 403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $ids = trim(Yii::$app->request->post('ids', ''));
        $ids = trim($ids, ',');

        $code = 1000;

        if (!$ids) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $canDisable = GoodsBrand::canDisable($ids);
        if (!$canDisable) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = 'id in(' . $ids . ')';
        if (!GoodsBrand::updateAll([
            'status' => GoodsBrand::STATUS_OFFLINE,
            'offline_time' => time(),
            'offline_reason' => Yii::$app->request->post('offline_reason', ''),
            'offline_person' => Lhzz::find()->where(['uid' => $user->id])->one()->nickname
        ], $where)
        ) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        Goods::disableGoodsByBrandIds(explode(',', $ids));

        return Json::encode([
            'code' => 200,
            'msg' => 'OK'
        ]);
    }

    /**
     * Enable brand records in batches action.
     *
     * @return string
     */
    public function actionBrandEnableBatch()
    {
        $user = Yii::$app->user->identity;
        if (!$user || $user->login_role_id != Yii::$app->params['lhzzRoleId']) {
            $code = 403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $ids = trim(Yii::$app->request->post('ids', ''));
        $ids = trim($ids, ',');

        $code = 1000;

        if (!$ids) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $canEnable = GoodsBrand::canEnable($ids);
        if (!$canEnable) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = 'id in(' . $ids . ')';
        if (!GoodsBrand::updateAll([
            'status' => GoodsBrand::STATUS_ONLINE,
            'online_time' => time(),
            'online_person' => Lhzz::find()->where(['uid' => $user->id])->one()->nickname
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
     * Brand review list action
     *
     * @return string
     */
    public function actionBrandReviewList()
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

        $reviewStatus = (int)Yii::$app->request->get('review_status', GoodsCategory::REVIEW_STATUS_NOT_REVIEWED);
        if (!in_array($reviewStatus, array_keys(Yii::$app->params['reviewStatuses']))) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = "review_status = {$reviewStatus}";

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

        if ($startTime) {
            $startTime = strtotime($startTime);
            $startTime && $where .= " and create_time >= {$startTime}";
        }
        if ($endTime) {
            $endTime = strtotime($endTime);
            $endTime && $where .= " and create_time <= {$endTime}";
        }

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', GoodsBrand::PAGE_SIZE_DEFAULT);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'brand_review_list' => [
                    'total' => (int)GoodsBrand::find()->where($where)->asArray()->count(),
                    'details' => GoodsBrand::pagination($where, GoodsBrand::$adminFields, $page, $size)
                ]
            ],
        ]);
    }

    /**
     * Admin brand list action
     *
     * @return string
     */
    public function actionBrandListAdmin()
    {
        $code = 1000;

        $user = Yii::$app->user->identity;
        if (!$user) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($user->login_role_id == Yii::$app->params['supplierRoleId']) {
            $supplier = Supplier::find()->where(['uid' => $user->id])->one();
            if (!$supplier) {
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            $where = "supplier_id = {$supplier->id}";
        } else {
            $status = (int)Yii::$app->request->get('status', GoodsBrand::STATUS_ONLINE);
            if (!in_array($status, array_keys(GoodsBrand::$statuses))) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            $where = 'review_status = ' . GoodsBrand::REVIEW_STATUS_APPROVE;
            $where .= " and supplier_id = 0 and status = {$status}";

            $pid = (int)Yii::$app->request->get('pid', 0);
            if ($pid > 0) {
                $categoryIds = GoodsCategory::level23Ids($pid);
                if (!$categoryIds) {
                    $where .= ' and 0';
                } else {
                    $ids = BrandCategory::brandIdsByCategoryIds($categoryIds);
                    $where .= ' and id in (' . implode(',', $ids) . ')';
                }
            }
        }

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', GoodsBrand::PAGE_SIZE_DEFAULT);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'brand_list_admin' => [
                    'total' => (int)GoodsBrand::find()->where($where)->asArray()->count(),
                    'details' => GoodsBrand::pagination($where, GoodsBrand::$adminFields, $page, $size)
                ]
            ],
        ]);
    }
}