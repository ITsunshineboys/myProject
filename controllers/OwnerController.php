<?php

namespace app\controllers;

use app\models\Apartment;
use app\models\AssortGoods;
use app\models\BrainpowerInitalSupervise;
use app\models\CarpentryAdd;
use app\models\CoefficientManagement;
use app\models\DecorationAdd;
use app\models\EditCategory;
use app\models\Effect;
use app\models\EffectEarnest;
use app\models\EffectPicture;
use app\models\EngineeringStandardCarpentryCoefficient;
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
    const TILER_MATERIAL     = [172,36,6];       // 泥工材料id
    const BACKMAN_MATERIAL   = [172,6,3];      // 杂工材料id


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
        'mason'      => 5,  // 泥瓦工id
        'backman'    => 6,  // 杂工id
//        'backman_'          => '泥工',
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
//        'emulsion_varnish'  => '乳胶漆工艺',
        'oil_paint' => 47,  //'油漆工艺',
        'tiler'             => '泥工工艺',
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
        $id  = trim(Yii::$app->request->get('id',''));
        $str = trim(Yii::$app->request->get('str',''));
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
        $total_cost = $goods_price[0]['cost']+$goods_price[1]['cost']+$goods_price[2]['cost']+$goods_price[3]['cost'];

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
        $total_cost = BasisDecorationService::algorithm(3,$material[0]['cost'],$material[1]['cost']);

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


        $labor_all_cost['price'] = BasisDecorationService::algorithm(1,$total,$labor['univalence']);
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
        $labor_all_cost['price'] = BasisDecorationService::p($total_area,$area,$labor_cost['univalence']);
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
        $coefficient = EngineeringStandardCarpentryCoefficient::findByAll(['and',['city_code'=>$get['city']],['project'=>$get['series']],['project'=>$get['style']]]);
        foreach ($coefficient as $one_){
            // 系列系数2
            if ($one_['series_or_style'] == 0 && $one_['coefficient'] == 2 && $one_['project'] == $get['series']){
                $coefficient2 = $one_['value'];
            }
            // 系列系数1
            if ($one_['series_or_style'] == 0 && $one_['coefficient'] == 1 && $one_['project'] == $get['series']){
                $coefficient1 = $one_['value'];
            }

            // 风格系数1
            if ($one_['series_or_style'] == 1 && $one_['coefficient'] == 1 && $one_['project'] == $get['style']){
                $coefficient3 = $one_['value'];
            }

            // 系列系数3
            if ($one_['series_or_style'] == 0 && $one_['coefficient'] == 3 && $one_['project'] == $get['series']){
                $coefficient4 = $one_['value'];
            }

            // 风格系数2
            if ($one_['series_or_style'] == 1 && $one_['coefficient'] == 2 && $one_['project'] == $get['style']){
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
            'data' => [
                'labor_all_cost' => $labour_charges,
                'data' => $material_total,
                'total_cost' => $total_area,
            ]
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
            if ($one_other['project_name'] == '其他腻子面积'){
                $v1 = $one_other['project_value'];
            }
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
        $putty_area = BasisDecorationService::algorithm(3,$finishing_coat_area,$v1);
        $putty_day = BasisDecorationService::algorithm(6,$putty_area,$putty);


        $goods = Goods::priceDetail(self::WALL_SPACE, self::LATEX_MATERIAL);
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
            'total_cost' => $total_cost,
            'bedroom_area' => round($bedroom_primer_area[1],2),
        ]);
    }

    /**
     * 泥作
     */
    public function actionMudMake()
    {
        $post = \Yii::$app->request->get();
        //工人一天单价
//        $_select = 'id,univalence,worker_kind';
        $labor_costs = LaborCost::profession($post, self::WORK_CATEGORY['mason']);

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
                    $covering_layer_day_area = $labor_cost['quantity']/100;
                    break;
                case $labor_cost['worker_kind_details'] == self::WORKMANSHIP['geostrophy_area']:
                    $geostrophy_day_area = $labor_cost['quantity']/100;
                    break;
                case $labor_cost['worker_kind_details'] == self::WORKMANSHIP['wall_brick_area']:
                    $wall_tile_day_area = $labor_cost['quantity']/100;
                    break;
            }
        }

        //泥作面积    mudMakeArea
        //厨房面积
        $project_view = ProjectView::find()->asArray()->where(['parent_project'=> '面积比例'])->all();
        foreach ($project_view as $value){
            if ($value['project'] == self::ROOM_AREA['kitchen_area']){
                $kitchen_particulars = $value['project_value'] / 100;
            }

            if ($value['project'] == self::ROOM_AREA['toilet_area']){
                $toilet_particulars = $value['project_value'] / 100;
            }

            if ($value['project'] == self::ROOM_AREA['hall_area']){
                $drawing_room_particulars = $value['project_value'] / 100;
            }
        }
        //TODO 少了%  所以还需要除以100
        $kitchen_area = $post['area'] * $kitchen_particulars*0.01;
        //卫生间面积
        $toilet_area = (int)$post['area'] * $toilet_particulars*0.01;
        //客餐厅面积
        $drawing_room_area = (int)$post['area'] * $drawing_room_particulars*0.01;

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll(self::PROJECT_NAME['tiler'], $post['city']);

        if ($craft == null){
            $code = 1059;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        foreach ($craft as $local_craft) {
            switch ($local_craft) {
                case $local_craft['project_details'] == BasisDecorationService::DetailsId2Title()['cement']:
                    $cement_craft = $local_craft['material'];
                    break;
                case $local_craft['project_details'] == BasisDecorationService::DetailsId2Title()['self_leveling']:
                    $self_leveling_craft = $local_craft['material'];
                    break;
                case $local_craft['project_details'] == BasisDecorationService::DetailsId2Title()['river_sand']:
                    $river_sand_craft = $local_craft['material'];
                    break;
            }
        }

//        保护层面积
        $covering_layer_area = $post['waterproof_total_area'];
//        保护层天数：保护层面积÷【每天做保护层面积】
        $covering_layer_day = $covering_layer_area / $covering_layer_day_area;



        $points = Points::findByOne('id,title',"title='泥作'");
        $perject = ProjectView::find()->where(['and',['points_id'=>$points['id']],['project'=>$post['high']]])->asArray()->one();
        $high = isset($perject['project_value'])?$perject['project_value']:2.8;

//        卫生间墙面积
        $toilet_wall_area = BasisDecorationService::mudMakeArea($toilet_area, $high, $post['toilet']);
//        厨房墙面积
        $kitchen_wall_area = BasisDecorationService::mudMakeArea($kitchen_area, $high, $post['kitchen'], 3);
//        墙砖面积
        $latex_paint_area = Apartment::find()
            ->asArray()
            ->where(['<=','min_area',$post['area']])
            ->andWhere(['>=','max_area',$post['area']])
            ->andWhere(['project_name'=>'其他墙面积'])
            ->andWhere(['points_id'=>$points['id']])
            ->one();
        $wall_area = $toilet_wall_area + $kitchen_wall_area + $latex_paint_area['project_value'];
//        墙砖天数
        $wall_day = $wall_area / $wall_tile_day_area;


//        地砖面积
        $land_area = Apartment::find()
            ->asArray()
            ->where(['<=','min_area',$post['area']])
            ->andWhere(['>=','max_area',$post['area']])
            ->andWhere(['project_name'=>'其他地面积'])
            ->andWhere(['points_id'=>$points['id']])
            ->one();
        $floor_tile_area = $drawing_room_area + $toilet_area + $kitchen_area + $land_area['project_value'];
//        地砖天数
        $floor_tile_day = $floor_tile_area / $geostrophy_day_area;



//        贴砖天数
        $tiling_day = $floor_tile_day + $wall_day;
//        总天数：保护层天数+贴砖天数
        $total_day = ceil($tiling_day + $covering_layer_day);
        //总的人工费
        $total_labor_cost['price'] = $total_day * $labor_costs['univalence'];
        $total_labor_cost['worker_kind'] = $labor_costs['worker_kind'];

        //材料费
        $goods = Goods::priceDetail(self::WALL_SPACE, self::TILER_MATERIAL);
        if ($goods == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data' => [
                    'mud_make_labor_price' => [],
                    'mud_make_material' => [],
                ]
            ]);
        }
