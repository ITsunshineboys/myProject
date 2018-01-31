<?php

namespace app\controllers;

use app\models\Apartment;
use app\models\AssortGoods;
use app\models\BrainpowerInitalSupervise;
use app\models\CarpentryAdd;
use app\models\CoefficientManagement;
use app\models\DecorationAdd;
use app\models\District;
use app\models\EditCategory;
use app\models\Effect;
use app\models\EffectEarnest;
use app\models\EffectPicture;
use app\models\EffectToponymy;
use app\models\EngineeringStandardCarpentryCoefficient;
use app\models\EngineeringStandardCraft;
use app\models\EngineeringUniversalCriterion;
use app\models\Goods;
use app\models\GoodsAttr;
use app\models\GoodsCategory;
use app\models\GoodsStyle;
use app\models\LaborCost;
use app\models\LogisticsTemplate;
use app\models\MaterialPropertyClassify;
use app\models\Points;
use app\models\ProjectView;
use app\models\Series;
use app\models\StairsDetails;
use app\models\Style;
use app\models\Worker;
use app\models\WorkerCraftNorm;
use app\models\WorkerType;
use app\models\WorksBackmanData;
use app\models\WorksData;
use app\models\WorksWorkerData;
use app\services\BasisDecorationService;
use app\services\ExceptionHandleService;
use app\services\LogisticsService;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use Yii;

class OwnerController extends Controller
{

    const JUDGE_VALUE = 0;
    const TYPE_VALUE = 1;
    const WALL = 4;
    const WALL_SPACE = 3;
    const DIGITAL = 2;
    const PRICE_UNITS = 100;


    /**
     * 基础装修有计算公式的必要材料id
     */
    const CIRCUIT_MATERIAL    = [43,30,40,32];       // 强弱电材料id
    const WATERWAY_MATERIAL  = [33,37];          // 水路材料id
    const WATERPROOF_MATERIAL= [56];             // 防水材料id
    const CARPENTRY_MATERIAL = [22,9,12,13];     // 木作材料id
    const LATEX_MATERIAL     = [38,24,25,28,5];  // 乳胶漆材料id
    const TILER_MATERIAL     = [172,36,6,45];       // 泥工材料id
    const BACKMAN_MATERIAL   = [172,6,3];      // 杂工材料id
    const BRICK   = [44];      // 杂工材料id


    /**
     * 有计算公式的材料id
     * 所对应的三级分类
     * 木地板,人造大理石,弯头,木门,浴霸,换气扇,吸顶灯,水龙头,床,床头柜,抽油烟机,灶具,立柜式空调,挂壁式空调,灯具,床垫,马桶,浴柜,花洒套装,淋浴隔断
     */
    const MATERIALS = [17,52,35,80,61,62,63,75,121,123,106,108,117,119,130,170,144,140,146,152];


    /**
     * 工种类型 id
     */
    const WORK_CATEGORY = [
        'plumber'    => 9,  // 水电工id
        'waterproof' => 18,  // 防水工id
        'woodworker' => 1,  // 木工id
        'painters'   => 13,  // 油漆工id
        'mason'      => 20,  // 泥瓦工id
        'backman'    => 4,  // 杂工id
    ];

    /**
     * points category details
     */
    const POINTS_CATEGORY = [
        'weak_current'  => 11, //'弱电点位',
        'strong_current'=> 10, //'强电点位',
        'waterway'      => 12, //'水路点位',
        'waterproof'    => 19, //'做工面积',
        'area_ratio'    => 68, //'面积比例',
        'flat_area'     => 3,  //'平顶面积',
        'modelling'     => 2,  //'造型长度',
        'undercoat'     => 14,  //'乳胶漆底漆面积',
        'finishing_coat'=> 15,  //'乳胶漆面漆面积',
        'putty'         => 16,  //'腻子面积',
        'thread'        => 17,  //'阴角线长度',
        'maskant'       => 21,  //'保护层长度',
        'floor_tile'    => 22,  //'贴地砖面积',
        'wall_brick'    => 23,  //'贴墙砖面积',
        'build_24'      => 5,  //'新建24墙面积',
        'dismantle_24'  => 6,  //'拆除24墙面积',
        'build_12'      => 7,  //'新建12墙面积',
        'dismantle_12'  => 8,  //'拆除12墙面积',
        'repair'        => 24,  //'补烂面积',
    ];

    /**
     *   工种 id
     */
    const PROJECT_DETAILS = [
        'weak_current'      => 2,//弱电Id
        'strong_current'    => 1,//强电id
        'waterway'          => 3,//水路id
        'waterproof'        => 69,// 防水-修改 防水id
        'carpentry'         => 4, // 木作
        'emulsion_varnish'  => '乳胶漆',
        'oil_paint'         => 5,// 油漆 -> 油漆id
        'tiler'             => 6,//泥工 -> 泥工id
    ];
    /**
     * 工艺名称
     */
    const CRAFT_NAME =[
        'weak'      => 25,  // 弱电工艺
        'strong'    => 29,  // 强电工艺
        'waterway'  => 32,  //'水路工艺',
        'waterproof'=> 35,  //'防水工艺',
        'carpentry' => 37,  //'木作工艺',
        'oil_paint' => 47,  //'油漆工艺',
        'tiler'     => 53,  //'泥工工艺',
        'backman'   => 7,  //'杂工',
        'backman_'   => 57,  //'杂工',
    ];

    /**
     * room area
     */
    const ROOM = [
        'kitchen_area' => 5,//'厨房面积百分比',
        'toilet_area'  => 6,//'卫生间面积百分比',
        'hall_area'    => 8,//'客餐厅及过道面积百分比',
        'bedroom_area' => 7,//'卧室面积百分比',
        'kitchen_height' => 1,//'厨房防水高度',
        'toilet_height' => 2,//'卫生间防水高度',
        'modelling' => 9,//'造型长度',
        'flat_area' => 10,//'平顶面积',
        'keel_height' => 38,//'1根龙骨做造型长度',
        'screw_height' => 39,//'1根丝杆做造型长度',
        'plaster_height' => 40,//'1张石膏板造型长度',
        'plaster_area' => 41,//'1张石膏板平顶面积',
        'tv_day' => 42,//'电视墙需要天数',
        'tv_plaster' => 43,//'电视墙所需石膏板',
        'keel_area' => 44,//'1根龙骨做平顶面积',
        'screw_area' => 45,//'1根丝杆做平顶面积',
        'tv_board' => 46,//'电视墙用细木工板',
        'putty' => 48,//'1平方腻子用量',
        'undercoat' => 49,//'1平方乳胶漆底漆',
        'finishing' => 50,//'1平方乳胶漆面漆',
        'wire' => 51,//'1米阴角线用量',
        'land' => 52,//'1平方石膏粉费用',
        'concrete' => 54,//'水泥用量',
        'self_leveling' => 55,//'自流平用量',
        'river_sand' => 56,//'河沙用量',
        'rubbish_12' => 58,//'12墙建渣运到楼下',
        'rubbish_24' => 59,//'24墙建渣运到楼下',
        'vehicle_12' => 60,//'运渣车-车拉12墙面积',
        'vehicle_24' => 61,//'运渣车-车拉24墙面积',
        'fare' => 62,//'运渣车-车费用',
        'concrete_12' => 63,//'12墙新建水泥用量',
        'concrete_24' => 64,//'24墙新建水泥用量',
        'concrete_repair' => 65,//'补烂水泥用量',
        'river_sand_12' => 66,//'12墙新建河沙用量',
        'river_sand_24' => 67,//'24墙新建河沙用量',
        'river_sand_repair' => 68,//'补烂河沙用量',

    ];

