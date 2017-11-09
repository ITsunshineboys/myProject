<?php

namespace app\controllers;

use app\models\BrainpowerInitalSupervise;
use app\models\CarpentryAdd;
use app\models\CoefficientManagement;
use app\models\DecorationAdd;
use app\models\Effect;
use app\models\EffectPicture;
use app\models\EngineeringStandardCraft;
use app\models\EngineeringUniversalCriterion;
use app\models\Goods;
use app\models\GoodsAttr;
use app\models\GoodsCategory;
use app\models\LaborCost;
use app\models\LogisticsTemplate;
use app\models\MaterialPropertyClassify;
use app\models\Points;
use app\models\ProjectView;
use app\models\Series;
use app\models\StairsDetails;
use app\models\Style;
use app\models\WorkerCraftNorm;
use app\models\WorksBackmanData;
use app\models\WorksData;
use app\models\WorksWorkerData;
use app\services\BasisDecorationService;
use app\services\ExceptionHandleService;
use app\services\LogisticsService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use Yii;

class OwnerController extends Controller
{

    const JUDGE_VALUE = 0;
    const TYPE_VALUE = 1;
    const WALL_HIGH = 2.8;
    const WALL = 4;
    const WALL_SPACE = 3;
    const DIGITAL = 2;
    const PRICE_UNITS = 100;
    const DEFAULT_CITY_CODE = 510100;

    const WEAK_MATERIAL   = ['网线', '线管', '底盒'];
    const STRING_MATERIAL = ['电线', '线管', '底盒'];
    const WATERWAY_MATERIAL = ['PPR水管', 'PVC管'];
    const WATERPROOF_MATERIAL = ['防水涂料'];
    const CARPENTRY_MATERIAL = ['石膏板', '龙骨', '丝杆'];
    const LATEX_MATERIAL = ['腻子', '乳胶漆底漆', '乳胶漆面漆', '阴角线', '石膏粉'];
    const TILER_MATERIAL = ['水泥', '自流平', '河沙'];
    const BACKMAN_MATERIAL = ['水泥','河沙','空心砖'];
    /**
     * work category details
     */
    const WORK_CATEGORY = [
        'plumber'           => '水电工',
        'waterproof_worker' => '防水工',
        'woodworker'        => '木工',
        'painters'          => '油漆工',
        'mason'             => '泥瓦工',
        'backman'           => '杂工',
    ];

    /**
     * points category details
     */
    const POINTS_CATEGORY = [
        'weak_current'  => '弱电点位',
        'strong_current'=> '强电点位',
        'waterway'      => '水路点位',
        'work_area'     => '做工面积',

    ];

    /**
     * 工种
     */
    const PROJECT_DETAILS = [
        'weak_current'      => '弱电',
        'strong_current'    => '强电',
        'waterway'          => '水路',
        'waterproof'        => '防水',
        'carpentry'         => '木作',
        'emulsion_varnish'  => '乳胶漆',
        'oil_paint'         => '油漆',
        'tiler'             => '泥工',
    ];

    /**
     * 其它信息
     */
    const WORKMANSHIP = [
        'flat_area'                     => '平顶面积',
        'modelling_length'              => '造型长度',
        'emulsion_varnish_primer_area'  => '乳胶漆底漆面积',
        'emulsion_varnish_cover_area'   => '乳胶漆面漆面积',
        'concave_line_length'           => '阴角线长度',
        'putty_area'                    => '腻子面积',
        'protective_layer_length'       => '保护层长度',
        'geostrophy_area'               => '贴地砖面积',
        'wall_brick_area'               => '贴墙砖面积',
    ];

    /**
     * room  detail
     */
    const ROOM_DETAIL = [
        'kitchen' => '厨房',
        'toilet'  => '卫生间',
        'hall'    => '客厅',
        'bedroom' => '卧室',
    ];

    /**
     * room area
     */
    const ROOM_AREA = [
        'kitchen_area' => '厨房面积',
        'toilet_area'  => '卫生间面积',
        'hall_area'    => '客餐厅及过道面积',
        'bedroom_area' => '卧室面积',
    ];

    const MATERIALS_CLASSIFY = [
        'auxiliary_material' => '辅材',
        'principal_material' => '主要材料',
        'immobilization' => '固定家具',
        'move' => '移动家具',
        'home_appliances' => '家电配套',
        'mild' => '软装配套',
        'capacity' => '智能配套',
        'live' => '生活配套',
    ];

