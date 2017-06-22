<?php

namespace app\controllers;

use app\models\AppliancesAssort;
use app\models\BasisDecoration;
use app\models\CarpentryAdd;
use app\models\CarpentryReconstruction;
use app\models\CircuitryReconstruction;
use app\models\DecorationAdd;
use app\models\DecorationList;
use app\models\DecorationParticulars;
use app\models\Effect;
use app\models\EffectPicture;
use app\models\EngineeringStandardCraft;
use app\models\EngineeringUniversalCriterion;
use app\models\FixationFurniture;
use app\models\Goods;
use app\models\GoodsCategory;
use app\models\IntelligenceAssort;
use app\models\LaborCost;
use app\models\LifeAssort;
use app\models\MaterialPropertyClassify;
use app\models\MoveFurniture;
use app\models\PaintReconstruction;
use app\models\PlasteringReconstruction;
use app\models\Points;
use app\models\PointsDetails;
use app\models\PointsTotal;
use app\models\Series;
use app\models\SoftOutfitAssort;
use app\models\StairsDetails;
use app\models\Style;
use app\models\StylePicture;
use app\models\WaterproofReconstruction;
use app\models\WaterwayReconstruction;
use app\services\BasisDecorationService;
use app\services\ExceptionHandleService;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;

class OwnerController extends Controller
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
                'only' => ['logout', 'about'],
                'rules' => [
                    [
                        'actions' => ['logout', 'about'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post',],
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
     * 系列、风格、楼梯
     */
    public function actionSeriesAndStyle()
    {
        $stairs_details = StairsDetails::find()->asArray()->all();
        $series = Series::findByAll();
        $style = Style::findByAll();
        $style_picture = StylePicture::findById($style);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'stairs_details' =>$stairs_details,
                'series' => $series,
                'style' => $style,
                'style_picture' => $style_picture
            ]
        ]);
    }

    /**
     * 搜索界面
     * @return string
     */
    public function actionSearch()
    {
        $post = \Yii::$app->request->post();
        $string = $post ?? '花好月圆';
        $effect = Effect::districtSearch($string);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'effect' => $effect,
            ]
        ]);
    }

    /**
     * 弱电价格
     * @return string
     */
    public function actionWeakCurrent()
    {
//        $receive = \Yii::$app->request->post();
//        $post = Json::decode($receive);
        $post = [
//            'effect_id' => 1,
            'master_bedroom' => 1,
            'secondary_bedroom' => 1,
            'sitting_room' => 1,
            'dining_room' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 62,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 1,
            'series' => 1,
            'province' => 510000,
            'city' => 510100
        ];
        $arr = [];
        $arr['profit'] = $post['1'] ?? 0.7;
        $arr['worker_kind'] = '水电';

        //人工价格
        $workers = LaborCost::univalence($post,$arr['worker_kind']);
        foreach ($workers as $worker){
            if ($worker['worker_kind_details'] == '弱电'){
                $Weak_labor = $worker;
            }
        }

        //点位查询
        if (!empty($post['effect_id']))
        {
            $weak_points = Points::weakPoints($post['effect_id']);
        }else{
            $weak_points = 0;
            $effect = Effect::find()->where(['id'=>1])->one();
            $points = Points::find()->where(['effect_id'=>$effect['id']])->all();
            foreach ($points as $one){
               if($one['weak_current_points'] !== 0 )
               {
                   $weak_current_place []  = $one['place'];
                   $weak_current_points [] = $one['weak_current_points'];
               }
            }
            $weak_current_all = array_combine($weak_current_place,$weak_current_points);
            $sitting_room = $weak_current_all['客厅'] * $post['sitting_room'];
            $master_bedroom = $weak_current_all['主卧'] * $post['master_bedroom'];
            $secondary_bedroom = $weak_current_all['次卧'] * $post['secondary_bedroom'];
            $weak_points = $sitting_room + $master_bedroom + $secondary_bedroom;
        }

        if(empty($post['effect_id'])){
            //查询弱电所需要材料
            $weak_current = [];
            $electric_wire = '电线';
            $weak_current [] = Goods::priceDetail(3, $electric_wire);
            $pipe = '线管';
            $weak_current [] = Goods::priceDetail(3, $pipe);
            $box = '底盒';
            $weak_current [] = Goods::priceDetail(3, $box);
        }else{
            $decoration_list = DecorationList::findById($post['effect_id']);
            $weak = CircuitryReconstruction::findByAll($decoration_list,'弱电');
            $weak_current = Goods::findQueryAll($weak,$post['city']);
        }

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll('弱电',$post['city']);

        //人工总费用
        $labor_all_cost = BasisDecorationService::laborFormula($weak_points,$Weak_labor);
        //材料总费用
        $material_price = BasisDecorationService::quantity($weak_points,$weak_current,$craft);
        $add_price = DecorationAdd::findByAll('弱电',$post['area'],$post['city']);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'weak_current_labor_price' => $labor_all_cost,
                'weak_current_material_price' => $material_price['total_cost'],
                'weak_current_reticle_quantity' => $material_price['wire_quantity'],
                'weak_current_reticle_cost' => $material_price['wire_cost'],
                'weak_current_spool_quantity' => $material_price['spool_quantity'],
                'weak_current_spool_cost' => $material_price['spool_cost'],
                'weak_current_bottom_case' => $material_price['bottom_case'],
                'weak_current_add_price' => $add_price,
                'weak_current'=>$weak_current
            ]
        ]);
    }

    /**
     * 强电价格
     * @return string
     */
    public function actionStrongCurrent()
    {
        //        $receive = \Yii::$app->request->post();
//        $post = Json::decode($receive);
        $post = [
            'effect_id' => 1,
            'master_bedroom' => 1,
            'secondary_bedroom' => 1,
            'sitting_room' => 1,
            'dining_room' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 62,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 1,
            'series' => 1,
            'province' => 510000,
            'city' => 510100
        ];
        $arr = [];
        $arr['profit'] = $post['1'] ?? 0.7;
        $arr['worker_kind'] = '水电';

        //人工价格
        $workers = LaborCost::univalence($post,$arr['worker_kind']);
        foreach ($workers as $worker){
            if ($worker['worker_kind_details'] == '强电'){
                $strong_labor = $worker;
            }
        }

        //点位查询
        if (!empty($post['effect_id']))
        {
            $strong_points = 0;
            $points = Points::strongPoints($post['effect_id']);
            $points_details = PointsDetails::AllQuantity($points);
        }else{
            $strong_points = 0;
            $effect = Effect::find()->where(['id'=>1])->one();
            $points = Points::strongPointsAll($effect);
            $points_total = PointsTotal::findByAll($points);
            $points_places = [];
            foreach ($points_total as $one){
                if($one['place'] == '客厅'){
                    $sitting_room =  $one['points_total'] *  $post['sitting_room'];
                    $sitting_room = $sitting_room ?? 0;
                    $points_places [] = $sitting_room;
                }elseif ($one['place'] == '主卧'){
                    $master_bedroom = $one['points_total'] * $post['master_bedroom'];
                    $master_bedroom = $master_bedroom ?? 0;
                    $points_places [] = $master_bedroom;
                }elseif ($one['place'] == '次卧'){
                    $secondary_bedroom = $one['points_total'] * $post['secondary_bedroom'];
                    $secondary_bedroom = $secondary_bedroom ?? 0;
                    $points_places [] = $secondary_bedroom;
                }elseif ($one['place'] == '餐厅'){
                    $dining_room = $one['points_total'] * $post['dining_room'];
                    $dining_room = $dining_room ?? 0;
                    $points_places [] = $dining_room;
                }elseif ($one['place'] == '厨房'){
                    $kitchen = $one['points_total'] * $post['kitchen'];
                    $kitchen = $kitchen ?? 0;
                    $points_places [] = $kitchen;
                }elseif ($one['place'] == '卫生间'){
                    $toilet = $one['points_total'] * $post['toilet'];
                    $toilet = $toilet ?? 0;
                    $points_places [] = $toilet;
                }elseif ($one['place'] !== '卫生间'  && $one['place'] !== '客厅' && $one['place'] !== '主卧' && $one['place'] !== '次卧' && $one['place'] !== '餐厅' && $one['place'] !== '厨房' && $one['place'] !== '卫生间' ){
                    $other [] = $one;
                }
            }
            $other_points = 0;
            foreach ($other as $other_one)
            {
                $other_points += $other_one['points_total'];
                $points_places [] = $other_points;
            }
            $points_details = array_sum($points_places);
        }

        //材料查询
        if(empty($post['effect_id'])){
            //查询弱电所需要材料
            $strong_current = [];
            $electric_wire = '电线';
            $strong_current [] = Goods::priceDetail(3, $electric_wire);
            $pipe = '线管';
            $strong_current [] = Goods::priceDetail(3, $pipe);
            $box = '底盒';
            $strong_current [] = Goods::priceDetail(3, $box);
        }else{
            $decoration_list = DecorationList::findById($post['effect_id']);
            $weak = CircuitryReconstruction::findByAll($decoration_list,'强电');
            $strong_current = Goods::findQueryAll($weak,$post['city']);
        }

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll('强电',$post['city']);

        //人工总费用
        $labor_all_cost = BasisDecorationService::laborFormula($points_details,$strong_labor);

        //材料总费用
        $material_price = BasisDecorationService::quantity($points_details,$strong_current,$craft);

        $add_price = DecorationAdd::findByAll('强电',$post['area'],$post['city']);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'strong_current_labor_price' => $labor_all_cost,
                'strong_current_material_price' => $material_price['total_cost'],
                'strong_current_wire_quantity' => $material_price['wire_quantity'],
                'strong_current_wire_cost' => $material_price['wire_cost'],
                'strong_current_spool_quantity' => $material_price['spool_quantity'],
                'strong_current_spool_cost' => $material_price['spool_cost'],
                'strong_current_bottom_case' => $material_price['bottom_case'],
                'strong_current_add_price' => $add_price,
                'strong_current'=>$strong_current
            ]
        ]);
    }

    /**
     * 水路
     * @return string
     */
    public function actionWaterway()
    {
//        $receive = \Yii::$app->request->post();
//        $post = Json::decode($receive);
            $post = [
//                'effect_id' => 1,
                'master_bedroom' => 1,
                'secondary_bedroom' => 1,
                'sitting_room' => 1,
                'dining_room' => 1,
                'window' => 2,
                'high' => 2.8,
                'area' => 62,
                'toilet' => 1,
                'kitchen' => 1,
                'style' => 1,
                'series' => 1,
                'province' => 510000,
                'city' => 510100
            ];
            $arr = [];
            $arr['profit'] = $post['1'] ?? 0.7;
            $arr['worker_kind'] = '水电';

            //人工价格
            $workers = LaborCost::univalence($post,$arr['worker_kind']);
            foreach ($workers as $worker){
                if ($worker['worker_kind_details'] == '防水'){
                    $waterway_labor = $worker;
                }
            }

            //点位查询
            if (!empty($post['effect_id']))
            {
                $waterway_points = Points::waterwayPoints($post['effect_id']);
            }else{
                $waterway_points = 0;
                $effect = Effect::find()->where(['id'=>1])->one();
                $points = Points::find()->where(['effect_id'=>$effect['id']])->all();
                $other = 0;
                foreach ($points as $one){
                    if($one['waterway_points'] !== 0 )
                    {
                        $waterway_current_place []  = $one['place'];
                        $waterway_current_points [] = $one['waterway_points'];
                    }

                    if($one['place'] !== '厨房' && $one['place'] !== '卫生间'){
                        $other += $one['waterway_points'];
                    }
                }
                $waterway_current_all = array_combine($waterway_current_place,$waterway_current_points);
                $kitchen = $waterway_current_all['厨房'] * $post['kitchen'];
                $toilet = $waterway_current_all['卫生间'] * $post['toilet'];
                $waterway_points = $kitchen + $toilet + $other;
            }

            if(empty($post['effect_id'])){
                //查询弱电所需要材料
                $waterway_current = [];
                $electric_wire = 'PPR';
                $waterway_current [] = Goods::priceDetail(3, $electric_wire);
                $pipe = 'PVC';
                $waterway_current [] = Goods::priceDetail(3, $pipe);
            }else{
                $decoration_list = DecorationList::findById($post['effect_id']);
                $weak = WaterwayReconstruction::findByAll($decoration_list);
                $waterway_current = Goods::findQueryAll($weak,$post['city']);
            }
            //当地工艺
            $craft = EngineeringStandardCraft::findByAll('水路',$post['city']);

            //人工总费用
            $labor_all_cost = BasisDecorationService::laborFormula($waterway_points,$waterway_labor);
            //材料总费用
            $material_price = BasisDecorationService::waterwayGoods($waterway_points,$waterway_current,$craft);
            //添加材料费用
            $add_price = DecorationAdd::findByAll('水路',$post['area'],$post['city']);

            return Json::encode([
                'code' => 200,
                'msg' => '成功',
                'data' => [
                    'waterway_labor_price' => $labor_all_cost,
                    'waterway_material_price' => $material_price['total_cost'],
                    'waterway_ppr_quantity' => $material_price['ppr_quantity'],
                    'waterway_ppr_cost' => $material_price['ppr_cost'],
                    'waterway_pvc_quantity' => $material_price['pvc_quantity'],
                    'waterway_pvc_cost' => $material_price['pvc_cost'],
                    'waterway_add_price' => $add_price,
                    'waterway_current'=>$waterway_current
                ]
            ]);
    }

    /**
     * 防水
     * @return string
     */
    public function actionWaterproof()
    {
//        $receive = \Yii::$app->request->post();
//        $post = Json::decode($receive);
        $post = [
//            'effect_id' => 1,
            'master_bedroom' => 1,
            'secondary_bedroom' => 1,
            'sitting_room' => 1,
            'dining_room' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 62,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 1,
            'series' => 1,
            'province' => 510000,
            'city' => 510100
        ];
        $arr = [];
        $arr['profit'] = $post['1'] ?? 0.7;
        $arr['worker_kind'] = '水电';

        //人工价格
        $workers = LaborCost::univalence($post,$arr['worker_kind']);
        foreach ($workers as $worker){
            if ($worker['worker_kind_details'] == '防水'){
                $waterproof_labor = $worker;
            }
        }
        //防水所需材料

        if(empty($post['effect_id'])){
            //查询弱电所需要材料
            $waterproof = [];
            $electric_wire = '防水涂剂';
            $waterproof [] = Goods::priceDetail(3, $electric_wire);
        }else{
            $decoration_list = DecorationList::findById($post['effect_id']);
            $weak = WaterproofReconstruction::findByAll($decoration_list);
            $waterproof [] = Goods::findQueryAll($weak,$post['city']);
        }

        //防水所需面积
        if(!empty($post['effect_id'])){
            $effect = DecorationList::findById($post['effect_id']);
            $area = DecorationParticulars::findByOne($effect);
            //地面面积
            $ground_total_area = BasisDecorationService::groundArea($area);
            //墙面周长
            $wall_space_total_perimeter = BasisDecorationService::wallSpace($area);
            $total_area_float = $ground_total_area + $wall_space_total_perimeter;
            //总面积
            $total_area = intval($total_area_float);
        }else{
            //厨房
            $kitchen = EngineeringUniversalCriterion::findByAll('厨房');
            $kitchen_area = BasisDecorationService::waterproofArea($kitchen,$post['area'],$post['kitchen']);
            //卫生间
            $toilet = EngineeringUniversalCriterion::findByAll('卫生间');
            $toilet_area = BasisDecorationService::waterproofArea($toilet,$post['area'],$post['kitchen']);
            //总面积
            $total_area = intval($kitchen_area + $toilet_area);
        }

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll('防水',$post['city']);

        //人工总费用（防水总面积÷【每天做工面积】）×【工人每天费用】
        $labor_all_cost = ceil($total_area / $waterproof_labor['day_area'] * $waterproof_labor['univalence']);
        //材料总费用
        $material_price = BasisDecorationService::waterproofGoods($total_area,$waterproof,$craft);

        //添加材料费用
        $add_price = DecorationAdd::findByAll('防水',$post['area'],$post['city']);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'waterproof_labor_price' => $labor_all_cost,
                'waterproof_material_price' => $material_price['cost'],
                'waterproof_material_quantity' => $material_price['quantity'],
                'waterproof_add_price' => $add_price,
                'waterproof'=>$waterproof
            ]
        ]);
    }

    /**
     * 木作
     */
    public function actionCarpentry()
    {
//        $receive = \Yii::$app->request->post();
//        $post = Json::decode($receive);
        $post = [
//            'effect_id' => 1,
            'room' => 1,
            'hall' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 40,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 1,
            'series' => 1,
            'province' => 510000,
            'city' => 510100
        ];
        $arr = [];
        $arr['profit'] = $post['1'] ?? 0.7;
        $arr['worker_kind'] = '木工';
        //人工一天价格
        $labor_costs = LaborCost::univalence($post['city'], $arr['worker_kind']);
        foreach ($labor_costs as $labor_cost){}
        $series_all = Series::find()->all();
        $style_all =Style::find()->all();
        $carpentry_add = CarpentryAdd::findByStipulate($post['series'],$post['style']);
        // 造型长度
        $modelling_length = BasisDecorationService::carpentryModellingLength($carpentry_add,$series_all,$post['series']);
        //造型天数
        $modelling_day = BasisDecorationService::carpentryModellingDay($modelling_length,$labor_cost['day_sculpt_length'],$series_all,$style_all);
        //平顶天数
        $flat_day = BasisDecorationService::flatDay($carpentry_add,$labor_cost['day_area'],$series_all,$style_all);
        //人工费
        $labour_charges = BasisDecorationService::carpentryLabor($modelling_day,$flat_day,1,$labor_cost['univalence']);

        //木工材料费
        if(!empty($post['effect_id']))
        {
            $decoration_list = DecorationList::findById($post['effect_id']);
            $carpentry_reconstruction = CarpentryReconstruction::find()->where(['decoration_list_id' => $decoration_list])->all();
            $goods_price = Goods::findQueryAll($carpentry_reconstruction);
        }else{
            $plasterboard = '石膏板';
            $goods_price = [];
            $goods_price [] = Goods::priceDetail(3,$plasterboard);
            $keel = '龙骨';
            $goods_price [] = Goods::priceDetail(3,$keel);
            $screw = '丝杆';
            $goods_price [] = Goods::priceDetail(3,$screw);
        }

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll('木作',$post['city']);

        //石膏板费用
        $plasterboard_cost = BasisDecorationService::carpentryPlasterboardCost($modelling_length,$carpentry_add['flat_area'],$goods_price,$craft);
        //龙骨费用
        $keel_cost = BasisDecorationService::carpentryKeelCost($modelling_length,$carpentry_add['flat_area'],$goods_price,$craft);
        //丝杆费用
        $pole_cost = BasisDecorationService::carpentryPoleCost($modelling_length,$carpentry_add['flat_area'],$goods_price,$craft);
        //材料费用
        $material_cost = ($keel_cost['cost'] + $plasterboard_cost['cost'] + $pole_cost['cost']);
//      添加费用
        $carpentry_add = DecorationAdd::CarpentryAddAll('木作',$post['series'],$post['style']);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'carpentry_labor_price' => $labour_charges,
                'carpentry_material_price' => $material_cost,
                'plasterboard_cost' => $plasterboard_cost,
                'keel_cost' => $keel_cost,
                'pole_cost' => $pole_cost,
                'carpentry_add_price' => $carpentry_add,
                'goods_price'=>$goods_price
            ]
        ]);
    }

    /**
     * 乳胶漆
     */
    public function actionCoating()
    {
//        $receive = \Yii::$app->request->post();
//        $post = Json::decode($receive);
        $post = [
//            'effect_id' => 1,
            'master_bedroom' => 1,
            'secondary_bedroom' => 1,
            'sitting_room' => 1,
            'dining_room' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 62,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 1,
            'series' => 1,
            'province' => 510000,
            'city' => 510100
        ];
        $arr = [];
        $arr['profit'] = $post['1'] ?? 0.7;
        $arr['worker_kind'] = '油漆工';
        //工人一天单价
        $labor_costs = LaborCost::univalence($post['city'], $arr['worker_kind']);
        $primer = 0;
        $finishing_coat = 0;
        $concave_line = 0;
        $putty = 0;
        foreach ($labor_costs as $labor_cost)
        {
            if ($labor_cost['worker_kind_details'] == '乳胶漆底漆'){
                $primer = $labor_cost['day_area'];
            }
            if ($labor_cost['worker_kind_details'] == '乳胶漆面漆'){
                $finishing_coat = $labor_cost['day_area'];
            }
            if ($labor_cost['worker_kind_details'] == '阴角线'){
                $concave_line = $labor_cost['day_area'];
            }
            if ($labor_cost['worker_kind_details'] == '腻子'){
                $putty = $labor_cost['day_area'];
            }
        }

        if (!empty($post['effect_id'])){
           $decoration_list = DecorationList::findById($post['effect_id']);
           $area = DecorationParticulars::findByOne($decoration_list);
           $tall = $tall ?? 2.8;
        }else{
            $project = '油漆';
            $areas  = EngineeringUniversalCriterion::findByAll($project);
            $area['masterBedroom_area'] = 0;
            $area['sittingRoom_diningRoom_area'] = 0;
            $tall = 0;
            foreach ($areas as $one){
                if ($one['project_particulars'] == '卧室面积'){
                    $area['masterBedroom_area'] = $one['project_value'];
                    $tall = $one['particular'];
                }elseif ($one['project_particulars'] == '客厅面积'){
                    $area['sittingRoom_diningRoom_area'] = $one['project_value'];
                    $tall = $one['particular'];
                }
            }
        }
        //卧室底漆面积
        $bedroom_primer_area = BasisDecorationService::paintedArea($area['masterBedroom_area'],$post['area'],$tall,$post['master_bedroom']);
        //客餐厅底漆面积
        $drawing_room_primer_area = BasisDecorationService::paintedArea($area['sittingRoom_diningRoom_area'],$post['area'],$tall,$post['sitting_room'],3);
//        乳胶漆底漆面积：卧室底漆面积+客厅底漆面积+餐厅底漆面积+其它面积1
        $primer_area = $bedroom_primer_area + $drawing_room_primer_area;
//        乳胶漆底漆天数：乳胶漆底漆面积÷【每天做乳胶漆底漆面积】
        $primer_day = ceil($primer_area / $primer);

        //卧室面漆面积
        $bedroom_finishing_coat_area = BasisDecorationService::paintedArea($area['masterBedroom_area'],$post['area'],$tall,$post['master_bedroom']);
        //客餐厅面漆面积
        $drawing_room_finishing_coat_area = BasisDecorationService::paintedArea($area['sittingRoom_diningRoom_area'],$post['area'],$tall,$post['sitting_room'],3);
        //乳胶漆面漆面积
        $finishing_coat_area = $bedroom_finishing_coat_area + $drawing_room_finishing_coat_area;
//        乳胶漆面漆天数：乳胶漆面漆面积÷【每天做乳胶漆面漆面积】
        $finishing_coat_day = ceil($finishing_coat_area / $finishing_coat);

//        卧室周长
        $bedroom_primer_perimeter = BasisDecorationService::paintedPerimeter($area['masterBedroom_area'],$post['area'],$post['master_bedroom']);
//        客厅周长
        $drawing_room_perimeter = BasisDecorationService::paintedPerimeter($area['sittingRoom_diningRoom_area'],$post['area'],$post['sitting_room']);
//        阴角线长度
        $concave_line_length = $bedroom_primer_perimeter + $drawing_room_perimeter;
//        阴角线天数：阴角线长度÷【每天做阴角线长度】
        $concave_line_day = ceil($concave_line_length / $concave_line);

//        腻子卧室墙面积
        $putty_bedroom_area = BasisDecorationService::paintedArea($area['masterBedroom_area'],$post['area'],$tall,$post['master_bedroom']);
//        腻子客餐厅面积
        $putty_drawing_room_area = BasisDecorationService::paintedArea($area['sittingRoom_diningRoom_area'],$post['area'],$tall,$post['sitting_room'],3);
//        腻子面积 卧室腻子面积+客厅腻子面积
        $putty_area = $putty_bedroom_area + $putty_drawing_room_area;
//        腻子天数 腻子面积÷【每天做腻子面积】
        $putty_day = ceil($putty_area / $putty);

        //材料
        if (!empty($post['effect_id'])){
            $decoration_list = DecorationList::findById($post['effect_id']);
            $paint_reconstruction = PaintReconstruction::find()->where(['decoration_list_id' => $decoration_list])->all();
            $goods_price = Goods::findQueryAll($paint_reconstruction);
        }else{
            $putty = '腻子';
            $goods_price = [];
            $goods_price [] = Goods::priceDetail(3,$putty);
            $emulsion_varnish_primer = '乳胶漆底漆';
            $goods_price [] = Goods::priceDetail(3,$emulsion_varnish_primer);
            $emulsion_varnish_finishing_coat = '乳胶漆面漆';
            $goods_price [] = Goods::priceDetail(3,$emulsion_varnish_finishing_coat);
            $concave_line = '阴角线';
            $goods_price [] = Goods::priceDetail(3,$concave_line);
            $gypsum_powder = '石膏粉';
            $goods_price [] = Goods::priceDetail(3,$gypsum_powder);
        }

        //当地工艺
        $crafts = EngineeringStandardCraft::findByAll('乳胶漆',$post['city']);

        foreach ($goods_price as $goods){
            if ($goods['title'] == '腻子'){
                $goods_putty = $goods;
            }
            if ($goods['title'] == '乳胶漆底漆'){
                $goods_primer = $goods;
            }
            if ($goods['title'] == '乳胶漆面漆'){
                $goods_finishing_coat = $goods;
            }
            if ($goods['title'] == '阴角线'){
                $goods_concave_line = $goods;
            }
            if ($goods['title'] == '石膏粉'){
                $goods_gypsum_powder = $goods;
            }
        }
        foreach ($crafts as $craft){
            if ($craft['project_details'] == '腻子'){
                $putty_craft  = $craft;
            }
            if ($craft['project_details'] == '乳胶漆底漆'){
                $primer_craft = $craft;
            }
            if ($craft['project_details'] == '乳胶漆面漆'){
                $finishing_coat_craft = $craft;
            }
            if ($craft['project_details'] == '阴角线'){
                $concave_line_craft = $craft;
            }
            if ($craft['project_details'] == '石膏粉'){
                $gypsum_powder_craft = $craft;
            }
        }
//        腻子费用
        $putty_cost = BasisDecorationService::paintedCost($goods_putty,$putty_craft,$putty_area);
//        底漆费用
        $primer_cost = BasisDecorationService::paintedCost($goods_primer,$primer_craft,$primer_area);
//        乳胶漆面漆费用
        $finishing_coat_cost= BasisDecorationService::paintedCost($goods_finishing_coat,$finishing_coat_craft,$finishing_coat_area);
//        阴角线费用
        $concave_line_cost = BasisDecorationService::paintedCost($goods_concave_line,$concave_line_craft,$concave_line_length);
//        石膏粉费用   石膏粉费用：个数×商品价格
//        个数：（【3元】×乳胶漆面漆面积÷商品价格）
        $gypsum_powder_cost['quantity'] = ceil($gypsum_powder_craft['material'] * $finishing_coat_area / $goods_gypsum_powder['platform_price']);
        $gypsum_powder_cost['cost'] = $gypsum_powder_cost['quantity'] * $goods_gypsum_powder['platform_price'];

        //总费用
        $total_cost = $putty_cost['cost'] + $primer_cost['cost'] + $finishing_coat_cost['cost'] + $concave_line_cost['cost'] + $gypsum_powder_cost['cost'];

        //总天数   乳胶漆天数+阴角线天数+腻子天数
        $total_day = $primer_day + $finishing_coat_day + $putty_day + $concave_line_day;
        //总人工费   人工费：（总天数）×【工人每天费用】
        $coating_labor_price = $total_day * $labor_cost['univalence'];

        //添加材料费用
        $carpentry_add = DecorationAdd::CarpentryAddAll('乳胶漆',$post['series'],$post['style']);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'coating_labor_price' => $coating_labor_price,
                'coating_material_price' => $total_cost,
                'putty_cost' => $putty_cost,
                'primer_cost' => $primer_cost,
                'finishing_coat_cost' => $finishing_coat_cost,
                'concave_line_cost' => $concave_line_cost,
                'gypsum_powder_cost' => $gypsum_powder_cost,
                'carpentry_add_price' => $carpentry_add,
                'goods_price' => $goods_price
            ]
        ]);
    }

    /**
     * 泥作
     */
    public function actionMudMake()
    {
//        $receive = \Yii::$app->request->post();
//        $post = Json::decode($receive);
        $post = [
//            'effect_id' => 1,
            'master_bedroom' => 1,
            'secondary_bedroom' => 1,
            'sitting_room' => 1,
            'dining_room' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 62,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 1,
            'series' => 1,
            'province' => 510000,
            'city' => 510100,
            'waterproof_total_area'=>50
        ];
        $arr = [];
        $arr['profit'] = $post['1'] ?? 0.7;
        $arr['worker_kind'] = '泥工';
        //工人一天单价
        $labor_costs = LaborCost::univalence($post['city'], $arr['worker_kind']);
        $labor_day_cost = 0;
        foreach ($labor_costs as $labor_cost){
            if ($labor_cost['worker_kind_details'] == '保护层'){
                $covering_layer_day_area = $labor_cost['day_area'];
                $labor_day_cost = $labor_cost['univalence'];
            }
            if ($labor_cost['worker_kind_details'] == '贴砖'){
                $tiling_day_area = $labor_cost['day_area'];
            }
        }

        //泥作面积
        if (!empty($post['effect_id'])){
            $decoration_list = DecorationList::findById($post['effect_id']);
            $decoration_particulars = DecorationParticulars::findByOne($decoration_list);
            //卫生间面积
            $toilet_area = $decoration_particulars['hostToilet_area'];
            //厨房面积
            $kitchen_area = $decoration_particulars['kitchen_area'];
            //客餐厅面积
            $drawing_room_area = $decoration_particulars['sittingRoom_diningRoom_area'];
        }else{
            //厨房面积
            $kitchen_particulars = EngineeringUniversalCriterion::mudMakeArea('厨房','厨房面积');
            $kitchen_area = $post['area'] * $kitchen_particulars['project_value'];
            //卫生间面积
            $toilet_particulars = EngineeringUniversalCriterion::mudMakeArea('卫生间','卫生间面积');
            $toilet_area = $post['area'] * $toilet_particulars['project_value'];
            //客餐厅面积
            $drawing_room_particulars = EngineeringUniversalCriterion::mudMakeArea('客厅','客厅面积');
            $drawing_room_area = $post['area'] * $drawing_room_particulars['project_value'];
        }

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll('泥工',$post['city']);
        foreach ($craft as $local_craft){
            if ($local_craft['project_details'] == '贴砖')
            {
                $wall_height = $local_craft['material'];
            }
            if ($local_craft['project_details'] == '水泥')
            {
                $cement_craft = $local_craft['material'];
            }
            if ($local_craft['project_details'] == '自流平')
            {
                $self_leveling_craft = $local_craft['material'];
            }
            if ($local_craft['project_details'] == '河沙')
            {
                $river_sand_craft = $local_craft['material'];
            }
        }
//        var_dump($self_leveling_craft);exit;
//        保护层面积
        $covering_layer_area = $post['waterproof_total_area'];
//        保护层天数：保护层面积÷【每天做保护层面积】
        $covering_layer_day = $covering_layer_area / $covering_layer_day_area;

//        卫生间墙面积
        $toilet_wall_area = BasisDecorationService::mudMakeArea($toilet_area,$wall_height,$post['toilet']);
//        厨房墙面积
        $kitchen_wall_area = BasisDecorationService::mudMakeArea($kitchen_area,$wall_height,$post['kitchen'],3);
//        墙砖面积
        $wall_area = $toilet_wall_area + $kitchen_wall_area;
//        墙砖天数
        $wall_day = $wall_area / $tiling_day_area;
//        地砖面积
        $floor_tile_area = $drawing_room_area + $toilet_area + $kitchen_area;
//        地砖天数
        $floor_tile_day = $floor_tile_area / $tiling_day_area;
//        贴砖天数
        $tiling_day = $floor_tile_day + $wall_day;
//        总天数：保护层天数+贴砖天数
        $total_day = ceil($tiling_day + $covering_layer_day);
        //总的人工费
        $total_labor_cost= $total_day * $labor_day_cost;

        //材料费
        if (!empty($post['effect_id'])){
            $decoration_list = DecorationList::findById($post['effect_id']);
            $plastering_reconstruction = PlasteringReconstruction::findById($decoration_list);
            $goods_price = Goods::findQueryAll($plastering_reconstruction,$post['city']);
            $wall_brick_value = 0;
            $wall_brick_price = 0;
            $floor_tile_value = 0;
            $floor_tile_price = 0;
            $drawing_room_price = 0;
            $drawing_room_value = 0;
            foreach ($goods_price as $goods_brick_area)
            {
                if ($goods_brick_area['title'] == '墙砖')
                {
                    $wall_brick_value = $goods_brick_area['value'];
                    $wall_brick_price = $goods_brick_area['platform_price'];
                }
                if ($goods_brick_area['title'] == '地砖')
                {
                    $floor_tile_value = $goods_brick_area['value'];
                    $floor_tile_price = $goods_brick_area['platform_price'];
                }
                if ($goods_brick_area['title'] == '客厅地砖')
                {
                    $drawing_room_price =  $goods_brick_area['platform_price'];
                    $drawing_room_value = $goods_brick_area['value'];
                }
            }
        }else{
            $cement = '水泥';
            $goods_price = [];
            $goods_price [] = Goods::priceDetail(3,$cement);
            $self_leveling = '自流平';
            $goods_price [] = Goods::priceDetail(3,$self_leveling);
            $wall_brick = '墙砖';
            $goods_price [] = Goods::priceDetail(3,$wall_brick);
            $floor_tile = '地砖';
            $goods_price [] = Goods::priceDetail(3,$floor_tile);
            $river_sand = '河沙';
            $goods_price [] = Goods::priceDetail(3,$river_sand);
            $river_sand = '客厅地砖';
            $goods_price [] = Goods::priceDetail(3,$river_sand);
            $wall_brick_value = 0;
            $wall_brick_price = 0;
            $floor_tile_value = 0;
            $floor_tile_price = 0;
            $drawing_room_price = 0;
            $drawing_room_value = 0;
            foreach ($goods_price as $goods_brick_area)
            {
                if ($goods_brick_area['title'] == '墙砖')
                {
                    $wall_brick_value = $goods_brick_area['value'];
                    $wall_brick_price = $goods_brick_area['platform_price'];
                }
                if ($goods_brick_area['title'] == '地砖')
                {
                    $floor_tile_value = $goods_brick_area['value'];
                    $floor_tile_price = $goods_brick_area['platform_price'];
                }
                if ($goods_brick_area['title'] == '客厅地砖')
                {
                    $drawing_room_price =  $goods_brick_area['platform_price'];
                    $drawing_room_value = $goods_brick_area['value'];
                }
            }
        }
//        水泥面积=保护层面积+ 地砖面积+墙砖面积
        $cement_area = $covering_layer_area + $floor_tile_area + $floor_tile_area;
//        水泥费用
        $cement_cost = BasisDecorationService::mudMakeCost($cement_area,$goods_price,$cement_craft,'水泥');
//        自流平面积
        $self_leveling_area = $drawing_room_area;
//        自流平费用
        $self_leveling_cost = BasisDecorationService::mudMakeCost($self_leveling_area,$goods_price,$self_leveling_craft,'自流平');
//        个数：（墙砖面积÷抓取墙砖面积）
        $wall_brick_area = ($wall_brick_value / 1000) * ($wall_brick_value / 1000);
        $wall_brick_cost ['quantity'] = ceil($wall_area / $wall_brick_area);
//        墙砖费用
        $wall_brick_cost['cost'] = $wall_brick_cost ['quantity'] * $wall_brick_price;
//        河沙费用
        $river_sand_cement_area = $covering_layer_area + $floor_tile_area + $wall_area;
        $river_sand_cost = BasisDecorationService::mudMakeCost($river_sand_cement_area,$goods_price,$river_sand_craft,'河沙');

//        厨房/卫生间地砖费用
        $kitchen_and_toilet_floor_tile_area = ($floor_tile_value / 1000) *  ($floor_tile_value / 1000);
        $kitchen_and_toilet['quantity'] = ceil(($kitchen_area + $toilet_area) / $kitchen_and_toilet_floor_tile_area);
        $kitchen_and_toilet['cost'] = $kitchen_and_toilet['quantity'] * $floor_tile_price;
//        客厅地砖费用
        $drawing_room_floor_tile_area = ($drawing_room_value / 1000) * ($drawing_room_value / 1000);
        $drawing_room_cost['quantity'] =  ceil($drawing_room_area * $drawing_room_floor_tile_area);
        $drawing_room_cost['cost'] = $drawing_room_cost['quantity'] * $drawing_room_price;
        //        地砖费用
        $floor_tile_cost =  $kitchen_and_toilet['cost'] + $drawing_room_cost['cost'];
        //材料总费用
        $material_cost_total = $floor_tile_cost['cost'] + $river_sand_cost['cost'] + $cement_cost['cost'] + $self_leveling_cost['cost'] + $wall_brick_cost['cost'];

        //添加材料费用
        $carpentry_add = DecorationAdd::CarpentryAddAll('泥工',$post['series'],$post['style']);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'mud_make_labor_price' => $total_labor_cost,
                'mud_make_material_price' => $material_cost_total,
                'cement_cost' => $cement_cost,
                'self_leveling_cost' => $self_leveling_cost,
                'wall_brick_cost' => $wall_brick_cost,
                'river_sand_cost' => $river_sand_cost,
                'kitchen_and_toilet_floor_tile' => $kitchen_and_toilet,
                'drawing_room_cost' => $drawing_room_cost,
                'carpentry_add' => $carpentry_add,
                'goods_price'=> $goods_price
            ]
        ]);
    }


    /**
     * 软装配套
     * @return string
     */
    public function actionSoftOutfitAssort()
    {
//        $receive = \Yii::$app->request->post();
//        $post = Json::decode($receive);
        $post = [
//            'effect_id' => 1,
            'master_bedroom' => 2,
            'secondary_bedroom' => 1,
            'sitting_room' => 1,
            'dining_room' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 62,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 1,
            'series' => 1,
            'province' => 510000,
            'city' => 510100,
            'waterproof_total_area'=>50
        ];


        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' =>[
                'appliances_goods' =>$goods_price,
                'quantity' => 1
            ]
        ]);
    }

    /**
     * 固定家具
     * @return string
     */
    public function actionFixationFurniture()
    {
//        $receive = \Yii::$app->request->post();
//        $post = Json::decode($receive);
        $post = [
//            'effect_id' => 1,
            'master_bedroom' => 2,
            'secondary_bedroom' => 1,
            'sitting_room' => 1,
            'dining_room' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 62,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 1,
            'series' => 1,
            'province' => 510000,
            'city' => 510100,
            'waterproof_total_area'=>50,
            'stairway_id' => 1
        ];
        $chest_quantity = $post['master_bedroom'];
        $chest = '衣柜';
        $chest_goods = Goods::priceDetail(3,$chest,$post['city'],$post['series'],$post['style']);
        $chest_goods ['show_price'] = $chest_goods['platform_price'] * $chest_quantity;
        $chest_goods ['show_quantity'] = $chest_quantity;

        $wine_cabinet = '酒柜';
        $wine_cabinet_goods = Goods::priceDetail(3,$wine_cabinet,$post['city'],$post['series'],$post['style']);
        $wine_cabinet_goods ['show_price'] = $wine_cabinet_goods['platform_price'] * 1;
        $wine_cabinet_goods ['show_quantity'] = 1;

        $cabinet = '橱柜';
        $cabinet_goods = Goods::priceDetail(3,$cabinet,$post['city'],$post['series'],$post['style']);
        $cabinet_goods ['show_price'] = $cabinet_goods['platform_price'] * 1;
        $cabinet_goods ['show_quantity'] = 1;

        $wall_cupboard = '吊柜';
        $wall_cupboard_goods = Goods::priceDetail(3,$wall_cupboard,$post['city'],$post['series'],$post['style']);
        $wall_cupboard_goods ['show_price'] = $wall_cupboard_goods['platform_price'] * 1;
        $wall_cupboard_goods ['show_quantity'] = 1;

        //楼梯
        if ($post['stairway_id'])
        {
            $stairway = '楼梯';
            $stairway_goods = Goods::priceDetail(3,$stairway,$post['city'],$post['series'],$post['style']);
            $stairway_goods ['show_price'] = $stairway_goods['platform_price'] * 1;
            $stairway_goods ['show_quantity'] = 1;
        }

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' =>[
                'chest' =>$chest_goods,
                'wine_cabinet' =>$wine_cabinet_goods,
                'cabinet'=>$cabinet_goods,
                'wall_cupboard'=>$wall_cupboard_goods,
                'stairway'=> $stairway_goods
            ]
        ]);
    }

    /**
     * 移动家具
     * @return string
     */
    public function actionMoveFurniture()
    {
//        $receive = \Yii::$app->request->post();
//        $post = Json::decode($receive);
        $post = [
//            'effect_id' => 1,
            'master_bedroom' => 2,
            'secondary_bedroom' => 1,
            'sitting_room' => 1,
            'dining_room' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 62,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 1,
            'series' => 1,
            'province' => 510000,
            'city' => 510100,
            'waterproof_total_area'=>50
        ];

//        客厅沙发,茶几,电视柜,餐桌椅,主卧床,主卧床头柜,次卧床,次卧床头柜,床垫
        $sitting_room_quantity = $post['sitting_room'];
        $sitting_room_sofa = '客厅沙发';
        $sitting_room_sofa_goods = Goods::priceDetail(3,$sitting_room_sofa,$post['city'],$post['series'],$post['style']);
        $sitting_room_sofa_goods ['show_price'] = $sitting_room_sofa_goods['platform_price'] * $sitting_room_quantity;
        $sitting_room_sofa_goods ['show_quantity'] = $sitting_room_quantity;

        $tea_table = '茶几';
        $tea_table_goods = Goods::priceDetail(3,$tea_table,$post['city'],$post['series'],$post['style']);
        $tea_table_goods ['show_price'] = $tea_table_goods['platform_price'] * 1;
        $tea_table_goods ['show_quantity'] = 1;

        $tv_bench = '电视柜';
        $tv_bench_goods = Goods::priceDetail(3,$tv_bench,$post['city'],$post['series'],$post['style']);
        $tv_bench_goods ['show_price'] = $tv_bench_goods['platform_price'] * 1;
        $tv_bench_goods ['show_quantity'] = 1;

        $dining_table= '餐桌椅';
        $dining_table_goods = Goods::priceDetail(3,$dining_table,$post['city'],$post['series'],$post['style']);
        $dining_table_goods ['show_price'] = $dining_table_goods['platform_price'] * 1;
        $dining_table_goods ['show_quantity'] = 1;

        $bedroom_quantity = $post['master_bedroom'];
        $bed = '床';
        $bed_goods = Goods::priceDetail(3,$bed,$post['city'],$post['series'],$post['style']);
        $bed_goods ['show_price'] = $bed_goods['platform_price'] * $bedroom_quantity;
        $bed_goods ['show_quantity'] = $bedroom_quantity;

        $night_table_quantity = $post['master_bedroom'] * 2;
        $night_table = '床头柜';
        $night_table_goods = Goods::priceDetail(3,$night_table,$post['city'],$post['series'],$post['style']);
        $night_table_goods ['show_price'] = $night_table_goods['platform_price'] * $night_table_quantity;
        $night_table_goods ['show_quantity'] = $night_table_quantity;

        $mattress = '床垫';
        $mattress_goods = Goods::priceDetail(3,$mattress,$post['city'],$post['series'],$post['style']);
        $mattress_goods ['show_price'] = $mattress_goods['platform_price'] * $bedroom_quantity;
        $mattress_goods ['show_quantity'] = $bedroom_quantity;

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' =>[
                'sitting_room_sofa' =>$sitting_room_sofa_goods,
                'tea_table' =>$tea_table_goods,
                'tv_bench' =>$tv_bench_goods,
                'dining_table' =>$dining_table_goods,
                'bed' =>$bed_goods,
                'night_table' =>$night_table_goods,
                'mattress' => $mattress_goods
            ]
        ]);
    }

    /**
     * 家电配套
     * @return string
     */
    public function actionAppliancesAssort()
    {
//        $receive = \Yii::$app->request->post();
//        $post = Json::decode($receive);
        $post = [
//            'effect_id' => 1,
            'master_bedroom' => 2,
            'secondary_bedroom' => 1,
            'sitting_room' => 1,
            'dining_room' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 62,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 1,
            'series' => 1,
            'province' => 510000,
            'city' => 510100,
            'waterproof_total_area'=>50
        ];
        //油烟机,灶具,热水器,冰箱,洗衣机,电视,空调,中央空调
        $kitchen_ventilator = '油烟机';
        $kitchen_ventilator_quantity = $post['kitchen'];
        $kitchen_ventilator_goods = Goods::priceDetail(3,$kitchen_ventilator,$post['city'],$post['series'],$post['style']);
        $kitchen_ventilator_goods ['show_price'] = $kitchen_ventilator_goods['platform_price'] * $kitchen_ventilator_quantity;
        $kitchen_ventilator_goods ['show_quantity'] = $kitchen_ventilator_quantity;

        $stove = '灶具';
        $stove_goods = Goods::priceDetail(3,$stove,$post['city'],$post['series'],$post['style']);
        $stove_goods ['show_price'] = $stove_goods['platform_price'] * $kitchen_ventilator_quantity;
        $stove_goods ['show_quantity'] = $kitchen_ventilator_quantity;

        $refrigerator = '冰箱';
        $refrigerator_goods = Goods::priceDetail(3,$refrigerator,$post['city'],$post['series'],$post['style']);
        $refrigerator_goods ['show_price'] = $refrigerator_goods['platform_price'] * 1;
        $refrigerator_goods ['show_quantity'] = 1;

        $washing_machine = '洗衣机';
        $washing_machine_goods = Goods::priceDetail(3,$washing_machine,$post['city'],$post['series'],$post['style']);
        $washing_machine_goods ['show_price'] = $washing_machine_goods['platform_price'] * 1;
        $washing_machine_goods ['show_quantity'] = 1;

        $television = '电视';
        $television_goods = Goods::priceDetail(3,$television,$post['city'],$post['series'],$post['style']);
        $television_goods ['show_price'] = $television_goods['platform_price'] * 1;
        $television_goods ['show_quantity'] = 1;

        if ($post['series'] >= 2 || $post['style'] >=2)
        {
            $air_conditioning = '中央空调';
            $air_conditioning_goods = Goods::priceDetail(3,$air_conditioning,$post['city'],$post['series'],$post['style']);
            $air_conditioning_goods ['show_price'] = $air_conditioning_goods['platform_price'] * 1;
            $air_conditioning_goods ['show_quantity'] = 1;

            $wall_hanging_goods = 0;
        }else{
            $vertical = '立式空调';
            $vertical_quantity = $post['sitting_room'];
            $air_conditioning_goods = Goods::priceDetail(3,$vertical,$post['city'],$post['series'],$post['style']);
            $air_conditioning_goods ['show_price'] = $air_conditioning_goods['platform_price'] * $vertical_quantity;
            $air_conditioning_goods ['show_quantity'] = $vertical_quantity;

            $wall_hanging = '挂壁式空调';
            $wall_hanging_quantity = $post['master_bedroom'];
            $wall_hanging_goods = Goods::priceDetail(3,$wall_hanging,$post['city'],$post['series'],$post['style']);
            $wall_hanging_goods ['show_price'] = $wall_hanging_goods['platform_price'] * $wall_hanging_quantity;
            $wall_hanging_goods ['show_quantity'] = $wall_hanging_quantity;
        }

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' =>[
                'kitchen_ventilator' =>$kitchen_ventilator_goods,
                'stove' =>$stove_goods,
                'refrigerator' =>$refrigerator_goods,
                'washing_machine' =>$washing_machine_goods,
                'television' =>$television_goods,
                'air_conditioning_goods' =>$television_goods,
                'air_condition' =>$air_conditioning_goods,
                'wall_hanging' =>$wall_hanging_goods,
            ]
        ]);
    }

    /**
     * 生活配套
     * @return string
     */
    public function actionLifeAssort()
    {
//        $receive = \Yii::$app->request->post();
//        $post = Json::decode($receive);
         $post = [
//            'effect_id' => 1,
            'master_bedroom' => 2,
            'secondary_bedroom' => 1,
            'sitting_room' => 1,
            'dining_room' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 62,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 1,
            'series' => 1,
            'province' => 510000,
            'city' => 510100,
            'waterproof_total_area'=>50
         ];
        //刀具,菜板,碗碟套装,勺铲套装,整理箱,洁厕液,纸巾盒,不锈钢洗菜盆带龙头,马桶刷,马桶,蹲便器,浴柜,花洒套装,淋浴隔断
        //三角阀,高压管,拖布池,拖布池龙头,洗衣机地漏
        $cutter = '刀具';
        $cutter_goods = Goods::priceDetail(3,$cutter,$post['city'],$post['series'],$post['style']);
        $cutter_goods ['show_price'] = $cutter_goods['platform_price'] * 1;
        $cutter_goods ['show_quantity'] = 1;

        $chopping_board = '菜板';
        $chopping_board_goods = Goods::priceDetail(3,$chopping_board,$post['city'],$post['series'],$post['style']);
        $chopping_board_goods ['show_price'] = $chopping_board_goods['platform_price'] * 1;
        $chopping_board_goods ['show_quantity'] = 1;

        $bowl_and_dish = '碗碟套装';
        $bowl_and_dish_goods = Goods::priceDetail(3,$bowl_and_dish,$post['city'],$post['series'],$post['style']);
        $bowl_and_dish_goods ['show_price'] = $bowl_and_dish_goods['platform_price'] * 1;
        $bowl_and_dish_goods ['show_quantity'] = 1;

        $spoon = '勺铲套装';
        $spoon_goods = Goods::priceDetail(3,$spoon,$post['city'],$post['series'],$post['style']);
        $spoon_goods ['show_price'] = $spoon_goods['platform_price'] * 1;
        $spoon_goods ['show_quantity'] = 1;

        $sorting_box = '整理箱';
        $sorting_box_goods = Goods::priceDetail(3,$sorting_box,$post['city'],$post['series'],$post['style']);
        $sorting_box_goods ['show_price'] = $sorting_box_goods['platform_price'] * 1;
        $sorting_box_goods ['show_quantity'] = 1;

        $toilet_cleaner = '洁厕液';
        $toilet_cleaner_goods = Goods::priceDetail(3,$toilet_cleaner,$post['city'],$post['series'],$post['style']);
        $toilet_cleaner_goods ['show_price'] = $toilet_cleaner_goods['platform_price'] * 1;
        $toilet_cleaner_goods ['show_quantity'] = 1;

        $tissue_box = '纸巾盒';
        $tissue_box_goods = Goods::priceDetail(3,$tissue_box,$post['city'],$post['series'],$post['style']);
        $tissue_box_goods ['show_price'] = $tissue_box_goods['platform_price'] * 1;
        $tissue_box_goods ['show_quantity'] = 1;

        $vegatable_basin = '不锈钢洗菜盆';
        $vegatable_basin_goods = Goods::priceDetail(3,$vegatable_basin,$post['city'],$post['series'],$post['style']);
        $vegatable_basin_goods ['show_price'] = $vegatable_basin_goods['platform_price'] * 1;
        $vegatable_basin_goods ['show_quantity'] = 1;

        $toilet_brush= '马桶刷';
        $toilet_brush_goods = Goods::priceDetail(3,$toilet_brush,$post['city'],$post['series'],$post['style']);
        $toilet_brush_goods ['show_price'] = $toilet_brush_goods['platform_price'] * 1;
        $toilet_brush_goods ['show_quantity'] = 1;

        $stinkpot = '马桶';
        $stinkpot_goods = Goods::priceDetail(3,$stinkpot,$post['city'],$post['series'],$post['style']);
        $stinkpot_goods ['show_price'] = $stinkpot_goods['platform_price'] * 1;
        $stinkpot_goods ['show_quantity'] = 1;

        $squatting_pan = '蹲便器';
        $squatting_pan_goods = Goods::priceDetail(3,$squatting_pan,$post['city'],$post['series'],$post['style']);
        $squatting_pan_goods ['show_price'] = $squatting_pan_goods['platform_price'] * 1;
        $squatting_pan_goods ['show_quantity'] = 1;

        $bath_cabinet = '浴柜';
        $bath_cabinet_goods = Goods::priceDetail(3,$bath_cabinet,$post['city'],$post['series'],$post['style']);
        $bath_cabinet_goods ['show_price'] = $bath_cabinet_goods['platform_price'] * 1;
        $bath_cabinet_goods ['show_quantity'] = 1;

        $sprinkler = '花洒套装';
        $sprinkler_goods = Goods::priceDetail(3,$sprinkler,$post['city'],$post['series'],$post['style']);
        $sprinkler_goods ['show_price'] = $sprinkler_goods['platform_price'] * 1;
        $sprinkler_goods ['show_quantity'] = 1;



        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' =>[
                'appliances_goods' =>$goods_price,
                'quantity' => 1
            ]
        ]);
    }

    /**
     * 智能配套
     * @return string   intelligence
     */
    public function actionIntelligenceAssort()
    {
//        $receive = \Yii::$app->request->post();
//        $post = Json::decode($receive);
        $post = [
//            'effect_id' => 1,
            'master_bedroom' => 2,
            'secondary_bedroom' => 1,
            'sitting_room' => 1,
            'dining_room' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 62,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 1,
            'series' => 1,
            'province' => 510000,
            'city' => 510100,
            'waterproof_total_area'=>50
        ];
        $classify = '智能配套';
        $material_property_classify = MaterialPropertyClassify::findByAll($classify);
        $goods = Goods::
        var_dump($material_property_classify);exit;
//        return Json::encode([
//            'code' => 200,
//            'msg' => '成功',
//            'data' =>[
//                'distribution_box' =>$distribution_box_goods,
//                'background_music' => $background_music_goods
//            ]
//        ]);
    }

    public function actionBasisList()
    {

    }

}