    const ROOM_DETAIL = [
        'hall' => '客厅',
        'secondary_bedroom' => '次卧',
        'kitchen' => '厨房',
        'toilet' => '卫生间',
    ];

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
//        $id  = trim(Yii::$app->request->get('id',''));
        $str = trim(Yii::$app->request->get('str',''));
        $city_code = (int)trim(Yii::$app->request->get('city_code','510100'));
        if ($str != null){
            $select = 'id,toponymy,province_code,city_code,district_code,street';
            $effect = Effect::districtSearch($str,$select,$city_code);
            foreach ($effect as &$value){
                $value['province']=District::findByCode($value['province_code'])['name'];
                $value['city']=District::findByCode($value['city_code'])['name'];
                $value['district']=District::findByCode($value['district_code'])['name'];
                $value['detailed_address'] = $value['province'].$value['city'].$value['district'].$value['street'];
                unset($value['province_code']);
                unset($value['city_code']);
                unset($value['district_code']);
            }
            return Json::encode([
                'code' => 200,
                'msg' => '成功',
                'data' => [
                    'list_effect' => $effect,
                ]
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
     * 强弱电价格
     * @return string
     */
    public function actionElectricity()
    {
        $get = Yii::$app->request->get();

        // 点位
        $where = 'id in ('.self::PROJECT_DETAILS['strong_current'].','.self::PROJECT_DETAILS['weak_current'].')';
        $points = Points::findByIds('id,title',$where);
        foreach ($points as $one_points){
            // 弱电
            if ($one_points['id'] ==  self::PROJECT_DETAILS['weak_current']){
                $weak_where = 'pid = '.$one_points['id'];
                $weak_points = Points::findByPid('title,count',$weak_where);
                $weak_overall_points = BasisDecorationService::weakPoints($weak_points,$get);
            }

            // 强电
            if ($one_points['id'] ==  self::PROJECT_DETAILS['strong_current']){
                $weak_where = 'pid = '.$one_points['id'];
                $strong_points = Points::findByPid('title,count',$weak_where);
                $strong_overall_points = BasisDecorationService::strongPoints($strong_points,$get);
            }
        }


        // 强弱电总点位
        $total_points = BasisDecorationService::algorithm(3,$weak_overall_points,$strong_overall_points);


        // 所需要材料查询
        $goods = Goods::priceDetail(self::WALL_SPACE, self::CIRCUIT_MATERIAL);
        $judge = BasisDecorationService::judge($goods,$get);

        // 当地工艺
        $weak_craft = WorkerType::craft(self::CRAFT_NAME['weak'],$get['city']);  // 弱电工艺
        foreach ($weak_craft as $weak_value){
            if ($weak_value['worker_name'] == '网线用量' ){
                $reticle = $weak_value['material'];
            }

            if ($weak_value['worker_name'] == '线管用料' ){
                $spool = $weak_value['material'];
            }
        }
        $strong_craft = WorkerType::craft(self::CRAFT_NAME['strong'],$get['city']);  // 强电工艺
        foreach ($strong_craft as $strong_value){
            if ($strong_value['worker_name'] == '电线用料' ){
                $wire = $strong_value['material'];
            }

            if ($strong_value['worker_name'] == '线管用料' ){
                $spool1 = $strong_value['material'];
            }
        }


        //商品属性抓取
        $reticle_attr = BasisDecorationService::goodsAttr($judge,BasisDecorationService::goodsNames()['reticle'],'长');
        $wire_attr = BasisDecorationService::goodsAttr($judge,BasisDecorationService::goodsNames()['wire'],'长');
        $spool_attr = BasisDecorationService::goodsAttr($judge,BasisDecorationService::goodsNames()['spool'],'长');
        $bottom_case_attr = BasisDecorationService::goodsAttr($judge,BasisDecorationService::goodsNames()['bottom_case'],'长');


        // 商品价格
        $goods_price[] = BasisDecorationService::plumberFormula(1,$weak_overall_points,$reticle_attr,$reticle);
        $goods_price[] = BasisDecorationService::plumberFormula(1,$strong_overall_points,$wire_attr,$wire);
        $goods_price[] = BasisDecorationService::plumberFormula(3,$strong_overall_points,$spool_attr,$spool,$spool1,$weak_overall_points);
        $goods_price[] = BasisDecorationService::plumberFormula(2,$total_points,$bottom_case_attr);
        $total_cost = round($goods_price[0]['cost']+$goods_price[1]['cost']+$goods_price[2]['cost']+$goods_price[3]['cost'],2);

        return Json::encode([
           'code' => 200,
           'msg'  => 'ok',
            'data'=> $goods_price,
            'total_cost'=> $total_cost,
        ]);
    }


    /**
     * 水路
     * @return string
     */
    public function actionWaterway()
    {
        $get = \Yii::$app->request->get();
        // 点位查询
        $where = 'id = '.self::PROJECT_DETAILS['waterway'];
        $points = Points::findByIds('id,title',$where);
        foreach ($points  as $p) {
            if ($p['id'] == self::PROJECT_DETAILS['waterway']) {
                $waterway_where          = 'pid = ' . $p['id'];
                $waterway_points         = Points::findByPid('title,count', $waterway_where);
                $waterway_overall_points = BasisDecorationService::waterwayPoints($waterway_points, $get);
            }
        }


        //查询弱电所需要材料
        $goods = Goods::priceDetail(self::WALL_SPACE,self::WATERWAY_MATERIAL);
        if ($goods == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data' => [
                    'waterway_labor_price' => [],
                    'waterway_material_price' => [],
                ]
            ]);
        }
        $waterway_current = BasisDecorationService::judge($goods, $get);

        // 商品属性抓取
        $ppr = BasisDecorationService::goodsAttr($waterway_current,BasisDecorationService::goodsNames()['ppr'],'长');
        $pvc = BasisDecorationService::goodsAttr($waterway_current,BasisDecorationService::goodsNames()['pvc'],'长');


        //当地工艺
        $craft = WorkerType::craft(self::CRAFT_NAME['waterway'], $get['city']);
        foreach ($craft as $value){
            if ($value['worker_name'] == 'PPR水管用料' ){
                $ppr_ = $value['material'];
            }

            if ($value['worker_name'] == 'PVC管用料' ){
                $pvc_ = $value['material'];
            }
        }


        //材料费用
        $material[] = BasisDecorationService::waterwayGoods(1,$waterway_overall_points,$ppr_,$ppr);
        $material[] = BasisDecorationService::waterwayGoods(1,$waterway_overall_points,$pvc_,$pvc);
        $total_cost = round(BasisDecorationService::algorithm(3,$material[0]['cost'],$material[1]['cost']),2);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => $material,
            'total_cost' => $total_cost,
        ]);
    }


    /**
     * 水电工 工人费用
     * @return string
     */
    public function actionPlumberPrice()
    {
        $get = \Yii::$app->request->get();

        //人工价格
        $labor = LaborCost::profession($get['city'],self::WORK_CATEGORY['plumber']);
        $day_workload = WorkerCraftNorm::findByLaborCostAll($labor['id']);

        foreach ($day_workload as $one_day){
            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['strong_current']){
                $strong = $one_day['quantity'];
            }

            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['weak_current']){
                $weak = $one_day['quantity'];
            }

            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['waterway']){
                $waterway = $one_day['quantity'];
            }
        }

        $points = Points::find()->asArray()->select('id,title,count')->where(['in','id',[1,2,3]])->all();
        foreach ($points  as $p){
            if ($p['id'] == self::PROJECT_DETAILS['waterway']){
                $waterway_where = 'pid = '.$p['id'];
                $waterway_points = Points::findByPid('title,count',$waterway_where);
                $waterway_overall_points = BasisDecorationService::waterwayPoints($waterway_points,$get);
            }

            if ($p['id'] == self::PROJECT_DETAILS['weak_current']){
                $weak_where = 'pid = '.$p['id'];
                $weak_points = Points::findByPid('title,count',$weak_where);
                $weak_overall_points = BasisDecorationService::weakPoints($weak_points,$get);
            }

            if ($p['id'] == self::PROJECT_DETAILS['strong_current']){
                $strong_where = 'pid = '.$p['id'];
                $strong_points = Points::findByPid('title,count',$strong_where);
                $strong_overall_points = BasisDecorationService::strongPoints($strong_points,$get);
                }

        }


        //人工总费用    $points['count'],$workers['univalence'],$worker_kind_details['quantity']
        $waterway_ = BasisDecorationService::laborFormula($waterway_overall_points,$waterway);
        $weak_     = BasisDecorationService::laborFormula($weak_overall_points,$weak);
        $strong_   = BasisDecorationService::laborFormula($strong_overall_points,$strong);
        $total = ceil(BasisDecorationService::algorithm(5,$waterway_,$weak_,$strong_));


        $labor_all_cost['price'] = round(BasisDecorationService::algorithm(1,$total,$labor['univalence']),2);
        $labor_all_cost['worker_kind'] = $labor['worker_name'];


        return Json::encode([
           'code' => 200,
           'msg' => 'ok',
           'labor_all_cost' => $labor_all_cost,
        ]);
    }

    /**
     * 防水
     * @return string
     */
    public function actionWaterproof()
    {
        $get = \Yii::$app->request->get();
        //人工价格
        $labor_cost = LaborCost::profession($get['city'],self::WORK_CATEGORY['waterproof']);
        $day_workload = WorkerCraftNorm::findByLaborCostAll($labor_cost['id']);
        foreach ($day_workload as $one_day){
            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['waterproof']){
                $area = $one_day['quantity'];
            }
        }


        //防水所需材料
        $goods = Goods::priceDetail(self::WALL_SPACE, self::WATERPROOF_MATERIAL);
        if ($goods == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data' => [
                    'waterproof_labor_price' => [],
                    'waterproof_material' => [],
                    'total_area' => [],
                ]
            ]);
        }
        $waterproof = BasisDecorationService::judge($goods,$get);


        $points = Points::find()
            ->select('id,title')
            ->where(['id'=>self::PROJECT_DETAILS['waterproof']])
            ->asArray()
            ->one();

        //  面积比例查询
        $area_ = ProjectView::findById(self::POINTS_CATEGORY['area_ratio'],$ratio = 10000);
        foreach ($area_ as $one_area){
            // 厨房
            if ($one_area['id'] == self::ROOM['kitchen_area']){
                $kitchen_ratio = $one_area['project_value'];
            }
            // 卫生间
            if ($one_area['id'] == self::ROOM['toilet_area']){
                $toilet_ratio = $one_area['project_value'];
            }
        }

        // 防水高度查询
        $height = ProjectView::findByAll([],['points_id'=>$points['id']]);
        foreach ($height as $one_height){
            // 厨房防水高度
            if ($one_height['id'] == self::ROOM['kitchen_height']){
                $kitchen_height = $one_height['project_value'];
            }
            // 卫生间防水高度
            if ($one_height['id'] == self::ROOM['toilet_height']){
                $toilet_height = $one_height['project_value'];
            }
        }


        // 厨房   //卫生间  //其它面积查询
        $kitchen_area = BasisDecorationService::waterproofArea($kitchen_ratio,$kitchen_height,$get,$get['kitchen'],4);
        $toilet_area = BasisDecorationService::waterproofArea($toilet_ratio,$toilet_height,$get,$get['toilet'],4);
        $apartment = Apartment::find()->asArray()->where(['<=','min_area',$get['area']])->andWhere(['>=','max_area',$get['area']])->andWhere(['points_id'=>$points['id']])->one();

        //总面积
        $total_area = round(BasisDecorationService::algorithm(5,$kitchen_area,$toilet_area,$apartment['project_value']),2);

        //当地工艺
        $craft = WorkerType::craft(self::CRAFT_NAME['waterproof'],$get['city']);

        //人工总费用（防水总面积÷【每天做工面积】）×【工人每天费用】
        $laborAll  = ceil(BasisDecorationService::algorithm(6,$total_area,$area));
        $labor_all_cost['price'] = ceil(BasisDecorationService::algorithm(1,$laborAll,$labor_cost['univalence']));
        $labor_all_cost['worker_kind'] = $labor_cost['worker_name'];

        // 商品属性
        $goods_attr = BasisDecorationService::goodsAttr($waterproof,BasisDecorationService::goodsNames()['waterproof_coating'],'重');

        //材料总费用
        $material_price = BasisDecorationService::waterproofGoods($total_area,$craft[0]['material'],$goods_attr);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'labor_all_cost' => $labor_all_cost,
            'data' => [$material_price],
            'total_cost' => $material_price['cost'],
            'total_area' => $total_area,
        ]);
    }

    /**
     * 木作
     */
    public function actionCarpentry()
    {
        $get = \Yii::$app->request->get();

        // 人工价格
        $labor_cost = LaborCost::profession($get['city'],self::WORK_CATEGORY['woodworker']);
        $day_workload = WorkerCraftNorm::findByLaborCostAll($labor_cost['id']);
        foreach ($day_workload as $one_day){
            // 造型长度
            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['modelling']){
                $modelling = $one_day['quantity'];
            }

            // 平顶面积
            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['flat_area']){
                $flat_area = $one_day['quantity'];
            }
        }


        $ProjectView = ProjectView::findByAll('id,project,project_value',['points_id'=>self::PROJECT_DETAILS['carpentry']]);
        foreach ($ProjectView as $one_view){
            // 造型长度
            if ($one_view['id'] == self::ROOM['modelling']){
                $defaults_modelling = $one_view['project_value'];
            }

            // 平顶面积
            if ($one_view['id'] == self::ROOM['flat_area']){
                $defaults_flat_area = $one_view['project_value'];
            }
        }

        // 系列系数 风格系数 查询
        $coefficient = EngineeringStandardCarpentryCoefficient::findByAll(['city_code'=>$get['city']]);
        foreach ($coefficient as $one_){

            // 系列系数2
            if ($one_['series_or_style'] == 0
                && $one_['coefficient'] == 2
                && $one_['project'] == $get['series']
            ){
                $coefficient2 = $one_['value'];
            }

            // 系列系数1
            if ($one_['series_or_style'] == 0
                && $one_['coefficient'] == 1
                && $one_['project'] == $get['series']
            ){
                $coefficient1 = $one_['value'];
            }

            // 风格系数1
            if ($one_['series_or_style'] == 1
                && $one_['coefficient'] == 1
                && $one_['project'] == $get['style']
            ){
                $coefficient3 = $one_['value'];
            }

            // 系列系数3
            if ($one_['series_or_style'] == 0
                && $one_['coefficient'] == 3
                && $one_['project'] == $get['series']
            ){
                $coefficient4 = $one_['value'];
            }

            // 风格系数2
            if ($one_['series_or_style'] == 1
                && $one_['coefficient'] == 2
                && $one_['project'] == $get['style']
            ){
                $coefficient5 = $one_['value'];
            }
        }



        //造型长度  平顶面积
        $modelling_length = BasisDecorationService::algorithm(1,$defaults_modelling,$coefficient2);
        $flat_area_ = $defaults_flat_area;

        //造型天数 造型长度÷【每天做造型长度】×系列系数1×风格系数1    //平顶天数  平顶面积÷【每天做平顶面积】×系列系数3×风格系数2
        $modelling_day = ceil(BasisDecorationService::algorithm(7,$modelling_length,$modelling,$coefficient1,$coefficient3));
        $flat_area_day = ceil(BasisDecorationService::algorithm(7,$flat_area_,$flat_area,$coefficient4,$coefficient5));


        //人工费
        $labour_charges['price'] = BasisDecorationService::algorithm(8,$modelling_day,$flat_area_day,$labor_cost['univalence']);
        $labour_charges['worker_kind'] = $labor_cost['worker_name'];


        //材料
        $goods = Goods::priceDetail(self::WALL_SPACE, self::CARPENTRY_MATERIAL);
        if ($goods == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data' => [
                    'carpentry_labor_price' => [],
                    'carpentry_material' => [],
                    'carpentry_add_price' => [],
                ]
            ]);
        }
        $goods_price = BasisDecorationService::judge($goods,$get);


        //当地工艺
        $craft = WorkerType::craft(self::CRAFT_NAME['carpentry'],$get['city']);
        foreach ($craft as $one_craft){
            switch ($one_craft){
                case  $one_craft['id'] == self::ROOM['keel_height']:
                    //1根龙骨做造型长度
                    $keel_height = $one_craft['material'];
                    break;
                case  $one_craft['id'] == self::ROOM['keel_area']:
                    //1根龙骨做平顶面积
                    $keel_area = $one_craft['material'];
                    break;
                case  $one_craft['id'] == self::ROOM['screw_height']:
                    //1根丝杆做造型长度
                    $screw_height = $one_craft['material'];
                    break;
                case  $one_craft['id'] == self::ROOM['screw_area']:
                    //1根丝杆做平顶面积
                    $screw_area = $one_craft['material'];
                    break;
                case  $one_craft['id'] == self::ROOM['plaster_height']:
                    //1张石膏板做造型长度
                    $plaster_height = $one_craft['material'];
                    break;
                case  $one_craft['id'] == self::ROOM['plaster_area']:
                    //1张石膏板做平顶面积
                    $plaster_area = $one_craft['material'];
                    break;
                case  $one_craft['id'] == self::ROOM['tv_day']:
                    //电视墙需要天数
                    $tv_day = $one_craft['material'];
                    break;
                case  $one_craft['id'] == self::ROOM['tv_plaster']:
                    //电视墙需要石膏板
                    $tv_plaster = $one_craft['material'];
                    break;
                case  $one_craft['id'] == self::ROOM['tv_board']:
                    //电视墙需要细木工板
                    $tv_board = $one_craft['material'];
                    break;
            }
        }

        // 商品属性  石膏板   龙骨 丝杆 木工板
        $plaster_attr = BasisDecorationService::goodsAttr($goods_price,BasisDecorationService::goodsNames()['plasterboard'],'面积');
        $keel_attr = BasisDecorationService::goodsAttr($goods_price,BasisDecorationService::goodsNames()['keel'],'长度');
        $screw_attr = BasisDecorationService::goodsAttr($goods_price,BasisDecorationService::goodsNames()['lead_screw'],'长度');
        $board_attr = BasisDecorationService::goodsAttr($goods_price,BasisDecorationService::goodsNames()['slab'],'',2);


        //石膏板费用  //龙骨费用   //丝杆费用 //木工板
        $material_total[] = BasisDecorationService::carpentryPlasterboardCost(1,$modelling_length,$flat_area_,$plaster_attr,$plaster_height,$plaster_area,$tv_plaster);
        $material_total[] = BasisDecorationService::carpentryPlasterboardCost(2,$modelling_length,$flat_area_,$keel_attr,$keel_height,$keel_area);
        $material_total[] = BasisDecorationService::carpentryPlasterboardCost(2,$modelling_length,$flat_area_,$screw_attr,$screw_height,$screw_area);
        $material_total[] = BasisDecorationService::carpentryPlasterboardCost(3,$tv_board,'',$board_attr);

        // 总费用
        $total_area = 0;
        foreach ($material_total as $total){
            $total_area +=  $total['cost'];
        }

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'labor_all_cost' => $labour_charges,
            'data' => $material_total,
            'total_cost' => round($total_area,2),
        ]);
    }

    /**
     * 乳胶漆
     */
    public function actionCoating()
    {
        $get = \Yii::$app->request->get();
        //工人一天单价
        $labor_costs = LaborCost::profession($get['city'], self::WORK_CATEGORY['painters']);
        $day_workload = WorkerCraftNorm::findByLaborCostAll($labor_costs['id']);
        foreach ($day_workload as $one_day){
            // 乳胶漆底漆面积
            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['undercoat']){
                $undercoat = $one_day['quantity'];
            }
            // 乳胶漆面漆面积
            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['finishing_coat']){
                $finishing_coat = $one_day['quantity'];
            }
            // 腻子面积
            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['putty']){
                $putty = $one_day['quantity'];
            }
            // 阴角线长度
            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['thread']){
                $thread = $one_day['quantity'];
            }
        }


        // 面积比例
        $where = ['points_id'=>self::POINTS_CATEGORY['area_ratio']];
        $ratio = ProjectView::findById($where,10000);
        foreach ($ratio as $value){
            // 客厅百分比
            if ($value['id'] == self::ROOM['hall_area']){
                $hall_area = $value['project_value'];
            }
            // 卧室百分比
            if ($value['id'] == self::ROOM['bedroom_area']){
                $bedroom_area = $value['project_value'];
            }
        }


        //卧室底漆面积   客餐厅底漆面积
        $bedroom_primer_area = BasisDecorationService::paintedArea($bedroom_area,$get['area'],$get['high'],$get['bedroom'],4);
        $hall_primer_area = BasisDecorationService::paintedArea($hall_area,$get['area'],$get['high'],$get['hall'],3);

        // 其它面积查询
        $other = Apartment::find()->asArray()->where(['points_id'=>5])->andWhere(['<=','min_area',$get['area']])->andWhere(['>=','max_area',$get['area']])->all();
        foreach ($other as $one_other){
            if ($one_other['project_name'] == '其他乳胶漆面积'){
                $v = $one_other['project_value'];
            }
//            if ($one_other['project_name'] == '其他腻子面积'){
//                $v1 = $one_other['project_value'];
//            }
            if ($one_other['project_name'] == '其他阴角线长度'){
                $v2 = $one_other['project_value'];
            }
        }