//        $goods_price = BasisDecorationService::priceConversion($goods);
        $goods_attr = BasisDecorationService::mudMakeMaterial($goods);

        $wall_brick = Goods::seriesAndStyle(self::WALL_SPACE,BasisDecorationService::goodsNames()['wall_brick'], $post);
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

        $floor_tile = Goods::seriesAndStyle(self::WALL_SPACE,BasisDecorationService::goodsNames()['floor_tile'], $post);
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
        $cement_cost = BasisDecorationService::mudMakeCost($cement_area, $goods, $cement_craft, $goods_attr,BasisDecorationService::goodsNames()['cement']);

//        自流平费用
        $self_leveling_area = $drawing_room_area;
        $self_leveling_cost = BasisDecorationService::mudMakeCost($self_leveling_area, $goods, $self_leveling_craft, $goods_attr,BasisDecorationService::goodsNames()['self_leveling']);

        //        河沙费用
        $river_sand_cement_area = $covering_layer_area + $floor_tile_area + $wall_area;
        $river_sand_cost = BasisDecorationService::mudMakeCost($river_sand_cement_area, $goods, $river_sand_craft, $goods_attr,BasisDecorationService::goodsNames()['river_sand']);



//        墙砖费用 墙砖费用：个数×抓取的商品价格 个数：（墙砖面积÷抓取墙砖面积）
        $wall_brick_cost['quantity'] = ceil($wall_area / $wall_brick_area);
        $wall_brick_cost['cost'] = round($wall_brick_cost ['quantity'] * $wall_brick_max['platform_price'],2);
        $wall_brick_cost['procurement'] = round($wall_brick_cost ['quantity'] * $wall_brick_max['purchase_price_decoration_company'],2);