    const AREA_PROPORTION = '面积比例';
    /**
     * Actions accessed by logged-in users
     */
    const ACCESS_LOGGED_IN_USER = [
        'certification',
        'view-identity',
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
     * 系列、风格和楼梯
     * @return string
     */
    public function actionSeriesAndStyle()
    {
        $show['stairs_details'] = StairsDetails::findByAll();
        $select = 'id,series,intro,theme';
        $_select ='id,style,intro,theme,images';
        $show['series'] = Series::findByAll($select);
        $show['style'] = Style::findByAll($_select);
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'show' => $show,
            ]
        ]);
    }

    /**
     * 搜索功能
     * @return string
     */
    public function actionSearch()
    {
        $id  = trim(Yii::$app->request->post('id',''));
        $str = trim(Yii::$app->request->post('str',''));
        if ($str != null){
            $select = 'id,toponymy,province,city,district_code,district,street';
            $effect = Effect::districtSearch($str,$select);
            foreach ($effect as &$value){
                $value['detailed_address'] = $value['province'].$value['city'].$value['district'].$value['street'];
            }
            return Json::encode([
                'code' => 200,
                'msg' => '成功',
                'data' => [
                    'list_effect' => $effect,
                ]
            ]);
        }

        if ($id != null){
            $id_effect = Effect::findOne(['id'=>$id]);
            return Json::encode([
                'code' => 200,
                'msg'  => '成功',
                'data' => Effect::findAll(['toponymy'=>$id_effect->toponymy]),
            ]);
        }

    }

    /**
     * 商品分类
     * @return string
     */
    public function actionClassify()
    {
        $classify['stair'] = GoodsCategory::findLevel(1);
        $classify['level'] = GoodsCategory::findLevel(2);
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'pid' => $classify,
            ]
        ]);
    }

    /**
     * 弱电价格
     * @return string
     */
    public function actionWeakCurrent()
    {
        $post = \Yii::$app->request->post();
        //人工价格
        $workers = LaborCost::profession($post['city'],self::WORK_CATEGORY['plumber']);
        if ($workers == null){
            $code = 1056;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $worker_kind_details = WorkerCraftNorm::findByLaborCostId($workers['id'],self::POINTS_CATEGORY['weak_current']);
        if ($worker_kind_details == null){
            $code = 1057;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        //      点位 和 材料查询
        $points_where = ['and',['level'=>1],['title'=>self::PROJECT_DETAILS['weak_current']]];
        $points = Points::findByOne('count',$points_where);
        if ($points == null){
            $code = 1058;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        //查询弱电所需要材料
        $goods_select ='goods.id,goods.category_id,goods.platform_price,goods.supplier_price,goods.purchase_price_decoration_company,goods_brand.name,gc.title,logistics_district.district_name,goods.series_id,goods.style_id,goods.subtitle,goods.profit_rate,gc.path,goods.cover_image,supplier.shop_name';
        $goods = Goods::priceDetail(self::WALL_SPACE, self::WEAK_MATERIAL,$goods_select);
        if ($goods == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $judge = BasisDecorationService::priceConversion($goods);
        $weak_current = BasisDecorationService::judge($judge,$post);

        //当地工艺
        $craft_select = 'id,material,project_details';
        $craft = EngineeringStandardCraft::findByAll(self::PROJECT_DETAILS['weak_current'],$post['city'],$craft_select);
        if ($craft == null){
            $code = 1059;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        //人工总费用
//        $labor_all_cost['price'] = BasisDecorationService::laborFormula($points['count'],$workers,$worker_kind_details);
        $labor_all_cost['price'] = BasisDecorationService::laborFormula($points['count'],$workers['univalence'],$worker_kind_details['quantity']);
        $labor_all_cost['worker_kind'] = $workers['worker_kind'];

        //材料总费用
        $material_price = BasisDecorationService::quantity($points['count'], $weak_current, $craft);
        $material = BasisDecorationService::electricianMaterial($weak_current, $material_price);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'weak_current_labor_price' => $labor_all_cost,
                'weak_current_material' => $material,
            ]
        ]);
    }

    /**
     * 强电价格
     * @return string
     */
    public function actionStrongCurrent()
    {
        $post = \Yii::$app->request->post();
        //人工价格
        $workers = LaborCost::profession($post['city'], self::WORK_CATEGORY['plumber']);
        if ($workers == null){
            $code = 1056;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $worker_kind_details = WorkerCraftNorm::findByLaborCostId($workers['id'],self::POINTS_CATEGORY['strong_current']);
        if ($worker_kind_details == null){
            $code = 1057;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        //强电点位
        $points_select = 'count';
        $points_where = ['and',['level'=>1],['title'=>self::PROJECT_DETAILS['weak_current']]];
        $points = Points::findByOne($points_select,$points_where);
        if ($points == null){
            $code = 1058;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        //查询弱电所需要材料
        $goods_select ='goods.id,goods.category_id,goods.platform_price,goods.supplier_price,goods.purchase_price_decoration_company,goods_brand.name,gc.title,logistics_district.district_name,goods.series_id,goods.style_id,goods.subtitle,goods.profit_rate,gc.path,goods.cover_image,supplier.shop_name';
        $goods = Goods::priceDetail(self::WALL_SPACE, self::STRING_MATERIAL,$goods_select);
        if ($goods == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $judge = BasisDecorationService::priceConversion($goods);
        $strong_current = BasisDecorationService::judge($judge, $post);

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll(self::PROJECT_DETAILS['strong_current'], $post['city']);
        if ($craft == null){
            $code = 1059;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        //人工总费用
        $labor_all_cost['price'] = BasisDecorationService::laborFormula($points['count'],$workers,$worker_kind_details);
        $labor_all_cost['worker_kind'] = $workers['worker_kind'];

        //材料总费用
        $material_price = BasisDecorationService::quantity($points['count'], $strong_current, $craft);
        $material = BasisDecorationService::electricianMaterial($strong_current, $material_price);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'strong_current_labor_price' => $labor_all_cost,
                'strong_current_material' => $material,
            ]
        ]);
    }

    /**
     * 水路
     * @return string
     */
    public function actionWaterway()
    {
        $post = \Yii::$app->request->post();
        //人工价格
        $waterway_labor = LaborCost::profession($post,self::WORK_CATEGORY['plumber']);
        if ($waterway_labor == null){
            $code = 1056;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $worker_kind_details = WorkerCraftNorm::findByLaborCostId($waterway_labor['id'],self::POINTS_CATEGORY['waterway']);
        if ($worker_kind_details == null){
            $code = 1057;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        //强电点位
        $points_select = 'count';
        $points_where = ['and',['level'=>1],['title'=>self::PROJECT_DETAILS['weak_current']]];
        $points = Points::findByOne($points_select,$points_where);
        if ($points == null){
            $code = 1058;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        //查询弱电所需要材料
        $select = "goods.id,goods.category_id,goods.platform_price,goods.supplier_price,goods.purchase_price_decoration_company,goods_brand.name,gc.title,logistics_district.district_name,goods.category_id,gc.path,goods.profit_rate,goods.subtitle,goods.series_id,goods.style_id,goods.cover_image,supplier.shop_name";
        $goods = Goods::priceDetail(self::WALL_SPACE,self::WATERWAY_MATERIAL,$select);
        if ($goods == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $judge = BasisDecorationService::priceConversion($goods);
        $waterway_current = BasisDecorationService::judge($judge, $post);

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll(self::PROJECT_DETAILS['waterway'], $post['city']);

        //人工总费用
        $labor_all_cost['price'] = BasisDecorationService::laborFormula($points['count'], $waterway_labor,$worker_kind_details);
        $labor_all_cost['worker_kind'] = $waterway_labor['worker_kind'];

        //材料总费用
        $material_price = BasisDecorationService::waterwayGoods($points['count'], $waterway_current, $craft);
        $material = BasisDecorationService::waterwayMaterial($waterway_current, $material_price);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'waterway_labor_price' => $labor_all_cost,
                'waterway_material_price' => $material,
            ]
        ]);
    }

    /**
     * 防水
     * @return string
     */
    public function actionWaterproof()
    {
        $post = \Yii::$app->request->post();
        //人工价格
        $_select = 'id,univalence,worker_kind';
        $__select = 'quantity,worker_kind_details';
        $waterproof_labor = LaborCost::profession($post, self::WORK_CATEGORY['waterproof_worker'],$_select);
        if ($waterproof_labor == null){
            $code = 1056;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $worker_kind_details = WorkerCraftNorm::findByLaborCostId($waterproof_labor['id'],self::POINTS_CATEGORY['work_area'],$__select);
        if ($worker_kind_details == null){
            $code = 1057;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        //防水所需材料
        $select = "goods.id,goods.category_id,goods.platform_price,goods.supplier_price,goods.purchase_price_decoration_company,goods_brand.name,gc.title,logistics_district.district_name,goods.category_id,gc.path,goods.profit_rate,goods.subtitle,goods.series_id,goods.style_id,goods.cover_image,supplier.shop_name";
        $goods = Goods::priceDetail(self::WALL_SPACE, self::WATERPROOF_MATERIAL,$select);
        if ($goods == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $judge = BasisDecorationService::priceConversion($goods);
        $waterproof = BasisDecorationService::judge($judge, $post);

        //厨房
        $kitchen = EngineeringUniversalCriterion::findByAll(BasisDecorationService::HOUSE_MESSAGE['kitchen']);
        $kitchen_area = BasisDecorationService::waterproofArea($kitchen, $post['area'], $post['kitchen']);
        //卫生间
        $toilet = EngineeringUniversalCriterion::findByAll(BasisDecorationService::HOUSE_MESSAGE['toilet']);
        $toilet_area = BasisDecorationService::waterproofArea($toilet, $post['area'], $post['toilet']);
        //总面积
        $total_area = $kitchen_area + $toilet_area;

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll(self::PROJECT_DETAILS['waterproof'], $post['city']);
        if ($craft == null){
            $code = 1059;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        //人工总费用（防水总面积÷【每天做工面积】）×【工人每天费用】
        $labor_all_cost['price'] = ceil($total_area / $worker_kind_details['quantity']) * $waterproof_labor['univalence'];
        $labor_all_cost['worker_kind'] = $waterproof_labor['worker_kind'];

        //材料总费用
        $material_price = BasisDecorationService::waterproofGoods($total_area, $waterproof, $craft);
        $material_total = [];
        foreach ($waterproof as $one_waterproof) {
            if ($one_waterproof['title'] == BasisDecorationService::GOODS_NAME['waterproof_coating']) {
                $one_waterproof['quantity'] = $material_price['quantity'];
                $one_waterproof['cost'] = $material_price['cost'];
                $goods_max [] = $one_waterproof;
            }
        }
        $material_total ['material'][] = BasisDecorationService::profitMargin($goods_max);
        $material_total ['total_cost'][] = $material_price['cost'];

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'waterproof_labor_price' => $labor_all_cost,
                'waterproof_material' => $material_total,
                'total_area' => $total_area,
            ]
        ]);
    }

    /**
     * 木作
     */
    public function actionCarpentry()
    {
        $post = \Yii::$app->request->post();
        $_select = 'id,univalence,worker_kind';
        $labor_cost = LaborCost::profession($post, self::WORK_CATEGORY['woodworker'],$_select);
        if ($labor_cost == null){
            $code = 1056;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $worker_kind_details = WorkerCraftNorm::findByLaborCostAll($labor_cost['id']);
        if ($worker_kind_details == null){
            $code = 1057;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        foreach ($worker_kind_details as $one_labor) {
            switch ($one_labor) {
                case $one_labor['worker_kind_details'] == self::WORKMANSHIP['flat_area']:
                    $flat = $one_labor['quantity'];
                    break;
                case $one_labor['worker_kind_details'] == self::WORKMANSHIP['modelling_length']:
                    $modelling = $one_labor['quantity'];
                    break;
            }
        }
        $series_all = Series::find()->asArray()->all();
        if ($series_all == null){
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => '系列不能为空',
            ]);
        }
        $style_all = Style::find()->asArray()->all();
        if ($style_all == null){
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => '风格不能为空',
            ]);
        }

        $carpentry_add = CarpentryAdd::findByStipulate($post['series'], $post['style']);
        if ($carpentry_add == null){
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => '木工添加项不能为空',
            ]);
        }

        // 造型长度
        $modelling_length = BasisDecorationService::carpentryModellingLength($carpentry_add,$series_all,$post['series']);
        //造型天数
        $modelling_day = BasisDecorationService::carpentryModellingDay($modelling_length, $modelling, $series_all, $style_all, $post['series']);
        //平顶天数
        $flat_day = BasisDecorationService::flatDay($carpentry_add, $flat, $series_all, $style_all, $post['series']);

        //人工费
        $labour_charges['price'] = BasisDecorationService::carpentryLabor($modelling_day, $flat_day, 1, $labor_cost['univalence']);
        $labour_charges['worker_kind'] = self::WORK_CATEGORY['woodworker'];

        //材料
        $select = "goods.id,goods.category_id,goods.platform_price,goods.supplier_price,goods.purchase_price_decoration_company,goods_brand.name,gc.title,logistics_district.district_name,goods.category_id,gc.path,goods.profit_rate,goods.subtitle,goods.series_id,goods.style_id,goods.cover_image,supplier.shop_name";
        $goods = Goods::priceDetail(self::WALL_SPACE, self::CARPENTRY_MATERIAL,$select);
        if ($goods == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $judge = BasisDecorationService::priceConversion($goods);
        $goods_price = BasisDecorationService::judge($judge, $post);

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll(self::PROJECT_DETAILS['carpentry'], $post['city']);
        if ($craft == null){
            $code = 1059;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        //石膏板费用
        $plasterboard_cost = BasisDecorationService::carpentryPlasterboardCost($modelling_length, $carpentry_add['flat_area'], $goods_price, $craft);
        //龙骨费用
        $keel_cost = BasisDecorationService::carpentryKeelCost($modelling_length, $carpentry_add['flat_area'], $goods_price, $craft);
        //丝杆费用
        $pole_cost = BasisDecorationService::carpentryPoleCost($modelling_length, $carpentry_add['flat_area'], $goods_price, $craft);
        //材料费用
        $material_cost = ($keel_cost['cost'] + $plasterboard_cost['cost'] + $pole_cost['cost']);
        $material_total = BasisDecorationService::carpentryGoods($goods_price,$keel_cost,$pole_cost,$plasterboard_cost,$material_cost);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'carpentry_labor_price' => $labour_charges,
                'carpentry_material' => $material_total,
                'carpentry_add_price' => $carpentry_add,
            ]
        ]);
    }

    /**
     * 乳胶漆
     */
    public function actionCoating()
    {
        $post = \Yii::$app->request->post();
        //工人一天单价
        $_select = 'id,univalence,worker_kind';
        $labor_costs = LaborCost::profession($post, self::WORK_CATEGORY['painters'],$_select);
        if ($labor_costs == null){
            $code = 1056;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $worker_kind_details = WorkerCraftNorm::findByLaborCostAll($labor_costs['id']);
        if ($worker_kind_details == null){
            $code = 1057;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        foreach ($worker_kind_details as $_labor_cost) {
            switch ($_labor_cost) {
                case $_labor_cost['worker_kind_details'] == self::WORKMANSHIP['emulsion_varnish_primer_area']:
                    $primer = $_labor_cost['quantity'];
                    break;
                case $_labor_cost['worker_kind_details'] == self::WORKMANSHIP['emulsion_varnish_cover_area']:
                    $finishing_coat = $_labor_cost['quantity'];
                    break;
                case $_labor_cost['worker_kind_details'] == self::WORKMANSHIP['concave_line_length']:
                    $concave_line = $_labor_cost['quantity'];
                    break;
                case $_labor_cost['worker_kind_details'] == self::WORKMANSHIP['putty_area']:
                    $putty = $_labor_cost['quantity'];
                    break;
            }
        }

        // 面积比例
        $points_where = ['title'=>self::AREA_PROPORTION];
        $points = Points::findByOne([],$points_where);
        if ($points == null){
            $code = 1058;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $proportion_where = ['points_id'=>$points['id']];
        $proportion = ProjectView::findByAll([],$proportion_where);
        if ($proportion == null){
            $code = 1060;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        foreach ($proportion as $one_proportion){
            if ($one_proportion['project'] == self::ROOM_AREA['kitchen_area']){
                $kitchen_area = $one_proportion;
            }
            if ($one_proportion['project'] == self::ROOM_AREA['toilet_area']){
                $toilet_area = $one_proportion;
            }
            if ($one_proportion['project'] == self::ROOM_AREA['hall_area']){
                $hall_area = $one_proportion;
            }
            if ($one_proportion['project'] == self::ROOM_AREA['bedroom_area']){
                $bedroom_area = $one_proportion;
            }
        }
        //卧室底漆面积
        $bedroom_primer_area = BasisDecorationService::paintedArea($kitchen_area['project_value'], $post['area'], $post['bedroom'],self::WALL_HIGH,self::WALL);

        //客餐厅底漆面积
        $drawing_room_primer_area = BasisDecorationService::paintedArea($hall_area['project_value'], $post['area'], $post['hall'], self::WALL_HIGH, self::WALL_SPACE);

//        乳胶漆底漆面积：卧室底漆面积+客厅底漆面积+餐厅底漆面积+其它面积1
        $primer_area = $bedroom_primer_area + $drawing_room_primer_area;
//        乳胶漆底漆天数：乳胶漆底漆面积÷【每天做乳胶漆底漆面积】
        $primer_day = $primer_area / $primer;

        //乳胶漆面漆面积
        $finishing_coat_area = $primer_area * self::DIGITAL;
//        乳胶漆面漆天数：乳胶漆面漆面积÷【每天做乳胶漆面漆面积】
        $finishing_coat_day = $finishing_coat_area / $finishing_coat;

//        卧室周长
        $bedroom_primer_perimeter = BasisDecorationService::paintedPerimeter($bedroom_area['project_value'], $post['area'], $post['bedroom'], self::WALL);
//        客厅周长
        $drawing_room_perimeter = BasisDecorationService::paintedPerimeter($hall_area['project_value'], $post['area'], $post['hall'], self::WALL_SPACE);
//        阴角线长度
        $concave_line_length = $bedroom_primer_perimeter + $drawing_room_perimeter;
//        阴角线天数：阴角线长度÷【每天做阴角线长度】
        $concave_line_day = $concave_line_length / $concave_line;

//        腻子卧室墙面积
        $putty_bedroom_area = BasisDecorationService::paintedArea($bedroom_area['project_value'], $post['area'], $post['bedroom'], self::WALL_HIGH, self::WALL);

//        腻子客餐厅面积
        $putty_drawing_room_area = BasisDecorationService::paintedArea($hall_area['project_value'], $post['area'], $post['hall'], self::WALL_HIGH, self::WALL_SPACE);
//        腻子面积 卧室腻子面积+客厅腻子面积
        $putty_area = $putty_bedroom_area + $putty_drawing_room_area;
//        腻子天数 腻子面积÷【每天做腻子面积】
        $putty_day = $putty_area / $putty;

        $select = 'goods.id,goods.category_id,goods.platform_price,goods.supplier_price,goods.purchase_price_decoration_company,goods_brand.name,gc.title,logistics_district.district_name,goods.category_id,gc.path,goods.profit_rate,goods.subtitle,goods.series_id,goods.style_id,goods.cover_image,supplier.shop_name';
        $goods = Goods::priceDetail(self::WALL_SPACE, self::LATEX_MATERIAL,$select);
        if ($goods == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $goods_price = BasisDecorationService::priceConversion($goods);

        //当地工艺
        $crafts = EngineeringStandardCraft::findByAll(self::PROJECT_DETAILS['emulsion_varnish'], $post['city']);
        if ($crafts == null){
            $code = 1059;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $series_and_style = BasisDecorationService::coatingSeriesAndStyle($goods_price, $post);
        foreach ($crafts as $craft) {
            switch ($craft) {
                case $craft['project_details'] == BasisDecorationService::GOODS_NAME['putty']:
                    $putty_craft = $craft;
                    break;
                case $craft['project_details'] == BasisDecorationService::GOODS_NAME['emulsion_varnish_primer']:
                    $primer_craft = $craft;
                    break;
                case $craft['project_details'] == BasisDecorationService::GOODS_NAME['emulsion_varnish_surface']:
                    $finishing_coat_craft = $craft;
                    break;
                case $craft['project_details'] == BasisDecorationService::GOODS_NAME['concave_line']:
                    $concave_line_craft = $craft;
                    break;
                case $craft['project_details'] == BasisDecorationService::GOODS_NAME['land_plaster']:
                    $gypsum_powder_craft = $craft;
                    break;
            }
        }

//        腻子费用
        $putty_cost = BasisDecorationService::paintedCost($series_and_style['putty'], $putty_craft,$putty_area);
//        底漆费用
        $primer_cost = BasisDecorationService::paintedCost($series_and_style['primer'], $primer_craft,$primer_area);
//        乳胶漆面漆费用
        $finishing_coat_cost = BasisDecorationService::paintedCost($series_and_style['finishing_coat'],$finishing_coat_craft,$finishing_coat_area);
//        阴角线费用
        $concave_line_cost = BasisDecorationService::paintedCost($series_and_style['concave_line'],$concave_line_craft,$concave_line_length);

//        石膏粉费用   石膏粉费用：个数×商品价格
//        个数：（【3元】×乳胶漆面漆面积÷商品价格）
        $gypsum_powder_cost['quantity'] = ceil($gypsum_powder_craft['material'] * $primer_area / $series_and_style['gypsum_powder']['platform_price']);
        $gypsum_powder_cost['cost'] = $gypsum_powder_cost['quantity'] * $series_and_style['gypsum_powder']['platform_price'];

        //总费用
        $total_cost = $putty_cost['cost'] + $primer_cost['cost'] + $finishing_coat_cost['cost'] + $concave_line_cost['cost'] + $gypsum_powder_cost['cost'];
        $material_total = [];
        foreach ($series_and_style as $one_goods_price) {
            switch ($one_goods_price) {
                case $one_goods_price['title'] == BasisDecorationService::GOODS_NAME['putty']:
                    $one_goods_price['quantity'] = $putty_cost['quantity'];
                    $one_goods_price['cost'] = $putty_cost['cost'];
                    $material_total['material'][] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == BasisDecorationService::GOODS_NAME['emulsion_varnish_primer']:
                    $one_goods_price['quantity'] = $primer_cost['quantity'];
                    $one_goods_price['cost'] = $primer_cost['cost'];
                    $material_total ['material'][] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == BasisDecorationService::GOODS_NAME['emulsion_varnish_surface']:
                    $one_goods_price['quantity'] = $finishing_coat_cost['quantity'];
                    $one_goods_price['cost']     = $finishing_coat_cost['cost'];
                    $material_total ['material'][] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == BasisDecorationService::GOODS_NAME['concave_line']:
                    $one_goods_price['quantity'] = $concave_line_cost['quantity'];
                    $one_goods_price['cost']     = $concave_line_cost['cost'];
                    $material_total ['material'][] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == BasisDecorationService::GOODS_NAME['land_plaster']:
                    $one_goods_price['quantity'] = $gypsum_powder_cost['quantity'];
                    $one_goods_price['cost']     = $gypsum_powder_cost['cost'];
                    $material_total ['material'][] = $one_goods_price;
                    break;
            }
        }
        $material_total['total_cost'] [] = $total_cost;
        //总天数   乳胶漆天数+阴角线天数+腻子天数
        $total_day = ceil($primer_day + $finishing_coat_day + $putty_day + $concave_line_day);

        //总人工费   人工费：（总天数）×【工人每天费用】
        $coating_labor_price['price'] = $total_day * $labor_costs['univalence'];
        $coating_labor_price['worker_kind'] = $labor_costs['worker_kind'];

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'coating_labor_price' => $coating_labor_price,
                'coating_material' => $material_total,
            ]
        ]);
    }

    /**
     * 泥作
     */
    public function actionMudMake()
    {
        $post = \Yii::$app->request->post();
        //工人一天单价
        $_select = 'id,univalence,worker_kind';
        $labor_costs = LaborCost::profession($post, self::WORK_CATEGORY['mason'],$_select);
        if ($labor_costs == null){
            $code = 1056;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $worker_kind_details = WorkerCraftNorm::findByLaborCostAll($labor_costs['id']);
        if ($worker_kind_details == null){
            $code = 1057;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        foreach ($worker_kind_details as $labor_cost) {
            switch ($labor_cost) {
                case $labor_cost['worker_kind_details'] == self::WORKMANSHIP['protective_layer_length']:
                    $covering_layer_day_area = $labor_cost['quantity'];
                    break;
                case $labor_cost['worker_kind_details'] == self::WORKMANSHIP['geostrophy_area']:
                    $geostrophy_day_area = $labor_cost['quantity'];
                    break;
                case $labor_cost['worker_kind_details'] == self::WORKMANSHIP['wall_brick_area']:
                    $wall_tile_day_area = $labor_cost['quantity'];
                    break;
            }
        }
        //泥作面积
        //厨房面积
        $kitchen_particulars = EngineeringUniversalCriterion::mudMakeArea(self::ROOM_DETAIL['kitchen'],self::ROOM_AREA['kitchen_area']);
        $kitchen_area = $post['area'] * $kitchen_particulars['project_value'];
        //卫生间面积
        $toilet_particulars = EngineeringUniversalCriterion::mudMakeArea(self::ROOM_DETAIL['toilet'],self::ROOM_AREA['toilet_area']);
        $toilet_area = $post['area'] * $toilet_particulars['project_value'];
        //客餐厅面积
        $drawing_room_particulars = EngineeringUniversalCriterion::mudMakeArea(self::ROOM_DETAIL['hall'],self::ROOM_AREA['hall_area']);
        $drawing_room_area = $post['area'] * $drawing_room_particulars['project_value'];

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll(self::PROJECT_DETAILS['tiler'], $post['city']);
        if ($craft == null){
            $code = 1059;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        foreach ($craft as $local_craft) {
            switch ($local_craft) {
                case $local_craft['project_details'] == BasisDecorationService::GOODS_NAME['tiling']:
                    $wall_height = $local_craft['material'];
                    break;
                case $local_craft['project_details'] == BasisDecorationService::GOODS_NAME['cement']:
                    $cement_craft = $local_craft['material'];
                    break;
                case $local_craft['project_details'] == BasisDecorationService::GOODS_NAME['self_leveling']:
                    $self_leveling_craft = $local_craft['material'];
                    break;
                case $local_craft['project_details'] == BasisDecorationService::GOODS_NAME['river_sand']:
                    $river_sand_craft = $local_craft['material'];
                    break;
            }
        }
//        保护层面积
        $covering_layer_area = $post['waterproof_total_area'];
//        保护层天数：保护层面积÷【每天做保护层面积】
        $covering_layer_day = $covering_layer_area / $covering_layer_day_area;

//        卫生间墙面积
        $toilet_wall_area = BasisDecorationService::mudMakeArea($toilet_area, $wall_height, $post['toilet']);
//        厨房墙面积
        $kitchen_wall_area = BasisDecorationService::mudMakeArea($kitchen_area, $wall_height, $post['kitchen'], 3);
//        墙砖面积
        $wall_area = $toilet_wall_area + $kitchen_wall_area;
//        墙砖天数
        $wall_day = $wall_area / $wall_tile_day_area;
//        地砖面积
        $floor_tile_area = $drawing_room_area + $toilet_area + $kitchen_area;
//        地砖天数
        $floor_tile_day = $floor_tile_area / $geostrophy_day_area;
//        贴砖天数
        $tiling_day = $floor_tile_day + $wall_day;
//        总天数：保护层天数+贴砖天数
        $total_day = ceil($tiling_day + $covering_layer_day);
        //总的人工费
        $total_labor_cost['price'] = $total_day * $labor_costs['univalence'];
        $total_labor_cost['worker_kind'] = self::PROJECT_DETAILS['tiler'];

        //材料费
        $select = "goods.id,goods.category_id,goods.platform_price,goods.supplier_price,goods.purchase_price_decoration_company,goods_brand.name,gc.title,logistics_district.district_name,goods.category_id,gc.path,goods.profit_rate,goods.subtitle,goods.series_id,goods.style_id,goods.cover_image,supplier.shop_name";
        $goods = Goods::priceDetail(self::WALL_SPACE, self::TILER_MATERIAL,$select);
        if ($goods == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data' => $goods,
            ]);
        }
        $goods_price = BasisDecorationService::priceConversion($goods);
        $goods_attr = BasisDecorationService::mudMakeMaterial($goods_price);

        $wall_brick = Goods::seriesAndStyle(self::WALL_SPACE,BasisDecorationService::GOODS_NAME['wall_brick'], $post);
        if ($wall_brick == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data' => $goods,
            ]);
        }
        $wall_brick_price = BasisDecorationService::priceConversion($wall_brick);
        $wall_brick_max = BasisDecorationService::profitMargin($wall_brick_price);
        $wall_brick_area = BasisDecorationService::wallBrickAttr($wall_brick_max['id']);

        $floor_tile = Goods::seriesAndStyle(self::WALL_SPACE,BasisDecorationService::GOODS_NAME['floor_tile'], $post);
        if ($floor_tile == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data' => $goods,
            ]);
        }
        $floor_tile_price = BasisDecorationService::priceConversion($floor_tile);
        $floor_tile_attr = BasisDecorationService::floorTile($floor_tile_price);

//        水泥费用
        $cement_area = $covering_layer_area + $floor_tile_area + $wall_area;
        $cement_cost = BasisDecorationService::mudMakeCost($cement_area, $goods_price, $cement_craft, $goods_attr,BasisDecorationService::GOODS_NAME['cement']);

//        自流平费用
        $self_leveling_area = $drawing_room_area;
        $self_leveling_cost = BasisDecorationService::mudMakeCost($self_leveling_area, $goods_price, $self_leveling_craft, $goods_attr,BasisDecorationService::GOODS_NAME['self_leveling']);

        //        河沙费用
        $river_sand_cement_area = $covering_layer_area + $floor_tile_area + $wall_area;
        $river_sand_cost = BasisDecorationService::mudMakeCost($river_sand_cement_area, $goods_price, $river_sand_craft, $goods_attr,BasisDecorationService::GOODS_NAME['river_sand']);

//        墙砖费用 墙砖费用：个数×抓取的商品价格 个数：（墙砖面积÷抓取墙砖面积）
        $wall_brick_cost['quantity'] = ceil($wall_area / $wall_brick_area);
        $wall_brick_cost['cost'] = $wall_brick_cost ['quantity'] * $wall_brick_max['platform_price'];

//        卫生间地砖个数和价格：（墙砖面积÷抓取墙砖面积）  厨房/卫生间墙砖费用
        $toilet_wall_brick_cost['quantity'] = ceil($toilet_area / $floor_tile_attr['toilet']['area']);
        $toilet_wall_brick_cost['cost'] = $toilet_wall_brick_cost['quantity'] * $floor_tile_attr['toilet']['price'];

//        厨房地砖费用 厨房地砖费用：个数×抓取的商品价格 个数：（厨房地砖面积÷抓取厨房地砖面积）
        $kitchen_wall_brick_cost['quantity'] = ceil($kitchen_area / $floor_tile_attr['kitchen']['area']);
        $kitchen_wall_brick_cost['cost'] = $kitchen_wall_brick_cost['quantity'] * $floor_tile_attr['kitchen']['price'];

//        客厅地砖费用
        $hall_wall_brick_cost['quantity'] = ceil($drawing_room_area / $floor_tile_attr['hall']['area']);
        $hall_wall_brick_cost['cost'] = $hall_wall_brick_cost['quantity'] * $floor_tile_attr['hall']['price'];
//        地砖总数量和费用
        $floor_cost['quantity'] = $toilet_wall_brick_cost['quantity'] + $kitchen_wall_brick_cost['quantity'] +  $hall_wall_brick_cost['quantity'];
        $floor_cost['cost'] = $toilet_wall_brick_cost['cost'] + $kitchen_wall_brick_cost['cost'] +  $hall_wall_brick_cost['cost'];

        //材料总费用
        $material_cost_total = $cement_cost['cost'] + $self_leveling_cost['cost'] + $river_sand_cost['cost'] + $wall_brick_cost['cost'] + $toilet_wall_brick_cost['cost'] + $kitchen_wall_brick_cost['cost'] + $hall_wall_brick_cost['cost'];

        // 水泥，河沙，自流平信息
        foreach ($goods_price as &$one_goods_price) {
            switch ($one_goods_price) {
                case $one_goods_price['title'] == BasisDecorationService::GOODS_NAME['river_sand']:
                    $one_goods_price['quantity'] = $river_sand_cost['quantity'];
                    $one_goods_price['cost'] = $river_sand_cost['cost'];
                    $river_sand[] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == BasisDecorationService::GOODS_NAME['cement']:
                    $one_goods_price['quantity'] = $cement_cost['quantity'];
                    $one_goods_price['cost'] = $cement_cost['cost'];
                    $cement[] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == BasisDecorationService::GOODS_NAME['self_leveling']:
                    $one_goods_price['quantity'] = $self_leveling_cost['quantity'];
                    $one_goods_price['cost'] = $self_leveling_cost['cost'];
                    $self_leveling[] = $one_goods_price;
                    break;
            }
        }

        $material_total['material'][] = BasisDecorationService::profitMargin($river_sand);
        $material_total['material'][] = BasisDecorationService::profitMargin($cement);
        $material_total['material'][] = BasisDecorationService::profitMargin($self_leveling);

        // 墙砖 卫生间地砖 厨房地砖 客厅地砖
        $wall_brick_max['quantity'] = $wall_brick_cost['quantity'];
        $wall_brick_max['cost'] = $wall_brick_cost['cost'];
        $material_total['material'][] = BasisDecorationService::profitMargin($wall_brick_max);

        foreach ($floor_tile_price as &$one_floor){
            $one_floor['quantity'] = $floor_cost['quantity'];
            $one_floor['cost'] = $floor_cost['cost'];
        }
        $material_total['material'][] = BasisDecorationService::profitMargin($floor_tile_price);
        $material_total['total_cost'] = $material_cost_total;

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'mud_make_labor_price' => $total_labor_cost,
                'mud_make_material' => $material_total,
            ]
        ]);
    }

    /**
     * 杂工
     */
    public function actionHandyman()
    {
        $post = \Yii::$app->request->post();
        $_select = 'id,univalence,worker_kind';
        $labor = LaborCost::profession($post, self::WORK_CATEGORY['backman'],$_select);
        if ($labor == null){
            $code = 1056;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $worker_kind_details = WorkerCraftNorm::findByLaborCostAll($labor['id']);
        if ($worker_kind_details == null){
            $code = 1057;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
//        总天数
        $total_day = BasisDecorationService::wallArea($post,$worker_kind_details);
//        清运建渣费用
        $craft = EngineeringStandardCraft::findByAll($labor['worker_kind'], $post['city']);
        if ($craft == null){
            $code = 1062;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($post['building_scrap'] == true) {
            $building_scrap = BasisDecorationService::haveBuildingScrap($post, $craft);
        } else {
            $building_scrap = BasisDecorationService::nothingBuildingScrap($post, $craft);
        }

//        总人工费
        $labor_cost['price'] = $total_day['total_day'] * $labor['univalence'] + $building_scrap['cost'];
        $labor_cost['worker_kind'] = $labor['worker_kind'];

        //材料费
        $select = "goods.id,goods.category_id,goods.platform_price,goods.supplier_price,goods.purchase_price_decoration_company,goods_brand.name,gc.title,logistics_district.district_name,goods.category_id,gc.path,goods.profit_rate,goods.subtitle,goods.series_id,goods.style_id,goods.cover_image,supplier.shop_name";
        $goods = Goods::priceDetail(self::WALL_SPACE, self::BACKMAN_MATERIAL,$select);
        if ($goods == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $goods_price = BasisDecorationService::priceConversion($goods);
        foreach ($goods_price as $max) {
            switch ($max) {
                case $max['title'] == BasisDecorationService::GOODS_NAME['cement']:
                    $goods_attr = GoodsAttr::findByGoodsIdUnit($max['id']);
                    if ($goods_attr == null){
                        $code = 1067;
                        return Json::encode([
                            'code' => $code,
                            'msg' => Yii::$app->params['errorCodes'][$code],
                        ]);
                    }
                    //水泥费用
                    $cement_cost = BasisDecorationService::cementCost($post,$craft,$max,$goods_attr);
                    $max['quantity'] = $cement_cost['quantity'];
                    $max['cost'] = $cement_cost['cost'];
                    $cement[] = $max;
                    break;
                case $max['title'] == BasisDecorationService::GOODS_NAME['air_brick']:
                    //空心砖费用
                    $brick_standard = GoodsAttr::findByGoodsId($max['id']);
                    if ($brick_standard == null){
                        $code = 1067;
                        return Json::encode([
                            'code' => $code,
                            'msg' => Yii::$app->params['errorCodes'][$code],
                        ]);
                    }
                    $brick_cost = BasisDecorationService::brickCost($post, $max, $brick_standard);
                    $max['quantity'] = $brick_cost['quantity'];
                    $max['cost'] = $brick_cost['cost'];
                    $air_brick[] = $max;
                    break;
                case $max['title'] == BasisDecorationService::GOODS_NAME['river_sand']:
                    $goods_attr = GoodsAttr::findByGoodsIdUnit($max['id']);
                    if ($goods_attr == null){
                        $code = 1067;
                        return Json::encode([
                            'code' => $code,
                            'msg' => Yii::$app->params['errorCodes'][$code],
                        ]);
                    }
                    //河沙费用
                    $river_sand_cost = BasisDecorationService::riverSandCost($post, $max, $craft, $goods_attr);
                    $max['quantity'] = $river_sand_cost['quantity'];
                    $max['cost'] = $river_sand_cost['cost'];
                    $river_sand[] = $max;
                    break;
            }
        }
        $material['material'][] = BasisDecorationService::profitMargin($cement);
        $material['material'][] = BasisDecorationService::profitMargin($air_brick);
        $material['material'][] = BasisDecorationService::profitMargin($river_sand);
        $material['total_cost'] = 0;
        foreach ($material['material'] as $total_cost){
            $material['total_cost'] += $total_cost['cost'];
        }

//        //总材料费
//        $total_material_cost = $cement_cost['cost'] + $brick_cost['cost'] + $river_sand_cost['cost'];
//        $material['total_cost'] = $total_material_cost;


        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'labor_cost' => $labor_cost,
                'total_material' => $material,
            ]
        ]);
    }

    /**
     * 添加材料查询
     */
    public function actionAddMaterials()
    {
        $code = trim(Yii::$app->request->post('code',''));
        $series = trim(Yii::$app->request->post('series',''));
        $style = trim(Yii::$app->request->post('style',''));
        $area = trim(Yii::$app->request->post('area',''));
        $add_select = 'id,one_materials,two_materials,three_materials,sku,max(quantity)';
        $add_where = ['and',['city_code'=>$code],['or',['style_id'=>$style],['series_id'=>$series],['and',['<=','min_area',$area],['>=','max_area',$area]]]];
        $add_materials = DecorationAdd::findByAll($add_select,$add_where);
        if ($add_materials == null){
            $code = 1063;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        foreach ($add_materials as $one_materials){
            $codes [] = $one_materials['sku'];
        }
        $goods_select = 'id,platform_price,sku';
        $goods = Goods::findBySkuAll($codes,$goods_select);
        if ($goods == null){
        $code = 1061;
        return Json::encode([
            'code' => $code,
            'msg' => Yii::$app->params['errorCodes'][$code],
        ]);
    }
        foreach ($add_materials as &$material){
            foreach ($goods as $one_goods){
                if ($one_goods['sku'] == $material['sku']) {
                    $material['platform_price'] = $one_goods['platform_price'] / 100;
                    $material['cost'] = $material['max(quantity)'] * $one_goods['platform_price'] / 100;
                }
            }
        }
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
           'add_list' =>  $add_materials,
        ]);
    }

    /**
     * 系数管理
     */
    public function actionCoefficient()
    {
        $post = Yii::$app->request->post();
        $coefficient = CoefficientManagement::find()->all();
        if ($coefficient == null) {
            $code = 1064;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }


        foreach ($coefficient as $one_coefficient){
            foreach ($post['list'] as &$materials){
                if ($one_coefficient->classify == $materials['one_title']){
                   $materials['goods_price'] = $materials['price'] * $one_coefficient['coefficient'];
                }
            }
        }


        foreach ($post['list'] as &$default){
            if (empty($default['goods_price'])){
                $default['goods_price'] = $default['price'] * 1;
            }
        }

        $special_offer = 0;
        $total_prices = 0;
        foreach ($post['list'] as $price){
            $total_prices += $price['price'];
           $special_offer += $price['goods_price'];

        }
        $total = sprintf('%.2f', (float)$total_prices);
        $special = sprintf('%.2f', (float)$special_offer);
        return Json::encode([
            'code'=> 200,
            'msg'=> 'OK',
           'data' => [
               'special_offer'=>$special,
               'total_prices'=>$total,
           ],
        ]);
    }

    /**
     * 配套设备列表
     * @return string
     */
    public function actionAssortFacility()
    {
        $post = Yii::$app->request->post();

        $assort_material = MaterialPropertyClassify::findByStatus();
        if ($assort_material == null) {
            $code = 1065;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        foreach ($assort_material as $assort){
            if ($assort['status'] != MaterialPropertyClassify::DEFAULT_STATUS){
                $have_assort[] = $assort;
            } else {
                $without_assort[] = $assort;
            }
        }

        //有计算公式
        foreach ($have_assort as $one_have_assort){
            $material_name[] = $one_have_assort['material'];
            $material_one[$one_have_assort['material']] = $one_have_assort;
        }

        $goods = Goods::assortList($material_name,$post['city']);
        if ($goods == null) {
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $goods_price  = BasisDecorationService::priceConversion($goods);
        $bedroom_area = EngineeringUniversalCriterion::mudMakeArea(self::ROOM_DETAIL['bedroom'],self::ROOM_AREA['bedroom_area']);
        //   生活配套
        $material[]   = BasisDecorationService::lifeAssortSeriesStyle($goods_price,$post);
        //   基础装修
        $material[][]   = BasisDecorationService::capacity($goods_price,$post);
        //   家电配套
        $material[]   = BasisDecorationService::appliancesAssortSeriesStyle($goods_price,$post);
        //   移动家具
        $material[]   = BasisDecorationService::moveFurnitureSeriesStyle($goods_price,$post);
        //   固定家具
        $material[][]  = BasisDecorationService::fixationFurnitureSeriesStyle($goods_price,$post);
        //   软装配套
        $material[]   = BasisDecorationService::mild($goods_price,$post);
        //   主材
        $material[]   = BasisDecorationService::principalMaterialSeriesStyle($goods_price, $material_one,$post,$bedroom_area);

        if ($post['stairway_id'] == 1) {
            //  楼梯信息
            $stairs = Goods::findByCategory(BasisDecorationService::GOODS_NAME['stairs']);
            if ($assort_material == null) {
                $code = 1061;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            $stairs_price = BasisDecorationService::priceConversion($stairs);
            foreach ($stairs_price as &$one_stairs_price) {
                if ($one_stairs_price['value'] == $post['stairs'] && $one_stairs_price['style_id'] == $post['style']) {
                    $one_stairs_price['quantity'] = $material_one[BasisDecorationService::GOODS_NAME['stairs']]['quantity'];
                    $one_stairs_price['cost'] = $one_stairs_price['platform_price'] * $one_stairs_price['quantity'];
                    $condition_stairs [] = $one_stairs_price;
                }
            }
        } else {
            $condition_stairs = null;
        }
        $material[][] = BasisDecorationService::profitMargin($condition_stairs);

        //无计算公式
        foreach ($without_assort as $one_without_assort){
            $without_assort_name[] = $one_without_assort['material'];
            $without_assort_one[$one_without_assort['material']] = $one_without_assort;
        }

        $without_assort_goods = Goods::assortList($without_assort_name,self::DEFAULT_CITY_CODE);
        if ($without_assort_goods == null) {
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $without_assort_goods_price = BasisDecorationService::priceConversion($without_assort_goods);
        $material[] = BasisDecorationService::withoutAssortGoods($without_assort_goods_price,$assort_material,$post);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' =>[
               'goods' => $material
            ],
        ]);
    }

    /**
     * Owner certification action(app)
     * @return string
     */
    public function actionCertification()
    {
        $code = 1000;

        $legalPerson = trim(Yii::$app->request->post('legal_person', ''));
        $identityNo = trim(Yii::$app->request->post('identity_no', ''));
        $identityCardFrontImage = trim(Yii::$app->request->post('identity_card_front_image', ''));
        $identityCardBackImage = trim(Yii::$app->request->post('identity_card_back_image', ''));
        if (!$legalPerson
            || !$identityNo
            || !$identityCardFrontImage
            || !$identityCardBackImage
        ) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $certRes = Yii::$app->user->identity->certificate(
            $identityNo,
            $legalPerson,
            $identityCardFrontImage,
            $identityCardBackImage
        );
        return Json::encode([
            'code' => $certRes,
            'msg' => 200 == $certRes ? 'OK' : Yii::$app->params['errorCodes'][$certRes],
        ]);
    }

    /**
     * View owner action
     *
     * @return string
     */
    public function actionViewIdentity()
    {
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'view-identity' => Yii::$app->user->identity->viewIdentity()
            ],
        ]);
    }

    /**
     * 主页列表
     * @return string
     */
    public function actionHomepage()
    {
        return Json::encode([
            'code' =>200,
            'msg'=>'ok',
            'data'=> BrainpowerInitalSupervise::find()
                ->select('id,image,district_code,street,toponymy')
                ->where(['status'=>BrainpowerInitalSupervise::STATUS_OPEN])
                ->orderBy(['sort' => SORT_ASC])
                ->all()
        ]);
    }

    /**
     * 案例列表
     * @return string
     */
    public function actionCaseList(){
        $code     = trim(Yii::$app->request->post('code',''));
        $street   = trim(Yii::$app->request->post('street',''));
        $toponymy = trim(Yii::$app->request->post('toponymy',''));

        $effect = Effect::findByCode($code,$street,$toponymy);
        if ($effect == null) {
            $code = 1066;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        foreach ($effect as &$one_effect){
            $one_effect['detailed_address'] = $one_effect['province'] . $one_effect['city'] . $one_effect['district'] .  $one_effect['street'];
            if ($one_effect['type'] == 1){
                $one_effect['case_picture'] = EffectPicture::findById( $one_effect['id']);
            }
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
           'data'=>$effect,
        ]);

    }

    /**
     * 案例数据
     * @return string
     */
    public function actionCaseParticulars()
    {
//        $id = trim(Yii::$app->request->post('id',''));
        $series = trim(Yii::$app->request->post('series',''));
        $style = trim(Yii::$app->request->post('style',''));
        $stairway = trim(Yii::$app->request->post('stairway',''));
        $stair_id = trim(Yii::$app->request->post('stair_id',''));
        $toponymy = trim(Yii::$app->request->post('toponymy',''));
        $particulars = trim(Yii::$app->request->post('particulars',''));
        $area = trim(Yii::$app->request->post('area',''));

        if ($stairway == self::JUDGE_VALUE){
            $where = ['and',['effect_picture.series_id'=>$series],['effect_picture.style_id'=>$style],['effect.stairway'=>$stairway],['effect.toponymy'=>$toponymy],['effect.particulars'=>$particulars],['effect.area'=>$area],['type'=>1]];
            $effect = Effect::effectAndEffectPicture('effect.id,effect_picture.effect_images',$where);
            if ($effect == null) {
                $code = 1068;
                return Json::encode([
                   'code' => $code,
                   'msg' => Yii::$app->params['errorCodes'][$code],
                    'data'=> $effect
                ]);
            }
        }
        elseif
        ($stairway == self::TYPE_VALUE) {
            $where = ['and',['effect_picture.series_id'=>$series],['effect_picture.style_id'=>$style],['effect.stairway'=>$stairway],['effect.stair_id'=>$stair_id],['effect.toponymy'=>$toponymy],['effect.particulars'=>$particulars],['effect.area'=>$area],['type'=>1]];
            $effect = Effect::effectAndEffectPicture('effect.id,effect_picture.effect_images',$where);
            if ($effect == null) {
                $code = 1068;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                    'data'=> $effect
                ]);
            }
        }

        $effect_where = 'effect_id = '.$effect['id'];
        $data = WorksData::find()->asArray()->select('effect_id,goods_first,goods_second,goods_three,three_category_id as id,goods_code,goods_quantity')->where($effect_where)->all();
        if ($data == null) {
            $code = 1067;
            return Json::encode([
                'code' => $code,
                'msg' => '信息有误',
            ]);
        }

        $backman_data = WorksBackmanData::find()->select('backman_option,backman_value')->where($effect_where)->all();
        if ($backman_data == null) {
            $code = 1067;
            return Json::encode([
                'code' => $code,
                'msg' => '信息有误',
            ]);
        }

        $worker_data = WorksWorkerData::find()->select([])->where($effect_where)->all();
        if ($worker_data == null) {
            $code = 1067;
            return Json::encode([
                'code' => $code,
                'msg' => '信息有误',
            ]);
        }

        if ($data != null) {
            foreach ($data as $one_goods) {
                $sku [] = $one_goods['goods_code'];
            }
            $select = "goods.id,goods.sku,goods.platform_price,goods.purchase_price_decoration_company,goods.logistics_template_id,goods_brand.name,goods.cover_image";
            $goods  = Goods::findBySkuAll($sku, $select);
            if ($goods == null) {
                $code = 1061;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            foreach ($data as &$case_works_datum) {
                foreach ($goods as $one_goods) {

                    if ($one_goods['sku'] == $case_works_datum['goods_code']) {
                        $cost                                      = $one_goods['platform_price'] / BasisDecorationService::GOODS_PRICE_UNITS;
                        $case_works_datum['goods_id']              = $one_goods['id'];
                        $case_works_datum['logistics_template_id'] = $one_goods['logistics_template_id'];
                        $case_works_datum['cost']                  = $cost * $case_works_datum['goods_quantity'];
                        $case_works_datum['name']                  = $one_goods['name'];
                        $case_works_datum['quantity']              = $case_works_datum['goods_quantity'];
                        $case_works_datum['image']                 = $one_goods['cover_image'];

                    }
                }
            }
            //物流信息

            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
                'data' => [
                    'images' => $effect,
                    'goods' => $data,
                    'backman_data' => $backman_data,
                    'worker_data' => $worker_data,
                ]
            ]);
        }
    }

    /**
     * 测试数据
     *
     * @return string
     */
    public function actionTest()
    {
        $a = BasisDecorationService::L(10,10,10);
        var_dump($a);
    }

}