//        乳胶漆底漆面积   乳胶漆底漆天数
        $primer_area = BasisDecorationService::algorithm(5,$bedroom_primer_area[0],$hall_primer_area[0],$v);
        $primer_day = BasisDecorationService::algorithm(6,$primer_area,$undercoat);



        //乳胶漆面漆面积  乳胶漆面漆天数
        $finishing_coat_area = BasisDecorationService::algorithm(1,$primer_area,2);
        $finishing_coat_day = BasisDecorationService::algorithm(6,$finishing_coat_area,$finishing_coat);



//        卧室周长 客餐厅及过道周长
        $bedroom_primer_perimeter = BasisDecorationService::paintedPerimeter($bedroom_primer_area[1],$get['bedroom'],4);
        $drawing_room_perimeter = BasisDecorationService::paintedPerimeter($hall_primer_area[1],$get['hall'],3);


//        阴角线长度   阴角线天数
        $concave_line_length = BasisDecorationService::algorithm(5,$bedroom_primer_perimeter,$drawing_room_perimeter,$v2);
        $concave_line_day = BasisDecorationService::algorithm(6,$concave_line_length,$thread);

//        腻子面积   腻子天数
        $putty_area = BasisDecorationService::algorithm(3,$primer_area,0);
        $putty_day = BasisDecorationService::algorithm(6,$putty_area,$putty);


        $goods = Goods::priceDetail(self::WALL_SPACE, self::LATEX_MATERIAL,$get['city']);
        $judge = BasisDecorationService::judge($goods,$get);


        //当地工艺
        $craft = WorkerType::craft(self::CRAFT_NAME['oil_paint'],$get['city']);
        foreach ($craft as $one_craft){
            // 腻子用量
            if ($one_craft['id'] == self::ROOM['putty']){
                $putty = $one_craft['material'];
            }
            // 底漆用量
            if ($one_craft['id'] == self::ROOM['undercoat']){
                $undercoat = $one_craft['material'];
            }
            // 面漆用量
            if ($one_craft['id'] == self::ROOM['finishing']){
                $finishing = $one_craft['material'];
            }
            // 阴角线用量
            if ($one_craft['id'] == self::ROOM['wire']){
                $wire = $one_craft['material'];
            }
            // 石膏粉用量
            if ($one_craft['id'] == self::ROOM['land']){
                $land = $one_craft['material'];
            }
        }

        // 商品属性
        $putty_attr = BasisDecorationService::goodsAttr($judge,BasisDecorationService::goodsNames()['putty'],'');
        $undercoat_attr = BasisDecorationService::goodsAttr($judge,BasisDecorationService::goodsNames()['emulsion_varnish_primer'],'');
        $finishing_attr = BasisDecorationService::goodsAttr($judge,BasisDecorationService::goodsNames()['emulsion_varnish_surface'],'');
        $wire_attr = BasisDecorationService::goodsAttr($judge,BasisDecorationService::goodsNames()['concave_line'],'长');
        $land_attr = BasisDecorationService::goodsAttr($judge,BasisDecorationService::goodsNames()['land_plaster'],'');