//        卫生间地砖个数和价格：（墙砖面积÷抓取墙砖面积）  厨房/卫生间墙砖费用
        $toilet_wall_brick_cost['quantity'] = ceil($toilet_area / $floor_tile_attr['toilet']['area']);
        $toilet_wall_brick_cost['cost'] = round($toilet_wall_brick_cost['quantity'] * $floor_tile_attr['toilet']['price'],2);
        $toilet_wall_brick_cost['procurement'] = round($toilet_wall_brick_cost['quantity'] * $floor_tile_attr['toilet']['purchase_price_decoration_company'],2);



//        厨房地砖费用 厨房地砖费用：个数×抓取的商品价格 个数：（厨房地砖面积÷抓取厨房地砖面积）
        $kitchen_wall_brick_cost['quantity'] = ceil($kitchen_area / $floor_tile_attr['kitchen']['area']);
        $kitchen_wall_brick_cost['cost'] = round($kitchen_wall_brick_cost['quantity'] * $floor_tile_attr['kitchen']['purchase_price_decoration_company'],2);
        $kitchen_wall_brick_cost['procurement'] = round($kitchen_wall_brick_cost['quantity'] * $floor_tile_attr['kitchen']['purchase_price_decoration_company'],2);



//        客厅地砖费用
        $hall_wall_brick_cost['quantity'] = ceil($drawing_room_area / $floor_tile_attr['hall']['area']);
        $hall_wall_brick_cost['cost'] = round($hall_wall_brick_cost['quantity'] * $floor_tile_attr['hall']['price'],2);
        $hall_wall_brick_cost['procurement'] = round($hall_wall_brick_cost['quantity'] * $floor_tile_attr['hall']['purchase_price_decoration_company'],2);



        foreach ($floor_tile_price as &$one_goods){
            foreach ($floor_tile_attr as $goods_id){
                if ($one_goods['id'] == $goods_id['id'] && $goods_id['name'] == '厨房')
                {
                    $one_goods['quantity'] = $kitchen_wall_brick_cost['quantity'];
                    $one_goods['cost'] = $kitchen_wall_brick_cost['cost'];
                    $one_goods['procurement'] = $kitchen_wall_brick_cost['procurement'];
                    $kitchen_goods[] = $one_goods;
                }

                if ($one_goods['id'] == $goods_id['id'] && $goods_id['name'] == '客厅')
                {
                    $one_goods['quantity'] = $hall_wall_brick_cost['quantity'];
                    $one_goods['cost'] = $hall_wall_brick_cost['cost'];
                    $one_goods['procurement'] = $hall_wall_brick_cost['procurement'];
                    $hall_goods[] = $one_goods;
                }

                if ($one_goods['id'] == $goods_id['id'] && $goods_id['name'] == '卫生间')
                {
                    $one_goods['quantity'] = $toilet_wall_brick_cost['quantity'];
                    $one_goods['cost'] = $toilet_wall_brick_cost['cost'];
                    $one_goods['procurement'] = $toilet_wall_brick_cost['procurement'];
                    $toilet_goods[] = $one_goods;
                }
            }
        }


        //  墙砖价格
        $wall_brick_max['quantity'] = $wall_brick_cost['quantity'];
        $wall_brick_max['cost'] = $wall_brick_cost['cost'];
        $wall_brick_max['procurement'] = $wall_brick_cost['procurement'];
        $material_total['material'][] = $wall_brick_max;

        // 地砖价格
        $material_total['material'][] = BasisDecorationService::profitMargin($kitchen_goods);
        $material_total['material'][] = BasisDecorationService::profitMargin($hall_goods);
        $material_total['material'][] = BasisDecorationService::profitMargin($toilet_goods);

        //材料总费用
        $material_cost_total = $cement_cost['cost'] + $self_leveling_cost['cost'] + $river_sand_cost['cost'] + $wall_brick_cost['cost'] + $toilet_wall_brick_cost['cost'] + $kitchen_wall_brick_cost['cost'] + $hall_wall_brick_cost['cost'];

        // 水泥，河沙，自流平信息
        foreach ($goods as &$one_goods_price) {
            switch ($one_goods_price) {
                case $one_goods_price['title'] == BasisDecorationService::goodsNames()['river_sand']:
                    $one_goods_price['quantity'] = $river_sand_cost['quantity'];
                    $one_goods_price['cost'] = $river_sand_cost['cost'];
                    $one_goods_price['procurement'] = $river_sand_cost['procurement'];
                    $river_sand[] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == BasisDecorationService::goodsNames()['cement']:
                    $one_goods_price['quantity'] = $cement_cost['quantity'];
                    $one_goods_price['cost'] = $cement_cost['cost'];
                    $one_goods_price['procurement'] = $cement_cost['procurement'];
                    $cement[] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == BasisDecorationService::goodsNames()['self_leveling']:
                    $one_goods_price['quantity'] = $self_leveling_cost['quantity'];
                    $one_goods_price['cost'] = $self_leveling_cost['cost'];
                    $one_goods_price['procurement'] = $self_leveling_cost['procurement'];
                    $self_leveling[] = $one_goods_price;
                    break;
            }
        }

        $material_total['material'][] = BasisDecorationService::profitMargin($river_sand);
        $material_total['material'][] = BasisDecorationService::profitMargin($cement);
        $material_total['material'][] = BasisDecorationService::profitMargin($self_leveling);
        $material_total['total_cost'] = round($material_cost_total,2);

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
//        $_select = 'id,univalence,worker_kind';
        $labor = LaborCost::profession($post, self::WORK_CATEGORY['backman']);
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

        $points = Points::findByOne('id,title',"id=7");
        $Apartment = Apartment::find()
            ->asArray()
            ->where(['<=','min_area',$post['area']])
            ->andWhere(['>=','max_area',$post['area']])
            ->andWhere(['project_name'=>'其他杂工天数'])
            ->andWhere(['points_id'=>$points['id']])
            ->one();
        if ($Apartment){
            $_area = $Apartment['project_value'];
        }else{
            $_area = 1;
        }
