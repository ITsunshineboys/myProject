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
use app\models\FixationFurniture;
use app\models\Goods;
use app\models\IntelligenceAssort;
use app\models\LaborCost;
use app\models\LifeAssort;
use app\models\MoveFurniture;
use app\models\Points;
use app\models\PointsDetails;
use app\models\Series;
use app\models\SoftOutfitAssort;
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
     * 系列和风格
     */
    public function actionSeriesAndStyle()
    {
        $series = Series::findByAll();
        $style = Style::findByAll();
        $style_picture = StylePicture::findById($style);
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
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
        //基础装修
//        $receive = \Yii::$app->request->post();
//        $post = Json::decode($receive);
        $post = [
//            'effect_id' => 1,
            'room' => 1,
            'hall' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 62,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 1,
            'series' => 1,
            'province' => '四川',
            'city' => '成都'
        ];
        $arr = [];
        $arr['profit'] = $post['1'] ?? 0.7;
        $arr['worker_kind'] = '水电';

        //人工一天价格
        $worker = LaborCost::univalence($post['province'], $post['city'], $arr['worker_kind']);
        $arr['day_standard'] = $worker[0]['day_points'];
        $arr['day_price'] = $worker[0]['univalence'];

        if(empty($post['effect_id'])){
            //查询弱电所需要材料
            $electric_wire = '电线';
            $weak_current = [];
            $wire = Goods::priceDetail(3, $electric_wire);
            $weak_current [] = BasisDecorationService::wire($wire['platform_price']);
            $pipe = '线管';
            $wire_pipe = Goods::priceDetail(3, $pipe);
            $weak_current [] = BasisDecorationService::wire($wire_pipe['platform_price']);
            $box = '底盒';
            $wire_box = Goods::priceDetail(3, $box);
            $weak_current [] = $wire_box['platform_price'];
        }else{
            $decoration_list = DecorationList::findById($post['effect_id']);
            $weak = CircuitryReconstruction::findByAll($decoration_list);
            $weak_current = [];
            $goods = Goods::findQueryAll($weak);
            foreach ($weak as $single)
            {
               if($single['material'] == '电线')
               {
                   foreach ($goods as $unit_price)
                   {
                       if($single['goods_id'] == $unit_price['id']){
                           $electric_wire = BasisDecorationService::wire($unit_price['platform_price']);
                           $weak_current [] = $electric_wire;
                       }
                   }
               }elseif ($single['material'] == '线管')
               {
                   foreach ($goods as $unit_price)
                   {
                       if($single['goods_id'] == $unit_price['id']){
                           $electric_wire = BasisDecorationService::wire($unit_price['platform_price']);
                           $weak_current [] = $electric_wire;
                       }
                   }
               }elseif ($single['material'] == '底盒'){
                   foreach ($goods as $unit_price)
                   {
                       if($single['goods_id'] == $unit_price['id']){
                           $weak_current [] = $unit_price['platform_price'];
                       }
                   }
               }elseif ($single['material'] !== '底盒' && $single['material'] !== '线管' && $single['material'] !== '电线')
               {
                   foreach ($goods as $unit_price)
                   {
                       if($single['goods_id'] == $unit_price['id'])
                       {
                           $weak_current [] = $unit_price['platform_price'];
                       }
                   }
               }
            }
        }

        if (!empty($post['effect_id']))
        {
            //查询所有弱电点位
            $effect_id = Effect::find()->where(['id' => $post['effect_id']])->one();
            $points = Points::weakLocation($effect_id['id']);
        } else {
            //查询所有弱电点位
            $effect = Effect::conditionQuery($post);
            $points = Points::weakLocation($effect['id']);
        }

        //基础弱电总价格
        $weak_price = ceil(BasisDecorationService::formula($arr, $points,$weak_current));
        $add_price = DecorationAdd::findByAll('弱电',$post['area']);
        $weak_current_price = $weak_price + $add_price;

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'weak_current_price' => $weak_current_price,
            ]
        ]);
    }

    /**
     * 强电价格
     * @return string
     */
    public function actionStrongCurrent()
    {
        //基础装修
        $receive = \Yii::$app->request->post();
        $post = Json::decode($receive);
        $post = [
            'effect_id' => 1,
            'room' => 1,
            'hall' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 62,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 1,
            'series' => 1,
            'province' => '四川',
            'city' => '成都'
        ];

        $arr = [];
        $arr['profit'] = $post['1'] ?? 0.7;
        $arr['worker_kind'] = '水电';

        //人工一天价格
        $worker = LaborCost::univalence($post['province'], $post['city'], $arr['worker_kind']);
        $arr['day_standard'] = $worker[0]['day_points'];
        $arr['day_price'] = $worker[0]['univalence'];
        //查询弱电所需要材料
        if(empty($post['effect_id'])){
            $strong_current = [];
            $electric_wire = '电线';
            $strong = Goods::priceDetail(3, $electric_wire);
            $strong_current [] = BasisDecorationService::wire($strong['platform_price']);
            $pipe = '线管';
            $wire_pipe = Goods::priceDetail(3, $pipe);
            $strong_current [] = BasisDecorationService::wire($wire_pipe['platform_price']);
            $box = '底盒';
            $strong_box = Goods::priceDetail(3, $box);
            $strong_current [] = $strong_box['platform_price'];
        }else{
            $decoration_list = DecorationList::findById($post['effect_id']);
            $weak = CircuitryReconstruction::findByAll($decoration_list);
            $strong_current = [];
            $goods = Goods::findQueryAll($weak);
            foreach ($weak as $single)
            {
                if($single['material'] == '电线')
                {
                    foreach ($goods as $unit_price)
                    {
                        if($single['goods_id'] == $unit_price['id']){
                            $electric_wire = BasisDecorationService::wire($unit_price['platform_price']);
                            $strong_current [] = $electric_wire;
                        }
                    }
                }elseif ($single['material'] == '线管')
                {
                    foreach ($goods as $unit_price)
                    {
                        if($single['goods_id'] == $unit_price['id']){
                            $electric_wire = BasisDecorationService::wire($unit_price['platform_price']);
                            $strong_current [] = $electric_wire;
                        }
                    }
                }elseif ($single['material'] == '底盒'){
                    foreach ($goods as $unit_price)
                    {
                        if($single['goods_id'] == $unit_price['id']){
                            $strong_current [] = $unit_price['platform_price'];
                        }
                    }
                }elseif ($single['material'] !== '底盒' && $single['material'] !== '线管' && $single['material'] !== '电线')
                {
                    foreach ($goods as $unit_price)
                    {
                        if($single['goods_id'] == $unit_price['id'])
                        {
                            $strong_current [] = $unit_price['platform_price'];
                        }
                    }
                }
            }
        }

        if (!empty($post['effect_id'])) {
            //查询所有强电点位
            $effect_id = Effect::find()->where(['id' => $post['effect_id']])->all();
            $all_place = Points::find()->where($effect_id[0]['id'])->all();
            $powerful_location = PointsDetails::AllQuantity($all_place);
        } else {
            $effect = Effect::conditionQuery($post);
            //查询所有强电点位
            $all_place = Points::find()->where($effect['id'])->all();
            $powerful_location = PointsDetails::AllQuantity($all_place);
        }

        //强电总价格
        $powerful_current = ceil(BasisDecorationService::formula($arr, $powerful_location,$strong_current));
        $add_price = DecorationAdd::findByAll('强电',$post['area']);
        $powerful_current_price = $powerful_current + $add_price;
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'strong_current_price' => $powerful_current_price,
            ]
        ]);
    }

    /**
     * 水路
     * @return string
     */
    public function actionWaterway()
    {
        //基础装修
        $receive = \Yii::$app->request->post();
        $post = Json::decode($receive);
        $post = [
//            'effect_id' => 1,
            'room' => 1,
            'hall' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 62,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 1,
            'series' => 1,
            'province' => '四川',
            'city' => '成都'
        ];
        $arr = [];
        $arr['profit'] = $post['1'] ?? 0.7;
        $arr['worker_kind'] = '水电';

        //人工一天价格
        $worker = LaborCost::univalence($post['province'], $post['city'], $arr['worker_kind']);
        $arr['day_standard'] = $worker[0]['day_points'];
        $arr['day_price'] = $worker[0]['univalence'];

        //查询水路所需要材料
        if(empty($post['effect_id']))
        {
            //ppr热水管
            $pipe = 'PPR热水管';
            $waterway = [];
            $ppr = Goods::priceDetail(3,$pipe);
            $waterway [] = BasisDecorationService::wire($ppr['platform_price'],4,0.5);
            //pvc管
            $pvc_pipe ='pvc管';
            $pvc = Goods::priceDetail(3,$pvc_pipe);
            $waterway [] = BasisDecorationService::wire($pvc['platform_price'],4,0.5);
        }else{

            $decoration_list = DecorationList::findById($post['effect_id']);
            $weak = WaterwayReconstruction::findByAll($decoration_list);
            $waterway = [];
            $goods = Goods::findQueryAll($weak);
            foreach ($weak as $single)
            {
                if($single['material'] == 'PPR热水管')
                {
                    foreach ($goods as $unit_price)
                    {
                        if ($single['goods_id'] == $unit_price['id'])
                        {
                            $electric_wire = BasisDecorationService::wire($unit_price['platform_price'],4,0.5);
                            $waterway [] = $electric_wire;
                        }
                    }
                }elseif ($single['material'] == 'pvc管')
                {
                    foreach ($goods as $unit_price)
                    {
                        if ($single['goods_id'] == $unit_price['id'])
                        {

                            $electric_wire = BasisDecorationService::wire($unit_price['platform_price'],4,0.5);
                            $waterway [] = $electric_wire;
                        }
                    }
                }elseif ($single['material'] !== 'PPR热水管' && $single['material'] !== 'pvc管')
                {
                    foreach ($goods as $unit_price)
                    {
                        if($single['goods_id'] == $unit_price['id'])
                        {
                            $waterway [] = $unit_price['platform_price'];
                        }
                    }
                }
            }
        }
        if (!empty($post['effect_id'])) {
            //查询所有水路点位
            $effect_id = Effect::find()->where(['id' => $post['effect_id']])->all();
            $points = Points::waterwayPoints($effect_id);
        } else {
            $effect = Effect::conditionQuery($post);
            $points = Points::waterwayPoints($effect['id']);
        }

        $waterway_remould = ceil(BasisDecorationService::formula($arr,$points,$waterway));
        $add_price = DecorationAdd::findByAll('水路',$post['area']);
        $waterway_remould_price = $waterway_remould + $add_price;
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'waterway_remould_price' => $waterway_remould_price,
            ]
        ]);
    }

    /**
     * 防水
     * @return string
     */
    public function actionWaterproof()
    {
        $receive = \Yii::$app->request->post();
        $post = Json::decode($receive);
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
            'province' => '四川',
            'city' => '成都'
        ];
        $arr = [];
        $arr['profit'] = $post['1'] ?? 0.7;
        $arr['worker_kind'] = '水电';

        //人工一天价格
        $worker = LaborCost::univalence($post['province'], $post['city'], $arr['worker_kind']);
        $arr['day_standard'] = $worker[0]['day_area'];
        $arr['day_price'] = $worker[0]['univalence'];

        //防水所需材料
        if(!empty($post['effect_id'])){
            $decoration_list = DecorationList::findById($post['effect_id']);
            $weak = WaterproofReconstruction::findByAll($decoration_list);
            $waterproof = [];
            $goods = Goods::findQueryAll($weak);
            $waterproof [] = BasisDecorationService::wire($goods['platform_price'],25,1.25);
        }else{
            //ppr热水管
            $pipe = '防水';
            $goods = Goods::priceDetail(3,$pipe);
            $waterproof = [];
            $waterproof [] = BasisDecorationService::wire($goods['platform_price'],25,1.25);
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
            $kitchen_percent = $post['12'] ?? 0.3;
            $kitchen_high = $post['13'] ?? 0.3;
            //厨房地面面积
            $kitchen_ground_area = $kitchen_percent * $post['area'];
            //厨房墙面面积
            $kitchen_wall_space_area = sqrt($kitchen_ground_area) * $kitchen_high;
            $kitchen_wall_space_perimeter = round($kitchen_wall_space_area,2);
            //厨房面积
            $kitchen_area = $kitchen_ground_area + $kitchen_wall_space_perimeter;

            $toilet_percent = $post['14'] ?? 0.3;
            $toilet_high = $post['13'] ?? 1.8;
            //卫生间地面面积
            $toilet_ground_area = $toilet_percent * $post['area'];
            //卫生间强面面积
            $toilet_wall_space_area = sqrt($toilet_ground_area) * $toilet_high;
            $toilet_wall_space_perimeter = round($toilet_wall_space_area,2);
            //卫生间面积
            $toilet_area = $toilet_ground_area + $toilet_wall_space_perimeter;
            //总面积
            $total_area = $kitchen_area + $toilet_area;
        }

        //防水价格
        $waterproof = ceil(BasisDecorationService::formula($arr,$total_area,$waterproof));
        $add_price = DecorationAdd::findByAll('防水',$post['area']);
        $waterproof_price = $waterproof + $add_price;
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'waterproof_price' => $waterproof_price,
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
            'province' => '四川',
            'city' => '成都'
        ];
        $arr = [];
        $arr['profit'] = $post['1'] ?? 0.7;
        $arr['worker_kind'] = '木工';
        //人工一天价格
        $labor_cost = LaborCost::univalence($post['province'], $post['city'], $arr['worker_kind']);
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
            var_dump($goods_price);exit;
        }else{
            $plasterboard = '石膏板';
            $goods_price = [];
            $goods_price [] = Goods::priceDetail(3,$plasterboard);
            $keel = '龙骨';
            $goods_price [] = Goods::priceDetail(3,$keel);
            $screw = '丝杆';
            $goods_price [] = Goods::priceDetail(3,$screw);
        }
        //石膏板费用
        $plasterboard_cost = BasisDecorationService::carpentryPlasterboardCost($modelling_length,$labor_cost['day_area'],2.5,2.5,$goods_price);
        //龙骨费用
        $keel_cost = BasisDecorationService::carpentryKeelCost($modelling_length,$labor_cost['day_area'],1.5,1.5,$goods_price);
        //丝杆费用
        $pole_cost = BasisDecorationService::carpentryPoleCost($modelling_length,$labor_cost['day_area'],2,2,$goods_price);
        //材料费用
        $material_cost = ($keel_cost + $plasterboard_cost + $pole_cost) / 0.7;
        $carpentry_cost = ceil($material_cost + $labour_charges);
        $add = DecorationAdd::CarpentryAddAll('木作',$post['series'],$post['style']);
        $carpentry_price = $carpentry_cost + $add;

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'carpentry_price' => $carpentry_price,
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
            $goods = Goods::findByConditionInquire($fixation_furniture);
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