//        腻子费用   底漆费用  面漆费用   阴角线费用   石膏粉费用
        $material_total[] = BasisDecorationService::paintedCost(1,$putty_area,$putty,$putty_attr);
        $material_total[] = BasisDecorationService::paintedCost(1,$primer_area,$undercoat,$undercoat_attr);
        $material_total[] = BasisDecorationService::paintedCost(1,$finishing_coat_area,$finishing,$finishing_attr);
        $material_total[] = BasisDecorationService::paintedCost(1,$concave_line_length,$wire,$wire_attr);
        $material_total[] = BasisDecorationService::paintedCost(2,$finishing_coat_area,$land,$land_attr);



        //总费用
        $total_cost = 0;
        foreach ($material_total as $v){
            $total_cost += $v['cost'];
        }

        //乳胶漆天数   总天数
        $latex_paint_day = BasisDecorationService::algorithm(3,$primer_day,$finishing_coat_day);
        $total_day = ceil(BasisDecorationService::algorithm(5,$latex_paint_day,$concave_line_day,$putty_day));

        //总人工费
        $coating_labor_price['price'] = BasisDecorationService::algorithm(1,$total_day,$labor_costs['univalence']);
        $coating_labor_price['worker_kind'] = $labor_costs['worker_name'];

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'labor_all_cost' => $coating_labor_price,
            'data' => $material_total,
            'total_cost' => round($total_cost,2),
            'bedroom_area' => round($bedroom_primer_area[1],2),
        ]);
    }

    /**
     * 泥作
     */
    public function actionMudMake()
    {
        $get = \Yii::$app->request->get();

        $labor_costs = LaborCost::profession($get['city'],self::WORK_CATEGORY['mason']);
        $day_workload = WorkerCraftNorm::findByLaborCostAll($labor_costs['id']);
        foreach ($day_workload as $one_day) {
            // 保护层
            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['maskant']){
                $maskant = $one_day['quantity'];
            }
            // 贴地砖面积
            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['floor_tile']){
                $floor_tile = $one_day['quantity'];
            }
            // 贴墙砖面积
            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['wall_brick']){
                $wall_brick = $one_day['quantity'];
            }
        }

        // 面积比例
        $ratio = ProjectView::findById(self::POINTS_CATEGORY['area_ratio'],10000);
        foreach ($ratio as $one_ratio){
            // 厨房
            if ($one_ratio['id'] == self::ROOM['kitchen_area']){
                $kitchen_ratio = $one_ratio['project_value'];
            }
            // 卫生间
            if ($one_ratio['id'] == self::ROOM['toilet_area']){
                $toilet_ratio = $one_ratio['project_value'];
            }
            // 客厅
            if ($one_ratio['id'] == self::ROOM['hall_area']){
                $hall_area = $one_ratio['project_value'];
            }
        }


        //厨房面积   卫生间面积  客餐厅面积
        $kitchen_area = BasisDecorationService::algorithm(1,$get['area'],$kitchen_ratio);
        $toilet_area = BasisDecorationService::algorithm(1,$get['area'],$toilet_ratio);
        $drawing_room_area = BasisDecorationService::algorithm(1,$get['area'],$hall_area);



        //当地工艺
        $craft = WorkerType::craft(self::CRAFT_NAME['tiler'],$get['city']);
        foreach ($craft as $one_craft){
            // 水泥用量
            if ($one_craft['id'] == self::ROOM['concrete']){
                $concrete = $one_craft['material'];
            }
            // 自流平用量
            if ($one_craft['id'] == self::ROOM['self_leveling']){
                $self_leveling = $one_craft['material'];
            }
            // 河沙用量
            if ($one_craft['id'] == self::ROOM['river_sand']){
                $river_sand = $one_craft['material'];
            }
        }

