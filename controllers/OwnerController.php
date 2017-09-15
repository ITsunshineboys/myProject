<?php

namespace app\controllers;

use app\models\BrainpowerInitalSupervise;
use app\models\CarpentryAdd;
use app\models\DecorationAdd;
use app\models\Effect;
use app\models\EffectPicture;
use app\models\EngineeringStandardCraft;
use app\models\EngineeringUniversalCriterion;
use app\models\Goods;
use app\models\GoodsAttr;
use app\models\GoodsCategory;
use app\models\LaborCost;
use app\models\MaterialPropertyClassify;
use app\models\Points;
use app\models\PointsTotal;
use app\models\Series;
use app\models\StairsDetails;
use app\models\Style;
use app\models\WorkerCraftNorm;
use app\models\WorksBackmanData;
use app\models\WorksData;
use app\services\BasisDecorationService;
use app\services\ExceptionHandleService;
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
     * Series style and  stair list interface
     * @return string
     */
    public function actionSeriesAndStyle()
    {
        $show['stairs_details'] = StairsDetails::findByAll();
        $show['series'] = Series::findByAll();
        $show['style'] = Style::findByAll();
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'show' => $show,
            ]
        ]);
    }

    /**
     * Search interface
     * @return string
     */
    public function actionSearch()
    {
        $post = Yii::$app->request->post();
        if (array_key_exists('id', $post)) {
            $list_effect = Effect::find()->where(['id' => $post['id']])->one();
            $list_effect_picture = EffectPicture::find()->where(['id' => $list_effect['id']])->all();
            $effect = Effect::districtSearch($list_effect['toponymy']);
            $id = [];
            foreach ($effect as $one_effect) {
                $id [] = $one_effect['id'];
            }
            $effect_picture = EffectPicture::find()
                ->where(['in', 'effect_id', $id])
                ->all();
        } elseif (array_key_exists('str', $post)) {
            if ($post['str'] !== null) {
                $list_effect = null;
                $list_effect_picture = null;
                $effect = Effect::districtSearch($post['str']);
                $id = [];
                foreach ($effect as $one_effect) {
                    $id = $one_effect['id'];
                }
                $effect_picture = EffectPicture::find()
                    ->asArray()
                    ->where(['in', 'id', $id])
                    ->all();
            } else {
                $list_effect = null;
                $list_effect_picture = null;
                $effect = null;
                $effect_picture = null;
            }

        } elseif ($post == null) {
            $list_effect = null;
            $list_effect_picture = null;
            $effect = null;
            $effect_picture = null;
        }

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'list_effect' => $list_effect,
                'list_effect_picture' => $list_effect_picture,
                'effect' => $effect,
                'effect_picture' => $effect_picture,
            ]
        ]);
    }

    /**
     * classify interface
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
        $workers = LaborCost::profession($post['city'],'水电工');
        $worker_kind_details = WorkerCraftNorm::findByLaborCostId($workers['id'],'弱电点位');

        //      点位 和 材料查询
        $points = Points::weakPoints();
        $weak_current_all = [];
        foreach ($points as $v => $k) {
            if ($k['weak_current_points'] !== 0) {
                $weak_current_all[$k['place']] = $k['weak_current_points'];
            }
        }
        $sitting_room = $weak_current_all['客餐厅'] * $post['hall'];
        $secondary_bedroom = $weak_current_all['卧室'] * $post['bedroom'];
        $weak_points = $sitting_room + $secondary_bedroom;

        //查询弱电所需要材料
        $material = ['网线', '线管', '底盒'];
        $goods = Goods::priceDetail(3, $material);
        $judge = BasisDecorationService::priceConversion($goods);
        $weak_current = BasisDecorationService::judge($judge, $post);

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll('弱电', $post['city']);

        //人工总费用
        $labor_all_cost['price'] = BasisDecorationService::laborFormula($weak_points,$workers,$worker_kind_details);
        $labor_all_cost['worker_kind'] = $workers['worker_kind'];

        //材料总费用
        $material_price = BasisDecorationService::quantity($weak_points, $weak_current, $craft);
        $material = BasisDecorationService::electricianMaterial($weak_current, $material_price);

        //添加材料
        $add_price_area = DecorationAdd::AllArea('弱电', $post['area'], $post['city']);
        $add_price = [];
        foreach ($add_price_area as $add_area) {
            $sku_area = Goods::skuAll($add_area['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_area['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

        $add_price_series = DecorationAdd::AllSeries('弱电', $post['series'], $post['city']);
        foreach ($add_price_series as $add_series) {
            $sku_area = Goods::skuAll($add_series['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_series['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

        $add_price_style = DecorationAdd::AllStyle('弱电', $post['style'], $post['city']);
        foreach ($add_price_style as $add_style) {
            $sku_area = Goods::skuAll($add_style['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_style['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'weak_current_labor_price' => $labor_all_cost,
                'weak_current_material' => $material,
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
        $post = \Yii::$app->request->post();
        $workers = LaborCost::profession($post, '水电工');
        $worker_kind_details = WorkerCraftNorm::findByLaborCostId($workers['id'],'强电点位');
        $points = Points::strongPointsAll();
        $points_total = PointsTotal::findByAll($points);
        $points_details = BasisDecorationService::strongCurrentPoints($points_total, $post);

        //查询弱电所需要材料
        $material = ['电线', '线管', '底盒'];
        $goods = Goods::priceDetail(3, $material);
        $judge = BasisDecorationService::priceConversion($goods);
        $strong_current = BasisDecorationService::judge($judge, $post);

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll('强电', $post['city']);

        //人工总费用
        $labor_all_cost['price'] = BasisDecorationService::laborFormula($points_details,$workers,$worker_kind_details);
        $labor_all_cost['worker_kind'] = $workers['worker_kind'];

        //材料总费用
        $material_price = BasisDecorationService::quantity($points_details, $strong_current, $craft);
        $material = BasisDecorationService::electricianMaterial($strong_current, $material_price);

        $add_price_area = DecorationAdd::AllArea('强电', $post['area'], $post['city']);
        $add_price = [];
        foreach ($add_price_area as $add_area) {
            $sku_area = Goods::skuAll($add_area['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_area['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

        $add_price_series = DecorationAdd::AllSeries('强电', $post['series'], $post['city']);
        foreach ($add_price_series as $add_series) {
            $sku_area = Goods::skuAll($add_series['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_series['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

        $add_price_style = DecorationAdd::AllStyle('弱电', $post['style'], $post['city']);
        foreach ($add_price_style as $add_style) {
            $sku_area = Goods::skuAll($add_style['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_style['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'strong_current_labor_price' => $labor_all_cost,
                'strong_current_material' => $material,
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
        $post = \Yii::$app->request->post();
        //人工价格
        $waterway_labor = LaborCost::profession($post, '水电工');
        $worker_kind_details = WorkerCraftNorm::findByLaborCostId($waterway_labor['id'],'水路点位');

        //点位 和材料 查询
        $points = Points::waterwayPoints();
        $other = 0;
        foreach ($points as $v => $k) {
            if ($k['waterway_points'] !== 0) {
                $waterway_current_all[$k['place']] = $k['waterway_points'];
            }
            if ($k['place'] !== '厨房' && $k['place'] !== '卫生间') {
                $other += $k['waterway_points'];
            }
        }
        $kitchen = $waterway_current_all['厨房'] * $post['kitchen'];
        $toilet = $waterway_current_all['卫生间'] * $post['toilet'];
        $waterway_points = $kitchen + $toilet + $other;

        //查询弱电所需要材料
        $material = ['PPR水管', 'PVC管'];
        $goods = Goods::priceDetail(3, $material);
        $judge = BasisDecorationService::priceConversion($goods);
        $waterway_current = BasisDecorationService::judge($judge, $post);

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll('水路', $post['city']);

        //人工总费用
        $labor_all_cost['price'] = BasisDecorationService::laborFormula($waterway_points, $waterway_labor,$worker_kind_details);
        $labor_all_cost['worker_kind'] = $waterway_labor['worker_kind'];
        //材料总费用
        $material_price = BasisDecorationService::waterwayGoods($waterway_points, $waterway_current, $craft);
        $material = BasisDecorationService::waterwayMaterial($waterway_current, $material_price);

        //添加材料费用
        $add_price_area = DecorationAdd::AllArea('水路', $post['area'], $post['city']);
        $add_price = [];
        foreach ($add_price_area as $add_area) {
            $sku_area = Goods::skuAll($add_area['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_area['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

        $add_price_series = DecorationAdd::AllSeries('水路', $post['series'], $post['city']);
        foreach ($add_price_series as $add_series) {
            $sku_area = Goods::skuAll($add_series['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_series['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }
        $add_price_style = DecorationAdd::AllStyle('水路', $post['style'], $post['city']);
        foreach ($add_price_style as $add_style) {
            $sku_area = Goods::skuAll($add_style['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_style['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'waterway_labor_price' => $labor_all_cost,
                'waterway_material_price' => $material,
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
        $post = \Yii::$app->request->post();
        //人工价格
        $waterproof_labor = LaborCost::profession($post, '防水工');
        $worker_kind_details = WorkerCraftNorm::findByLaborCostId($waterproof_labor['id'],'做工面积');
        //防水所需材料

        //查询弱电所需要材料
        $material = ['防水涂料'];
        $goods = Goods::priceDetail(3, $material);
        $judge = BasisDecorationService::priceConversion($goods);
        $waterproof = BasisDecorationService::judge($judge, $post);

        //厨房
        $kitchen = EngineeringUniversalCriterion::findByAll('厨房');
        $kitchen_area = BasisDecorationService::waterproofArea($kitchen, $post['area'], $post['kitchen']);
        //卫生间
        $toilet = EngineeringUniversalCriterion::findByAll('卫生间');
        $toilet_area = BasisDecorationService::waterproofArea($toilet, $post['area'], $post['toilet']);
        //总面积
        $total_area = $kitchen_area + $toilet_area;

        //当地工艺
        $craft = EngineeringStandardCraft::findByAll('防水', $post['city']);

        //人工总费用（防水总面积÷【每天做工面积】）×【工人每天费用】
        $labor_all_cost['price'] = ceil($total_area / $worker_kind_details['quantity']) * $waterproof_labor['univalence'];
        $labor_all_cost['worker_kind'] = $waterproof_labor['worker_kind'];

        //材料总费用
        $material_price = BasisDecorationService::waterproofGoods($total_area, $waterproof, $craft);
        $material_total = [];
        foreach ($waterproof as $one_waterproof) {
            if ($one_waterproof['title'] == '防水涂料') {
                $one_waterproof['quantity'] = $material_price['quantity'];
                $one_waterproof['cost'] = $material_price['cost'];
                $goods_max [] = $one_waterproof;
            }
        }
        $material_total ['material'][] = BasisDecorationService::profitMargin($goods_max);
        $material_total['total_cost'][] = $material_price['cost'];

        //添加材料费用
        $add_price_area = DecorationAdd::AllArea('防水', $post['area'], $post['city']);
        $add_price = [];
        foreach ($add_price_area as $add_area) {
            $sku_area = Goods::skuAll($add_area['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_area['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

        $add_price_series = DecorationAdd::AllSeries('防水', $post['series'], $post['city']);
        foreach ($add_price_series as $add_series) {
            $sku_area = Goods::skuAll($add_series['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_series['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }
        $add_price_style = DecorationAdd::AllStyle('防水', $post['style'], $post['city']);
        foreach ($add_price_style as $add_style) {
            $sku_area = Goods::skuAll($add_style['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_style['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'waterproof_labor_price' => $labor_all_cost,
                'waterproof_material' => $material_total,
                'waterproof_add_price' => $add_price,
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
        $labor_cost = LaborCost::profession($post, '木工');
        $price = $labor_cost['univalence'];
        $worker_kind_details = WorkerCraftNorm::findByLaborCostAll($labor_cost['id']);
        foreach ($worker_kind_details as $one_labor) {
            switch ($one_labor) {
                case $one_labor['worker_kind_details'] == '平顶面积':
                    $flat = $one_labor['quantity'];
                    break;
                case $one_labor['worker_kind_details'] == '造型长度':
                    $modelling = $one_labor['quantity'];
                    break;
            }
        }
        $series_all = Series::find()->asArray()->all();
        $style_all = Style::find()->asArray()->all();
        $carpentry_add = CarpentryAdd::findByStipulate($post['series'], $post['style']);
        // 造型长度
        $modelling_length = BasisDecorationService::carpentryModellingLength($carpentry_add, $series_all, $post['series']);
        //造型天数
        $modelling_day = BasisDecorationService::carpentryModellingDay($modelling_length, $modelling, $series_all, $style_all, $post['series']);
        //平顶天数
        $flat_day = BasisDecorationService::flatDay($carpentry_add, $flat, $series_all, $style_all, $post['series']);

        //人工费
        $labour_charges['price'] = BasisDecorationService::carpentryLabor($modelling_day, $flat_day, 1, $price);
        $labour_charges['worker_kind'] = '木工';
        //材料
        $material = ['石膏板', '龙骨', '丝杆'];
        $goods = Goods::priceDetail(3, $material);
        $judge = BasisDecorationService::priceConversion($goods);
        $goods_price = BasisDecorationService::judge($judge, $post);
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

        $material_total = [];
        foreach ($goods_price as $one_goods_price) {
            switch ($one_goods_price) {
                case $one_goods_price['title'] == '石膏板':
                    $goods_max = BasisDecorationService::profitMargin($one_goods_price);
                    $goods_max['quantity'] = $plasterboard_cost['quantity'];
                    $goods_max['cost'] = $plasterboard_cost['cost'];
                    $material_total ['material'][] = $goods_max;
                    break;
                case $one_goods_price['title'] == '龙骨':
                    $goods_max = BasisDecorationService::profitMargin($one_goods_price);
                    $goods_max['quantity'] = $keel_cost['quantity'];
                    $goods_max['cost'] = $keel_cost['cost'];
                    $material_total ['material'][] = $goods_max;
                    break;
                case $one_goods_price['title'] == '丝杆':
                    $goods_max = BasisDecorationService::profitMargin($one_goods_price);
                    $goods_max['quantity'] = $pole_cost['quantity'];
                    $goods_max['cost'] = $pole_cost['cost'];
                    $material_total ['material'][] = $goods_max;
                    break;
            }
        }
        $material_total['total_cost'][] = $material_cost;

//      添加费用
        $add_price_area = DecorationAdd::AllArea('木作', $post['area'], $post['city']);
        $add_price = [];
        foreach ($add_price_area as $add_area) {
            $sku_area = Goods::skuAll($add_area['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_area['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

        $add_price_series = DecorationAdd::AllSeries('木作', $post['series'], $post['city']);
        foreach ($add_price_series as $add_series) {
            $sku_area = Goods::skuAll($add_series['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_series['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }
        $add_price_style = DecorationAdd::AllStyle('木作', $post['style'], $post['city']);
        foreach ($add_price_style as $add_style) {
            $sku_area = Goods::skuAll($add_style['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_style['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

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
        $labor_costs = LaborCost::profession($post,'油漆工');
        $worker_kind_details = WorkerCraftNorm::findByLaborCostAll($labor_costs['id']);

        foreach ($worker_kind_details as $labor_cost) {
            switch ($labor_cost) {
                case $labor_cost['worker_kind_details'] == '乳胶漆底漆面积':
                    $primer = $labor_cost['quantity'];
                    break;
                case $labor_cost['worker_kind_details'] == '乳胶漆面漆面积':
                    $finishing_coat = $labor_cost['quantity'];
                    break;
                case $labor_cost['worker_kind_details'] == '阴角线长度':
                    $concave_line = $labor_cost['quantity'];
                    break;
                case $labor_cost['worker_kind_details'] == '腻子面积':
                    $putty = $labor_cost['quantity'];
                    break;
            }
        }
        $project = '油漆';
        $areas = EngineeringUniversalCriterion::findByAll($project);
        $area['masterBedroom_area'] = 0;
        $area['sittingRoom_diningRoom_area'] = 0;
        foreach ($areas as $one) {
            switch ($one) {
                case $one['project_particulars'] == '卧室面积':
                    $area['masterBedroom_area'] = $one['project_value'];
                    $tall = $one['storey'];
                    break;
                case $one['project_particulars'] == '客厅面积':
                    $area['sittingRoom_diningRoom_area'] = $one['project_value'];
                    $tall = $one['storey'];
                    break;
            }
        }
        //卧室底漆面积
        $bedroom_primer_area = BasisDecorationService::paintedArea($area['masterBedroom_area'], $post['area'], $post['bedroom'], 2.8, 4);

        //客餐厅底漆面积
        $drawing_room_primer_area = BasisDecorationService::paintedArea($area['sittingRoom_diningRoom_area'], $post['area'], $post['hall'], 2.8, 3);
//        乳胶漆底漆面积：卧室底漆面积+客厅底漆面积+餐厅底漆面积+其它面积1
        $primer_area = $bedroom_primer_area + $drawing_room_primer_area;
//        乳胶漆底漆天数：乳胶漆底漆面积÷【每天做乳胶漆底漆面积】
        $primer_day = $primer_area / $primer;

        //乳胶漆面漆面积
        $finishing_coat_area = $primer_area * 2;
//        乳胶漆面漆天数：乳胶漆面漆面积÷【每天做乳胶漆面漆面积】
        $finishing_coat_day = $finishing_coat_area / $finishing_coat;

//        卧室周长
        $bedroom_primer_perimeter = BasisDecorationService::paintedPerimeter($area['masterBedroom_area'], $post['area'], $post['bedroom'], 4);
//        客厅周长
        $drawing_room_perimeter = BasisDecorationService::paintedPerimeter($area['sittingRoom_diningRoom_area'], $post['area'], $post['hall'], 3);
//        阴角线长度
        $concave_line_length = $bedroom_primer_perimeter + $drawing_room_perimeter;
//        阴角线天数：阴角线长度÷【每天做阴角线长度】
        $concave_line_day = $concave_line_length / $concave_line;

//        腻子卧室墙面积
        $putty_bedroom_area = BasisDecorationService::paintedArea($area['masterBedroom_area'], $post['area'], $post['bedroom'], 2.8, 4);

//        腻子客餐厅面积
        $putty_drawing_room_area = BasisDecorationService::paintedArea($area['sittingRoom_diningRoom_area'], $post['area'], $post['hall'], 2.8, 3);
//        腻子面积 卧室腻子面积+客厅腻子面积
        $putty_area = $putty_bedroom_area + $putty_drawing_room_area;
//        腻子天数 腻子面积÷【每天做腻子面积】
        $putty_day = $putty_area / $putty;

        $material = ['腻子', '乳胶漆底漆', '乳胶漆面漆', '阴角线', '石膏粉'];
        $goods = Goods::priceDetail(3, $material);
        $goods_price = BasisDecorationService::priceConversion($goods);

        //当地工艺
        $crafts = EngineeringStandardCraft::findByAll('乳胶漆', $post['city']);
        $series_and_style = BasisDecorationService::coatingSeriesAndStyle($goods_price, $crafts, $post);
        foreach ($crafts as $craft) {
            switch ($craft) {
                case $craft['project_details'] == '腻子':
                    $putty_craft = $craft;
                    break;
                case $craft['project_details'] == '乳胶漆底漆':
                    $primer_craft = $craft;
                    break;
                case $craft['project_details'] == '乳胶漆面漆':
                    $finishing_coat_craft = $craft;
                    break;
                case $craft['project_details'] == '阴角线':
                    $concave_line_craft = $craft;
                    break;
                case $craft['project_details'] == '石膏粉':
                    $gypsum_powder_craft = $craft;
                    break;
            }
        }

        var_dump($series_and_style['putty']);
        var_dump($putty_craft);
        var_dump($putty_area);exit;
//        腻子费用
        $putty_cost = BasisDecorationService::paintedCost($series_and_style['putty'], $putty_craft, $putty_area);
////        底漆费用
        $primer_cost = BasisDecorationService::paintedCost($series_and_style['primer'], $primer_craft, $primer_area);
//        乳胶漆面漆费用
        $finishing_coat_cost = BasisDecorationService::paintedCost($series_and_style['finishing_coat'], $finishing_coat_craft, $finishing_coat_area);
//        阴角线费用
        $concave_line_cost = BasisDecorationService::paintedCost($series_and_style['concave_line'], $concave_line_craft, $concave_line_length);

//        石膏粉费用   石膏粉费用：个数×商品价格
//        个数：（【3元】×乳胶漆面漆面积÷商品价格）
        $gypsum_powder_cost['quantity'] = ceil($gypsum_powder_craft['material'] * $primer_area / $series_and_style['gypsum_powder']['platform_price']);
        $gypsum_powder_cost['cost'] = $gypsum_powder_cost['quantity'] * $series_and_style['gypsum_powder']['platform_price'];

        //总费用
        $total_cost = $putty_cost['cost'] + $primer_cost['cost'] + $finishing_coat_cost['cost'] + $concave_line_cost['cost'] + $gypsum_powder_cost['cost'];
        $material_total = [];
        foreach ($series_and_style as $one_goods_price) {
            switch ($one_goods_price) {
                case $one_goods_price['title'] == '腻子':
                    $one_goods_price['quantity'] = $putty_cost['quantity'];
                    $one_goods_price['cost'] = $putty_cost['cost'];
                    $material_total['material'][] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == '乳胶漆底漆':
                    $one_goods_price['quantity'] = $primer_cost['quantity'];
                    $one_goods_price['cost'] = $primer_cost['cost'];
                    $material_total ['material'][] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == '乳胶漆面漆':
                    $one_goods_price['quantity'] = $finishing_coat_cost['quantity'];
                    $one_goods_price['cost'] = $finishing_coat_cost['cost'];
                    $material_total ['material'][] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == '阴角线':
                    $one_goods_price['quantity'] = $concave_line_cost['quantity'];
                    $one_goods_price['cost'] = $concave_line_cost['cost'];
                    $material_total ['material'][] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == '石膏粉':
                    $one_goods_price['quantity'] = $gypsum_powder_cost['quantity'];
                    $one_goods_price['cost'] = $gypsum_powder_cost['cost'];
                    $material_total ['material'][] = $one_goods_price;
                    break;
            }
        }
        $material_total['total_cost'] = $total_cost;
        //总天数   乳胶漆天数+阴角线天数+腻子天数
        $total_day = ceil($primer_day + $finishing_coat_day + $putty_day + $concave_line_day);

        //总人工费   人工费：（总天数）×【工人每天费用】
        $coating_labor_price['price'] = $total_day * $labor_costs['univalence'];
        $coating_labor_price['worker_kind'] = $labor_costs['worker_kind'];

        //添加材料费用
        $add_price_area = DecorationAdd::AllArea('油漆', $post['area'], $post['city']);
        $add_price = [];
        foreach ($add_price_area as $add_area) {
            $sku_area = Goods::skuAll($add_area['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_area['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

        $add_price_series = DecorationAdd::AllSeries('油漆', $post['series'], $post['city']);
        foreach ($add_price_series as $add_series) {
            $sku_area = Goods::skuAll($add_series['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_series['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }
        $add_price_style = DecorationAdd::AllStyle('油漆', $post['style'], $post['city']);
        foreach ($add_price_style as $add_style) {
            $sku_area = Goods::skuAll($add_style['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_style['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'coating_labor_price' => $coating_labor_price,
                'coating_material' => $material_total,
                'add_price' => $add_price,
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
        $labor_costs = LaborCost::profession($post,'泥瓦工');
        $labor_day_cost = $labor_costs['univalence'];
        $worker_kind_details = WorkerCraftNorm::findByLaborCostAll($labor_costs['id']);
        foreach ($worker_kind_details as $labor_cost) {
            switch ($labor_cost) {
                case $labor_cost['worker_kind_details'] == '保护层长度':
                    $covering_layer_day_area = $labor_cost['quantity'];
                    break;
                case $labor_cost['worker_kind_details'] == '贴地砖面积':
                    $geostrophy_day_area = $labor_cost['quantity'];
                    break;
                case $labor_cost['worker_kind_details'] == '贴墙砖面积':
                    $wall_tile_day_area = $labor_cost['quantity'];
                    break;
            }
        }
        //泥作面积
        //厨房面积
        $kitchen_particulars = EngineeringUniversalCriterion::mudMakeArea('厨房', '厨房面积');
        $kitchen_area = $post['area'] * $kitchen_particulars['project_value'];
        //卫生间面积
        $toilet_particulars = EngineeringUniversalCriterion::mudMakeArea('卫生间', '卫生间面积');
        $toilet_area = $post['area'] * $toilet_particulars['project_value'];
        //客餐厅面积
        $drawing_room_particulars = EngineeringUniversalCriterion::mudMakeArea('客厅', '客厅面积');
        $drawing_room_area = $post['area'] * $drawing_room_particulars['project_value'];


        //当地工艺
        $craft = EngineeringStandardCraft::findByAll('泥工', $post['city']);
        foreach ($craft as $local_craft) {
            switch ($local_craft) {
                case $local_craft['project_details'] == '贴砖':
                    $wall_height = $local_craft['material'];
                    break;
                case $local_craft['project_details'] == '水泥':
                    $cement_craft = $local_craft['material'];
                    break;
                case $local_craft['project_details'] == '自流平':
                    $self_leveling_craft = $local_craft['material'];
                    break;
                case $local_craft['project_details'] == '河沙':
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
        $total_labor_cost['price'] = $total_day * $labor_day_cost;
        $total_labor_cost['worker_kind'] = '泥工';

        //材料费
        $material = ['水泥', '自流平', '河沙'];
        $goods = Goods::priceDetail(3, $material);
        $goods_price = BasisDecorationService::priceConversion($goods);
        $goods_attr = BasisDecorationService::mudMakeMaterial($goods_price);

        $wall_brick = Goods::seriesAndStyle(3, '墙砖', $post);
        $wall_brick_price = BasisDecorationService::priceConversion($wall_brick);
        $wall_brick_max = BasisDecorationService::profitMargin($wall_brick_price);
        $wall_brick_area = BasisDecorationService::wallBrickAttr($wall_brick_max['id']);

        $floor_tile = Goods::seriesAndStyle(3, '地砖', $post);
        $floor_tile_price = BasisDecorationService::priceConversion($floor_tile);
        $floor_tile_attr = BasisDecorationService::floorTile($floor_tile_price);

//        水泥费用
        $cement_area = $covering_layer_area + $floor_tile_area + $wall_area;
        $cement_cost = BasisDecorationService::mudMakeCost($cement_area, $goods_price, $cement_craft, $goods_attr, '水泥');

//        自流平费用
        $self_leveling_area = $drawing_room_area;
        $self_leveling_cost = BasisDecorationService::mudMakeCost($self_leveling_area, $goods_price, $self_leveling_craft, $goods_attr, '自流平');

        //        河沙费用
        $river_sand_cement_area = $covering_layer_area + $floor_tile_area + $wall_area;
        $river_sand_cost = BasisDecorationService::mudMakeCost($river_sand_cement_area, $goods_price, $river_sand_craft, $goods_attr, '河沙');

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

        //材料总费用
        $material_cost_total = $cement_cost['cost'] + $self_leveling_cost['cost'] + $river_sand_cost['cost'] + $wall_brick_cost['cost'] + $toilet_wall_brick_cost['cost'] + $kitchen_wall_brick_cost['cost'] + $hall_wall_brick_cost['cost'];

        foreach ($goods_price as $one_goods_price) {
            switch ($one_goods_price) {
                case $one_goods_price['title'] == '河沙':
                    $one_goods_price['quantity'] = $river_sand_cost['quantity'];
                    $one_goods_price['cost'] = $river_sand_cost['cost'];
                    $material_total ['material'][] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == '水泥':
                    $one_goods_price['quantity'] = $cement_cost['quantity'];
                    $one_goods_price['cost'] = $cement_cost['cost'];
                    $material_total ['material'][] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == '自流平':
                    $one_goods_price['quantity'] = $self_leveling_cost['quantity'];
                    $one_goods_price['cost'] = $self_leveling_cost['cost'];
                    $material_total ['material'][] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == '墙砖':
                    $one_goods_price['quantity'] = $wall_brick_cost['quantity'];
                    $one_goods_price['cost'] = $wall_brick_cost['cost'];
                    $material_total ['material'][] = $one_goods_price;
                    break;
            }
        }
        $wall_brick_max['quantity'] = $wall_brick_cost['quantity'];
        $wall_brick_max['cost'] = $wall_brick_cost['cost'];
        $material_total ['material'][] = $wall_brick_max;

        foreach ($floor_tile_price as $one_floor_tile_price) {
            switch ($one_floor_tile_price) {
                case $one_floor_tile_price['id'] == $floor_tile_attr['hall']['id']:
                    $one_floor_tile_price['quantity'] = $hall_wall_brick_cost['quantity'];
                    $one_floor_tile_price['cost'] = $hall_wall_brick_cost['cost'];
                    $material_total ['material'][] = $one_floor_tile_price;
                    break;
                case $one_floor_tile_price['id'] == $floor_tile_attr['kitchen']['id']:
                    $one_floor_tile_price['quantity'] = $kitchen_wall_brick_cost['quantity'];
                    $one_floor_tile_price['cost'] = $kitchen_wall_brick_cost['cost'];
                    $material_total ['material'][] = $one_floor_tile_price;
                    break;
                case $one_floor_tile_price['id'] == $floor_tile_attr['toilet']['id']:
                    $one_floor_tile_price['quantity'] = $toilet_wall_brick_cost['quantity'];
                    $one_floor_tile_price['cost'] = $toilet_wall_brick_cost['cost'];
                    $material_total['material'][] = $one_floor_tile_price;
                    break;
            }
        }
        $material_total['total_cost'] = $material_cost_total;

        //添加材料费用
        $add_price_area = DecorationAdd::AllArea('泥作', $post['area'], $post['city']);
        $add_price = [];
        foreach ($add_price_area as $add_area) {
            $sku_area = Goods::skuAll($add_area['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_area['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

        $add_price_series = DecorationAdd::AllSeries('泥作', $post['series'], $post['city']);
        foreach ($add_price_series as $add_series) {
            $sku_area = Goods::skuAll($add_series['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_series['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }
        $add_price_style = DecorationAdd::AllStyle('泥作', $post['style'], $post['city']);
        foreach ($add_price_style as $add_style) {
            $sku_area = Goods::skuAll($add_style['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_style['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'mud_make_labor_price' => $total_labor_cost,
                'mud_make_material' => $material_total,
                'add_price' => $add_price,
            ]
        ]);
    }

    /**
     * 杂工
     */
    public function actionHandyman()
    {
        $post = \Yii::$app->request->post();
        $handyman = '杂工';
        $labor = LaborCost::profession($post, '杂工');
        $worker_kind_details = WorkerCraftNorm::findByLaborCostAll($labor['id']);
//        总天数
        $total_day = BasisDecorationService::wallArea($post,$worker_kind_details);
//        清运建渣费用
        $craft = EngineeringStandardCraft::findByAll($handyman, $post['city']);
        if ($post['building_scrap'] == true) {
            $building_scrap = BasisDecorationService::haveBuildingScrap($post, $craft);
        } else {
            $building_scrap = BasisDecorationService::nothingBuildingScrap($post, $craft);
        }
//        总人工费
        $labor_cost['price'] = $total_day['total_day'] * $labor['univalence'] + $building_scrap['cost'];
        $labor_cost['worker_kind'] = $handyman;
        //材料费
        $material = ['水泥', '河沙', '空心砖'];
        $goods = Goods::priceDetail(3, $material);
        $goods_price = BasisDecorationService::priceConversion($goods);
        $material = [];
        foreach ($goods_price as $max) {
            switch ($max) {
                case $max['title'] == '水泥':
                    $goods_max = BasisDecorationService::profitMargin($max);
                    $goods_attr = GoodsAttr::findByGoodsIdUnit($goods_max['id']);
                    //水泥费用
                    $cement_cost = BasisDecorationService::cementCost($post, $craft, $goods_max, $goods_attr);
                    $goods_max['quantity'] = $cement_cost['quantity'];
                    $goods_max['cost'] = $cement_cost['cost'];
                    $material ['material'][] = $goods_max;
                    break;
                case $max['title'] == '空心砖':
                    $goods_max = BasisDecorationService::profitMargin($max);
                    //空心砖费用
                    $brick_standard = GoodsAttr::findByGoodsId($goods_max['id']);
                    $brick_cost = BasisDecorationService::brickCost($post, $goods_max, $brick_standard);
                    $goods_max['quantity'] = $brick_cost['quantity'];
                    $goods_max['cost'] = $brick_cost['cost'];
                    $material['material'] [] = $goods_max;
                    break;
                case $max['title'] == '河沙':
                    $goods_max = BasisDecorationService::profitMargin($max);
                    $goods_attr = GoodsAttr::findByGoodsIdUnit($goods_max['id']);
                    //河沙费用
                    $river_sand = BasisDecorationService::riverSandCost($post, $goods_max, $craft, $goods_attr);
                    $goods_max['quantity'] = $river_sand['quantity'];
                    $goods_max['cost'] = $river_sand['cost'];
                    $material['material'][] = $goods_max;
                    break;
            }
        }
        //总材料费
        $total_material_cost = $cement_cost['cost'] + $brick_cost['cost'] + $river_sand['cost'];
        $material['total_cost'] = $total_material_cost;

        //添加材料费用
        $add_price_area = DecorationAdd::AllArea('杂工', $post['area'], $post['city']);
        $add_price = [];
        foreach ($add_price_area as $add_area) {
            $sku_area = Goods::skuAll($add_area['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_area['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

        $add_price_series = DecorationAdd::AllSeries('杂工', $post['series'], $post['city']);
        foreach ($add_price_series as $add_series) {
            $sku_area = Goods::skuAll($add_series['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_series['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }
        $add_price_style = DecorationAdd::AllStyle('杂工', $post['style'], $post['city']);
        foreach ($add_price_style as $add_style) {
            $sku_area = Goods::skuAll($add_style['sku']);
            if ($sku_area !== null) {
                $add_price [] = $add_style['quantity'] * $sku_area['platform_price'];
            } else {
                $add_price [] = 0;
            }
        }

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'labor_cost' => $labor_cost,
                'total_material' => $material,
                'add_price' => $add_price,
            ]
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
        foreach ($assort_material as $assort){
            if ($assort['status'] != MaterialPropertyClassify::DEFAULT_STATUS){
                $have_assort[] = $assort;
            }else{
                $without_assort[] = $assort;
            }
        }

        //有计算公式
        foreach ($have_assort as $one_have_assort){
            $material_name[] = $one_have_assort['material'];
            $material_one[$one_have_assort['material']] = $one_have_assort;
        }
        $goods = Goods::assortList($material_name,510100);
        $goods_price = BasisDecorationService::priceConversion($goods);
        $bedroom_area = EngineeringUniversalCriterion::mudMakeArea('卧室', '卧室面积');
        $material[] = BasisDecorationService::lifeAssortSeriesStyle($goods_price,$post);
        $material[] = BasisDecorationService::capacity($goods_price, $post);
        $material[] = BasisDecorationService::appliancesAssortSeriesStyle($goods_price,$post);
        $material[] = BasisDecorationService::moveFurnitureSeriesStyle($goods_price,$post);
        $material[] = BasisDecorationService::fixationFurnitureSeriesStyle($goods_price,$post);
        $material[] = BasisDecorationService::mild($goods_price,$post);
        $material[] = BasisDecorationService::principalMaterialSeriesStyle($goods_price, $material_one,$post,$bedroom_area);
        if ($post['stairway_id'] == 1) {
            $stairs = Goods::findByCategory('楼梯');
            $stairs_price = BasisDecorationService::priceConversion($stairs);
            foreach ($stairs_price as $one_stairs_price) {
                if ($one_stairs_price['value'] == $post['stairs'] && $one_stairs_price['style_id'] == $post['style']) {
                    $one_stairs_price['quantity'] = $material_one['楼梯']['quantity'];
                    $one_stairs_price['cost'] = $one_stairs_price['platform_price'] * $one_stairs_price['quantity'];
                    $condition_stairs [] = $one_stairs_price;
                }
            }
        } else {
            $condition_stairs = null;
        }
        $material[] = BasisDecorationService::profitMargin($condition_stairs);

        //无计算公式
        foreach ($without_assort as $one_without_assort){
            $without_assort_name[] = $one_without_assort['material'];
            $without_assort_one[$one_without_assort['material']] = $one_without_assort;
        }
        $without_assort_goods = Goods::assortList($without_assort_name,510100);
        $without_assort_goods_price = BasisDecorationService::priceConversion($without_assort_goods);
        $material[] = BasisDecorationService::withoutAssortGoods($without_assort_goods_price,$assort_material,$post);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'goods' => $material,
            ]
        ]);
    }

    /**
     * noopsyche quote total
     */
    public function actionNoopsycheTotal()
    {
        $post = Yii::$app->request->post();

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
     * homepage  list
     * @return string
     */
    public function actionHomepage()
    {
       return Json::encode([
          'code' =>200,
           'msg'=>'ok',
           'data'=> BrainpowerInitalSupervise::find()
               ->where(['status'=>BrainpowerInitalSupervise::STATUS_OPEN])
               ->orderBy(['sort' => SORT_ASC])
               ->all()
       ]);
    }

    /**
     * case list
     * @return string
     */
    public function actionCaseList(){
        $code     = trim(Yii::$app->request->get('code',''));
        $street   = trim(Yii::$app->request->get('street',''));
        $toponymy = trim(Yii::$app->request->get('toponymy',''));
        $effect['case_effect'] = Effect::findByCode($code,$street,$toponymy);
        $effect['case_picture'] = EffectPicture::findById( $effect['case_effect']['id']);
        $effect['case_works_backman_data'] = WorksBackmanData::findById($effect['case_effect']['id']);
        $effect['case_works_data'] = WorksData::findById($effect['case_effect']['id']);
        $effect['case_works_worker_data'] = WorksBackmanData::findById($effect['case_effect']['id']);

        return Json::encode([
            'code' =>200,
            'msg'=>'ok',
            'data'=> $effect
        ]);
    }

    public function actionPriceTotal()
    {

    }
}