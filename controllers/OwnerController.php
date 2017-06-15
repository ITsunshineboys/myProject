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
use app\models\IntelligenceAssort;
use app\models\LaborCost;
use app\models\LifeAssort;
use app\models\MoveFurniture;
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
        $stairs_details = StairsDetails::find()->all();
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
                'style_picture' => $style_picture,
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
        $worker = LaborCost::univalence($post,$arr['worker_kind']);

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
        $labor_all_cost = BasisDecorationService::laborFormula($weak_points,$worker);
        //材料总费用
        $material_price = BasisDecorationService::quantity($weak_points,$weak_current,$craft);
        $add_price = DecorationAdd::findByAll('弱电',$post['area'],$post['city']);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'weak_current_labor_price' => $labor_all_cost,
                'weak_current_material_price' => $material_price,
                'weak_current_add_price' => $add_price,
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
        $worker = LaborCost::univalence($post,$arr['worker_kind']);

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
        $labor_all_cost = BasisDecorationService::laborFormula($points_details,$worker);

        //材料总费用
        $material_price = BasisDecorationService::quantity($points_details,$strong_current,$craft);
        $add_price = DecorationAdd::findByAll('强电',$post['area'],$post['city']);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'strong_current_labor_price' => $labor_all_cost,
                'strong_current_material_price' => $material_price,
                'strong_current_add_price' => $add_price,
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
            $worker = LaborCost::univalence($post,$arr['worker_kind']);

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
            $labor_all_cost = BasisDecorationService::laborFormula($waterway_points,$worker);
            //材料总费用
            $material_price = BasisDecorationService::waterwayGoods($waterway_points,$waterway_current,$craft);
            //添加材料费用
            $add_price = DecorationAdd::findByAll('水路',$post['area'],$post['city']);

            return Json::encode([
                'code' => 200,
                'msg' => '成功',
                'data' => [
                    'waterway_labor_price' => $labor_all_cost,
                    'waterway_material_price' => $material_price,
                    'waterway_add_price' => $add_price,
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
        $worker = LaborCost::univalence($post,$arr['worker_kind']);

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

        //人工总费用
        $labor_all_cost = BasisDecorationService::laborFormula($total_area,$worker);
        //材料总费用
        $material_price = BasisDecorationService::waterproofGoods($total_area,$waterproof,$craft);
        //添加材料费用
        $add_price = DecorationAdd::findByAll('防水',$post['area'],$post['city']);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'waterproof_labor_price' => $labor_all_cost,
                'waterproof_material_price' => $material_price,
                'waterproof_add_price' => $add_price,
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
        $labor_cost = LaborCost::univalence($post['city'], $arr['worker_kind']);
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
        $material_cost = ($keel_cost + $plasterboard_cost + $pole_cost);
//      总费用
        $carpentry_add = DecorationAdd::CarpentryAddAll('木作',$post['series'],$post['style']);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'carpentry_labor_price' => $labour_charges,
                'carpentry_material_price' => $material_cost,
                'carpentry_add_price' => $carpentry_add,
            ]
        ]);
    }

    /**
     * 乳胶漆
     */
    public function actionCoating()
    {

    }

    /**
     * 泥作
     */
    public function actionMudMake()
    {

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
            'room' => 1,
            'hall' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 40,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 2,
            'series' => 1,
            'province' => '四川',
            'city' => '成都'
        ];
        if(!empty($post['effect_id'])){
            $decoration_list = DecorationList::findById($post['effect_id']);
            $soft_outfit_assort = SoftOutfitAssort::find()->where(['decoration_list_id'=>$decoration_list])->one();
            $goods_price = Goods::find()->where(['category_id'=>$soft_outfit_assort])->all();
        }else{
            $mating = '软装配套';
            $goods = Goods::findByIdAll(1,$mating,$post['series'],$post['style']);
            $goods_price = [];
            foreach ($goods as $v=>$k)
            {
                if($k['name'] == '快乐酒吧'){
                    $c [] = ($k['platform_price'] - $k['supplier_price']) / $k['supplier_price'];
                    $max = array_search(max($c), $c);
                    $max_pub_price = $goods[$max];
                }elseif ($k['name'] == '窗帘'){
                    $c [] = ($k['platform_price'] - $k['supplier_price']) / $k['supplier_price'];
                    $max = array_search(max($c), $c);
                    $max_curtain_price = $goods[$max];
                }
            }
            $goods_price [] = $max_pub_price;
            $goods_price [] = $max_curtain_price;
        }

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
            'effect_id' => 1,
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
        if(!empty($post['effect_id']))
        {
            $fixation_furniture = FixationFurniture::findById($post);
//            $goods = Goods::findByConditionInquire($fixation_furniture);
            exit;
        }
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
     * 移动家具
     * @return string
     */
    public function actionMoveFurniture()
    {
        $receive = \Yii::$app->request->post();
        $post = Json::decode($receive);
        $post = [
//            'effect_id' => 1,
            'room' => 1,
            'hall' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 40,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 2,
            'series' => 1,
            'province' => '四川',
            'city' => '成都'
        ];
        if(!empty($post['effect_id'])){
            $decoration_list = DecorationList::findById($post['effect_id']);
            $soft_outfit_assort = MoveFurniture::find()->where(['decoration_list_id'=>$decoration_list])->one();
            $goods_price = Goods::find()->where(['category_id'=>$soft_outfit_assort])->all();
        }else{
            $mating = '移动家具';
            $goods = Goods::findByIdAll(1,$mating,$post['series'],$post['style']);
            $goods_price = [];
            foreach ($goods as $v=>$k)
            {
                if($k['name'] == '沙发'){
                    $c [] = ($k['platform_price'] - $k['supplier_price']) / $k['supplier_price'];
                    $max = array_search(max($c), $c);
                    $max_sofa_price = $goods[$max];
                }elseif ($k['name'] == '床'){
                    $c [] = ($k['platform_price'] - $k['supplier_price']) / $k['supplier_price'];
                    $max = array_search(max($c), $c);
                    $max_bed_price = $goods[$max];
                }elseif ($k['name'] == '茶几'){
                    $c [] = ($k['platform_price'] - $k['supplier_price']) / $k['supplier_price'];
                    $max = array_search(max($c), $c);
                    $max_tea_table_price = $goods[$max];
                }
            }
            $goods_price [] = $max_sofa_price;
            $goods_price [] = $max_bed_price;
            $goods_price [] = $max_tea_table_price;
        }
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
     * 家电配套
     * @return string
     */
    public function actionAppliancesAssort()
    {
        $receive = \Yii::$app->request->post();
        $post = Json::decode($receive);
        $post = [
//            'effect_id' => 1,
            'room' => 1,
            'hall' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 40,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 2,
            'series' => 1,
            'province' => '四川',
            'city' => '成都'
        ];
        if(!empty($post['effect_id'])){
            $decoration_list = DecorationList::findById($post['effect_id']);
            $soft_outfit_assort = AppliancesAssort::find()->where(['decoration_list_id'=>$decoration_list])->one();
            $goods_price = Goods::find()->where(['category_id'=>$soft_outfit_assort])->all();
        }else{
            $mating = '家电配套';
            $goods = Goods::findByIdAll(1,$mating,$post['series'],$post['style']);
            $goods_price = [];
            foreach ($goods as $v=>$k)
            {
                if($k['name'] == '电视'){
                    $c [] = ($k['platform_price'] - $k['supplier_price']) / $k['supplier_price'];
                    $max = array_search(max($c), $c);
                    $max_television_price = $goods[$max];
                }elseif ($k['name'] == '洗衣机'){
                    $c [] = ($k['platform_price'] - $k['supplier_price']) / $k['supplier_price'];
                    $max = array_search(max($c), $c);
                    $max_washing_price = $goods[$max];
                }elseif ($k['name'] == '冰箱'){
                    $c [] = ($k['platform_price'] - $k['supplier_price']) / $k['supplier_price'];
                    $max = array_search(max($c), $c);
                    $max_refrigerator_price = $goods[$max];
                }elseif ($k['name'] == '空调'){
                    $c [] = ($k['platform_price'] - $k['supplier_price']) / $k['supplier_price'];
                    $max = array_search(max($c), $c);
                    $max_air_conditioners_price = $goods[$max];
                }
            }
            $goods_price [] = $max_television_price;
            $goods_price [] = $max_washing_price;
            $goods_price [] = $max_refrigerator_price;
            $goods_price [] = $max_air_conditioners_price;
        }
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
     * 生活配套
     * @return string
     */
    public function actionLifeAssort()
    {
        $receive = \Yii::$app->request->post();
        $post = Json::decode($receive);
        $post = [
//            'effect_id' => 1,
            'room' => 1,
            'hall' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 40,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 2,
            'series' => 1,
            'province' => '四川',
            'city' => '成都'
        ];
        if(!empty($post['effect_id'])){
            $decoration_list = DecorationList::findById($post['effect_id']);
            $soft_outfit_assort = LifeAssort::find()->where(['decoration_list_id'=>$decoration_list])->one();
            $goods_price = Goods::find()->where(['category_id'=>$soft_outfit_assort])->all();
        }else{
            $mating = '生活配套';
            $goods = Goods::findByIdAll(1,$mating,$post['series'],$post['style']);
            $goods_price = [];
            foreach ($goods as $v=>$k)
            {
                if($k['name'] == '沙发'){
                    $c [] = ($k['platform_price'] - $k['supplier_price']) / $k['supplier_price'];
                    $max = array_search(max($c), $c);
                    $max_sofa_price = $goods[$max];
                }elseif ($k['name'] == '床'){
                    $c [] = ($k['platform_price'] - $k['supplier_price']) / $k['supplier_price'];
                    $max = array_search(max($c), $c);
                    $max_bed_price = $goods[$max];
                }elseif ($k['name'] == '茶几'){
                    $c [] = ($k['platform_price'] - $k['supplier_price']) / $k['supplier_price'];
                    $max = array_search(max($c), $c);
                    $max_tea_table_price = $goods[$max];
                }
            }
            $goods_price [] = $max_sofa_price;
            $goods_price [] = $max_bed_price;
            $goods_price [] = $max_tea_table_price;
        }
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
     * @return string
     */
    public function actionIntelligenceAssort()
    {
        $receive = \Yii::$app->request->post();
        $post = Json::decode($receive);
        $post = [
//            'effect_id' => 1,
            'room' => 1,
            'hall' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 40,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 2,
            'series' => 1,
            'province' => '四川',
            'city' => '成都'
        ];
        if(!empty($post['effect_id'])){
            $decoration_list = DecorationList::findById($post['effect_id']);
            $soft_outfit_assort = IntelligenceAssort::find()->where(['decoration_list_id'=>$decoration_list])->one();
            $goods_price = Goods::find()->where(['category_id'=>$soft_outfit_assort])->all();
        }else{
            $mating = '智能配套';
            $goods = Goods::findByIdAll(1,$mating,$post['series'],$post['style']);
            $goods_price = [];
            foreach ($goods as $v=>$k)
            {
                if($k['name'] == '沙发'){
                    $c [] = ($k['platform_price'] - $k['supplier_price']) / $k['supplier_price'];
                    $max = array_search(max($c), $c);
                    $max_sofa_price = $goods[$max];
                }elseif ($k['name'] == '床'){
                    $c [] = ($k['platform_price'] - $k['supplier_price']) / $k['supplier_price'];
                    $max = array_search(max($c), $c);
                    $max_bed_price = $goods[$max];
                }elseif ($k['name'] == '茶几'){
                    $c [] = ($k['platform_price'] - $k['supplier_price']) / $k['supplier_price'];
                    $max = array_search(max($c), $c);
                    $max_tea_table_price = $goods[$max];
                }
            }
            $goods_price [] = $max_sofa_price;
            $goods_price [] = $max_bed_price;
            $goods_price [] = $max_tea_table_price;
        }
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' =>[
                'appliances_goods' =>$goods_price,
                'quantity' => 1
            ]
        ]);
    }

    public function actionBasisList()
    {

    }

}