//        保护层面积  保护层天数  保护层面积÷【每天做保护层面积】
        $covering_layer_area = $get['waterproof_total_area'];
        $covering_layer_day = BasisDecorationService::algorithm(6,$covering_layer_area,$maskant);

        // 贴砖高度查询
        $project = ProjectView::pointsId(self::PROJECT_DETAILS['tiler'],$get['high']);
        $high = isset($project['project_value'])?$project['project_value']:2.8;


//        卫生间墙面积  厨房墙面积
        $toilet_wall_area = BasisDecorationService::mudMakeArea($toilet_area,$high,$get['toilet'],4);
        $kitchen_wall_area = BasisDecorationService::mudMakeArea($kitchen_area,$high,$get['kitchen'],3);

//        其它面积查询
        $latex_paint_area = Apartment::find()
            ->asArray()
            ->where(['<=','min_area',$get['area']])
            ->andWhere(['>=','max_area',$get['area']])
            ->andWhere(['points_id'=>self::PROJECT_DETAILS['tiler']])
            ->all();

        foreach ($latex_paint_area as $value){
            if ($value['project_name'] == '其他地面积'){
                $other_land_area = $value['project_value'];
            }
            if ($value['project_name'] == '其他墙面积'){
                $other_wall_area = $value['project_value'];
            }
        }

        // 墙砖面积   墙砖天数
        $wall_area = BasisDecorationService::algorithm(5,$toilet_wall_area,$kitchen_wall_area,$other_wall_area);
        $wall_day = BasisDecorationService::algorithm(6,$wall_area,$wall_brick);

        // 地砖面积   地砖天数
        $floor_tile_area = BasisDecorationService::algorithm(11,$toilet_area,$kitchen_area,$drawing_room_area,$other_land_area);
        $floor_tile_day = BasisDecorationService::algorithm(6,$floor_tile_area,$floor_tile);



