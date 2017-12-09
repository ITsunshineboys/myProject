<?php

namespace app\controllers;

use app\models\GoodsCategory;
use app\services\ExceptionHandleService;
use app\models\Supplier;
use app\models\Goods;
use app\models\GoodsRecommendSupplier;
use app\models\UserRole;
use app\services\ModelService;
use app\services\StringService;
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
        'index-app',
        'reset-district',
        'reset-icon',
        'shop-types',
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
                    'reset-district' => ['post',],
                    'reset-icon' => ['post',],
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

        $supplier = Supplier::find()->where(['uid' => $user->id])->one();
        if ($supplier && $supplier->status == Supplier::STATUS_APPROVED) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $supplier && Supplier::deleteAll(['id' => $supplier->id]);
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
                'supplier_view' => $supplier->view(),
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
        $code = 1000;

        $supplierId = (int)Yii::$app->request->get('supplier_id', 0);
//        $districtCode = (int)Yii::$app->request->get('district_code', Yii::$app->params['district_default']);
        if (!$supplierId) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', ModelService::PAGE_SIZE_DEFAULT);
        $where = [
            'type' => GoodsRecommendSupplier::RECOMMEND_GOODS_TYPE_SECOND,
            'status' => GoodsRecommendSupplier::STATUS_ONLINE,
            'supplier_id' => $supplierId,
//            'district_code' => $districtCode,
        ];

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'recommend_second' => GoodsRecommendSupplier::pagination($where, GoodsRecommendSupplier::$appFields, $page, $size, ['sorting_number' => SORT_ASC]),
            ],
        ]);
    }

    /**
     * Shop index action.
     *
     * @return string
     */
    public function actionIndex()
    {
        $code = 1000;

        $supplierId = (int)Yii::$app->request->get('supplier_id', 0);
        if (!$supplierId) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $supplier = Supplier::find()->where(['id' => $supplierId, 'status' => Supplier::STATUS_ONLINE])->one();
        if (!$supplier) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $data = ModelService::selectModelFields($supplier, Supplier::FIELDS_SHOP_INDEX_MODEL);
        $data['carousel'] = GoodsRecommendSupplier::carousel($supplierId);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'index' => $data
            ]
        ]);
    }

    /**
     * Shop index action(for app).
     *
     * @return string
     */
    public function actionIndexApp()
    {
        $user = Yii::$app->user->identity;
        $supplier = UserRole::roleUser($user, Yii::$app->params['supplierRoleId']);
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'supplier_index_app' => [
                    'stat' => Supplier::statData($supplier->id),
                    'aite_cube_no' => $user->aite_cube_no,
                    'shop_name' => $supplier->shop_name,
                    'shop_no' => $supplier->shop_no,
                    'icon' => $supplier->icon,
                    'id' => $supplier->id,
                ]
            ],
        ]);
    }

    /**
     * Reset district action.
     *
     * @return string
     */
    public function actionResetDistrict()
    {
        $districtCode = (int)Yii::$app->request->post('district_code', 0);
        $address = trim(Yii::$app->request->post('address', ''));
        $supplier = UserRole::roleUser(Yii::$app->user->identity, Yii::$app->params['supplierRoleId']);
        $res = ModelService::resetDistrict($supplier, $districtCode, $address);
        return Json::encode([
            'code' => $res,
            'msg' => 200 == $res ? '修改地区成功' : Yii::$app->params['errorCodes'][$res],
        ]);
    }

    /**
     * Reset icon action.
     *
     * @return string
     */
    public function actionResetIcon()
    {
        $icon = trim(Yii::$app->request->post('icon', ''));
        $supplier = UserRole::roleUser(Yii::$app->user->identity, Yii::$app->params['supplierRoleId']);
        $res = ModelService::resetIcon($supplier, $icon);
        return Json::encode([
            'code' => $res,
            'msg' => 200 == $res ? '修改Logo成功' : Yii::$app->params['errorCodes'][$res],
        ]);
    }

    /**
     * Shop types action.
     *
     * @return string
     */
    public function actionShopTypes()
    {
        $shopTypes = [];
        foreach (Supplier::TYPE_SHOP_APP as $id => $type) {
            $shopTypes[] = [
                'id' => $id,
                'type' => $type,
            ];
        }

        return Json::encode(
            [
                'code' => 200,
                'msg' => 'OK',
                'data' => [
                    'shop_types' => $shopTypes
                ],
            ]);
    }

    /**
     * 通过店铺号获取商家信息
     * @return string
     */
    public  function  actionGetSupplierInfoByShopNo()
    {
        $shop_no = trim(Yii::$app->request->get('shop_no', ''));
        $Supplier=Supplier::find()
            ->select('shop_name,nickname,type_shop,category_id,district_code,district_name')
            ->where(['shop_no'=>$shop_no])
            ->asArray()
            ->one();
        if (!$Supplier)
        {
            return Json::encode([
                'code' => 1000,
                'msg' => Yii::$app->params['errorCodes'][1000],
            ]);
        }
        $Supplier['type_shop']=Supplier::TYPE_SHOP[$Supplier['type_shop']];
        $three_category=GoodsCategory::findOne(Supplier::TYPE_SHOP[$Supplier['category_id']]);
        $Supplier['category']='';
        if ($three_category)
        {
            $category_arr=explode(',',$three_category->path);
            $first_category=GoodsCategory::find()
                ->select('path,title,parent_title')
                ->where(['id'=>$category_arr[0]])
                ->one();
            $Supplier['category']=$first_category->title.'-'.$three_category->parent_title.'-'.$three_category->title;
        }
    }


}