//        总天数
        $total_day = BasisDecorationService::wallArea($post,$worker_kind_details,$_area);

//        清运建渣费用
        $craft = EngineeringStandardCraft::findByAll($points['id'], $post['city']);
        if ($craft == null){
            $code = 1062;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }


        if ($post['building_scrap'] == 'true') {
            $building_scrap = BasisDecorationService::haveBuildingScrap($post, $craft);
        } else {
            $building_scrap = BasisDecorationService::nothingBuildingScrap($post, $craft);
        }


//        总人工费
        $labor_cost['price'] = $total_day['total_day'] * $labor['univalence'] + $building_scrap['cost'];
        $labor_cost['worker_kind'] = $labor['worker_kind'];

        //材料费
        $goods = Goods::priceDetail(self::WALL_SPACE, self::BACKMAN_MATERIAL);
        if ($goods == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data' => [
                    'labor_cost' => [],
                    'total_material' => [],
                ]
            ]);
        }
//        $goods_price = BasisDecorationService::priceConversion($goods);

        foreach ($goods as $max) {
            switch ($max) {
                case $max['title'] == BasisDecorationService::goodsNames()['cement']:
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
                    $max['procurement'] = $cement_cost['procurement'];
                    $cement[] = $max;
                    break;
                case $max['title'] == BasisDecorationService::goodsNames()['air_brick']:
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
                    $max['procurement'] = $brick_cost['procurement'];
                    $air_brick[] = $max;
                    break;
                case $max['title'] == BasisDecorationService::goodsNames()['river_sand']:
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
                    $max['procurement'] = $river_sand_cost['procurement'];
                    $river_sand[] = $max;
                    break;
            }
        }
        $material['material'][] = BasisDecorationService::profitMargin($cement);
        $material['material'][] = BasisDecorationService::profitMargin($air_brick);
        $material['material'][] = BasisDecorationService::profitMargin($river_sand);


        $goods_value['material'] = [];
        foreach ($material['material'] as $value){
            if ($value['quantity'] != 0){
                $goods_value['material'] [] = $value;
            }
        }
        $goods_value['total_cost'] = 0;
        foreach ($goods_value['material'] as $total_cost){
            $goods_value['total_cost'] += $total_cost['cost'];
        }

        if (empty($goods_value['material'])){
            return Json::encode([
                'code' => 200,
                'msg' => '成功',
                'data' => [
                    'labor_cost' => $labor_cost,
                    'total_material' => $goods_value,
                ]
            ]);
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
        $add_select = 'decoration_add.id,decoration_add.c_id,decoration_add.sku,max(d.quantity) as quantity';
        $add_where = ['and',['decoration_add.city_code'=>$code],['or',['d.style_id'=>$style],['d.series_id'=>$series],['and',['<=','d.min_area',$area],['>=','d.max_area',$area]]]];
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
        $goods = Goods::findBySkuAll($codes);
        if ($goods == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        foreach ($add_materials as $material){
            foreach ($goods as &$one_goods){
                if ($one_goods['sku'] == $material['sku']) {
                    $one_goods['quantity'] = (int)$material['quantity'];
                    $one_goods['cost'] = $material['quantity'] * $one_goods['platform_price'];
                    $one_goods['procurement'] = $material['quantity'] * $one_goods['purchase_price_decoration_company'];
                }
            }
        }
        return Json::encode([
            'code' => 200,
            'msg'  => 'ok',
            'add_list' => $goods,
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
        $goods = [];
        if (is_array($post)){
            foreach ($coefficient as $one_coefficient) {
                $classify = GoodsCategory::find()->select('title')->where(['id' => $one_coefficient['category_id']])->asArray()->one();
                foreach ($post['list'] as &$materials) {
                    if ($materials['price'] != 0) {
                        if ($classify['title'] == $materials['one_title']) {
                            $materials['goods_price'] = $materials['procurement'] / ($one_coefficient['coefficient']*0.01);
                            $goods []                 = $materials;
                        }

                    }

                }

            }


//            foreach ($goods as &$default){
//                    $default['goods_price'] = $default['goods_price'] * 1;
////                if (empty($default['goods_price'])){
////                    $default['goods_price'] = $default['price'] * 1;
////                }
//            }
            $special_offer = 0;
            $total_prices = 0;

            foreach ($goods as $price){
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


    }

    /**
     * 配套设备列表
     * @return string
     */
    public function actionAssortFacility()
    {
        $post = Yii::$app->request->get();

        // 有资料 计算公式
        $goods = Goods::assortList(self::MATERIALS,$post['city']);
        $goods_price  = BasisDecorationService::priceConversion($goods);
        $material[]   = BasisDecorationService::formula($goods_price,$post);



        //无计算公式
        $assort_material = AssortGoods::find()->asArray()->where(['state'=>1])->all();


        foreach ($assort_material as $one_without_assort){

            $without_assort_name[] = $one_without_assort['title'];
            $without_assort_one[$one_without_assort['title']] = $one_without_assort;
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



        $condition_stairs = [];
        //  楼梯信息
        if ($post['stairway_id'] == 1) {
            $stairs = Goods::findByCategory(BasisDecorationService::goodsNames()['stairs']);
            $stairs_price = BasisDecorationService::priceConversion($stairs);
            foreach ($stairs_price as &$one_stairs_price) {

                if ($one_stairs_price['value'] == $post['stairs'] && $one_stairs_price['style_id'] == $post['style']) {
                    $one_stairs_price['quantity'] = 1;
                    $one_stairs_price['cost'] = $one_stairs_price['platform_price'] * $one_stairs_price['quantity'];
                    $one_stairs_price['procurement'] = $one_stairs_price['purchase_price_decoration_company'] * $one_stairs_price['quantity'];
                    $condition_stairs [] = $one_stairs_price;
                }

            }
            $material[][] = BasisDecorationService::profitMargin($condition_stairs);
        }



        $goods_material = [];
        foreach ($material as $one){
            if($one != null){
                $goods_material [] = $one;
            }
        }


        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' =>[
               'goods' => Json::decode(Json::encode($goods_material),true),
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
                ->select('id,image,district_code,street,toponymy,house_type_name,effect_id')//TODO 新增 effect_id
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
        $code     = (int)trim(Yii::$app->request->get('code',''));
        $street   = trim(Yii::$app->request->get('street',''));
        $toponymy = trim(Yii::$app->request->get('toponymy',''));
        $id = (int)trim(Yii::$app->request->get('id',''));


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
           'data'=>[
               'list' => $effect,
               'id' => $id,
           ],
        ]);

    }

    /**
     * 案例数据
     * @return string
     */
    public function actionCaseParticulars()
    {
//        $id = trim(Yii::$app->request->post('id',''));
        $series = (int)trim(Yii::$app->request->get('series',''));
        $style = (int)trim(Yii::$app->request->get('style',''));
        $stairway = trim(Yii::$app->request->get('stairway',''));
        $stair_id = trim(Yii::$app->request->get('stair_id',''));
        $toponymy = trim(Yii::$app->request->get('toponymy',''));
        $particulars = trim(Yii::$app->request->get('particulars',''));
        $area = (int)trim(Yii::$app->request->get('area',''));

        if ($stairway == self::JUDGE_VALUE){
            $where = ['and',['effect_picture.series_id'=>$series],['effect_picture.style_id'=>$style],['effect.stairway'=>$stairway],['effect.toponymy'=>$toponymy],['effect.particulars'=>$particulars],['effect.area'=>$area],['type'=>1]];
            $effect = Effect::effectAndEffectPicture('effect.id,effect_picture.effect_images,effect.window,effect.stairway,effect.stair_id',$where);
            if ($effect == null) {
                $code = 1068;
                return Json::encode([
                   'code' => $code,
                   'msg' => Yii::$app->params['errorCodes'][$code],
                    'data'=> $effect
                ]);
            }
        } elseif ($stairway == self::TYPE_VALUE) {
            $where = ['and',['effect_picture.series_id'=>$series],['effect_picture.style_id'=>$style],['effect.stairway'=>$stairway],['effect.stair_id'=>$stair_id],['effect.toponymy'=>$toponymy],['effect.particulars'=>$particulars],['effect.area'=>$area],['type'=>1]];
            $effect = Effect::effectAndEffectPicture('effect.id,effect_picture.effect_images,effect.window,effect.stairway,effect.stair_id',$where);
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
        //TODO 要求 数量必须是Int 类型
        foreach ($data as &$v){
            $v['goods_quantity'] = (int)$v['goods_quantity'];
        }
//        $backman_data = WorksBackmanData::find()->select('backman_option,backman_value')->where([])->all();
        $worker_data = WorksWorkerData::find()->select([])->where($effect_where)->all();

        if ($data != null) {
            foreach ($data as $one_goods) {
                $sku [] = $one_goods['goods_code'];
            }

            $goods  = Goods::findBySkuAll($sku);
            if ($goods == null) {
                return Json::encode([
                    'code' => 200,
                    'msg' => 'ok',
                    'data' => [
                        'images' => $effect,
                        'goods' => $data,
//                        'backman_data' => $backman_data,
                        'worker_data' => $worker_data,
                    ]
                ]);
            }


            foreach ($data as $case_works_datum) {
                foreach ($goods as &$one_goods) {
                    if ($one_goods['sku'] == $case_works_datum['goods_code']) {
                        $one_goods['goods_first']  = $case_works_datum['goods_first'];
                        $one_goods['goods_second'] = $case_works_datum['goods_second'];
                        $one_goods['goods_three']  = $case_works_datum['goods_three'];
                        $one_goods['quantity']     = $case_works_datum['goods_quantity'];
                        $one_goods['cost']         = $one_goods['quantity'] * $one_goods['platform_price'];
                        $one_goods['procurement']  = round($one_goods['purchase_price_decoration_company'] * $case_works_datum['goods_quantity'],2);
                    }
                }
            }

            //  匹配 通用管理 数据
            $assort_goods = AssortGoods::find()->asArray()->where(['state'=>0])->all();
            $goods_ = [];
            foreach ($assort_goods as $assort){
                foreach ($goods as $woks){
                    if ($assort['title'] == $woks['title']){
                        $goods_[] = $woks;
                    }
                }
            }


            //  大理石 数据
            if ($effect['window'] != 0){
                $stone  = Goods::findByCategory('人造大理石');
                foreach ($stone as &$_goods){
                    $substr = substr($_goods['path'],0,strlen($_goods['path'])-1);
                    $where ="id in (".$substr.")";
                    $goods_category = GoodsCategory::find()->asArray()->select('id,title')->where($where)->all();
                    $_goods['goods_first'] = $goods_category['0']['title'];
                    $_goods['goods_second'] = $goods_category['1']['title'];
                    $_goods['goods_three'] = $goods_category['2']['title'];

                    $_goods['platform_price'] = $_goods['platform_price'] / 100;
                    $_goods['supplier_price'] = $_goods['supplier_price'] / 100;
                    $_goods['purchase_price_decoration_company'] = $_goods['purchase_price_decoration_company'] / 100;
                    $_goods['quantity'] = $effect['window'];
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
                $stairs = Goods::findByCategory(BasisDecorationService::GOODS_NAME['stairs']);
                $stairs_price = BasisDecorationService::priceConversion($stairs);
                $effect_ = EffectPicture::find()->asArray()->where(['effect_id'=>$effect['id']])->one();
                $stairs_details = StairsDetails::find()->asArray()->all();
                foreach ($stairs_details as $detail){
                    if ($effect['stair_id'] == $detail['id']){
                        $ma = $detail['attribute'];
                    }
                }
                $condition_stairs=[];
                foreach ($stairs_price as &$one_stairs_price) {
                    if ($one_stairs_price['value'] == $ma && $one_stairs_price['style_id'] == $effect_['style_id']) {
                        $substr = substr($one_stairs_price['path'],0,strlen($one_stairs_price['path'])-1);
                        $where ="id in (".$substr.")";
                        $goods_category = GoodsCategory::find()->asArray()->select('id,title')->where($where)->all();
                        $one_stairs_price['quantity'] = 1;
                        $one_stairs_price['goods_first'] = $goods_category['0']['title'];
                        $one_stairs_price['goods_second'] = $goods_category['1']['title'];
                        $one_stairs_price['goods_three'] = $goods_category['2']['title'];
                        $one_stairs_price['cost'] = round($one_stairs_price['platform_price'] * $one_stairs_price['quantity'],2);
                        $one_stairs_price['procurement'] = round($one_stairs_price['purchase_price_decoration_company'] * $one_stairs_price['quantity'],2);
                        unset($one_stairs_price['path']);
                        $condition_stairs [] = $one_stairs_price;
                    }
                }
                $material = BasisDecorationService::profitMargin($condition_stairs);
            }else{
                $material = [];
            }
        }

        //  合并 三个数组  $goods_     $material    $griotte
        $_goods = [];
        foreach ($goods_ as $value){
            $_goods [] = $value;
        }
        $_goods [] = $material;
        $_goods [] = $griotte;
        $array_filter = array_filter($_goods);
        $array_merge = array_merge($array_filter);


        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => [
                'images' => $effect,
                'goods' => $array_merge,
//                    'backman_data' => $backman_data,
                'worker_data' => $worker_data,
            ]
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


        // 判断是否有计算公式
        $change_goods = 0;
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
            $material = BasisDecorationService::oneFormula($goods,$get);
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
        var_dump(GoodsCategory::find()->asArray()->all());
    }

}