//        贴砖天数   总天数：保护层天数+贴砖天数
        $tiling_day = BasisDecorationService::algorithm(3,$wall_day,$floor_tile_day);
        $total_day = ceil(BasisDecorationService::algorithm(3,$tiling_day,$covering_layer_day));

        //总的人工费
        $total_labor_cost['price'] = BasisDecorationService::algorithm(1,$total_day,$labor_costs['univalence']);
        $total_labor_cost['worker_kind'] = $labor_costs['worker_name'];


        //材料费
        $goods = Goods::priceDetail(self::WALL_SPACE,self::TILER_MATERIAL);
        $judge = BasisDecorationService::judge($goods,$get);

        //商品属性
        $cement_attr = BasisDecorationService::goodsAttr($judge,BasisDecorationService::goodsNames()['cement'],'重');
        $self_leveling_attr = BasisDecorationService::goodsAttr($judge,BasisDecorationService::goodsNames()['self_leveling'],'重');
        $river_sand_attr = BasisDecorationService::goodsAttr($judge,BasisDecorationService::goodsNames()['river_sand'],'重');
        $wall_brick_attr = BasisDecorationService::goodsAttr($judge,BasisDecorationService::goodsNames()['wall_brick'],'',2);

        //地砖 商品查询   44
        $brick = Goods::priceDetail(self::WALL_SPACE,self::BRICK);
        foreach ($brick as &$one_brick){
            $one_brick['attr'] = GoodsAttr::findByGoodsIdUnits($one_brick['id'],'');
        }
        $series = Series::find()->select('id,series')->where(['id'=>$get['series']])->one();
        $style = Style::find()->select('id,style')->where(['id'=>$get['style']])->one();
        foreach ($brick as $one_brick){
            foreach ($one_brick['attr'] as $attr_){

                if ($attr_['value'] == '卫生间'
                    && strpos($one_brick['series_name'],$series->series) !== false
                    && strpos($one_brick['style_name'],$style->style) !== false
                ){
                    $toilet_goods[] = $one_brick;
                }


                if ($attr_['value'] == '厨房'
                    && strpos($one_brick['series_name'],$series->series) !== false
                    && strpos($one_brick['style_name'],$style->style) !== false
                ){
                    $kitchen_goods[] = $one_brick;
                }


                if ($attr_['value'] == '客厅'
                    && strpos($one_brick['series_name'],$series->series) !== false
                    && strpos($one_brick['style_name'],$style->style) !== false
                ){
                    $hall_goods[] = $one_brick;
                }
            }

        }
        $max_toilet_goods[] = BasisDecorationService::profitMargin($toilet_goods);
        $max_kitchen_goods[] = BasisDecorationService::profitMargin($kitchen_goods);
        $max_hall_goods[] = BasisDecorationService::profitMargin($hall_goods);


//        水泥费用    自流平费用  河沙费用  墙砖费用 卫生间
        $self_leveling_area = $drawing_room_area;
        $cement_area = BasisDecorationService::algorithm(5,$covering_layer_area,$floor_tile_area,$wall_area);
        $material_total[] = BasisDecorationService::mudMakeCost(1,$cement_area,$concrete,$cement_attr);
        $material_total[] = BasisDecorationService::mudMakeCost(1,$self_leveling_area,$self_leveling,$self_leveling_attr);
        $material_total[] = BasisDecorationService::mudMakeCost(1,$cement_area,$river_sand,$river_sand_attr);
        $material_total[] = BasisDecorationService::mudMakeCost(2,$wall_area,'',$wall_brick_attr);
        $material_total[] = BasisDecorationService::mudMakeCost(3,$toilet_area,'',$max_toilet_goods);
        $material_total[] = BasisDecorationService::mudMakeCost(3,$kitchen_area,'',$max_kitchen_goods);
        $material_total[] = BasisDecorationService::mudMakeCost(3,$drawing_room_area,'',$max_hall_goods);

        //总费用
        $total_cost = 0;
        foreach ($material_total as $v){
            $total_cost += $v['cost'];
        }


        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'labor_all_cost' => $total_labor_cost,
            'data' => $material_total,
            'total_cost' => round($total_cost,2),
            'hall_area' => round($drawing_room_area,2),
        ]);
    }

    /**
     * 杂工
     */
    public function actionHandyman()
    {
        $get = \Yii::$app->request->get();
        if (!isset($get['12_dismantle']) || empty($get['12_dismantle'])){
            $get['12_dismantle'] = 0;
        }
        if (!isset($get['24_dismantle'])|| empty($get['24_dismantle'])){
            $get['24_dismantle'] = 0;
        }
        if (!isset($get['repair'])|| empty($get['repair'])){
            $get['repair'] = 0;
        }
        if (!isset($get['12_new_construction'])|| empty($get['12_new_construction'])){
            $get['12_new_construction'] = 0;
        }
        if (!isset($get['24_new_construction'])|| empty($get['24_new_construction'])){
            $get['24_new_construction'] = 0;
        }


        $labor_costs = LaborCost::profession($get['city'],self::WORK_CATEGORY['backman']);
        $day_workload = WorkerCraftNorm::findByLaborCostAll($labor_costs['id']);
        foreach ($day_workload as $one_day) {
            // 新建24
            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['build_24']){
                $build_24 = $one_day['quantity'];
            }
            // 新建12
            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['build_12']){
                $build_12 = $one_day['quantity'];
            }
            // 拆除24
            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['dismantle_24']){
                $dismantle_24 = $one_day['quantity'];
            }
            // 拆除12
            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['dismantle_12']){
                $dismantle_12 = $one_day['quantity'];
            }
            // 补烂面积
            if ($one_day['worker_type_id'] == self::POINTS_CATEGORY['repair']){
                $repair = $one_day['quantity'];
            }
        }

        // 其它面积查询
        $apartment = Apartment::find()
            ->asArray()
            ->where(['<=','min_area',$get['area']])
            ->andWhere(['>=','max_area',$get['area']])
            ->andWhere(['points_id'=>self::CRAFT_NAME['backman']])
            ->all();

        // 拆除天数  新建天数  补烂天数  总天数
        $dismantle_day = BasisDecorationService::wallArea(1,$get,$dismantle_12,$dismantle_24);
        $build_day = BasisDecorationService::wallArea(2,$get,$build_12,$build_24);
        $repair_day = BasisDecorationService::wallArea(3,$get,$repair);
        $total_day = ceil(BasisDecorationService::algorithm(11,$dismantle_day,$build_day,$repair_day,$apartment[0]['project_value']));

        //当地工艺
        $craft = WorkerType::craft(self::CRAFT_NAME['backman_'],$get['city']);
        foreach ($craft as $one_craft){
            // 12墙建渣运到楼下
            if ($one_craft['id'] == self::ROOM['rubbish_12']){
                $rubbish_12 = $one_craft['material'];
            }
            // 24墙建渣运到楼下
            if ($one_craft['id'] == self::ROOM['rubbish_24']){
                $rubbish_24 = $one_craft['material'];
            }
            // 车拉12墙面积
            if ($one_craft['id'] == self::ROOM['vehicle_12']){
                $vehicle_12 = $one_craft['material'];
            }
            // 车拉24墙面积
            if ($one_craft['id'] == self::ROOM['vehicle_24']){
                $vehicle_24 = $one_craft['material'];
            }
            // 车费用
            if ($one_craft['id'] == self::ROOM['fare']){
                $fare = $one_craft['material'];
            }
            // 12墙新建水泥用量
            if ($one_craft['id'] == self::ROOM['concrete_12']){
                $concrete_12 = $one_craft['material'];
            }
            // 24墙新建水泥用量
            if ($one_craft['id'] == self::ROOM['concrete_24']){
                $concrete_24 = $one_craft['material'];
            }
            // 补烂水泥
            if ($one_craft['id'] == self::ROOM['concrete_repair']){
                $concrete_repair = $one_craft['material'];
            }
            // 12墙新建河沙用量
            if ($one_craft['id'] == self::ROOM['river_sand_12']){
                $river_sand_12 = $one_craft['material'];
            }
            // 24墙新建河沙用量
            if ($one_craft['id'] == self::ROOM['river_sand_24']){
                $river_sand_24 = $one_craft['material'];
            }
            // 补烂河沙
            if ($one_craft['id'] == self::ROOM['river_sand_repair']){
                $river_sand_repair = $one_craft['material'];
            }
        }


        if ($get['building_scrap'] == 1) {
            $cost_12 = BasisDecorationService::haveBuildingScrap(1,$get['12_dismantle'],$rubbish_12);
            $cost_24 = BasisDecorationService::haveBuildingScrap(1,$get['24_dismantle'],$rubbish_24);
            $building_scrap = ceil(BasisDecorationService::algorithm(3,$cost_12['cost'],$cost_24['cost']));
        } else {
            $cost_12 = BasisDecorationService::haveBuildingScrap(2,$get['12_dismantle'],$rubbish_12,$vehicle_12,$fare);
            $cost_24 = BasisDecorationService::haveBuildingScrap(2,$get['24_dismantle'],$rubbish_24,$vehicle_24,$fare);
            $building_scrap = ceil(BasisDecorationService::algorithm(3,$cost_12['cost'],$cost_24['cost']));

        }

