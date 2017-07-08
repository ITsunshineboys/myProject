<?php

namespace app\controllers;

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
use app\models\Goods;
use app\models\GoodsAttr;
use app\models\LaborCost;
use app\models\MaterialPropertyClassify;
use app\models\PaintReconstruction;
use app\models\PlasteringReconstruction;
use app\models\Points;
use app\models\PointsDetails;
use app\models\PointsTotal;
use app\models\Series;
use app\models\StairsDetails;
use app\models\Style;
use app\models\StylePicture;
use app\models\WaterproofReconstruction;
use app\models\WaterwayReconstruction;
use app\models\User;
use app\services\BasisDecorationService;
use app\services\ExceptionHandleService;
use app\services\ModelService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use Yii;

class OwnerController extends Controller
{
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
     * 系列、风格、楼梯
     */
    public function actionSeriesAndStyle()
    {
        $show['stairs_details'] = StairsDetails::find()->asArray()->all();
        $show['series'] = Series::findByAll();
        $show['style'] = Style::findByAll();
        if (!is_null($show['style'])) {
            $show['style_picture'] = StylePicture::findById($show['style']);
        } else {
            $show['style_picture'] = null;
        }
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'show' => $show,
            ]
        ]);
    }

    /**
     * 搜索界面
     * @return string
     */
    public function actionSearch()
    {
        $effect = Effect::find()->asArray()->all();
        $id = [];
        foreach ($effect as $one_effect)
        {
            $id [] = $one_effect['id'];
        }
        $effect_picture = EffectPicture::find()->where(['in','effect_id',$id])->all();
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'effect' => $effect,
                'effect_picture' => $effect_picture,
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
            'bedroom' => 1,
            'hall' => 1,
            'window' => 2,
            'high' => 2.8,
            'area' => 62,
            'toilet' => 1,
            'kitchen' => 1,
            'style' => 1,
            'series' => 1,
            'province' => 510000,
            'city' => 510100,
            'stairs_details_id' => 1,
        ];
        $arr = [];
        $arr['worker_kind'] = '电工';

        //人工价格
        $workers = LaborCost::univalence($post, $arr['worker_kind']);
        foreach ($workers as $worker) {
            if ($worker['worker_kind_details'] == '弱电') {
                $Weak_labor = $worker;
            }
        }
        //点位查询
        if (!empty($post['effect_id'])) {
            $weak_points = Points::weakPoints($post['effect_id']);
        } else {
            $weak_points = 0;
            $effect = Effect::find()->where(['id' => 1])->one();
            $points = Points::find()->where(['effect_id' => $effect['id']])->all();
            foreach ($points as $one) {
                if ($one['weak_current_points'] !== 0) {
                    $weak_current_place [] = $one['place'];
                    $weak_current_points [] = $one['weak_current_points'];
                }
            }

            $weak_current_all = array_combine($weak_current_place, $weak_current_points);
            $sitting_room = $weak_current_all['客厅'] * $post['hall'];
            $master_bedroom = $weak_current_all['主卧'] * 1;
            $secondary_bedroom = $weak_current_all['次卧'] * $post['bedroom'] - 1;
            $weak_points = $sitting_room + $master_bedroom + $secondary_bedroom;
        }

        if (empty($post['effect_id'])) {
            //查询弱电所需要材料
            $weak_current = [];
            $electric_wire = '网线';
            $weak_current [] = Goods::priceDetail(3, $electric_wire);
            $pipe = '线管';
            $weak_current [] = Goods::priceDetail(3, $pipe);
            $box = '底盒';
            $weak_current [] = Goods::priceDetail(3, $box);
        } else {
            $decoration_list = DecorationList::findById($post['effect_id']);
            $weak = CircuitryReconstruction::findByAll($decoration_list, '弱电');
            $weak_current = Goods::findQueryAll($weak, $post['city']);
        }

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll('弱电', $post['city']);

        //人工总费用
        $labor_all_cost = BasisDecorationService::laborFormula($weak_points, $Weak_labor);

        //材料总费用
        $material_price = BasisDecorationService::quantity($weak_points, $weak_current, $craft);

        //添加材料

        $add_price_area = DecorationAdd::AllArea('弱电', $post['area'], $post['city']);
        $add_price = [];
        foreach ($add_price_area as $add_area)
        {
            $sku_area =  Goods::skuAll($add_area['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_area['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }

        $add_price_series = DecorationAdd::AllSeries('弱电', $post['series'], $post['city']);
        foreach ($add_price_series as $add_series)
        {
            $sku_area =  Goods::skuAll($add_series['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_series['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }
        $add_price_style = DecorationAdd::AllStyle('弱电', $post['style'], $post['city']);
        foreach ($add_price_style as $add_style)
        {
            $sku_area =  Goods::skuAll($add_style['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_style['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }

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
                'weak_current' => $weak_current
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
        $arr['worker_kind'] = '电工';

        //人工价格
        $workers = LaborCost::univalence($post, $arr['worker_kind']);
        foreach ($workers as $worker) {
            if ($worker['worker_kind_details'] == '强电') {
                $strong_labor = $worker;
            }
        }

        //点位查询
        if (!empty($post['effect_id'])) {
            $strong_points = 0;
            $points = Points::strongPoints($post['effect_id']);
            $points_details = PointsDetails::AllQuantity($points);
        } else {
            $strong_points = 0;
            $effect = Effect::find()->where(['id' => 1])->one();
            $points = Points::strongPointsAll($effect);
            $points_total = PointsTotal::findByAll($points);
            $points_places = [];
            foreach ($points_total as $one) {
                if ($one['place'] == '客厅') {
                    $sitting_room = $one['points_total'] * $post['sitting_room'];
                    $sitting_room = $sitting_room ?: 0;
                    $points_places [] = $sitting_room;
                } elseif ($one['place'] == '主卧') {
                    $master_bedroom = $one['points_total'] * $post['master_bedroom'];
                    $master_bedroom = $master_bedroom ?: 0;
                    $points_places [] = $master_bedroom;
                } elseif ($one['place'] == '次卧') {
                    $secondary_bedroom = $one['points_total'] * $post['secondary_bedroom'];
                    $secondary_bedroom = $secondary_bedroom ?: 0;
                    $points_places [] = $secondary_bedroom;
                } elseif ($one['place'] == '餐厅') {
                    $dining_room = $one['points_total'] * $post['dining_room'];
                    $dining_room = $dining_room ?: 0;
                    $points_places [] = $dining_room;
                } elseif ($one['place'] == '厨房') {
                    $kitchen = $one['points_total'] * $post['kitchen'];
                    $kitchen = $kitchen ?: 0;
                    $points_places [] = $kitchen;
                } elseif ($one['place'] == '卫生间') {
                    $toilet = $one['points_total'] * $post['toilet'];
                    $toilet = $toilet ?: 0;
                    $points_places [] = $toilet;
                } elseif ($one['place'] !== '卫生间' && $one['place'] !== '客厅' && $one['place'] !== '主卧' && $one['place'] !== '次卧' && $one['place'] !== '餐厅' && $one['place'] !== '厨房' && $one['place'] !== '卫生间') {
                    $other [] = $one;
                }
            }
            $other_points = 0;
            foreach ($other as $other_one) {
                $other_points += $other_one['points_total'];
                $points_places [] = $other_points;
            }
            $points_details = array_sum($points_places);
        }

        //材料查询
        if (empty($post['effect_id'])) {
            //查询弱电所需要材料
            $strong_current = [];
            $electric_wire = '电线';
            $strong_current [] = Goods::priceDetail(3, $electric_wire);
            $pipe = '线管';
            $strong_current [] = Goods::priceDetail(3, $pipe);
            $box = '底盒';
            $strong_current [] = Goods::priceDetail(3, $box);
        } else {
            $decoration_list = DecorationList::findById($post['effect_id']);
            $weak = CircuitryReconstruction::findByAll($decoration_list, '强电');
            $strong_current = Goods::findQueryAll($weak, $post['city']);
        }

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll('强电', $post['city']);

        //人工总费用
        $labor_all_cost = BasisDecorationService::laborFormula($points_details, $strong_labor);

        //材料总费用
        $material_price = BasisDecorationService::quantity($points_details, $strong_current, $craft);

        $add_price_area = DecorationAdd::AllArea('强电', $post['area'], $post['city']);
        $add_price = [];
        foreach ($add_price_area as $add_area)
        {
            $sku_area =  Goods::skuAll($add_area['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_area['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }

        $add_price_series = DecorationAdd::AllSeries('强电', $post['series'], $post['city']);
        foreach ($add_price_series as $add_series)
        {
            $sku_area =  Goods::skuAll($add_series['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_series['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }
        $add_price_style = DecorationAdd::AllStyle('弱电', $post['style'], $post['city']);
        foreach ($add_price_style as $add_style)
        {
            $sku_area =  Goods::skuAll($add_style['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_style['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }

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
                'strong_current' => $strong_current
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
        $arr['worker_kind'] = '水路工';

        //人工价格
        $workers = LaborCost::univalence($post, $arr['worker_kind']);
        foreach ($workers as $worker) {
            if ($worker['worker_kind_details'] == '水路工') {
                $waterway_labor = $worker;
            }
        }

        //点位查询
        if (!empty($post['effect_id'])) {
            $waterway_points = Points::waterwayPoints($post['effect_id']);
        } else {
            $waterway_points = 0;
            $effect = Effect::find()->where(['id' => 1])->one();
            $points = Points::find()->where(['effect_id' => $effect['id']])->all();
            $other = 0;
            foreach ($points as $one) {
                if ($one['waterway_points'] !== 0) {
                    $waterway_current_place [] = $one['place'];
                    $waterway_current_points [] = $one['waterway_points'];
                }

                if ($one['place'] !== '厨房' && $one['place'] !== '卫生间') {
                    $other += $one['waterway_points'];
                }
            }
            $waterway_current_all = array_combine($waterway_current_place, $waterway_current_points);
            $kitchen = $waterway_current_all['厨房'] * $post['kitchen'];
            $toilet = $waterway_current_all['卫生间'] * $post['toilet'];
            $waterway_points = $kitchen + $toilet + $other;
        }

        if (empty($post['effect_id'])) {
            //查询弱电所需要材料
            $waterway_current = [];
            $electric_wire = 'PPR';
            $waterway_current [] = Goods::priceDetail(3, $electric_wire);
            $pipe = 'PVC';
            $waterway_current [] = Goods::priceDetail(3, $pipe);
        } else {
            $decoration_list = DecorationList::findById($post['effect_id']);
            $weak = WaterwayReconstruction::findByAll($decoration_list);
            $waterway_current = Goods::findQueryAll($weak, $post['city']);
        }
        //当地工艺
        $craft = EngineeringStandardCraft::findByAll('水路', $post['city']);

        //人工总费用
        $labor_all_cost = BasisDecorationService::laborFormula($waterway_points, $waterway_labor);
        //材料总费用
        $material_price = BasisDecorationService::waterwayGoods($waterway_points, $waterway_current, $craft);
        //添加材料费用
        $add_price_area = DecorationAdd::AllArea('水路', $post['area'], $post['city']);
        $add_price = [];
        foreach ($add_price_area as $add_area)
        {
            $sku_area =  Goods::skuAll($add_area['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_area['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }

        $add_price_series = DecorationAdd::AllSeries('水路', $post['series'], $post['city']);
        foreach ($add_price_series as $add_series)
        {
            $sku_area =  Goods::skuAll($add_series['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_series['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }
        $add_price_style = DecorationAdd::AllStyle('水路', $post['style'], $post['city']);
        foreach ($add_price_style as $add_style)
        {
            $sku_area =  Goods::skuAll($add_style['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_style['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }

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
                'waterway_current' => $waterway_current
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
        $arr['worker_kind'] = '防水工';

        //人工价格
        $workers = LaborCost::univalence($post, $arr['worker_kind']);
        foreach ($workers as $worker) {
            if ($worker['worker_kind_details'] == '防水工') {
                $waterproof_labor = $worker;
            }
        }
        //防水所需材料

        if (empty($post['effect_id'])) {
            //查询弱电所需要材料
            $waterproof = [];
            $electric_wire = '防水涂剂';
            $waterproof [] = Goods::priceDetail(3, $electric_wire);
        } else {
            $decoration_list = DecorationList::findById($post['effect_id']);
            $weak = WaterproofReconstruction::findByAll($decoration_list);
            $waterproof [] = Goods::findQueryAll($weak, $post['city']);
        }

        //防水所需面积
        if (!empty($post['effect_id'])) {
            $effect = DecorationList::findById($post['effect_id']);
            $area = DecorationParticulars::findByOne($effect);
            //地面面积
            $ground_total_area = BasisDecorationService::groundArea($area);
            //墙面周长
            $wall_space_total_perimeter = BasisDecorationService::wallSpace($area);
            $total_area_float = $ground_total_area + $wall_space_total_perimeter;
            //总面积
            $total_area = intval($total_area_float);
        } else {
            //厨房
            $kitchen = EngineeringUniversalCriterion::findByAll('厨房');
            $kitchen_area = BasisDecorationService::waterproofArea($kitchen, $post['area'], $post['kitchen']);
            //卫生间
            $toilet = EngineeringUniversalCriterion::findByAll('卫生间');
            $toilet_area = BasisDecorationService::waterproofArea($toilet, $post['area'], $post['kitchen']);
            //总面积
            $total_area = intval($kitchen_area + $toilet_area);
        }

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll('防水', $post['city']);

        //人工总费用（防水总面积÷【每天做工面积】）×【工人每天费用】
        $labor_all_cost = ceil($total_area / $waterproof_labor['quantity'] * $waterproof_labor['univalence']);
        //材料总费用
        $material_price = BasisDecorationService::waterproofGoods($total_area, $waterproof, $craft);

        //添加材料费用
        $add_price_area = DecorationAdd::AllArea('防水', $post['area'], $post['city']);
        $add_price = [];
        foreach ($add_price_area as $add_area)
        {
            $sku_area =  Goods::skuAll($add_area['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_area['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }

        $add_price_series = DecorationAdd::AllSeries('防水', $post['series'], $post['city']);
        foreach ($add_price_series as $add_series)
        {
            $sku_area =  Goods::skuAll($add_series['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_series['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }
        $add_price_style = DecorationAdd::AllStyle('防水', $post['style'], $post['city']);
        foreach ($add_price_style as $add_style)
        {
            $sku_area =  Goods::skuAll($add_style['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_style['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'waterproof_labor_price' => $labor_all_cost,
                'waterproof_material_price' => $material_price['cost'],
                'waterproof_material_quantity' => $material_price['quantity'],
                'waterproof_add_price' => $add_price,
                'waterproof' => $waterproof
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
        $arr['worker_kind'] = '木工';
        //人工一天价格
        $labor_costs = LaborCost::univalence($post['city'], $arr['worker_kind']);
        foreach ($labor_costs as $labor_cost) {
        }
        $series_all = Series::find()->all();
        $style_all = Style::find()->all();
        $carpentry_add = CarpentryAdd::findByStipulate($post['series'], $post['style']);
        // 造型长度
        $modelling_length = BasisDecorationService::carpentryModellingLength($carpentry_add, $series_all, $post['series']);
        //造型天数
        $modelling_day = BasisDecorationService::carpentryModellingDay($modelling_length, $labor_cost['quantity'], $series_all, $style_all);
        //平顶天数
        $flat_day = BasisDecorationService::flatDay($carpentry_add, $labor_cost['quantity'], $series_all, $style_all);
        //人工费
        $labour_charges = BasisDecorationService::carpentryLabor($modelling_day, $flat_day, 1, $labor_cost['univalence']);

        //木工材料费
        if (!empty($post['effect_id'])) {
            $decoration_list = DecorationList::findById($post['effect_id']);
            $carpentry_reconstruction = CarpentryReconstruction::find()->where(['decoration_list_id' => $decoration_list])->all();
            $goods_price = Goods::findQueryAll($carpentry_reconstruction);
        } else {
            $plasterboard = '石膏板';
            $goods_price = [];
            $goods_price [] = Goods::priceDetail(3, $plasterboard);
            $keel = '龙骨';
            $goods_price [] = Goods::priceDetail(3, $keel);
            $screw = '丝杆';
            $goods_price [] = Goods::priceDetail(3, $screw);
        }

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll('木作', $post['city']);

        //石膏板费用
        $plasterboard_cost = BasisDecorationService::carpentryPlasterboardCost($modelling_length, $carpentry_add['flat_area'], $goods_price, $craft);

        //龙骨费用
        $keel_cost = BasisDecorationService::carpentryKeelCost($modelling_length, $carpentry_add['flat_area'], $goods_price, $craft);
        //丝杆费用
        $pole_cost = BasisDecorationService::carpentryPoleCost($modelling_length, $carpentry_add['flat_area'], $goods_price, $craft);
        //材料费用
        $material_cost = ($keel_cost['cost'] + $plasterboard_cost['cost'] + $pole_cost['cost']);
//      添加费用
        $add_price_area = DecorationAdd::AllArea('木作', $post['area'], $post['city']);
        $add_price = [];
        foreach ($add_price_area as $add_area)
        {
            $sku_area =  Goods::skuAll($add_area['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_area['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }

        $add_price_series = DecorationAdd::AllSeries('木作', $post['series'], $post['city']);
        foreach ($add_price_series as $add_series)
        {
            $sku_area =  Goods::skuAll($add_series['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_series['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }
        $add_price_style = DecorationAdd::AllStyle('木作', $post['style'], $post['city']);
        foreach ($add_price_style as $add_style)
        {
            $sku_area =  Goods::skuAll($add_style['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_style['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }

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
                'goods_price' => $goods_price
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
        $arr['worker_kind'] = '油漆工';
        //工人一天单价
        $labor_costs = LaborCost::univalence($post['city'], $arr['worker_kind']);
        $primer = 0;
        $finishing_coat = 0;
        $concave_line = 0;
        $putty = 0;
        foreach ($labor_costs as $labor_cost) {
            if ($labor_cost['worker_kind_details'] == '乳胶漆底漆') {
                $primer = $labor_cost['quantity'];
            }
            if ($labor_cost['worker_kind_details'] == '乳胶漆面漆') {
                $finishing_coat = $labor_cost['quantity'];
            }
            if ($labor_cost['worker_kind_details'] == '阴角线') {
                $concave_line = $labor_cost['quantity'];
            }
            if ($labor_cost['worker_kind_details'] == '腻子') {
                $putty = $labor_cost['quantity'];
            }
        }

        if (!empty($post['effect_id'])) {
            $decoration_list = DecorationList::findById($post['effect_id']);
            $area = DecorationParticulars::findByOne($decoration_list);
            $tall =  2.8;
        } else {
            $project = '油漆';
            $areas = EngineeringUniversalCriterion::findByAll($project);
            $area['masterBedroom_area'] = 0;
            $area['sittingRoom_diningRoom_area'] = 0;
            $tall = 0;
            foreach ($areas as $one) {
                if ($one['project_particulars'] == '卧室面积')
                {
                    $area['masterBedroom_area'] = $one['project_value'];
                    $tall = $one['storey'];
                }
                if ($one['project_particulars'] == '客厅面积')
                {
                    $area['sittingRoom_diningRoom_area'] = $one['project_value'];
                    $tall = $one['storey'];
                }
            }
        }
        //卧室底漆面积
        $bedroom_primer_area = BasisDecorationService::paintedArea($area['masterBedroom_area'], $post['area'], $tall, $post['master_bedroom']);
        //客餐厅底漆面积
        $drawing_room_primer_area = BasisDecorationService::paintedArea($area['sittingRoom_diningRoom_area'], $post['area'], $tall, $post['sitting_room'], 3);
//        乳胶漆底漆面积：卧室底漆面积+客厅底漆面积+餐厅底漆面积+其它面积1
        $primer_area = $bedroom_primer_area + $drawing_room_primer_area;
//        乳胶漆底漆天数：乳胶漆底漆面积÷【每天做乳胶漆底漆面积】
        $primer_day = ceil($primer_area / $primer);

        //卧室面漆面积
        $bedroom_finishing_coat_area = BasisDecorationService::paintedArea($area['masterBedroom_area'], $post['area'], $tall, $post['master_bedroom']);
        //客餐厅面漆面积
        $drawing_room_finishing_coat_area = BasisDecorationService::paintedArea($area['sittingRoom_diningRoom_area'], $post['area'], $tall, $post['sitting_room'], 3);
        //乳胶漆面漆面积
        $finishing_coat_area = $bedroom_finishing_coat_area + $drawing_room_finishing_coat_area;
//        乳胶漆面漆天数：乳胶漆面漆面积÷【每天做乳胶漆面漆面积】
        $finishing_coat_day = ceil($finishing_coat_area / $finishing_coat);

//        卧室周长
        $bedroom_primer_perimeter = BasisDecorationService::paintedPerimeter($area['masterBedroom_area'], $post['area'], $post['master_bedroom']);
//        客厅周长
        $drawing_room_perimeter = BasisDecorationService::paintedPerimeter($area['sittingRoom_diningRoom_area'], $post['area'], $post['sitting_room']);
//        阴角线长度
        $concave_line_length = $bedroom_primer_perimeter + $drawing_room_perimeter;
//        阴角线天数：阴角线长度÷【每天做阴角线长度】
        $concave_line_day = ceil($concave_line_length / $concave_line);

//        腻子卧室墙面积
        $putty_bedroom_area = BasisDecorationService::paintedArea($area['masterBedroom_area'], $post['area'], $tall, $post['master_bedroom']);
//        腻子客餐厅面积
        $putty_drawing_room_area = BasisDecorationService::paintedArea($area['sittingRoom_diningRoom_area'], $post['area'], $tall, $post['sitting_room'], 3);
//        腻子面积 卧室腻子面积+客厅腻子面积
        $putty_area = $putty_bedroom_area + $putty_drawing_room_area;
//        腻子天数 腻子面积÷【每天做腻子面积】
        $putty_day = ceil($putty_area / $putty);
        //材料
        if (!empty($post['effect_id'])) {
            $decoration_list = DecorationList::findById($post['effect_id']);
            $paint_reconstruction = PaintReconstruction::find()->where(['decoration_list_id' => $decoration_list])->all();
            $goods_price = Goods::findQueryAll($paint_reconstruction);
        } else {
            $putty = '腻子';
            $goods_price = [];
            $goods_price [] = Goods::priceDetail(3, $putty);
            $emulsion_varnish_primer = '乳胶漆底漆';
            $goods_price [] = Goods::priceDetail(3, $emulsion_varnish_primer);
            $emulsion_varnish_finishing_coat = '乳胶漆面漆';
            $goods_price [] = Goods::priceDetail(3, $emulsion_varnish_finishing_coat);
            $concave_line = '阴角线';
            $goods_price [] = Goods::priceDetail(3, $concave_line);
            $gypsum_powder = '石膏粉';
            $goods_price [] = Goods::priceDetail(3, $gypsum_powder);
        }

        //当地工艺
        $crafts = EngineeringStandardCraft::findByAll('乳胶漆', $post['city']);
        foreach ($goods_price as $goods) {
            if ($goods['title'] == '腻子') {
                $goods_putty = $goods;
            }else
            {
                $goods_putty = null;
            }
            if ($goods['title'] == '乳胶漆底漆') {
                $goods_primer = $goods;
            }else
            {
                $goods_primer = null;
            }
            if ($goods['title'] == '乳胶漆面漆') {
                $goods_finishing_coat = $goods;
            }else
            {
                $goods_finishing_coat = null;
            }
            if ($goods['title'] == '阴角线') {
                $goods_concave_line = $goods;
            }else
            {
                $goods_concave_line = null;
            }
            if ($goods['title'] == '石膏粉') {
                $goods_gypsum_powder = $goods;
            }else
            {
                $goods_gypsum_powder['platform_price'] = 1;
            }
        }

        foreach ($crafts as $craft) {
            if ($craft['project_details'] == '腻子') {
                $putty_craft = $craft;
            }
            if ($craft['project_details'] == '乳胶漆底漆') {
                $primer_craft = $craft;
            }
            if ($craft['project_details'] == '乳胶漆面漆') {
                $finishing_coat_craft = $craft;
            }
            if ($craft['project_details'] == '阴角线') {
                $concave_line_craft = $craft;
            }
            if ($craft['project_details'] == '石膏粉') {
                $gypsum_powder_craft = $craft;
            }
        }
//        腻子费用
        $putty_cost = BasisDecorationService::paintedCost($goods_putty, $putty_craft, $putty_area);
//        底漆费用
        $primer_cost = BasisDecorationService::paintedCost($goods_primer, $primer_craft, $primer_area);
//        乳胶漆面漆费用
        $finishing_coat_cost = BasisDecorationService::paintedCost($goods_finishing_coat, $finishing_coat_craft, $finishing_coat_area);
//        阴角线费用
        $concave_line_cost = BasisDecorationService::paintedCost($goods_concave_line, $concave_line_craft, $concave_line_length);
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
        $add_price_area = DecorationAdd::AllArea('油漆', $post['area'], $post['city']);
        $add_price = [];
        foreach ($add_price_area as $add_area)
        {
            $sku_area =  Goods::skuAll($add_area['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_area['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }

        $add_price_series = DecorationAdd::AllSeries('油漆', $post['series'], $post['city']);
        foreach ($add_price_series as $add_series)
        {
            $sku_area =  Goods::skuAll($add_series['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_series['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }
        $add_price_style = DecorationAdd::AllStyle('油漆', $post['style'], $post['city']);
        foreach ($add_price_style as $add_style)
        {
            $sku_area =  Goods::skuAll($add_style['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_style['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }

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
                'add_price' => $add_price,
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
            'waterproof_total_area' => 50
        ];
        $arr = [];
        $arr['worker_kind'] = '泥工';
        //工人一天单价
        $labor_costs = LaborCost::univalence($post['city'], $arr['worker_kind']);
        $labor_day_cost = 0;
        foreach ($labor_costs as $labor_cost) {
            if ($labor_cost['worker_kind_details'] == '保护层') {
                $covering_layer_day_area = $labor_cost['quantity'];
                $labor_day_cost = $labor_cost['univalence'];
            }
            if ($labor_cost['worker_kind_details'] == '贴砖') {
                $tiling_day_area = $labor_cost['quantity'];
            }
        }
        //泥作面积
        if (!empty($post['effect_id'])) {
            $decoration_list = DecorationList::findById($post['effect_id']);
            $decoration_particulars = DecorationParticulars::findByOne($decoration_list);
            //卫生间面积
            $toilet_area = $decoration_particulars['hostToilet_area'];
            //厨房面积
            $kitchen_area = $decoration_particulars['kitchen_area'];
            //客餐厅面积
            $drawing_room_area = $decoration_particulars['sittingRoom_diningRoom_area'];
        } else {
            //厨房面积
            $kitchen_particulars = EngineeringUniversalCriterion::mudMakeArea('厨房', '厨房面积');
            $kitchen_area = $post['area'] * $kitchen_particulars['project_value'];
            //卫生间面积
            $toilet_particulars = EngineeringUniversalCriterion::mudMakeArea('卫生间', '卫生间面积');
            $toilet_area = $post['area'] * $toilet_particulars['project_value'];
            //客餐厅面积
            $drawing_room_particulars = EngineeringUniversalCriterion::mudMakeArea('客厅', '客厅面积');
            $drawing_room_area = $post['area'] * $drawing_room_particulars['project_value'];
        }

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll('泥工', $post['city']);
        foreach ($craft as $local_craft) {
            if ($local_craft['project_details'] == '贴砖') {
                $wall_height = $local_craft['material'];
            }
            if ($local_craft['project_details'] == '水泥') {
                $cement_craft = $local_craft['material'];
            }
            if ($local_craft['project_details'] == '自流平') {
                $self_leveling_craft = $local_craft['material'];
            }
            if ($local_craft['project_details'] == '河沙') {
                $river_sand_craft = $local_craft['material'];
            }
        }
//        var_dump($self_leveling_craft);exit;
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
        $total_labor_cost = $total_day * $labor_day_cost;

        //材料费
        if (!empty($post['effect_id'])) {
            $decoration_list = DecorationList::findById($post['effect_id']);
            $plastering_reconstruction = PlasteringReconstruction::findById($decoration_list);
            $goods_price = Goods::findQueryAll($plastering_reconstruction, $post['city']);
            $wall_brick_value = 0;
            $wall_brick_price = 0;
            $floor_tile_value = 0;
            $floor_tile_price = 0;
            $drawing_room_price = 0;
            $drawing_room_value = 0;
            foreach ($goods_price as $goods_brick_area) {
                if ($goods_brick_area['title'] == '墙砖') {
                    $wall_brick_value = $goods_brick_area['value'];
                    $wall_brick_price = $goods_brick_area['platform_price'];
                }
                if ($goods_brick_area['title'] == '地砖') {
                    $floor_tile_value = $goods_brick_area['value'];
                    $floor_tile_price = $goods_brick_area['platform_price'];
                }
                if ($goods_brick_area['title'] == '客厅地砖') {
                    $drawing_room_price = $goods_brick_area['platform_price'];
                    $drawing_room_value = $goods_brick_area['value'];
                }
            }
        } else {
            $cement = '水泥';
            $goods_price = [];
            $goods_price [] = Goods::priceDetail(3, $cement);
            $self_leveling = '自流平';
            $goods_price [] = Goods::priceDetail(3, $self_leveling);
            $wall_brick = '墙砖';
            $goods_price [] = Goods::priceDetail(3, $wall_brick);
            $floor_tile = '地砖';
            $goods_price [] = Goods::priceDetail(3, $floor_tile);
            $river_sand = '河沙';
            $goods_price [] = Goods::priceDetail(3, $river_sand);
            $river_sand = '客厅地砖';
            $goods_price [] = Goods::priceDetail(3, $river_sand);
            $wall_brick_value = 0;
            $wall_brick_price = 0;
            $floor_tile_value = 0;
            $floor_tile_price = 0;
            $drawing_room_price = 0;
            $drawing_room_value = 0;
            foreach ($goods_price as $goods_brick_area) {
                if ($goods_brick_area['title'] == '墙砖') {
                    $wall_brick_value = $goods_brick_area['value'];
                    $wall_brick_price = $goods_brick_area['platform_price'];
                }
                if ($goods_brick_area['title'] == '地砖') {
                    $floor_tile_value = $goods_brick_area['value'];
                    $floor_tile_price = $goods_brick_area['platform_price'];
                }
                if ($goods_brick_area['title'] == '客厅地砖') {
                    $drawing_room_price = $goods_brick_area['platform_price'];
                    $drawing_room_value = $goods_brick_area['value'];
                }
            }
        }
//        水泥面积=保护层面积+ 地砖面积+墙砖面积
        $cement_area = $covering_layer_area + $floor_tile_area + $floor_tile_area;
//        水泥费用
        $cement_cost = BasisDecorationService::mudMakeCost($cement_area, $goods_price, $cement_craft, '水泥');
//        自流平面积
        $self_leveling_area = $drawing_room_area;
//        自流平费用
        $self_leveling_cost = BasisDecorationService::mudMakeCost($self_leveling_area, $goods_price, $self_leveling_craft, '自流平');
//        厨房/卫生间个数：（墙砖面积÷抓取墙砖面积）
        $wall_brick_area = ($wall_brick_value / 1000) * ($wall_brick_value / 1000);
        if ($wall_brick_area == 0)
        {
            $wall_brick_cost ['quantity'] = 0;
        }else
        {
            $wall_brick_cost ['quantity'] = ceil($wall_area / $wall_brick_area);
        }
//        厨房/卫生间墙砖费用
        $wall_brick_cost['cost'] = $wall_brick_cost ['quantity'] * $wall_brick_price;
//        河沙费用
        $river_sand_cement_area = $covering_layer_area + $floor_tile_area + $wall_area;
        $river_sand_cost = BasisDecorationService::mudMakeCost($river_sand_cement_area, $goods_price, $river_sand_craft, '河沙');

//        厨房/卫生间地砖费用
        $kitchen_and_toilet_floor_tile_area = ($floor_tile_value / 1000) * ($floor_tile_value / 1000);
        if ($kitchen_and_toilet_floor_tile_area == 0)
        {
            $kitchen_and_toilet['quantity'] = 0;
        }else
        {
            $kitchen_and_toilet['quantity'] = ceil(($kitchen_area + $toilet_area) / $kitchen_and_toilet_floor_tile_area);
        }
        $kitchen_and_toilet['cost'] = $kitchen_and_toilet['quantity'] * $floor_tile_price;
//        客厅地砖费用
        $drawing_room_floor_tile_area = ($drawing_room_value / 1000) * ($drawing_room_value / 1000);
        $drawing_room_cost['quantity'] = ceil($drawing_room_area * $drawing_room_floor_tile_area);
        $drawing_room_cost['cost'] = $drawing_room_cost['quantity'] * $drawing_room_price;
        //        地砖费用
        $floor_tile_cost = $kitchen_and_toilet['cost'] + $drawing_room_cost['cost'];
        //材料总费用
        $material_cost_total = $floor_tile_cost['cost'] + $river_sand_cost['cost'] + $cement_cost['cost'] + $self_leveling_cost['cost'] + $wall_brick_cost['cost'];

        //添加材料费用
        $add_price_area = DecorationAdd::AllArea('泥作', $post['area'], $post['city']);
        $add_price = [];
        foreach ($add_price_area as $add_area)
        {
            $sku_area =  Goods::skuAll($add_area['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_area['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }

        $add_price_series = DecorationAdd::AllSeries('泥作', $post['series'], $post['city']);
        foreach ($add_price_series as $add_series)
        {
            $sku_area =  Goods::skuAll($add_series['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_series['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }
        $add_price_style = DecorationAdd::AllStyle('泥作', $post['style'], $post['city']);
        foreach ($add_price_style as $add_style)
        {
            $sku_area =  Goods::skuAll($add_style['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_style['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }

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
                'add_price' => $add_price,
                'goods_price' => $goods_price
            ]
        ]);
    }

    /**
     * 杂工
     */
    public function actionHandyman()
    {
//        $receive = \Yii::$app->request->post();
//        $post = Json::decode($receive);
        $post = [
            'province' => 510000,
            'city' => 510100,
            '12_dismantle' => 20,
            '24_dismantle' => 25,
            'repair' => 22,
            '12_new_construction' => 20,
            '24_new_construction' => 25,
            'building_scrap' => true,
            'area' =>62,
            'series' =>1,
            'style' =>1
        ];
        $handyman = '杂工';
        $labor = LaborCost::univalence($post, $handyman);

//        总天数
        $total_day = BasisDecorationService::wallArea($post, $labor);

//        清运建渣费用
        $craft = EngineeringStandardCraft::findByAll($handyman, $post['city']);
        if ($post['building_scrap'] == true) {
            $building_scrap = BasisDecorationService::haveBuildingScrap($post, $craft);
        } else {
            $building_scrap = BasisDecorationService::nothingBuildingScrap($post, $craft);
        }

//        总人工费
        $labor_cost = $total_day['total_day'] * $labor[0]['univalence'] + $building_scrap['cost'];

        //材料费
        $cement = '水泥';
        $cement_price = Goods::priceDetail(3, $cement, $post['city']);
        $river_sand = '河沙';
        $river_sand_price = Goods::priceDetail(3, $river_sand, $post['city']);
        $brick = '空心砖';
        $brick_price = Goods::priceDetail(3, $brick, $post['city']);
        $brick_standard = GoodsAttr::findByGoodsId($brick_price['id']);

        //水泥费用
        $cement_cost = BasisDecorationService::cementCost($post, $craft, $cement_price);
        //空心砖费用
        $brick_cost = BasisDecorationService::brickCost($post, $brick_price, $brick_standard);
        //河沙费用
        $river_sand = BasisDecorationService::riverSandCost($post, $river_sand_price, $craft);
        //总材料费
        $total_material_cost = $cement_cost['cost'] + $brick_cost['cost'] + $river_sand['cost'];

        //添加材料费用
        $add_price_area = DecorationAdd::AllArea('杂工', $post['area'], $post['city']);
        $add_price = [];
        foreach ($add_price_area as $add_area)
        {
            $sku_area =  Goods::skuAll($add_area['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_area['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }

        $add_price_series = DecorationAdd::AllSeries('杂工', $post['series'], $post['city']);
        foreach ($add_price_series as $add_series)
        {
            $sku_area =  Goods::skuAll($add_series['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_series['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }
        $add_price_style = DecorationAdd::AllStyle('杂工', $post['style'], $post['city']);
        foreach ($add_price_style as $add_style)
        {
            $sku_area =  Goods::skuAll($add_style['sku']);
            if ($sku_area !== null)
            {
                $add_price [] = $add_style['quantity'] *  $sku_area['platform_price'];
            }else
            {
                $add_price [] = 0;
            }
        }

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'total_material_cost' => $total_material_cost,
                'labor_cost' => $labor_cost,
                'cement_cost' => $cement_cost,
                'brick_cost' => $brick_cost,
                'river_sand' => $river_sand,
                'add_price' => $add_price,

            ]
        ]);
    }

    /**
     * 主材
     */
    public function actionPrincipalMaterial()
    {
//        $post = Yii::$app->request->port();
//        $data = Json::decode($post);
//        $data = [
//            'wall_brick_cost'=> $wall_brick_cost,
//        ];
//        $material = '主材';
//        $material_property_classify = MaterialPropertyClassify::findByAll($material);
//        $goods = Goods::categoryById($material_property_classify);
//        var_dump($goods);exit;
            $a = [
              'a' => [
                  'a'=>123,
                  'b'=>456,
                  'c'=>789,
                  'd'=>1122,
              ],
                'b' => [
                    'a'=>11222,
                    'b'=>147,
                    'c'=>258,
                    'd'=>369,
                ],

            ];
            foreach ($a as $k=>$v)
            {
                if ($v['a'] ==$v['a'] ) {
                    echo '$a相同';
                    exit;
                }else
                {
                    echo 'butong';
                    exit;
                }
            }

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
            'waterproof_total_area' => 50
        ];
        $classify = '软装配套';
        $material_property_classify = MaterialPropertyClassify::findByAll($classify);
        $goods = Goods::categoryById($material_property_classify);
        foreach ($goods as &$one_goods) {
            foreach ($material_property_classify as $quantity) {
                if ($one_goods['title'] == $quantity['material']) {
                    $one_goods['show_price'] = $one_goods['platform_price'] * $quantity['quantity'];
                    $one_goods['show_quantity'] = $quantity['quantity'];
                }

                if ($one_goods['title'] == '客厅装饰吊灯') {
                    $one_goods['show_price'] = $one_goods['platform_price'] * $post['sitting_room'];
                    $one_goods['show_quantity'] = $post['sitting_room'];;
                }

                if ($one_goods['title'] == '次卧装饰吊灯') {
                    $one_goods['show_price'] = $one_goods['platform_price'] * $post['master_bedroom'] - 1;
                    $one_goods['show_quantity'] = $post['master_bedroom'];;
                }

                if ($one_goods['title'] == '厨房平板灯') {
                    $one_goods['show_price'] = $one_goods['platform_price'] * $post['kitchen'];
                    $one_goods['show_quantity'] = $post['kitchen'];;
                }
                if ($one_goods['title'] == '窗帘') {
                    $curtain_quantity = $post['sitting_room'] + $post['master_bedroom'];
                    $one_goods['show_price'] = $one_goods['platform_price'] * $curtain_quantity;
                    $one_goods['show_quantity'] = $curtain_quantity;
                }
            }
        }
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'goods' => $goods,
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
            'waterproof_total_area' => 50,
            'stairway_id' => 1
        ];
        $classify = '固定家具';
        $material_property_classify = MaterialPropertyClassify::findByAll($classify);
        $goods = Goods::categoryById($material_property_classify);
        foreach ($goods as &$one_goods) {
            $one_goods['show_price'] = $one_goods['platform_price'] * 1;
            $one_goods['show_quantity'] = 1;
            if ($one_goods['title'] == '衣柜') {
                $one_goods['show_price'] = $one_goods['platform_price'] * $post['master_bedroom'];;
                $one_goods['show_quantity'] = $post['master_bedroom'];;
            }
            if ($one_goods['title'] == '楼梯') {
                if ($post['stairway_id'] >= 1) {
                    $one_goods['show_price'] = $one_goods['platform_price'] * 1;
                    $one_goods['show_quantity'] = 1;
                } else {
                    $one_goods = NULL;
                }
            }
        }
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'goods' => $goods,
            ]
        ]);
    }

    /**
     * 移动家具
     * @return string
     */
    public function actionMoveFurniture()
    {
//       $receive = \Yii::$app->request->post();
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
            'waterproof_total_area' => 50,
        ];
        if ($post['toilet'] >= 2) {
            $drawing_room = $post['sitting_room'] - 1;
        } else {
            $drawing_room = $post['sitting_room'];
        }
        $classify = '移动家具';
        $material_property_classify = MaterialPropertyClassify::findByAll($classify);
        $goods = Goods::categoryById($material_property_classify);
        foreach ($goods as &$one_goods) {
            $one_goods['show_price'] = $one_goods['platform_price'] * 1;
            $one_goods['show_quantity'] = 1;
            if ($one_goods['title'] == '沙发') {
                $one_goods['show_price'] = $one_goods['platform_price'] * $drawing_room;
                $one_goods['show_quantity'] = $drawing_room;
            }
            if ($one_goods['title'] == '床') {
                $one_goods['show_price'] = $one_goods['platform_price'] * $post['master_bedroom'];
                $one_goods['show_quantity'] = $post['master_bedroom'];
            }
            if ($one_goods['title'] == '床头柜') {
                $one_goods['show_price'] = $one_goods['platform_price'] * ($post['master_bedroom'] * 2);
                $one_goods['show_quantity'] = ($post['master_bedroom'] * 2);
            }
        }
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'goods' => $goods,
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
            'waterproof_total_area' => 50
        ];
        if ($post['toilet'] >= 2) {
            $drawing_room = $post['sitting_room'] - 1;
        } else {
            $drawing_room = $post['sitting_room'];
        }
        $classify = '家电配套';
        $material_property_classify = MaterialPropertyClassify::findByAll($classify);
        $goods = Goods::categoryById($material_property_classify);
        foreach ($goods as &$one_goods) {
            $one_goods['show_price'] = $one_goods['platform_price'] * 1;
            $one_goods['show_quantity'] = 1;
            if ($one_goods['title'] == '油烟机') {
                $one_goods['show_price'] = $one_goods['platform_price'] * $post['kitchen'];
                $one_goods['show_quantity'] = $post['kitchen'];
            }
            if ($one_goods['title'] == '灶具') {
                $one_goods['show_price'] = $one_goods['platform_price'] * $post['kitchen'];
                $one_goods['show_quantity'] = $post['kitchen'];
            }
            if ($one_goods['title'] == '立柜式空调') {
                $one_goods['show_price'] = $one_goods['platform_price'] * $drawing_room;
                $one_goods['show_quantity'] = $drawing_room;
            }
            if ($one_goods['title'] == '挂壁式空调') {
                $one_goods['show_price'] = $one_goods['platform_price'] * $post['master_bedroom'];
                $one_goods['show_quantity'] = $post['master_bedroom'];
            }
            if ($one_goods['title'] == '中央空调') {
                if ($post['series'] >= 2) {
                    $one_goods['show_price'] = $one_goods['platform_price'] * 1;
                    $one_goods['show_quantity'] = 1;
                } else {
                    $one_goods = NULL;
                }
            }
        }

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'goods' => $goods,
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
            'toilet' => 3,
            'kitchen' => 1,
            'style' => 1,
            'series' => 1,
            'province' => 510000,
            'city' => 510100,
            'waterproof_total_area' => 50
        ];

        if ($post['toilet'] >= 2) {
            $toilet = $post['toilet'] - 1;
        } else {
            $toilet = $post['toilet'];
        }
        $classify = '生活配套';
        $material_property_classify = MaterialPropertyClassify::findByAll($classify);
        $goods = Goods::categoryById($material_property_classify);
        foreach ($goods as &$one_goods) {
            $one_goods['show_price'] = $one_goods['platform_price'] * 1;
            $one_goods['show_quantity'] = 1;
            if ($one_goods['title'] == '马桶') {
                $one_goods['show_price'] = $one_goods['platform_price'] * $toilet;
                $one_goods['show_quantity'] = $post['toilet'];
            }
            if ($one_goods['title'] == '蹲便器') {
                $one_goods['show_price'] = $one_goods['platform_price'] * $toilet;
                $one_goods['show_quantity'] = $post['toilet'];
            }
            if ($one_goods['title'] == '浴柜') {
                $one_goods['show_price'] = $one_goods['platform_price'] * $post['toilet'];
                $one_goods['show_quantity'] = $post['toilet'];
            }
            if ($one_goods['title'] == '花洒套装') {
                $one_goods['show_price'] = $one_goods['platform_price'] * $post['toilet'];
                $one_goods['show_quantity'] = $post['toilet'];
            }
            if ($one_goods['title'] == '淋浴隔断') {
                $one_goods['show_price'] = $one_goods['platform_price'] * $post['toilet'];
                $one_goods['show_quantity'] = $post['toilet'];
            }
            if ( $one_goods['title'] == '床垫') {
                $one_goods['show_price'] = $one_goods['platform_price'] * $post['master_bedroom'];
                $one_goods['show_quantity'] = $post['master_bedroom'];
            }
        }

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'goods' => $goods,
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
            'series' => 2,
            'province' => 510000,
            'city' => 510100,
            'waterproof_total_area' => 50
        ];

        $classify = '智能配套';
        $material_property_classify = MaterialPropertyClassify::findByAll($classify);
        $goods = Goods::categoryById($material_property_classify);
        foreach ($goods as &$one_goods) {
            $one_goods['show_price'] = $one_goods['platform_price'] * 1;
            $one_goods['show_quantity'] = 1;
        }
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'goods' => $goods,
            ]
        ]);
    }


    /**
     * Owner certification action(app)
     *
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

        $user = Yii::$app->user->identity;
        $user->legal_person = $legalPerson;
        $user->identity_no = $identityNo;
        $user->identity_card_front_image = $identityCardFrontImage;
        $user->identity_card_back_image = $identityCardBackImage;

        if (!$user->validateIdentityNo() || !$user->validateLegalPerson()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$user->save()) {
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
     * View owner action
     *
     * @return string
     */
    public function actionViewIdentity()
    {
        $user = Yii::$app->user->identity;

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'view-identity' => ModelService::viewModelByFields($user, User::FIELDS_VIEW_IDENTITY),
            ],
        ]);
    }
}