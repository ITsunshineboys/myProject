<?php

namespace app\controllers;

use app\models\BrandApplicationImage;
use app\models\BrandCategory;
use app\models\District;
use app\models\GoodsBrand;
use app\models\GoodsRecommend;
use app\models\GoodsCategory;
use app\models\Goods;
use app\models\GoodsRecommendViewLog;
use app\models\Series;
use app\models\Style;
use app\models\Supplier;
use app\models\Lhzz;
use app\models\LogisticsTemplate;
use app\models\LogisticsDistrict;
use app\models\GoodsAttr;
use app\models\GoodsImage;
use app\models\GoodsComment;
use app\models\UploadForm;
use app\models\User;
use app\models\BrandApplication;
use app\models\GoodsStat;
use app\models\GoodsOrder;
use app\models\GoodsRecommendSupplier;
use app\models\UserMobile;
use app\models\UserStatus;
use app\models\UserRole;
use app\models\Role;
use app\services\ExceptionHandleService;
use app\services\StringService;
use app\services\ModelService;
use app\services\AuthService;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\db\Query;
use yii\log\Logger;

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
        'recommend-add',
        'recommend-edit',
        'recommend-sort',
//        'recommend-click-record',
        'recommend-add-supplier',
        'recommend-edit-supplier',
        'recommend-delete-supplier',
        'recommend-delete-batch-supplier',
        'recommend-admin-index-supplier',
        'category-review',
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
        'category-reason-reset',
        'category-review-list',
//        'category-brands',
        'category-attrs',
        'brand-add',
        'brand-review',
        'brand-edit',
        'brand-offline-reason-reset',
        'brand-reason-reset',
        'brand-status-toggle',
        'brand-disable-batch',
        'brand-enable-batch',
        'brand-review-list',
        'brand-list-admin',
        'brand-application-add',
        'brand-application-list-admin',
        'brand-application-review-list',
        'brand-application-review-note-reset',
        'brand-application-review',
        'logistics-template-add',
        'logistics-template-edit',
        'logistics-template-view',
        'logistics-templates-supplier',
        'logistics-template-status-toggle',
        'goods-attr-add',
        'goods-attr-list-admin',
        'goods-add',
        'goods-edit',
        'goods-edit-lhzz',
        'goods-attrs-admin',
        'goods-status-toggle',
        'goods-disable-batch',
        'goods-delete-batch',
        'goods-enable-batch',
        'goods-offline-reason-reset',
        'goods-reason-reset',
        'goods-list-admin',
        'goods-inventory-reset',