//        总人工费
        $labor_cost['price'] = BasisDecorationService::algorithm(13,$total_day,$labor_costs['univalence'],$building_scrap);
        $labor_cost['worker_kind'] = $labor_costs['worker_name'];


        //材料费
        $goods = Goods::priceDetail(self::WALL_SPACE, self::BACKMAN_MATERIAL);
        $judge = BasisDecorationService::judge($goods,$get);


        // 商品属性
        $air_brick_attr = BasisDecorationService::goodsAttr($judge,BasisDecorationService::goodsNames()['air_brick'],'',2);
        $river_sand_attr = BasisDecorationService::goodsAttr($judge,BasisDecorationService::goodsNames()['river_sand'],'重');
        $cement_attr = BasisDecorationService::goodsAttr($judge,BasisDecorationService::goodsNames()['cement'],'重');

        //水泥费用   河沙费用   空心砖费用
        $material_total[] = BasisDecorationService::handyman(1,$get,$concrete_repair,$concrete_12,$concrete_24,$cement_attr);
        $material_total[] = BasisDecorationService::handyman(1,$get,$river_sand_repair,$river_sand_12,$river_sand_24,$river_sand_attr);
        $material_total[] = BasisDecorationService::handyman(2,$get,'','','',$air_brick_attr);


        //总费用
        $total_cost = 0;
        foreach ($material_total as $v){
            $total_cost += $v['cost'];
        }

        $a = [];
        foreach ($material_total as $marerial){
            if ($marerial['quantity'] != 0){
                $a [] = $marerial;
            }
        }


        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'labor_all_cost' => $labor_cost,
            'data' => $a,
            'total_cost' => round($total_cost,2),
        ]);
    }

    /**
     * 添加材料查询
     */
    public function actionAddMaterials()
    {
        $code = trim(Yii::$app->request->get('city',''));
        $series = trim(Yii::$app->request->get('series',''));
        $style = trim(Yii::$app->request->get('style',''));
        $area = trim(Yii::$app->request->get('area',''));



        $add_materials = DecorationAdd::findByAll($code,$style,$series,$area);
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
        $goods = Goods::findBySkuAll($codes);
        if ($goods == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $style = BasisDecorationService::style($goods);
        foreach ($add_materials as $material){
            foreach ($style as &$one_goods){
                if ($one_goods['sku'] == $material['sku']) {
                    $one_goods['quantity'] = (int)$material['quantity'];
                    $one_goods['cost'] = round($material['quantity'] * $one_goods['platform_price'],2);
                    $one_goods['procurement'] = round($material['quantity'] * $one_goods['purchase_price_decoration_company'],2);
                }
            }
        }
        return Json::encode([
            'code' => 200,
            'msg'  => 'ok',
            'add_list' => $style,
        ]);
    }

    /**
     * 系数管理
     */
    public function actionCoefficient()
    {
        $post = Yii::$app->request->post();
        $coefficient = CoefficientManagement::find()->asArray()->all();

        if ($coefficient == null) {
            $code = 1064;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        foreach ($coefficient as $one_coefficient) {
            $classify = GoodsCategory::find()->select('id,title')->where(['id' => $one_coefficient['category_id']])->one();
            foreach ($post['list'] as &$materials) {
                if ($classify->id == $materials['category_id']) {
                    $materials['goods_price'] = $materials['procurement'] / ($one_coefficient['coefficient'] / 100);
                }
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
        $get = Yii::$app->request->get();

        // 有资料 计算公式
        $goods = Goods::assortList(self::MATERIALS,$get['city']);
        $lamp = [];
        foreach ($goods as $oneLamp){
            if ($oneLamp['title'] == BasisDecorationService::goodsNames()['lamp'] && strpos($oneLamp['series_id'],$get['series'])!== false && strpos($oneLamp['style_id'],$get['style'])!== false){
                $attr = BasisDecorationService::goodsAttr([$oneLamp],BasisDecorationService::goodsNames()['lamp'],'适用处',1);
                $lamp [] = $attr;
            }
        }

        $material[] = BasisDecorationService::lamp($lamp,$get);
        $material[]= BasisDecorationService::formula($goods,$get);


        //无计算公式
        $assort_material = AssortGoods::find()->asArray()->where(['state'=>1])->all();
        if ($assort_material){
            foreach ($assort_material as $one_without_assort){
                $without_assort_name[] = $one_without_assort['category_id'];
            }
            $_goods = Goods::assortList($without_assort_name,$get['city']);
            $material[]= BasisDecorationService::withoutAssortGoods($_goods,$assort_material,$get);
        }



        $condition_stairs = [];
        //  楼梯信息
        if ($get['stairway_id'] == 1) {
            $stairs = Goods::findByCategory(BasisDecorationService::goodsNames()['stairs']);
            $stairs_details = StairsDetails::find()->asArray()->all();
            foreach ($stairs_details as $detail){
               if ($detail['id'] == $get['stairs']){
                   $sm = $detail['attribute'];
               }
            }

            foreach ($stairs as &$one_stairs_price) {
                if (
                    $one_stairs_price['value'] == $sm
                    && $one_stairs_price['style_id'] == $get['style']
                ) {
                    $one_stairs_price['quantity'] = (int)1;
                    $one_stairs_price['cost'] = round(BasisDecorationService::algorithm(1,$one_stairs_price['platform_price'],$one_stairs_price['quantity']),2);
                    $one_stairs_price['procurement'] = round(BasisDecorationService::algorithm(1,$one_stairs_price['purchase_price_decoration_company'],$one_stairs_price['quantity']),2);
                    $condition_stairs [] = $one_stairs_price;
                }

            }
            $style = BasisDecorationService::style($condition_stairs);
            $material[][]= BasisDecorationService::profitMargin($style);
        }


        $goods_material = [];
        foreach ($material as $one){
            if($one != null){
                $goods_material[]= $one;
            }
        }


        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' =>[
               'goods' => $goods_material,
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
        $data=BrainpowerInitalSupervise::find()
            ->select('id,image,district_code,street,toponymy,house_type_name,effect_id')//TODO 新增 effect_id
            ->where(['status'=>BrainpowerInitalSupervise::STATUS_OPEN])
            ->orderBy(['sort' => SORT_ASC])
            ->all();
        foreach ($data as &$v){
            $toponymy_id=EffectToponymy::find()
                    ->asArray()
                    ->select('id')
                    ->where(['district_code'=>$v['district_code'],'street'=>$v['street'],'toponymy'=>$v['toponymy']])
                    ->one()['id'];
            $v['id']=$toponymy_id;
        }

        return Json::encode([
            'code' =>200,
            'msg'=>'ok',
            'data'=>$data
        ]);


    }

    /**
     * 小区案例列表
     * @return string
     */
    public function actionEffectCaseList(){
        $id=(int)trim(Yii::$app->request->get('id',''));
        $effect= EffectToponymy::effectdatas($id);
        if ($effect == null) {
            $code = 1066;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        foreach ($effect as &$one_effect){
            $one_effect['detailed_address'] = $one_effect['district'] .  $one_effect['street'];
            if ($one_effect['type'] == 1){
                $one_effect['case_picture'] = EffectPicture::findById( $one_effect['id']);
            }
        }
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data'=>[
                'list' => $effect,
//                'id' => $id,
            ],
        ]);

    }

    /**
     * 案例数据
     * @return string
     */
    public function actionParticulars()
    {
        $id = (int)Yii::$app->request->get('id','');
        $effect = Effect::find()->asArray()->select('id,bedroom,sittingRoom_diningRoom as hall,toilet,kitchen,window,area,high,province,city,district,toponymy,street,particulars,stairway,stair_id,house_image,type,city_code')->where(['id'=>$id])->one();
        $effect['street'] = $effect['district'].$effect['street'];
        $effect['case_picture'] = EffectPicture::find()->asArray()->where(['effect_id'=>$id])->all();
        if ($effect['type'] == 0){
            return Json::encode([
               'code' => 200,
               'msg' => 'ok',
               'effect' => $effect,
            ]);
        }

        $worker_data = WorksWorkerData::findById($id);
        if ($worker_data){
            foreach ($worker_data as &$one_){
                $one_['price'] = $one_['worker_price'];
                unset($one_['worker_price']);
            }
        }
        $data = WorksData::find()->asArray()->select('effect_id,goods_first,goods_second,goods_three,three_category_id as id,goods_code,sum(goods_quantity) as goods_quantity')->where(['effect_id'=>$id])->groupBy(['goods_code'])->all();

        if (!$data){
            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
                'effect' => $effect,
                'worker_cost' => $worker_data,
                'goods' =>[],
            ]);
        }


        $sku = [];
        foreach ($data as $one_data){
            $sku [] = $one_data['goods_code'];
        }
        $goods  = Goods::findBySkuAll($sku);
        foreach ($data as $case_works_datum) {
            foreach ($goods as &$one_goods) {
                if ($one_goods['sku'] == $case_works_datum['goods_code']) {
                    $one_goods['goods_first']  = $case_works_datum['goods_first'];
                    $one_goods['goods_second'] = $case_works_datum['goods_second'];
                    $one_goods['goods_three']  = $case_works_datum['goods_three'];
                    $one_goods['quantity']     = (int)ceil($case_works_datum['goods_quantity']);
                    $one_goods['cost']         = round($one_goods['quantity'] * $one_goods['platform_price'],2);
                    $one_goods['procurement']  = round($one_goods['purchase_price_decoration_company'] * $case_works_datum['goods_quantity'],2);
                }
            }
        }


        //  匹配 通用管理 数据
        $assort_goods = AssortGoods::find()->asArray()->where(['state'=>0])->all();
        $goods_ = [];
        foreach ($assort_goods as $assort){
            foreach ($goods as $woks){
                if ($assort['category_id'] == $woks['category_id']){
                    $goods_[] = $woks;
                }
            }
        }
        $_style = BasisDecorationService::style($goods_);

        //  大理石 数据
        if ($effect['window'] != 0){
            $stone  = Goods::priceDetail(3,52,$effect['city_code']);
            foreach ($stone as &$_goods){
                $substr = substr($_goods['path'],0,strlen($_goods['path'])-1);
                $where ="id in (".$substr.")";
                $goods_category = GoodsCategory::find()->asArray()->select('id,title')->where($where)->all();
                $_goods['goods_first'] = $goods_category['0']['title'];
                $_goods['goods_second'] = $goods_category['1']['title'];
                $_goods['goods_three'] = $goods_category['2']['title'];

                $_goods['quantity'] = (int)$effect['window'];
                $_goods['cost'] = round($_goods['quantity'] * $_goods['platform_price'],2);
                $_goods['procurement'] = round($_goods['quantity'] * $_goods['purchase_price_decoration_company'],2);
                $marble[] = $_goods;
            }
            $griotte = BasisDecorationService::profitMargin($marble);
        }else{
            $griotte = [];
        }



        //   楼梯  数据
        if ($effect['stairway'] != 0){
            $stairs = Goods::findByCategory(BasisDecorationService::goodsNames()['stairs']);
            $stairs_details = StairsDetails::find()->asArray()->all();
            $ma = [];
            foreach ($stairs_details as $detail){
                if ($effect['stair_id'] == $detail['id'] ){
                    $ma = $detail['attribute'];
                }
            }

            $condition_stairs=[];
            foreach ($stairs as &$one_stairs_price) {
                if (
                    $one_stairs_price['value'] == $ma
                    && $one_stairs_price['style_id'] == $effect['case_picture'][0]['style_id']
                ) {
                    $substr = substr($one_stairs_price['path'],0,strlen($one_stairs_price['path'])-1);
                    $where ="id in (".$substr.")";
                    $goods_category = GoodsCategory::find()->asArray()->select('id,title')->where($where)->all();
                    $one_stairs_price['quantity'] = 1;
                    $one_stairs_price['goods_first'] = $goods_category['0']['title'];
                    $one_stairs_price['goods_second'] = $goods_category['1']['title'];
                    $one_stairs_price['goods_three'] = $goods_category['2']['title'];
                    $one_stairs_price['cost'] = round($one_stairs_price['platform_price'] * $one_stairs_price['quantity'],2);
                    $one_stairs_price['procurement'] = round($one_stairs_price['purchase_price_decoration_company'] * $one_stairs_price['quantity'],2);
//                    unset($one_stairs_price['path']);
                    $condition_stairs [] = $one_stairs_price;
                }
            }

            $style = BasisDecorationService::style($condition_stairs);
            $material = BasisDecorationService::profitMargin($style);

        }else{
            $material = [];
        }


        //  合并 三个数组  $goods_     $material    $griotte
        $_goods = [];
        foreach ($_style as $value){
            $_goods [] = $value;
        }
        $_goods [] = $material;
        $_goods [] = $griotte;
        $array_filter = array_filter($_goods);
        $array_merge = array_merge($array_filter);


        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'effect' => $effect,
            'worker_cost' => $worker_data,
            'goods' => $array_merge,
        ]);

    }


    /**
     * 默认数量
     * @return string
     */
    public function actionChangeGoods()
    {
        $get = Yii::$app->request->get();
//        $id = (int)\Yii::$app->request->get('id','');

        $goods = Goods::find()
            ->select('goods.id,goods.category_id,gc.title,goods.market_price,goods.platform_price,goods.supplier_price,goods.purchase_price_decoration_company')
            ->leftJoin('goods_category AS gc', 'goods.category_id = gc.id')
            ->where(['goods.id'=>$get['id']])
            ->asArray()
            ->one();

        $changeGoods = BasisDecorationService::count($goods,$get);
        $change_goods = $changeGoods[0];
        if ($change_goods != 0){
            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
                'quantity' => $change_goods,
            ]);
        }
        // 判断是否有计算公式
        $value = BasisDecorationService::judgeGoods($goods['category_id'],self::MATERIALS);

        //  无计算公式
        if ($value == false) {
            $assort_goods = AssortGoods::find()
                ->where(['state'=>1])
                ->andWhere(['category_id'=>$goods['category_id']])
                ->one();
            $change_goods = $assort_goods['quantity'];
        }

        // 有计算公式
        if ($value == true){
            $material = BasisDecorationService::oneFormula($goods,$get,$changeGoods[1]);
            $change_goods = $material;
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'quantity' => $change_goods,
        ]);

    }

    /**
     * 测试数据
     *
     * @return string
     */
    public function actionTest()
    {
        $a = Goods::find()->where(["id"=>189])->one();
        $b = GoodsAttr::find()->where(['goods_id'=>$a['id']])->one();
        $c = GoodsStyle::find()->where(['goods_id'=>$a['id']])->one();
        return Json::encode([
           "a"=>[$a,$b,$c]
        ]);

    }

}