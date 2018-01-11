<?php

namespace app\controllers;

use app\models\GoodsCategory;
use app\models\LineSupplier;
use app\models\LineSupplierGoods;
use app\models\LogisticsDistrict;
use app\models\Role;
use app\models\User;
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
        'get-supplier-info-by-shop-no',
        'line-supplier-list',
        'add-line-supplier',
        'switch-line-supplier-status',
        'get-edit-supplier-info-by-shop-no',
        'up-line-supplier',
        'line-supplier-goods-list',
        'find-supplier-line-goods',
        'find-supplier-line-by-district-code',
        'add-line-supplier-goods',
        'up-line-supplier-goods',
        'switch-line-supplier-goods-status',
        'del-line-supplier',
        'del-line-supplier-goods',
        'supplier-be-audited-list',
        'supplier-be-audited-detail',
        'get-up-supplier-line-goods'
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
                    'add-line-supplier' => ['post',],
                    'switch-line-supplier-status'=> ['post',],
                    'up-line-supplier'=> ['post',],
                    'add-line-supplier-goods'=> ['post',],
                    'up-line-supplier-goods'=> ['post',],
                    'switch-line-supplier-goods-status'=> ['post',],
                    'del-line-supplier'=> ['post',],
                    'del-line-supplier-goods'=> ['post',],
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
        $user = Yii::$app->user->identity;

        $supplier = Supplier::find()->where(['uid' => $user->id])->one();
        if ($supplier) {
            $userRole = UserRole::find()
                ->where([
                    'user_id' => $user->id,
                    'role_id' => Yii::$app->params['supplierRoleId'],
                ])
                ->andWhere(['in', 'review_status', [Role::AUTHENTICATION_STATUS_APPROVED]])
                ->one();
            if ($userRole) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }

        $code = Supplier::certificationApplication($user, Yii::$app->request->post(), $supplier);
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
//            'status' => GoodsRecommendSupplier::STATUS_ONLINE,
            'delete_time' => 0,
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
                    'balance' => StringService::formatPrice($supplier->availableamount*0.01),
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
     * 通过店铺号获取线下体验店添加商家信息
     * @return string
     */
    public  function  actionGetSupplierInfoByShopNo()
    {
        $shop_no = trim(Yii::$app->request->get('shop_no', ''));
        $Supplier=Supplier::find()
            ->select('shop_name,type_shop,category_id,district_code,id,status')
            ->where(['shop_no'=>$shop_no])
            ->asArray()
            ->one();
        if (!$Supplier)
        {
            $code=1076;
            return Json::encode([
                'code' => $code,
                'msg' =>Yii::$app->params['errorCodes'][$code],
            ]);
        }
        if ($Supplier['status']!=Supplier::STATUS_ONLINE && $Supplier['status']!=Supplier::STATUS_OFFLINE)
        {
            $code=1076;
            return Json::encode([
                'code' => $code,
                'msg' =>Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $line_supplier=LineSupplier::find()
            ->where(['supplier_id'=>$Supplier['id']])
            ->one();
        if ($line_supplier)
        {
            $code=1077;
            return Json::encode([
                'code' => $code,
                'msg' =>Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $Supplier['type_shop']=Supplier::TYPE_SHOP[$Supplier['type_shop']];
        $three_category=GoodsCategory::findOne($Supplier['category_id']);
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
        unset($Supplier['category_id']);
        unset($Supplier['id']);
        return Json::encode(
        [
            'code' => 200,
            'msg' => 'OK',
            'data' => $Supplier,
        ]);
    }


    /**
     * 线下体验店商家列表
     * @return string
     */
    public  function  actionLineSupplierList()
    {
        $request=\Yii::$app->request;
        $keyword=$request->get('keyword','');
        $status=$request->get('status','');
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', Supplier::PAGE_SIZE_DEFAULT);
        $district_code=$request->get('district_code',0);
        if (!is_numeric($district_code))
        {
            $district_code=0;
        }
        $district_code=LogisticsDistrict::GetVagueDistrictCode($district_code);
        $where="L.district_code  like '%{$district_code}%' ";
        if ($keyword)
        {
            $where .=" and CONCAT(S.shop_name,S.shop_no) like '%{$keyword}%'";
        }
        if ($status==1 || $status==2)
        {
            $where .=" and  L.status={$status}";
        }
        $data=LineSupplier::pagination($where,$page,$size);
        return Json::encode
        ([
            'code'=>200,
            'msg' =>'ok',
            'data' => $data
        ]);
    }

    /**
     * 添加线下体验店商家
     * @return string
     */
    public  function  actionAddLineSupplier()
    {
        $post=\Yii::$app->request->post();
        if (!array_key_exists('shop_no',$post))
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $supplier=Supplier::find()
            ->where(['shop_no'=>$post['shop_no']])
            ->one();
        if (!$supplier)
        {
            $code=1076;
            return Json::encode
            ([
                'code' => $code,
                'msg' =>Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (
            $supplier->status!=Supplier::STATUS_ONLINE
            && $supplier->status!=Supplier::STATUS_OFFLINE
        )
        {
            $code=1076;
            return Json::encode([
                'code' => $code,
                'msg' =>Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $line_supplier=LineSupplier::find()
            ->where(['supplier_id'=>$supplier->id])
            ->one();
        if ($line_supplier)
        {
            $code=1077;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $code=Supplier::AddLineSupplier($post,$supplier->id);
        return Json::encode([
            'code' => $code,
            'msg' => 200 == $code ? 'ok' : Yii::$app->params['errorCodes'][$code],
        ]);
    }


    /**
     * 开启或者关闭线下体验店
     * @return string
     */
    public  function  actionSwitchLineSupplierStatus()
    {
        $code=LineSupplier::SwitchLineSupplierStatus(\Yii::$app->request->post());
        return Json::encode([
            'code' => $code,
            'msg' => 200 == $code ? 'ok' : Yii::$app->params['errorCodes'][$code],
        ]);
    }


    /**
     * 通过店铺号获取线下体验店编辑商家信息
     * @return string
     */
    public  function  actionGetEditSupplierInfoByShopNo()
    {
        $shop_no = trim(Yii::$app->request->get('shop_no', ''));
        $Supplier=Supplier::find()
            ->select('shop_name,type_shop,category_id,id,status')
            ->where(['shop_no'=>$shop_no])
            ->asArray()
            ->one();
        if (!$Supplier)
        {
            $code=1076;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        if ($Supplier['status']!=Supplier::STATUS_ONLINE && $Supplier['status']!=Supplier::STATUS_OFFLINE)
        {
            $code=1076;
            return Json::encode([
                'code' => $code,
                'msg' =>Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $line_supplier=LineSupplier::find()
            ->where(['supplier_id'=>$Supplier['id']])
            ->one();
        if ($line_supplier)
        {
            $Supplier['mobile']=$line_supplier['mobile'];
            $pro=substr($line_supplier['district_code'],0,2);
            $ci=substr($line_supplier['district_code'],2,2);
            $dis=substr($line_supplier['district_code'],4,2);
            $Supplier['pro']=$pro.'0000';
            $Supplier['ci']=$pro.$ci.'00';
            $Supplier['dis']=(string)$line_supplier['district_code'];
            $Supplier['district_code']=$line_supplier['district_code'];
            $Supplier['address']=$line_supplier['address'];
        }
        $Supplier['type_shop']=Supplier::TYPE_SHOP[$Supplier['type_shop']];
        $three_category=GoodsCategory::findOne($Supplier['category_id']);
        $Supplier['category']='';
        if ($three_category)
        {
            $category_arr=explode(',',$three_category->path);
            $first_category=GoodsCategory::find()
                ->select('path,title,parent_title')
                ->where(['id'=>$category_arr[0]])
                ->one();
            $Supplier['category']=$first_category->title.'-'.$three_category->parent_title.'-'.$three_category->title;
            $Supplier['status']=$line_supplier->status;

        }
        unset($Supplier['category_id']);
        unset($Supplier['id']);
        return Json::encode(
        [
            'code' => 200,
            'msg' => 'OK',
            'data' => $Supplier,
        ]);
    }


    /**
     * 编辑线下体验店商家
     * @return string
     */
    public  function  actionUpLineSupplier()
    {
        $post=\Yii::$app->request->post();
        if (!array_key_exists('shop_no',$post))
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $supplier=Supplier::find()
            ->where(['shop_no'=>$post['shop_no']])
            ->one();
        if (
            !$supplier
            || $supplier->status!=Supplier::STATUS_ONLINE
        )
        {
            $code=1076;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $code=Supplier::UpLineSupplier($post,$supplier->id);
        return Json::encode([
            'code' => $code,
            'msg' => 200 == $code ? 'ok' : Yii::$app->params['errorCodes'][$code],
        ]);
    }


    /**
     * 线下体验店商品列表
     * @return string
     */
    public  function  actionLineSupplierGoodsList()
    {
        $request=\Yii::$app->request;
        $keyword=$request->get('keyword','');
        $status=$request->get('status','');
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', Supplier::PAGE_SIZE_DEFAULT);
        $district_code=$request->get('district_code',0);
        if (!is_numeric($district_code))
        {
            $district_code=0;
        }
        $district_code=LogisticsDistrict::GetVagueDistrictCode($district_code);
        $where="L.district_code  like '%{$district_code}%' ";
        if ($keyword)
        {
            $where .=" and  CONCAT(S.shop_name,S.shop_no,G.sku,G.title) like '%{$keyword}%'";
        }
        if ($status==1 || $status==2)
        {
            $where .=" and  LG.status={$status}";
        }
        $data=LineSupplierGoods::pagination($where,$page,$size);
        return Json::encode([
            'code'=>200,
            'msg' =>'ok',
            'data' => $data
        ]);
    }

    /**
     * 通过sku（商品编号）  获取店铺名称 and商品名称
     * @return string
     */
    public  function   actionFindSupplierLineGoods()
    {
        $sku=\Yii::$app->request->get('sku');
        $goods=Goods::find()
            ->select('title,supplier_id,id,status')
            ->where(['sku'=>$sku])
            ->one();
        if (!$goods)
        {
            $code=1078;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        if ($goods->status==Goods::STATUS_OFFLINE)
        {
            $code=1086;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        if ($goods->status!=Goods::STATUS_ONLINE)
        {
            $code=1078;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $lineSupplierGoods=LineSupplierGoods::find()
            ->where(['goods_id'=>$goods->id])
            ->one();
        if ($lineSupplierGoods)
        {
            $code=1079;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $supplier=Supplier::findOne($goods->supplier_id);
        return Json::encode([
            'code'=>200,
            'msg' =>'ok',
            'data' =>[
                'goods_name'=>$goods->title,
                'shop_name'=>$supplier->shop_name
            ]
        ]);

    }

    /**
     * 通过地区编号获取线下体验店商家信息
     * @return string
     */
    public  function   actionFindSupplierLineByDistrictCode()
    {
        $district_code=\Yii::$app->request->get('district_code',0);
        $keyword=\Yii::$app->request->get('keyword','');
        $district_code=LogisticsDistrict::GetVagueDistrictCode($district_code);
        $data=LineSupplier::FindLineSupplierByDistrictCode($district_code,$keyword);
        return Json::encode([
            'code'=>200,
            'msg' =>'ok',
            'data' =>$data
        ]);
    }

    /**
     * 添加线下体验店商品
     * @return string
     */
    public  function  actionAddLineSupplierGoods()
    {
        $post=Yii::$app->request->post();
        $code=1000;
        if (
            !array_key_exists('line_id',$post)
            ||!array_key_exists('sku',$post))
        {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $goods=Goods::find()->where(['sku'=>$post['sku']])->one();
        if (!$goods)
        {
            return Json::encode([
                'code' => $code,
                'msg' => '没有此商品,请重新输入',
            ]);
        }
        if ($goods->status==Goods::STATUS_OFFLINE)
        {
            $code=1086;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        if ($goods->status!=Goods::STATUS_ONLINE)
        {
            $code=1078;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $LineSupplierGoods=LineSupplierGoods::find()
            ->where(['goods_id'=>$goods->id])
            ->one();
        if ($LineSupplierGoods)
        {
            return Json::encode([
                'code' => $code,
                'msg' =>'商品编号重复,请重新输入'
            ]);
        }
         $code=LineSupplierGoods::AddLineGoods($post);
         return Json::encode([
             'code' => $code,
             'msg' => 200 == $code ? 'ok' : Yii::$app->params['errorCodes'][$code],
         ]);
    }


    /**
     * 编辑线下体验店商品
     * @return string
     */
    public  function  actionUpLineSupplierGoods()
    {
        $post=Yii::$app->request->post();
        $code=LineSupplierGoods::UpLineGoods($post);
        return Json::encode
        ([
            'code' => $code,
            'msg' => 200 == $code ? 'ok' : Yii::$app->params['errorCodes'][$code],
        ]);
    }


    /**
     * 开启 or  关闭 线下体验店品状态
     * @return string
     */
    public  function  actionSwitchLineSupplierGoodsStatus()
    {
        $code=LineSupplierGoods::SwitchLineSupplierGoodsStatus(\Yii::$app->request->post());
        return Json::encode([
            'code' => $code,
            'msg' => 200 == $code ? 'ok' : Yii::$app->params['errorCodes'][$code],
        ]);
    }


    /**
     * 删除线下体验店商家
     * @return string
     */
    public  function  actionDelLineSupplier()
    {
        $code=LineSupplier::DelLineSupplier(\Yii::$app->request->post('shop_no'));
        return Json::encode([
            'code' => $code,
            'msg' => 200 == $code ? 'ok' : Yii::$app->params['errorCodes'][$code],
        ]);
    }


    /**
     * 删除线下体验店商品
     * @return string
     */
    public  function  actionDelLineSupplierGoods()
    {
        $code=LineSupplierGoods::DelLineSupplierGoods(\Yii::$app->request->post('line_goods_id'));
        return Json::encode([
            'code' => $code,
            'msg' => 200 == $code ? 'ok' : Yii::$app->params['errorCodes'][$code],
        ]);
    }


    /**
     * 商家入驻列表
     * @return string
     */
    public  function  actionSupplierBeAuditedList()
    {
        $request=\Yii::$app->request;
        $code = 1000;
        $timeType = trim(Yii::$app->request->get('time_type'));
        !$timeType && $timeType = 'all';
        if (!in_array($timeType, array_keys(Yii::$app->params['timeTypes']))) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where="L.role_id=".Yii::$app->params['supplierRoleId'];
        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));

            if (($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime))
            ){
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
            $startTime && $where .= " and L.review_apply_time >= {$startTime}";
        }

        if ($endTime) {
            $endTime = strtotime($endTime);
            $endTime && $where .= " and L.review_apply_time <= {$endTime}";
        }
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', ModelService::PAGE_SIZE_DEFAULT);
        $keyword=$request->get('keyword');
        if ($keyword)
        {
                $where .=" and CONCAT(U.mobile,S.shop_name,U.aite_cube_no) like '%{$keyword}%'";
        }
        $sort = Yii::$app->request->get('sort', []);
        $model = new UserRole();
        $orderBy = $sort ? ModelService::sortFields($model, $sort) : ModelService::sortFields($model);
        if ($orderBy)
        {
            $orderBy='L.'.$orderBy;
        }
        $review_status=$request->get('review_status',3);
        if (in_array($review_status,[0,1,2]))
        {
            $where.=' and  L.review_status='.$review_status;
        }
        $select='L.review_apply_time,L.review_status,U.mobile,S.shop_name,S.type_shop,S.category_id,S.id,S.shop_no,U.aite_cube_no,L.review_remark,L.review_time';
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'list' => UserRole::paginationBySupplier($where,$select,  $page, $size,$orderBy)
            ]
        ]);
    }


    /**
     * 入驻详情
     * @return string
     */
    public  function  actionSupplierBeAuditedDetail()
    {
        $supplier_id=Yii::$app->request->get('supplier_id');
        $Supplier=Supplier::findOne($supplier_id);
        if (!$Supplier)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $category=GoodsCategory::GetCateGoryById($Supplier->category_id);
        $user=User::findOne($Supplier->uid);
        $user_role=UserRole::find()
            ->where(['user_id'=>$user->id])
            ->andWhere(['role_id'=>Yii::$app->params['supplierRoleId']])
            ->one();
        $shop_type=Supplier::TYPE_SHOP[$Supplier->type_shop];
        $type_org=Supplier::TYPE_ORG[$Supplier->type_org];
        $reviewer=User::find()
            ->where(['id'=>$user_role->reviewer_uid])
            ->select('nickname')
            ->one();
        if ($reviewer)
        {
            $reviewer_name=$reviewer->nickname;
        }else{
            $reviewer_name='';
        }
        $review_time=$user_role->review_time;
        if ($review_time!=0)
        {
            $review_time=date('Y-m-d H:i',$user_role->review_time);
        }else{
            $review_time='';
        }
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'category' =>$category ,
                'shop_no'=>$Supplier->shop_no,
                'shop_name'=>$Supplier->shop_name,
                'shop_type'=>$shop_type,
                'name'=>$Supplier->name,
                'licence'=>$Supplier->licence,
                'type_org'=>$type_org,
                'licence_image'=>$Supplier->licence_image,
                'legal_person'=>$user->legal_person,
                'identity_no'=>$user->identity_no,
                'identity_card_front_image'=>$user->identity_card_front_image,
                'identity_card_back_image'=>$user->identity_card_back_image,
                'mobile'=>$user->mobile,
                'aite_cube_no'=>$user->aite_cube_no,
                'review_apply_time'=>date('Y-m-d H:i',$user_role->review_apply_time),
                'review_time'=>$review_time,
                'review_status'=>$user_role->review_status,
                'review_remark'=>$user_role->review_status==UserRole::REVIEW_AGREE?$Supplier->approve_reason:$Supplier->reject_reason,
                'reviewer_name'=>$reviewer_name,
            ]
        ]);
    }


    /**
     * 商家入驻审核
     * @return string
     */
    public  static function actionSupplierBeAuditedApplyHandle()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $status=Yii::$app->request->post('status');
        $review_remark=Yii::$app->request->post('review_remark');
        if ($status!=UserRole::REVIEW_AGREE && $status!=UserRole::REVIEW_DISAGREE )
        {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier_id=Yii::$app->request->post('supplier_id');
        $supplier=Supplier::findOne($supplier_id);
        if (!$supplier) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user_role=UserRole::find()
             ->where(['user_id'=>$supplier->uid])
             ->andWhere(['role_id'=>Yii::$app->params['supplierRoleId']])
             ->one();
        $tran = Yii::$app->db->beginTransaction();
        $time=time();
        try{
            $user_role->review_status=$status;
            $user_role->review_time=$time;
            $user_role->review_remark=$review_remark;
            $user_role->reviewer_uid=$user->id;
            if (!$user_role->save(false))
            {
                $tran->rollBack();
            }
            if ($status==UserRole::REVIEW_DISAGREE) {
                $supplier->status=Supplier::STATUS_OFFLINE;
                $supplier->reject_reason=$review_remark;
            }
            if ($status==UserRole::REVIEW_AGREE)
            {
                $supplier->status=Supplier::STATUS_ONLINE;
                $supplier->approve_reason=$review_remark;
            }
            if (!$supplier->save(false))
            {
                $tran->rollBack();
            }
            $tran->commit();
            return Json::encode([
                'code' =>  200,
                'msg'  => 'ok'
            ]);
        }catch (\Exception $e){
            $tran->rollBack();
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /**
     * @return string
     */
    public  function  actionGetUpSupplierLineGoods()
    {
        $sku=Yii::$app->request->get('sku');
        $line_id=Yii::$app->request->get('line_id');
        $Goods=Goods::find()
            ->where(['sku'=>$sku])
            ->one();
        if (!$Goods)
        {
            $code=1078;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $lineGoods=LineSupplierGoods::find()
            ->where(['goods_id'=>$Goods->id])
            ->one();
        if (!$lineGoods)
        {
            $code=1078;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $belongSupplier=Supplier::findOne($Goods->supplier_id);
        if (!$belongSupplier)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $lineSupplier=LineSupplier::findOne($line_id);
        if (!$lineSupplier)
        {
            $code=1076;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $lineShop=Supplier::findOne($lineSupplier->supplier_id);
        if (!$lineShop)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $pro=substr($lineSupplier->district_code,0,2);
        $ci=substr($lineSupplier->district_code,2,2);
        $dis=substr($lineSupplier->district_code,4,2);
        return Json::encode([
            'code' => 200,
            'msg'  => 'ok',
            'data'=>[
                'goods_name'=>$Goods->title,
                'belong_shop_name'=>$belongSupplier->shop_name,
                'pro'=>$pro.'0000',
                'ci'=>$pro.$ci.'00',
                'dis'=>$pro.$ci.$dis,
                'line_shop_name'=>$lineShop->shop_name,
                'line_id'=>$lineSupplier->id,
                'district'=>LogisticsDistrict::GetLineDistrictByDistrictCode($lineSupplier->district_code),
                'mobile'=>$lineSupplier->mobile,
                'status'=>$lineGoods->status,
            ]
        ]);
    }








}