//        'goods-images',
        'goods-by-sku',
        'supplier-add',
        'check-role-get-identity',
        'supplier-icon-reset',
        'supplier-view-admin',
        'shop-data',
        'supplier-index-admin',
        'index-admin',
        'user-identity',
        'user-add',
        'reset-mobile',
        'reset-mobile-logs',
        'user-status-toggle',
        'user-disable-batch',
        'user-disable-remark-reset',
        'user-enable-batch',
        'user-view-lhzz',
        'reset-user-status-logs',
        'user-list',
        'index-admin-lhzz',
        'supplier-status-toggle',
        'supplier-list',
        'categories-have-style-series',
        'categories-style-series-reset',
        'series-list',
        'series-time-sort',
        'series-add',
        'series-edit',
        'series-status',
        'style-list',
        'style-time-sort',
        'style-add',
        'style-edit',
        'style-status',
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AuthService::className(),
                'denyCallback' => function ($rule, $action) {
                    new ExceptionHandleService(func_get_args()[0]);
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
                    'recommend-add-supplier' => ['post',],
                    'recommend-edit-supplier' => ['post',],
                    'recommend-delete-supplier' => ['post',],
                    'recommend-delete-batch-supplier' => ['post',],
                    'category-review' => ['post',],
                    'category-add' => ['post',],
                    'category-edit' => ['post',],
                    'category-status-toggle' => ['post',],
                    'category-disable-batch' => ['post',],
                    'category-enable-batch' => ['post',],
                    'category-offline-reason-reset' => ['post',],
                    'category-reason-reset' => ['post',],
                    'brand-add' => ['post',],
                    'brand-review' => ['post',],
                    'brand-edit' => ['post',],
                    'brand-offline-reason-reset' => ['post',],
                    'brand-reason-reset' => ['post',],
                    'brand-status-toggle' => ['post',],
                    'brand-disable-batch' => ['post',],
                    'brand-enable-batch' => ['post',],
                    'brand-application-add' => ['post',],
                    'brand-application-review-note-reset' => ['post',],
                    'brand-application-review' => ['post',],
                    'logistics-template-add' => ['post',],
                    'logistics-template-edit' => ['post',],
                    'logistics-template-status-toggle' => ['post',],
                    'goods-attr-add' => ['post',],
                    'goods-add' => ['post',],
                    'goods-edit' => ['post',],
                    'goods-status-toggle' => ['post',],
                    'goods-disable-batch' => ['post',],
                    'goods-delete-batch' => ['post',],
                    'goods-enable-batch' => ['post',],
                    'goods-offline-reason-reset' => ['post',],
                    'goods-reason-reset' => ['post',],
                    'goods-inventory-reset' => ['post',],
                    'supplier-add' => ['post',],
                    'supplier-icon-reset' => ['post',],
                    'user-add' => ['post',],
                    'reset-mobile' => ['post',],
                    'user-status-toggle' => ['post',],
                    'user-disable-batch' => ['post',],
                    'user-disable-remark-reset' => ['post',],
                    'user-enable-batch' => ['post',],
                    'supplier-status-toggle' => ['post',],
                    'categories-style-series-reset' => ['post',],
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
        $districtCode = (int)Yii::$app->request->get('district_code', Yii::$app->params['district_default']);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'carousel' => GoodsRecommend::carousel($districtCode),
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
        $districtCode = (int)Yii::$app->request->get('district_code', Yii::$app->params['district_default']);
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', GoodsRecommend::PAGE_SIZE_DEFAULT);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'recommend_second' => GoodsRecommend::second($districtCode, $page, $size),
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
//        $pid == 0 && array_unshift($categories, GoodsCategory::forAll());
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
     * Get goods categories action.
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
        $code = 1000;

        $categoryId = (int)Yii::$app->request->get('category_id', 0);
        if (!$categoryId) {
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
                'category_goods' => [],
            ],
        ];

        $districtCode = (int)Yii::$app->request->get('district_code', Yii::$app->params['district_default']);
        $goodsIds = Goods::findIdsByDistrictCode($districtCode);
        if (!$goodsIds) {
            return Json::encode($ret);
        }

        $platformPriceMin = (int)Yii::$app->request->get('platform_price_min', 0);
        $platformPriceMax = (int)Yii::$app->request->get('platform_price_max', 0);
        $brandId = (int)Yii::$app->request->get('brand_id', 0);
        $styleId = (int)Yii::$app->request->get('style_id', 0);
        $seriesId = (int)Yii::$app->request->get('series_id', 0);

        $where = "category_id = {$categoryId} and status = " . Goods::STATUS_ONLINE;
        $platformPriceMin && $where .= " and platform_price >= {$platformPriceMin}";
        $platformPriceMax && $where .= " and platform_price <= {$platformPriceMax}";
        $brandId && $where .= " and brand_id = {$brandId}";
        $styleId && $where .= " and style_id = {$styleId}";
        $seriesId && $where .= " and series_id = {$seriesId}";

        $where .= ' and id in(' . implode(',', $goodsIds) . ')';

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', Goods::PAGE_SIZE_DEFAULT);
        $select = Goods::CATEGORY_GOODS_APP;

        $categoryGoods = $sort
            ? Goods::pagination($where, $select, $page, $size, $orderBy)
            : Goods::pagination($where, $select, $page, $size);
        $ret['data']['category_goods'] = $categoryGoods;
        return Json::encode($ret);
    }

    /**
     * Search brands action.
     *
     * @return string
     */
    public function actionSearch()
    {
        $res = [
            'categories' => [],
            'goods' => [],
            'category_id' => 0,
        ];

        $keyword = trim(Yii::$app->request->get('keyword', ''));
        if ($keyword) {
            $categories = GoodsCategory::findByTitle($keyword);
            if ($categories) {
                $res['categories'] = $categories;
            } else {
                $goods = Goods::findByTitle($keyword);
                if ($goods) {
                    $res['goods'] = $goods;
                    $res['category_id'] = Goods::findOne($goods[0]['id'])->category_id;
                }
            }
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'search' => $res,
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
        $goods = $orderByArr ? Goods::findByBrandId($brandId, Goods::BRAND_GOODS_APP, $page, $size, $orderByArr) : Goods::findByBrandId($brandId, $select, $page, $size);
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

        $where = 'delete_time > 0 and type = ' . $type . ' and district_code = ' . $districtCode;

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
        $districtCode = (int)Yii::$app->request->get('district_code', Yii::$app->params['district_default']);

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

        $ret['data']['recommend_admin_index']['details'] = GoodsRecommend::pagination($where, GoodsRecommend::$adminFields, 1, GoodsRecommend::PAGE_SIZE_DEFAULT_ADMIN_INDEX, ['sorting_number' => SORT_ASC]);
        return Json::encode($ret);
    }

    /**
     * Get goods by sku action
     *
     * @return string
     */
    public function actionGoodsBySku()
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
            $recommend->supplier_name = $supplier->shop_name;
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

        $recommend->attributes = $postData;

        if (!empty($postData['sku'])) {
            $goods = Goods::find()->where(['sku' => $recommend->sku])->one();
            $supplier = Supplier::findOne($goods->supplier_id);
            $recommend->supplier_id = $supplier->id;
            $recommend->supplier_name = $supplier->shop_name;
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
            'code' => $code,
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
//        if (!$recommendViewLog->canLogIpNumber()) {
//            return Json::encode([
//                'code' => 200,
//                'msg' => 'OK',
//            ]);
//        }

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
    public function actionCategoryReview()
    {
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
            if (isset($category->errors['title'])) {
                $customErrCode = ModelService::customErrCode($category->errors['title'][0]);
                if ($customErrCode !== false) {
                    $code = $customErrCode;
                }
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
        null !== Yii::$app->request->post('offline_reason') && $category->offline_reason = trim(Yii::$app->request->post('offline_reason', ''));
        $pid = (int)Yii::$app->request->post('pid', '');
        $category->setLevelPath($pid);
        $category->pid = $pid;

        $category->scenario = GoodsCategory::SCENARIO_EDIT;
        if (!$category->validate()) {
            if (isset($category->errors['title'])) {
                $customErrCode = ModelService::customErrCode($category->errors['title'][0]);
                if ($customErrCode !== false) {
                    $code = $customErrCode;
                }
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
        $operator = UserRole::roleUser(Yii::$app->user->identity, Yii::$app->session[User::LOGIN_ROLE_ID]);
        if ($model->deleted == GoodsCategory::STATUS_ONLINE) {
            $model->deleted = GoodsCategory::STATUS_OFFLINE;
            $model->online_time = $now;
            $model->online_person = $operator->nickname;
        } else {
            $model->deleted = GoodsCategory::STATUS_ONLINE;
            $model->offline_time = $now;
            $model->offline_reason = Yii::$app->request->post('offline_reason', '');
            $model->offline_person = $operator->nickname;
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
            Goods::disableGoodsByCategoryIds($categoryIds, $operator);
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
        $user = Yii::$app->user->identity;
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
        Goods::disableGoodsByCategoryIds($categoryIds, Lhzz::findByUser(Yii::$app->user->identity));

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
        $user = Yii::$app->user->identity;
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
                $ids = GoodsCategory::level23Ids($pid, false, (bool)$status);
                if (!$ids) {
                    $where .= ' and 0';
                } else {
                    $where .= ' and id in (' . implode(',', $ids) . ')';
                }
            }
        }

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', GoodsCategory::PAGE_SIZE_DEFAULT);

        $total = (int)GoodsCategory::find()->where($where)->asArray()->count();
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'category_list_admin' => [
                    'total' => $total,
                    'details' => $total > 0 ? GoodsCategory::pagination($where, GoodsCategory::$adminFields, $page, $size, $orderBy) : []
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
        $code = 1000;

        $sort = Yii::$app->request->get('sort', []);
        $model = new GoodsCategory;
        $orderBy = $sort ? ModelService::sortFields($model, $sort) : ModelService::sortFields($model);
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
                    'details' => GoodsCategory::pagination($where, GoodsCategory::$adminFields, $page, $size, $orderBy)
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
            if (isset($brand->errors['name'])) {
                $customErrCode = ModelService::customErrCode($brand->errors['name'][0]);
                if ($customErrCode !== false) {
                    $code = $customErrCode;
                }
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
            if (!$category || $category->level != GoodsCategory::LEVEL3 || $category->deleted != 0) {
                $transaction->rollBack();

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
     * Review brand application action
     *
     * @return string
     */
    public function actionBrandApplicationReview()
    {
        $code = 1000;

        $id = (int)Yii::$app->request->post('id', 0);
        $brandApplication = BrandApplication::find()
            ->where(['id' => $id, 'review_status' => ModelService::REVIEW_STATUS_NOT_REVIEWED])
            ->one();

        if (!$brandApplication) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $brandApplication->review_status = (int)Yii::$app->request->post('review_status', ModelService::REVIEW_STATUS_APPROVE);
        $brandApplication->review_note = trim(Yii::$app->request->post('review_note', ''));

        $brandApplication->scenario = ModelService::SCENARIO_REVIEW;
        if (!$brandApplication->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$brandApplication->save()) {
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

        if ($brand->status == GoodsBrand::STATUS_OFFLINE && !empty(Yii::$app->request->post('offline_reason'))) {
            $brand->offline_reason = trim(Yii::$app->request->post('offline_reason'));
        }

        $brand->name = trim(Yii::$app->request->post('name', ''));
        $brand->certificate = trim(Yii::$app->request->post('certificate', ''));
        $brand->logo = trim(Yii::$app->request->post('logo', ''));

        $brand->scenario = GoodsBrand::SCENARIO_EDIT;
        if (!$brand->validate()) {
            if (isset($brand->errors['name'])) {
                $customErrCode = ModelService::customErrCode($brand->errors['name'][0]);
                if ($customErrCode !== false) {
                    $code = $customErrCode;
                }
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
        if (!StringService::checkArrayIdentity($categoryIdsArrOld, $categoryIdsArr)) {
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
                $category = GoodsCategory::findOne($categoryId);
                if (!$category || $category->level != GoodsCategory::LEVEL3) {
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
     * Reset brand review reason action
     *
     * @return string
     */
    public function actionBrandReasonReset()
    {
        $code = 1000;

        $id = (int)Yii::$app->request->post('id', 0);

        $goodsBrand = GoodsBrand::find()
            ->where(['id' => $id])
            ->andWhere(['in', 'review_status', [
                GoodsBrand::REVIEW_STATUS_APPROVE,
                GoodsBrand::REVIEW_STATUS_REJECT,
            ]])
            ->one();

        if (!$goodsBrand) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $goodsBrand->reason = trim(Yii::$app->request->post('reason', ''));

        if (!$goodsBrand->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$goodsBrand->save()) {
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
     * Reset brand application review note action
     *
     * @return string
     */
    public function actionBrandApplicationReviewNoteReset()
    {
        $code = 1000;

        $id = (int)Yii::$app->request->post('id', 0);

        $brandApplication = BrandApplication::find()
            ->where(['id' => $id])
            ->andWhere(['in', 'review_status', [
                ModelService::REVIEW_STATUS_APPROVE,
                ModelService::REVIEW_STATUS_REJECT,
            ]])
            ->one();

        if (!$brandApplication) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $brandApplication->review_note = trim(Yii::$app->request->post('review_note', ''));

        if (!$brandApplication->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$brandApplication->save()) {
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
     * Reset category review reason action
     *
     * @return string
     */
    public function actionCategoryReasonReset()
    {
        $code = 1000;

        $id = (int)Yii::$app->request->post('id', 0);

        $goodsCategory = GoodsCategory::find()
            ->where(['id' => $id])
            ->andWhere(['in', 'review_status', [
                GoodsCategory::REVIEW_STATUS_APPROVE,
                GoodsCategory::REVIEW_STATUS_REJECT,
            ]])
            ->one();

        if (!$goodsCategory) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $goodsCategory->reason = trim(Yii::$app->request->post('reason', ''));

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
        $user = Yii::$app->user->identity;
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
            Goods::disableGoodsByBrandIds([$model->id], Lhzz::findByUser(Yii::$app->user->identity));
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
        $user = Yii::$app->user->identity;
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

        Goods::disableGoodsByBrandIds(explode(',', $ids), Lhzz::findByUser(Yii::$app->user->identity));

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
        $user = Yii::$app->user->identity;
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
        $code = 1000;

        $sort = Yii::$app->request->get('sort', []);
        $model = new GoodsBrand;
        $orderBy = $sort ? ModelService::sortFields($model, $sort) : ModelService::sortFields($model);
        if ($orderBy === false) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = '1';

        $keyword = trim(Yii::$app->request->get('keyword', ''));
        if (!$keyword) {
            $reviewStatus = (int)Yii::$app->request->get('review_status', Yii::$app->params['value_all']);
            if ($reviewStatus != Yii::$app->params['value_all'] && !in_array($reviewStatus, array_keys(Yii::$app->params['reviewStatuses']))) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            if ($reviewStatus != Yii::$app->params['value_all']) {
                $where .= " and review_status = {$reviewStatus}";
            }

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
        } else {
            $where .= " and supplier_name like '%{$keyword}%'";
            $ids = GoodsBrand::findIdsByMobile($keyword);
            $ids && $where .= " or id in(" . implode(',', $ids) . ")";
        }

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', GoodsBrand::PAGE_SIZE_DEFAULT);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'brand_review_list' => [
                    'total' => (int)GoodsBrand::find()->where($where)->asArray()->count(),
                    'details' => GoodsBrand::pagination($where, GoodsBrand::FIELDS_REVIEW_LIST, $page, $size, $orderBy)
                ]
            ],
        ]);
    }

    /**
     * Brand application review list action
     *
     * @return string
     */
    public function actionBrandApplicationReviewList()
    {
        $code = 1000;

        $sort = Yii::$app->request->get('sort', []);
        $model = new BrandApplication;
        $orderBy = $sort ? ModelService::sortFields($model, $sort) : ModelService::sortFields($model);
        if ($orderBy === false) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = '1';

        $keyword = trim(Yii::$app->request->get('keyword', ''));
        if (!$keyword) {
            $reviewStatus = (int)Yii::$app->request->get('review_status', Yii::$app->params['value_all']);
            if ($reviewStatus != Yii::$app->params['value_all'] && !in_array($reviewStatus, array_keys(Yii::$app->params['reviewStatuses']))) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            if ($reviewStatus != Yii::$app->params['value_all']) {
                $where .= " and review_status = {$reviewStatus}";
            }

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
        } else {
            $where .= " and (supplier_name like '%{$keyword}%' or mobile like '%{$keyword}%')";
        }

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', ModelService::PAGE_SIZE_DEFAULT);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'brand_application_review_list' => [
                    'total' => (int)BrandApplication::find()->where($where)->asArray()->count(),
                    'details' => BrandApplication::pagination($where, BrandApplication::FIELDS_REVIEW_ADMIN, $page, $size, $orderBy)
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

        $sort = Yii::$app->request->get('sort', []);
        $model = new GoodsBrand;
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
                    'details' => GoodsBrand::pagination($where, GoodsBrand::$adminFields, $page, $size, $orderBy)
                ]
            ],
        ]);
    }

    /**
     * Add logistics template action
     *
     * @return string
     */
    public function actionLogisticsTemplateAdd()
    {
        $code = 1000;

        $logisticsTemplate = new LogisticsTemplate;
        $logisticsTemplate->attributes = Yii::$app->request->post();
        $districtCodes = trim(Yii::$app->request->post('district_codes', ''));
        $districtCodes = trim($districtCodes, ',');
        if (!$districtCodes) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($logisticsTemplate->delivery_method == LogisticsTemplate::DELIVERY_METHOD_HOME) {
            unset($logisticsTemplate->delivery_cost_default);
            unset($logisticsTemplate->delivery_cost_delta);
            unset($logisticsTemplate->delivery_number_default);
            unset($logisticsTemplate->delivery_number_delta);
        }

        if (!$logisticsTemplate->validate()) {
            if (isset($logisticsTemplate->errors['name'])) {
                $customErrCode = ModelService::customErrCode($logisticsTemplate->errors['name'][0]);
                if ($customErrCode !== false) {
                    $code = $customErrCode;
                }
            }

            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $transaction = Yii::$app->db->beginTransaction();

        if (!$logisticsTemplate->save()) {
            $transaction->rollBack();

            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $code = LogisticsDistrict::insertByTemplateIdAndDistrictCodes($logisticsTemplate->id,
            explode(',', $districtCodes));
        if ($code != 200) {
            $transaction->rollBack();

            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $transaction->commit();

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
        ]);
    }

    /**
     * Edit logistis template action
     *
     * @return string
     */
    public function actionLogisticsTemplateEdit()
    {
        $code = 1000;

        $id = (int)Yii::$app->request->post('id', 0);
        $logisticsTemplate = LogisticsTemplate::findOne($id);
        if (!$logisticsTemplate) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $logisticsTemplate->attributes = Yii::$app->request->post();
        $districtCodes = trim(Yii::$app->request->post('district_codes', ''));
        $districtCodes = trim($districtCodes, ',');
        if (!$districtCodes) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($logisticsTemplate->delivery_method == LogisticsTemplate::DELIVERY_METHOD_HOME) {
            unset($logisticsTemplate->delivery_cost_default);
            unset($logisticsTemplate->delivery_cost_delta);
            unset($logisticsTemplate->delivery_number_default);
            unset($logisticsTemplate->delivery_number_delta);
        }

        if (!$logisticsTemplate->validate()) {
            if (isset($logisticsTemplate->errors['name'])) {
                $customErrCode = ModelService::customErrCode($logisticsTemplate->errors['name'][0]);
                if ($customErrCode !== false) {
                    $code = $customErrCode;
                }
            }

            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $transaction = Yii::$app->db->beginTransaction();

        if (!$logisticsTemplate->save()) {
            $transaction->rollBack();

            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $districtCodesArr = explode(',', $districtCodes);
        $districtCodesArrOld = LogisticsDistrict::districtCodesByTemplateId($logisticsTemplate->id);
        if (StringService::checkArrayIdentity($districtCodesArr, $districtCodesArrOld)) {
            $deletedNum = LogisticsDistrict::deleteAll(
                [
                    'template_id' => $logisticsTemplate->id,
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

            $code = LogisticsDistrict::insertByTemplateIdAndDistrictCodes($logisticsTemplate->id, $districtCodesArr);
            if ($code != 200) {
                $transaction->rollBack();

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
     * View logistis template action
     *
     * @return string
     */
    public function actionLogisticsTemplateView()
    {
        $code = 1000;

        $id = (int)Yii::$app->request->get('id', 0);
        $logisticsTemplate = LogisticsTemplate::findOne($id);
        if (!$logisticsTemplate) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $logisticsTemplate = (object)$logisticsTemplate->attributes;
        $logisticsTemplate->delivery_method = LogisticsTemplate::DELIVERY_METHOD[$logisticsTemplate->delivery_method];
        $districtCodes = LogisticsDistrict::districtCodesByTemplateId($logisticsTemplate->id);
        $logisticsTemplate->district_codes = $districtCodes;
        $logisticsTemplate->district_names = StringService::districtNamesByCodes($districtCodes);

        unset($logisticsTemplate->id);
        unset($logisticsTemplate->supplier_id);
//        unset($logisticsTemplate->name);
        unset($logisticsTemplate->status);

        $districtCodesPro = [];
        foreach ($districtCodes as $districtCode) {
            $districtCodesPro[] = District::findByCode($districtCode)->pid;
        }
        $logisticsTemplate->district_codes_parent = array_unique($districtCodesPro);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'logistics_template' => $logisticsTemplate
            ],
        ]);
    }

    /**
     * Supplier logistis templates action
     *
     * @return string
     */
    public function actionLogisticsTemplatesSupplier()
    {
        $operator = UserRole::roleUser(Yii::$app->user->identity, Yii::$app->session[User::LOGIN_ROLE_ID]);
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'logistics_templates_supplier' => LogisticsTemplate::findBySupplierId($operator->id, LogisticsTemplate::FIELDS_LIST_ADMIN)
            ],
        ]);
    }

    /**
     * Toggle logistics template status action.
     *
     * @return string
     */
    public function actionLogisticsTemplateStatusToggle()
    {
        $id = (int)Yii::$app->request->post('id', 0);

        $code = 1000;

        if (!$id) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $model = LogisticsTemplate::findOne($id);
        if (!$model) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($model->status == LogisticsTemplate::STATUS_OFFLINE) {
            $model->status = LogisticsTemplate::STATUS_ONLINE;
        } else {
            $model->status = LogisticsTemplate::STATUS_OFFLINE;
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
     * Add/edit goods attributes action
     *
     * @return string
     */
    public function actionGoodsAttrAdd()
    {
        $code = 1000;

        $names = Yii::$app->request->post('names', []);
        $values = Yii::$app->request->post('values', []);
        $units = Yii::$app->request->post('units', []);
        $additionTypes = Yii::$app->request->post('addition_types', []);
        $categoryId = (int)Yii::$app->request->post('category_id', 0);

        if ($categoryId <= 0) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $attrCnt = count($names);
        if ($attrCnt > 0) {
            if (!($attrCnt == count($units) && $attrCnt == count($additionTypes))) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!GoodsAttr::validateNames($names)) {
                $code = 1009;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!GoodsAttr::validateValues($values, $additionTypes)) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }

        $user = Yii::$app->user->identity;
        $lhzz = Lhzz::find()->where(['uid' => $user->id])->one();
        $category = GoodsCategory::find()->where(['id' => $categoryId, 'level' => GoodsCategory::LEVEL3])->one();
        if (!$category) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $category->attr_op_uid = $lhzz->id;
        $category->attr_op_username = $lhzz->nickname;
        $category->attr_op_time = time();
        $category->attr_number = $attrCnt;

        $transaction = Yii::$app->db->beginTransaction();

        if (!$category->save()) {
            $transaction->rollBack();

            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        GoodsAttr::deleteAll(['category_id' => $categoryId, 'goods_id' => 0]);

        foreach ($names as $i => $name) {
            $goodsAttr = new GoodsAttr;
            $goodsAttr->name = $name;
            $goodsAttr->unit = $units[$i];
            $goodsAttr->addition_type = $additionTypes[$i];
            $goodsAttr->category_id = $categoryId;
            $goodsAttr->addition_type == GoodsAttr::ADDITION_TYPE_DROPDOWN_LIST && $goodsAttr->value = $values[$i];

            if (!$goodsAttr->validate()) {
                $transaction->rollBack();

                if (isset($goodsAttr->errors['name'])) {
                    $customErrCode = ModelService::customErrCode($goodsAttr->errors['name'][0]);
                    if ($customErrCode !== false) {
                        $code = $customErrCode;
                    }
                }

                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$goodsAttr->save()) {
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
     * Goods attributes list action
     *
     * @return string
     */
    public function actionGoodsAttrListAdmin()
    {
        $code = 1000;

        $sort = Yii::$app->request->get('sort', []);
        $model = new GoodsCategory;
        $orderBy = $sort ? ModelService::sortFields($model, $sort) : ModelService::sortFields($model);
        if ($orderBy === false) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = 'review_status = ' . GoodsCategory::REVIEW_STATUS_APPROVE;

        $pid = (int)Yii::$app->request->get('pid', 0);
        $ids = $pid > 0
            ? GoodsCategory::level23Ids($pid, true, false)
            : GoodsCategory::allLevel3CategoryIds(false);
        $where .= !$ids ? ' and 0' : ' and id in (' . implode(',', $ids) . ')';

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', GoodsCategory::PAGE_SIZE_DEFAULT);

        $details = GoodsCategory::pagination($where, GoodsCategory::$attrAdminFields, $page, $size, $orderBy);
        foreach ($details as &$detail) {
            $detail['attrs'] = GoodsAttr::detailsByCategoryId($detail['id']);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'goods_attr_list_admin' => [
                    'total' => (int)GoodsCategory::find()->where($where)->asArray()->count(),
                    'details' => $details
                ]
            ],
        ]);
    }

    /**
     * Category brands, styles and series action
     *
     * @return string
     */
    public function actionCategoryBrandsStylesSeries()
    {
        $ret = [
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'category_brands_styles_series' => GoodsCategory::CATEGORY_BRANDS_STYLES_SERIES
            ],
        ];

        $categoryId = (int)Yii::$app->request->get('category_id', 0);
        $fields = Yii::$app->request->get('fields', []);

        $brandsStylesSeries = GoodsCategory::brandsStylesSeriesByCategoryId($categoryId, $fields);
        if (!is_array($brandsStylesSeries)) {
            return Json::encode([
                'code' => $brandsStylesSeries,
                'msg' => Yii::$app->params['errorCodes'][$brandsStylesSeries],
            ]);
        }

        $ret['data']['category_brands_styles_series'] = $brandsStylesSeries;
        return Json::encode($ret);
    }

    /**
     * Category attributes action
     *
     * @return string
     */
    public function actionCategoryAttrs()
    {
        $ret = [
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'category_attrs' => []
            ],
        ];

        $categoryId = (int)Yii::$app->request->get('category_id', 0);
        $categoryId > 0 && $ret['data']['category_attrs'] = GoodsAttr::detailsByCategoryId($categoryId);
        return Json::encode($ret);
    }

    /**
     * Goods attributes action(admin)
     *
     * @return string
     */
    public function actionGoodsAttrsAdmin()
    {
        $ret = [
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'goods_attrs_admin' => []
            ],
        ];

        $goodsId = (int)Yii::$app->request->get('goods_id', 0);
        $goodsId > 0 && $ret['data']['goods_attrs_admin'] = GoodsAttr::detailsByGoodsId($goodsId);
        return Json::encode($ret);
    }

    /**
     * Goods attributes action
     *
     * @return string
     */
    public function actionGoodsAttrs()
    {
        $ret = [
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'goods-attrs' => []
            ],
        ];

        $goodsId = (int)Yii::$app->request->get('goods_id', 0);
        $goodsId > 0 && $ret['data']['goods-attrs'] = GoodsAttr::frontDetailsByGoodsId($goodsId);
        return Json::encode($ret);
    }

    /**
     * Add goods action
     *
     * @return string
     */
    public function actionGoodsAdd()
    {
        $code = 1000;

        $goods = new Goods;
        $goods->attributes = Yii::$app->request->post();
        $images = Yii::$app->request->post('images', []);

        $goods->scenario = Goods::SCENARIO_ADD;
        if (!$goods->validate()
            || !GoodsImage::validateImages($images)
        ) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $transaction = Yii::$app->db->beginTransaction();

        if (!$goods->save()) {
            $transaction->rollBack();

            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $goods->sku = $goods->category_id . $goods->id;
        if (!$goods->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $names = Yii::$app->request->post('names', []);
        $values = Yii::$app->request->post('values', []);
        if (array_diff(GoodsAttr::findNecessaryAttrs($goods->category_id), $names)) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!StringService::checkEmptyElement($names)) {
            $attrCnt = count($names);
            if ($attrCnt > 0) {
                if ($attrCnt != count($values)) {
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code],
                    ]);
                }

                if (!GoodsAttr::validateNames($names)) {
                    $code = 1009;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code],
                    ]);
                }
            }

            $code = GoodsAttr::addByAttrs($goods, $names, $values);
            if (200 != $code) {
                $transaction->rollBack();

                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }

        if (!StringService::checkEmptyElement($images)) {
            $code = GoodsImage::addByAttrs($goods, $images);
            if (200 != $code) {
                $transaction->rollBack();

                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }

        $transaction->commit();

        $goods->generateQrCodeImage();

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
        ]);
    }

    /**
     * Edit goods action
     *
     * @return string
     */
    public function actionGoodsEdit()
    {
        $code = 1000;

        $id = (int)Yii::$app->request->post('id', 0);
        $images = Yii::$app->request->post('images', []);
        if ($id <= 0) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $goods = Goods::findOne($id);
        $user = Yii::$app->user->identity;

        if (!GoodsImage::validateImages($images)
            || !$goods
            || !$goods->canEdit($user)
        ) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $postData = Yii::$app->request->post();
        $goods->sanitize($user, $postData);
        $goods->attributes = $postData;

        if ($goods->needSetStatusToWait()) {
            $goods->status = Goods::STATUS_WAIT_ONLINE;
        }

        $goods->scenario = Goods::SCENARIO_ADD;

        if (!$goods->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $transaction = Yii::$app->db->beginTransaction();

        if (!$goods->save()) {
            $transaction->rollBack();

            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $names = Yii::$app->request->post('names', []);
        $values = Yii::$app->request->post('values', []);
        if (GoodsAttr::changedAttr($id, $names, $values)) {
            if (array_diff(GoodsAttr::findNecessaryAttrs($goods->category_id), $names)) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            GoodsAttr::deleteAll([
                'goods_id' => $id
            ]);

            $attrCnt = count($names);
            if ($attrCnt > 0) {
                if ($attrCnt != count($values)) {
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code],
                    ]);
                }

                if (!GoodsAttr::validateNames($names)) {
                    $code = 1009;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code],
                    ]);
                }
            }

            $code = GoodsAttr::addByAttrs($goods, $names, $values);
            if (200 != $code) {
                $transaction->rollBack();

                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }

        if ($user->login_role_id == Yii::$app->params['supplierRoleId']
            && in_array($goods->status, [Goods::STATUS_WAIT_ONLINE, Goods::STATUS_ONLINE])
        ) {
            GoodsImage::deleteAll([
                'goods_id' => $id
            ]);

            $code = GoodsImage::addByAttrs($goods, $images);
            if (200 != $code) {
                $transaction->rollBack();

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
     * Lhzz edit goods action
     *
     * @return string
     */
    public function actionGoodsEditLhzz()
    {
        $code = 1000;

        $id = (int)Yii::$app->request->post('id', 0);
        if ($id <= 0) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $goods = Goods::findOne($id);
        $user = Yii::$app->user->identity;

        if (!$goods
            || !$goods->canEdit($user)
        ) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $postData = Yii::$app->request->post();
        $goods->sanitize($user, $postData);
        $goods->attributes = $postData;

        $goods->scenario = Goods::SCENARIO_EDIT_LHZZ;

        if (!$goods->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$goods->save()) {
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
     * Toggle goods status action.
     *
     * @return string
     */
    public function actionGoodsStatusToggle()
    {
        $id = (int)Yii::$app->request->post('id', 0);

        $code = 1000;

        if (!$id) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $model = Goods::find()
            ->where(['id' => $id])
            ->andWhere(['in', 'status', [Goods::STATUS_OFFLINE, Goods::STATUS_WAIT_ONLINE, Goods::STATUS_ONLINE]])
            ->one();

        if (!$model) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $user = Yii::$app->user->identity;

        if (in_array($model->status, [Goods::STATUS_WAIT_ONLINE, Goods::STATUS_OFFLINE])) {
            $code = $model->canOnline($user);
            if (200 != $code) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }

        $now = time();
        $operator = UserRole::roleUser($user, Yii::$app->session[User::LOGIN_ROLE_ID]);
        if (in_array($model->status, [Goods::STATUS_WAIT_ONLINE, Goods::STATUS_OFFLINE])) {
            if ($user->login_role_id == Yii::$app->params['lhzzRoleId']) {
                $model->status = Goods::STATUS_ONLINE;
                $model->online_time = $now;
                $model->online_uid = $operator->id;
                $model->online_person = $operator->nickname;
            } else {
                $model->status = Goods::STATUS_WAIT_ONLINE;
            }
        } else {
            $model->status = Goods::STATUS_OFFLINE;
            $model->offline_time = $now;
            $model->offline_uid = $user->login_role_id == Yii::$app->params['lhzzRoleId'] ? $operator->id : 0;
            $model->offline_person = $operator->nickname;
            if ($user->login_role_id == Yii::$app->params['lhzzRoleId']) {
                $offlineReason = Yii::$app->request->post('offline_reason', '');
                !$offlineReason && $offlineReason = Yii::$app->params['lhzz']['offline_reason'];
                $model->offline_reason = $offlineReason;
            }
        }

        if (!$model->validate()) {
            if (YII_DEBUG) {
                StringService::writeLog(Goods::tableName(), json_encode($model->errors));
            }
            $code = 1000;
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

        return Json::encode([
            'code' => 200,
            'msg' => 'OK'
        ]);
    }

    /**
     * Disable goods records in batches action.
     *
     * @return string
     */
    public function actionGoodsDisableBatch()
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

        $canDisable = Goods::canDisable($ids);
        if (!$canDisable) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = 'id in(' . $ids . ')';
        $user = Yii::$app->user->identity;
        $operator = UserRole::roleUser($user, Yii::$app->session[User::LOGIN_ROLE_ID]);

        $updates = [
            'status' => Goods::STATUS_OFFLINE,
            'offline_time' => time(),
            'offline_uid' => $user->login_role_id == Yii::$app->params['lhzzRoleId'] ? $operator->id : 0,
            'offline_person' => $operator->nickname
        ];
        if ($user->login_role_id == Yii::$app->params['lhzzRoleId']) {
            $offlineReason = Yii::$app->request->post('offline_reason', '');
            !$offlineReason && $offlineReason = Yii::$app->params['lhzz']['offline_reason'];
            $updates['offline_reason'] = $offlineReason;
        }

        $transaction = Yii::$app->db->beginTransaction();

        if (Goods::updateAll($updates, $where) != count(explode(',', $ids))) {
            $transaction->rollBack();

            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $transaction->commit();

        return Json::encode([
            'code' => 200,
            'msg' => 'OK'
        ]);
    }

    /**
     * Enable goods records in batches action.
     *
     * @return string
     */
    public function actionGoodsEnableBatch()
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

        $res = Goods::canEnableBatch(explode(',', $ids), Yii::$app->user->identity);
        if (200 != $res) {
            return Json::encode([
                'code' => $res,
                'msg' => Yii::$app->params['errorCodes'][$res],
            ]);
        }

        $where = 'id in(' . $ids . ')';
        $user = Yii::$app->user->identity;
        $operator = Lhzz::find()->where(['uid' => $user->id])->one();

        $updates = [
            'status' => Goods::STATUS_ONLINE,
            'online_time' => time(),
            'online_uid' => $operator->id,
            'online_person' => $operator->nickname
        ];

        $transaction = Yii::$app->db->beginTransaction();

        if (Goods::updateAll($updates, $where) != count(explode(',', $ids))) {
            $transaction->rollBack();

            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $transaction->commit();

        return Json::encode([
            'code' => 200,
            'msg' => 'OK'
        ]);
    }

    /**
     * Delete goods records in batches action.
     *
     * @return string
     */
    public function actionGoodsDeleteBatch()
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

        $canDelete = Goods::canDelete($ids);
        if (!$canDelete) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = 'id in(' . $ids . ')';

        $updates = [
            'status' => Goods::STATUS_DELETED,
            'delete_time' => time(),
        ];

        $transaction = Yii::$app->db->beginTransaction();

        if (Goods::updateAll($updates, $where) != count(explode(',', $ids))) {
            $transaction->rollBack();

            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $transaction->commit();

        return Json::encode([
            'code' => 200,
            'msg' => 'OK'
        ]);
    }

    /**
     * Reset goods offline reason action
     *
     * @return string
     */
    public function actionGoodsOfflineReasonReset()
    {
        $code = 1000;

        $id = (int)Yii::$app->request->post('id', 0);
        $goods = Goods::find()->where(['id' => $id, 'status' => Goods::STATUS_OFFLINE])->one();
        if (!$goods) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $goods->offline_reason = trim(Yii::$app->request->post('offline_reason', ''));

        if (!$goods->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$goods->save()) {
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
     * Reset goods inventory action
     *
     * @return string
     */
    public function actionGoodsInventoryReset()
    {
        $code = 1000;

        $id = (int)Yii::$app->request->post('id', 0);

        $goods = Goods::find()
            ->where(['id' => $id])
            ->andWhere(['in', 'status', [Goods::STATUS_OFFLINE, Goods::STATUS_WAIT_ONLINE, Goods::STATUS_ONLINE]])
            ->one();

        if (!$goods) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $goods->left_number = (int)Yii::$app->request->post('left_number', 0);

        if (!$goods->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$goods->save()) {
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
     * Reset goods review reason action
     *
     * @return string
     */
    public function actionGoodsReasonReset()
    {
        $code = 1000;

        $id = (int)Yii::$app->request->post('id', 0);

        $goods = Goods::find()
            ->where(['id' => $id, 'status' => Goods::STATUS_WAIT_ONLINE])
            ->one();

        if (!$goods) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $goods->reason = trim(Yii::$app->request->post('reason', ''));

        if (!$goods->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$goods->save()) {
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
     * Admin goods list action
     *
     * @return string
     */
    public function actionGoodsListAdmin()
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
        $model = new Goods;
        $orderBy = $sort ? ModelService::sortFields($model, $sort) : ModelService::sortFields($model);
        if ($orderBy === false) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $status = (int)Yii::$app->request->get('status', Goods::STATUS_ONLINE);
        if (!in_array($status, array_keys(Goods::$statuses))) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = "status = {$status}";
        $keyword = trim(Yii::$app->request->get('keyword', ''));
        if ($keyword) {
            $where .= " and (sku like '%{$keyword}%' or title like '%{$keyword}%')";
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

            $where .= " and supplier_id = {$supplier->id}";
        } else {
            $supplierId = (int)Yii::$app->request->get('supplier_id', 0);
            if (!$supplierId) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            $where .= " and supplier_id = {$supplierId}";
        }

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', GoodsCategory::PAGE_SIZE_DEFAULT);
        $fromLhzz = $user->login_role_id == Yii::$app->params['lhzzRoleId'];

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'goods_list_admin' => [
                    'total' => (int)Goods::find()->where($where)->asArray()->count(),
                    'details' => Goods::pagination($where, Goods::FIELDS_ADMIN, $page, $size, $orderBy, $fromLhzz)
                ]
            ],
        ]);
    }

    /**
     * Goods images action
     *
     * @return string
     */
    public function actionGoodsImages()
    {
        $ret = [
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'goods-images' => []
            ],
        ];

        $goodsId = (int)Yii::$app->request->get('goods_id', 0);
        $goodsId > 0 && $ret['data']['goods-images'] = GoodsImage::imagesByGoodsId($goodsId);
        return Json::encode($ret);
    }

    /**
     * View goods action
     *
     * @return string
     */
    public function actionGoodsView()
    {
        $code = 1000;

        $id = (int)Yii::$app->request->get('id', 0);
        if ($id <= 0) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $cacheKey = Goods::GOODS_QR_PREFIX . $id;
        $cache = Yii::$app->cache;
        $data = $cache->get($cacheKey);
        if ($data) {
            return Json::encode([
                'code' => 200,
                'msg' => 'OK',
                'data' => [
                    'goods_view' => $data,
                ],
            ]);
        }

        $where['id'] = $id;
//        $where['status'] = Goods::STATUS_ONLINE;
//        if (Yii::$app->user->identity) {
//            unset($where['status']);
//        }
        $goods = Goods::find()->where($where)->one();

        if (!$goods) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $data = $goods->view(Yii::$app->request->userIP);
        $cache->set($cacheKey, $data, Yii::$app->params['goods']['viewCacheTime']);
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'goods_view' => $data,
            ],
        ]);
    }

    /**
     * View goods comments action
     *
     * @return string
     */
    public function actionGoodsComments()
    {
        $ret = [
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'goods-comments' => [
                    'stat' => [],
                    'details' => []
                ],
            ],
        ];

        $levelScore = trim(Yii::$app->request->get('level_score', ''));
        $goodsId = (int)Yii::$app->request->get('id', 0);
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', GoodsComment::PAGE_SIZE_DEFAULT);

        $where = '1';

        if ($levelScore && !in_array($levelScore, array_keys(GoodsComment::LEVELS_SCORE))) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($goodsId <= 0) {
            return Json::encode($ret);
        }

        $where .= " and goods_id = {$goodsId}";

        if ($levelScore) {
            list($min, $max) = GoodsComment::LEVELS_SCORE[$levelScore];
            $where .= " and score >= {$min} and score <= {$max}";
        }

        $ret['data']['goods-comments']['stat'] = GoodsComment::statByGoodsId($goodsId);
        $ret['data']['goods-comments']['details'] = GoodsComment::pagination($where, GoodsComment::FIELDS_APP, $page, $size);
        return Json::encode($ret);
    }

    /**
     * Add supplier action(lhzz admin)
     *
     * @return string
     */
    public function actionSupplierAdd()
    {
        $code = 1000;

        $mobile = (int)Yii::$app->request->post('mobile', 0);
        if (!$mobile) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $checkRoleRes = User::checkRoleAndGetIdentityByMobile($mobile);
        if (is_int($checkRoleRes)) {
            return Json::encode([
                'code' => $checkRoleRes,
                'msg' => Yii::$app->params['errorCodes'][$checkRoleRes],
            ]);
        }

        $data = Yii::$app->request->post();
        $data['status'] = Supplier::STATUS_ONLINE;
        $operator = UserRole::roleUser(Yii::$app->user->identity, Yii::$app->session[User::LOGIN_ROLE_ID]);
        $code = Supplier::add($checkRoleRes, $data, $operator);
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
     * Check role and get identity action(lhzz admin)
     *
     * @return string
     */
    public function actionCheckRoleGetIdentity()
    {
        $code = 1000;

        $mobile = (int)Yii::$app->request->get('mobile', 0);
        if (!$mobile) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $checkRoleRes = User::checkRoleAndGetIdentityByMobile($mobile);
        if (is_int($checkRoleRes)) {
            return Json::encode([
                'code' => $checkRoleRes,
                'msg' => Yii::$app->params['errorCodes'][$checkRoleRes],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'identity' => ModelService::viewModelByFields($checkRoleRes, User::FIELDS_VIEW_IDENTITY),
            ],
        ]);
    }

    /**
     * Add brand application action
     *
     * @return string
     */
    public function actionBrandApplicationAdd()
    {
        $user = Yii::$app->user->identity;

        $transaction = Yii::$app->db->beginTransaction();

        $brandApplication = BrandApplication::addByAttrs($user, Yii::$app->request->post());
        if (!is_object($brandApplication)) {
            $transaction->rollBack();

            return Json::encode([
                'code' => $brandApplication,
                'msg' => Yii::$app->params['errorCodes'][$brandApplication],
            ]);
        }

        $authorizationNames = Yii::$app->request->post('authorization_names', []);
        $images = Yii::$app->request->post('images', []);

        $code = BrandApplicationImage::addByAttrs($brandApplication, $images, $authorizationNames);
        if (200 != $code) {
            $transaction->rollBack();

            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $transaction->commit();

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
        ]);
    }

    /**
     * Get brand application list action(admin).
     *
     * @return string
     */
    public function actionBrandApplicationListAdmin()
    {
        $user = Yii::$app->user->identity;
        if ($user->login_role_id == Yii::$app->params['lhzzRoleId']) {
            $supplierId = (int)Yii::$app->request->get('supplier_id', 0);
        } else {
            $supplierId = UserRole::roleUser($user, Yii::$app->params['supplierRoleId'])->id;
        }

        if (!$supplierId) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = ['supplier_id' => $supplierId];
//        if ($user->login_role_id == Yii::$app->params['supplierRoleId']) {
//            $where = array_merge($where, ['review_status' => Role::AUTHENTICATION_STATUS_APPROVED]);
//        }

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', BrandApplication::PAGE_SIZE_DEFAULT);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'brand_application_list_admin' => [
                    'total' => (int)BrandApplication::find()->where($where)->asArray()->count(),
                    'details' => BrandApplication::pagination($where, BrandApplication::FIELDS_ADMIN, $page, $size)
                ]
            ],
        ]);
    }

    /**
     * Reset supplier icon action
     *
     * @return string
     */
    public function actionSupplierIconReset()
    {
        $code = 1000;

        $id = (int)Yii::$app->request->post('id', 0);
        $icon = trim(Yii::$app->request->post('icon', ''));

        if (!$icon) {
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

        $supplier->icon = UploadForm::getUploadImageRelativePath($icon);
        if (!$supplier->isAttributeChanged('icon')) {
            return Json::encode([
                'code' => 200,
                'msg' => 'OK',
            ]);
        }

        if (!$supplier->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$supplier->save()) {
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
     * View supplier action(admin)
     *
     * @return string
     */
    public function actionSupplierViewAdmin()
    {
        $code = 1000;

        $user = Yii::$app->user->identity;
        if ($user->login_role_id != Yii::$app->params['supplierRoleId']) {
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
        } else {
            $supplier = UserRole::roleUser($user, Yii::$app->params['supplierRoleId']);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'supplier_view_admin' => $supplier->viewAdmin(),
            ],
        ]);
    }

    /**
     * View supplier shop data action
     *
     * @return string
     */
    public function actionShopData()
    {
        $code = 1000;

        $timeType = trim(Yii::$app->request->get('time_type', ''));
        if (!$timeType || !in_array($timeType, array_keys(Yii::$app->params['timeTypes']))) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = '1';

        $user = Yii::$app->user->identity;
        if ($user->login_role_id == Yii::$app->params['supplierRoleId']) {
            $supplierId = Supplier::find()->where(['uid' => $user->id])->one()->id;
            $where .= " and supplier_id = {$supplierId}";
        } else {
            $supplierId = (int)Yii::$app->request->get('supplier_id', 0);
            $supplierId && $where .= " and supplier_id = {$supplierId}";
        }

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
        } else {
            list($startTime, $endTime) = StringService::startEndDate($timeType);
            $startTime = explode(' ', $startTime)[0];
            $endTime = explode(' ', $endTime)[0];
        }

        if ($startTime) {
            $startTime = str_replace('-', '', $startTime);
            $startTime && $where .= " and create_date >= {$startTime}";
        }
        if ($endTime) {
            $endTime = str_replace('-', '', $endTime);
            $endTime && $where .= " and create_date <= {$endTime}";
        }

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', GoodsStat::PAGE_SIZE_DEFAULT);
        $paginationData = GoodsStat::pagination($where, GoodsStat::FIELDS_ADMIN, $page, $size);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'shop_data' => [
                    'total_sold_number' => GoodsStat::totalSoldNumber($where),
                    'total_amount_sold' => StringService::formatPrice(GoodsStat::totalAmountSold($where) / 100),
                    'total_ip_number' => GoodsStat::totalIpNumber($where),
                    'total_viewed_number' => GoodsStat::totalViewedNumber($where),
                    'total' => $paginationData['total'],
                    'details' => $paginationData['details']
                ]
            ],
        ]);
    }

    /**
     * Supplier index action(admin)
     *
     * @return string
     */
    public function actionSupplierIndexAdmin()
    {
        $supplier = UserRole::roleUser(Yii::$app->user->identity, Yii::$app->params['supplierRoleId']);
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'supplier_index_admin' => Supplier::statData($supplier->id)
            ],
        ]);
    }

    /**
     * Mall index action(admin)
     *
     * @return string
     */
    public function actionIndexAdmin()
    {
        $timeType = 'today';

        list($startTime, $endTime) = StringService::startEndDate($timeType);

        $intStartTime = strtotime($startTime);
        $intEndTime = strtotime($endTime);
        $todayOrderNumber = GoodsOrder::totalOrderNumber($intStartTime, $intEndTime);
        $todayAmountOrder = GoodsOrder::totalAmountOrder($intStartTime, $intEndTime);
        $deltaSupplierNumber = Supplier::deltaNumber($intStartTime, $intEndTime);

        $where = '1';

        $startTime = explode(' ', $startTime)[0];
        $endTime = explode(' ', $endTime)[0];

        if ($startTime) {
            $startTime = str_replace('-', '', $startTime);
            $startTime && $where .= " and create_date >= {$startTime}";
        }
        if ($endTime) {
            $endTime = str_replace('-', '', $endTime);
            $endTime && $where .= " and create_date <= {$endTime}";
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'index_admin' => [
                    'today_date' => date('Y-m-d'),
                    'today_amount_order' => $todayAmountOrder,
                    'today_order_number' => $todayOrderNumber,
                    'today_ip_number' => GoodsStat::totalIpNumber($where),
                    'today_viewed_number' => GoodsStat::totalViewedNumber($where),
                    'delta_supplier_number' => $deltaSupplierNumber,
                    'total_supplier_number' => (int)Supplier::find()->where(['status' => Supplier::STATUS_ONLINE])->count(),
                ]
            ],
        ]);
    }

    /**
     * series list
     * @return string
     */
    public function actionSeriesList()
    {
        $request = Yii::$app->request;
        $pages = $request->get('page', '1');
        $size = $request->get('size', '12');
        $all = Series::pagination($pages, $size);
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'series_list' => $all
            ]
        ]);
    }

    public function actionSeriesTimeSort()
    {
        $sort = trim(Yii::$app->request->get('sort', ''));
        $pages = trim(Yii::$app->request->get('page', '1'));
        $size = trim(Yii::$app->request->get('size', '12'));
        $series = Series::findByTimeSort($sort, $pages, $size);
        return Json::encode([
            'list' => $series
        ]);
    }

    /**
     * series add
     * @return string
     */
    public function actionSeriesAdd()
    {
        $code = 1000;
        $post = Yii::$app->request->post();
        $series = new Series();
        $series->series = $post['series'];
        $series->theme = $post['theme'];
        $series->intro = $post['intro'];
        $series->series_grade = $post['series_grade'];
        $series->creation_time = time();
        $series->status = Series::STATUS_ONLINE;
        if (!$series->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$series->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }
    }

    /**
     * series edit
     * @return string
     */
    public function actionSeriesEdit()
    {
        $code = 1000;
        $post = Yii::$app->request->post();
        $series = new Series();
        $series_edit = $series->findOne($post['id']);
        $series_edit->series = $post['series'];
        $series_edit->theme = $post['theme'];
        $series_edit->intro = $post['intro'];
        $series_edit->series_grade = $post['series_grade'];
        if (!$series_edit->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$series_edit->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }
    }

    /**
     * series status
     * @return string
     */
    public function actionSeriesStatus()
    {
        $code = 1000;
        $post = Yii::$app->request->post();
        $series = new Series();
        $series_edit = $series->findOne($post['id']);
        $series_edit->status = $post['status'];
        if (!$series_edit->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$series_edit->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }
    }

    /**
     * style list
     * @return string
     */
    public function actionStyleList()
    {
        $request = Yii::$app->request;
        $pages = $request->get('page', '1');
        $size = $request->get('size', '12');
        $all = Style::pagination($pages, $size);
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'series_list' => $all
            ]
        ]);
    }

    public function actionStyleTimeSort()
    {
        $sort = trim(Yii::$app->request->get('sort', ''));
        $pages = trim(Yii::$app->request->get('page', '1'));
        $size = trim(Yii::$app->request->get('size', '12'));
        $style = Style::findByTimeSort($sort, $pages, $size);
        return Json::encode([
            'list' => $style
        ]);
    }

    /**
     * style add
     * @return string
     */
    public function actionStyleAdd()
    {
        $code = 1000;
        $post = Yii::$app->request->post();
        $style = new Style();
        $style->style = $post['style'];
        $style->theme = $post['theme'];
        $style->intro = $post['intro'];
        $style->images = $post['images'];
        $style->creation_time = time();
        $style->status = Style::STATUS_ONLINE;
        if (!$style->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$style->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }
    }

    /**
     * style edit
     * @return string
     */
    public function actionStyleEdit()
    {
        $code = 1000;
        $post = Yii::$app->request->post();
        $style = new Style();
        $style_edit = $style->findOne($post['id']);
        $style_edit->style = $post['style'];
        $style_edit->theme = $post['theme'];
        $style_edit->intro = $post['intro'];
        $style_edit->images = $post['images'];
        if (!$style_edit->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$style_edit->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }
    }

    /**
     * style status
     * @return string
     */
    public function actionStyleStatus()
    {
        $code = 1000;
        $post = Yii::$app->request->post();
        $series = new Style();
        $series_edit = $series->findOne($post['id']);
        $series_edit->status = $post['status'];
        if (!$series_edit->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$series_edit->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }
    }

    /**
     * Supplier add recommend action
     *
     * @return string
     */
    public function actionRecommendAddSupplier()
    {
        $recommend = new GoodsRecommendSupplier;
        $recommend->attributes = Yii::$app->request->post();
        $recommend->status = GoodsRecommend::STATUS_ONLINE;
        if (isset($recommend->district_code)) {
            unset($recommend->district_code);
        }

        $code = 1000;

        if (!$recommend->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($recommend->sku) {
            $supplier = UserRole::roleUser(Yii::$app->user->identity, Yii::$app->params['supplierRoleId']);
            if (!Goods::checkSupplierGoodsBySupplierIdAndSku($supplier->id, $recommend->sku)) {
                $code = 1039;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            $goods = Goods::find()->where(['sku' => $recommend->sku])->one();
            $recommend->supplier_id = $supplier->id;
            $recommend->supplier_name = $supplier->shop_name;
            $recommend->url = Goods::GOODS_DETAIL_URL_PREFIX . $goods->id;
            $recommend->platform_price = $goods->platform_price;
            $recommend->description = $goods->subtitle;
            $recommend->title = $goods->title;
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
     * Supplier edit recommend action
     *
     * @return string
     */
    public function actionRecommendEditSupplier()
    {
        $code = 1000;

        $id = (int)Yii::$app->request->post('id', 0);
        $recommend = GoodsRecommendSupplier::findOne($id);
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

        $recommend->attributes = $postData;

        if (!empty($postData['sku'])) {
            $supplier = UserRole::roleUser(Yii::$app->user->identity, Yii::$app->params['supplierRoleId']);
            if (!Goods::checkSupplierGoodsBySupplierIdAndSku($supplier->id, $recommend->sku)) {
                $code = 1039;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            $goods = Goods::find()->where(['sku' => $recommend->sku])->one();
            $recommend->supplier_id = $supplier->id;
            $recommend->supplier_name = $supplier->shop_name;
            $recommend->url = Goods::GOODS_DETAIL_URL_PREFIX . $goods->id;
            $recommend->platform_price = $goods->platform_price;
            $recommend->description = $goods->subtitle;
            $recommend->title = $goods->title;
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
     * Supplier delete recommend record action.
     *
     * @return string
     */
    public function actionRecommendDeleteSupplier()
    {
        $id = (int)Yii::$app->request->post('id', 0);

        $code = 1000;

        if (!$id) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $recommend = GoodsRecommendSupplier::findOne($id);
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

//        if ($recommend->status != GoodsRecommendSupplier::STATUS_OFFLINE) {
//            $code = 1003;
//            return Json::encode([
//                'code' => $code,
//                'msg' => Yii::$app->params['errorCodes'][$code],
//            ]);
//        }

        $recommend->delete_time = time();
        $recommend->status = GoodsRecommendSupplier::STATUS_OFFLINE;
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
     * Supplier delete recommend records in batches action.
     *
     * @return string
     */
    public function actionRecommendDeleteBatchSupplier()
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

        $canDelete = GoodsRecommendSupplier::canDelete($ids);
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
        if (!GoodsRecommendSupplier::updateAll([
            'delete_time' => time(),
            'status' => GoodsRecommendSupplier::STATUS_OFFLINE,
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
     * Recommend index action(supplier admin)
     *
     * @return string
     */
    public function actionRecommendAdminIndexSupplier()
    {
        $type = (int)Yii::$app->request->get('type', GoodsRecommend::RECOMMEND_GOODS_TYPE_CAROUSEL);
        if (!in_array($type, GoodsRecommendSupplier::$types)) {
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
                'recommend_admin_index_supplier' => [
                    'details' => []
                ]
            ],
        ];

        $supplier = UserRole::roleUser(Yii::$app->user->identity, Yii::$app->params['supplierRoleId']);
        $where = 'delete_time = 0 and type = ' . $type . ' and supplier_id = ' . $supplier->id;

        $ret['data']['recommend_admin_index_supplier']['details'] = GoodsRecommendSupplier::pagination(
            $where,
            GoodsRecommendSupplier::$adminFields,
            1,
            GoodsRecommendSupplier::PAGE_SIZE_DEFAULT_ADMIN_INDEX,
            ['sorting_number' => SORT_ASC]);
        return Json::encode($ret);
    }

    /**
     * Supplier sort recommend action
     *
     * @return string
     */
    public function actionRecommendSortSupplier()
    {
        $ids = trim(Yii::$app->request->post('ids', ''));
        $ids = trim($ids, ',');

        $idArr = explode(',', $ids);
        $code = GoodsRecommendSupplier::sort($idArr);

        return Json::encode([
            'code' => 200,
            'msg' => 200 == $code ? 'OK' : Yii::$app->params['errorCodes'][$code],
        ]);
    }

    /**
     * Categories for level 3 action
     *
     * @return string
     */
    public function actionCategoriesLevel3()
    {
        $code = 1000;

        $pid = (int)Yii::$app->request->get('pid', 0);
        if ($pid <= 0) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'categories_level3' => GoodsCategory::level3CategoriesByLevel1Pid($pid)
        ]);
    }

    /**
     * User identity action
     *
     * @return string
     */
    public function actionUserIdentity()
    {
        $user = Yii::$app->user->identity;

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'user-identity' => $user->viewIdentityLhzz(),
            ],
        ]);
    }

    /**
     * Add user(lhzz)
     *
     * @return string
     */
    public function actionUserAdd()
    {
        $data = array_merge(Yii::$app->request->post(), ['operator' => Yii::$app->user->identity]);
        $res = User::register($data, false);
        echo Json::encode([
            'code' => is_array($res) ? 200 : $res,
            'msg' => is_array($res) ? 'OK' : Yii::$app->params['errorCodes'][$res]
        ]);
        Yii::$app->trigger(Yii::$app->params['events']['async']);
    }

    /**
     * Reset mobile action.
     *
     * @return string
     */
    public function actionResetMobile()
    {
        $code = 1000;

        $mobile = (int)Yii::$app->request->post('mobile', 0);
        if (!StringService::isMobile($mobile)) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $userId = (int)Yii::$app->request->post('user_id', 0);
        $user = User::findOne($userId);
        if (!$user) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $code = $user->resetMobile($mobile, Yii::$app->user->identity);
        if (200 != $code) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => '更换手机号成功',
        ]);
    }

    /**
     * Reset mobile logs action.
     *
     * @return string
     */
    public function actionResetMobileLogs()
    {
        $userId = (int)Yii::$app->request->get('user_id', 0);
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', ModelService::PAGE_SIZE_DEFAULT);
        $sort = Yii::$app->request->get('sort', []);
        $model = new UserMobile;
        $orderBy = $sort ? ModelService::sortFields($model, $sort) : ModelService::sortFields($model);

        if (!$userId || $orderBy === false) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'reset_mobile_logs' => UserMobile::pagination(['uid' => $userId], UserMobile::FIELDS_BINDING_LOGS, $page, $size, $orderBy)
            ],
        ]);
    }

    /**
     * Toggle user status action.
     *
     * @return string
     */
    public function actionUserStatusToggle()
    {
        $userId = (int)Yii::$app->request->post('user_id', 0);
        $remark = trim(Yii::$app->request->post('remark', ''));

        $code = 1000;

        if (!$userId) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $user = User::findOne($userId);
        if (!$user) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $code = $user->toggleStatus(Yii::$app->user->identity, $remark);
        if (200 !== $code) {
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
     * Disable users in batches action.
     *
     * @return string
     */
    public function actionUserDisableBatch()
    {
        $userIds = trim(Yii::$app->request->post('user_ids', ''));
        $userIds = trim($userIds, ',');
        $remark = trim(Yii::$app->request->post('remark', ''));

        $code = 1000;
        if (!$userIds) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $disableInBatchRes = User::disableInBatch(explode(',', $userIds), Yii::$app->user->identity, $remark);
        return Json::encode([
            'code' => $disableInBatchRes,
            'msg' => $disableInBatchRes == 200 ? 'OK' : Yii::$app->params['errorCodes'][$disableInBatchRes]
        ]);
    }

    /**
     * Reset disable user remark action
     *
     * @return string
     */
    public function actionUserDisableRemarkReset()
    {
        $code = 1000;

        $id = (int)Yii::$app->request->post('id', 0);
        $userStatus = UserStatus::find()->where(['uid' => $id, 'status' => User::STATUS_OFFLINE])->one();
        if (!$userStatus) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $userStatus->remark = trim(Yii::$app->request->post('remark', ''));
        $res = $userStatus->save();
        return Json::encode([
            'code' => $res ? 200 : 500,
            'msg' => $res ? 'OK' : Yii::$app->params['errorCodes'][$code],
        ]);
    }

    /**
     * Enable users in batches action.
     *
     * @return string
     */
    public function actionUserEnableBatch()
    {
        $userIds = trim(Yii::$app->request->post('user_ids', ''));
        $userIds = trim($userIds, ',');

        $code = 1000;
        if (!$userIds) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $enableInBatchRes = User::enableInBatch(explode(',', $userIds), Yii::$app->user->identity);
        return Json::encode([
            'code' => $enableInBatchRes,
            'msg' => $enableInBatchRes == 200 ? 'OK' : Yii::$app->params['errorCodes'][$enableInBatchRes]
        ]);
    }

    /**
     * View owner action(lhzz)
     *
     * @return string
     */
    public function actionUserViewLhzz()
    {
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'user-view' => Yii::$app->user->identity->viewLhzz(),
            ],
        ]);
    }

    /**
     * Reset user status logs action.
     *
     * @return string
     */
    public function actionResetUserStatusLogs()
    {
        $userId = (int)Yii::$app->request->get('user_id', 0);
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', ModelService::PAGE_SIZE_DEFAULT);
        $sort = Yii::$app->request->get('sort', []);
        $model = new UserStatus;
        $orderBy = $sort ? ModelService::sortFields($model, $sort) : ModelService::sortFields($model);

        if (!$userId || $orderBy === false) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'reset_user_status_logs' => UserStatus::pagination(['uid' => $userId], UserStatus::FIELDS_STATUS_LOGS, $page, $size, $orderBy)
            ],
        ]);
    }

    /**
     * User list action.
     *
     * @return string
     */
    public function actionUserList()
    {
        $code = 1000;

        $timeType = trim(Yii::$app->request->get('time_type'));
        !$timeType && $timeType = 'all';
        if (!in_array($timeType, array_keys(Yii::$app->params['timeTypes']))) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $status = (int)(Yii::$app->request->get('status'));
        if (!in_array($status, array_keys(User::STATUSES))) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where = $status == User::STATUS_ONLINE ? 'deadtime = 0' : 'deadtime > 0';

        $keyword = trim(Yii::$app->request->get('keyword', ''));
        if (!$keyword) {
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
        } else {
            $where .= " and (mobile like '%{$keyword}%' or nickname like '%{$keyword}%')";
        }

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', ModelService::PAGE_SIZE_DEFAULT);
        $sort = Yii::$app->request->get('sort', []);
        $model = new UserStatus;
        $orderBy = $sort ? ModelService::sortFields($model, $sort) : ModelService::sortFields($model);

        if ($orderBy === false) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'user_list' => User::pagination($where, User::FIELDS_USER_LIST_LHZZ, $page, $size, $orderBy)
            ],
        ]);
    }

    /**
     * Lhzz admin index action.
     *
     * @return string
     */
    public function actionIndexAdminLhzz()
    {
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'index_admin_lhzz' => User::totalNumberStat()
            ]
        ]);
    }

    /**
     * Toggle supplier status
     *
     * @return string
     */
    public function actionSupplierStatusToggle()
    {
        $code = 1000;

        $supplierId = (int)Yii::$app->request->post('supplier_id', 0);
        if (!$supplierId) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $supplier = Supplier::find()
            ->where(['id' => $supplierId])
            ->andWhere(['in', 'status', array_keys(Supplier::STATUSES_ONLINE_OFFLINE)])
            ->one();
        if (!$supplier) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $operator = UserRole::roleUser(Yii::$app->user->identity, Yii::$app->session[User::LOGIN_ROLE_ID]);
        $res = $supplier->status == Supplier::STATUS_OFFLINE
            ? $supplier->online($operator)
            : $supplier->offline($operator);
        return Json::encode([
            'code' => $res,
            'msg' => 200 == $res ? 'OK' : Yii::$app->params['errorCodes'][$res]
        ]);
    }

    /**
     * Supplier list action
     *
     * @return string
     */
    public function actionSupplierList()
    {
        $code = 1000;

        $keyword = trim(Yii::$app->request->get('keyword', ''));
        $categoryId = (int)Yii::$app->request->get('category_id', 0);
        $shopType = (int)Yii::$app->request->get('shop_type', Yii::$app->params['value_all']);
        $status = (int)Yii::$app->request->get('status', Yii::$app->params['value_all']);
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', ModelService::PAGE_SIZE_DEFAULT);

        if (!Supplier::checkShopType($shopType) || !Supplier::checkStatus($status)) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $sort = Yii::$app->request->get('sort', []);
        $model = new Supplier;
        $orderBy = $sort ? ModelService::sortFields($model, $sort) : ModelService::sortFields($model);
        if ($orderBy === false) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($sort) {
            if (stripos($orderBy, Supplier::FIELD_SALES_VOLUMN_MONTH) !== -1
                || stripos($orderBy, Supplier::FIELD_SALES_AMOUNT_MONTH) !== -1
            ) {
                $orderBy = 'month DESC,' . $orderBy;
            }
        }

        $query = new Query;
        if (!$keyword) {
            if ($shopType != Yii::$app->params['value_all']) {
                $query->andWhere(['type_shop' => $shopType]);
            }
            if ($status != Yii::$app->params['value_all']) {
                $query->andWhere(['status' => $status]);
            } else {
                $query->andWhere(['in', 'status', array_keys(Supplier::STATUSES_ONLINE_OFFLINE)]);
            }
            if ($categoryId) {
                $query->andWhere(['in', 'category_id', GoodsCategory::level23Ids($categoryId, true)]);
            }
        } else {
            $query->andWhere(['in', 'status', array_keys(Supplier::STATUSES_ONLINE_OFFLINE)]);
            $query->andWhere(['or', ['like', 'shop_no', $keyword], ['like', 'shop_name', $keyword]]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'supplier_list' => ModelService::pagination(
                    $query,
                    Supplier::FIELDS_LIST,
                    Supplier::FIELDS_LIST_EXTRA,
                    new Supplier,
                    $page,
                    $size,
                    ModelService::FORMAT_DATA_METHOD,
                    ModelService::EXTRA_DATA_METHOD,
                    $orderBy)
            ],
        ]);
    }

    /**
     * Categories which have style or/and series
     *
     * @return string
     */
    public function actionCategoriesHaveStyleSeries()
    {
        $pid = (int)Yii::$app->request->get('pid', 0);
        $type = trim(Yii::$app->request->get('type', ''));
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'have_style_series_categories' => GoodsCategory::haveStyleSeriesCategoriesByPid($pid, $type)
            ]
        ]);
    }

    /**
     * Reset category attribute has_style and/or has_series action
     *
     * @return string
     */
    public function actionCategoriesStyleSeriesReset()
    {
        $type = trim(Yii::$app->request->post('type', ''));
        $categoryIds = Yii::$app->request->post('category_ids', []);
        $operator = UserRole::roleUser(Yii::$app->user->identity, Yii::$app->session[User::LOGIN_ROLE_ID]);
        $res = GoodsCategory::resetStyleSeries($operator, StringService::merge($categoryIds), $type);
        return Json::encode([
            'code' => 200,
            'msg' => 200 == $res ? 'OK' : Yii::$app->params['errorCodes'][$res],
        ]);
    }
}
