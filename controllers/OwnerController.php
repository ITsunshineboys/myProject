<?php

namespace app\controllers;

use app\models\AppliancesAssort;
use app\models\BasisDecoration;
use app\models\BasisMaterial;
use app\models\CircuitryReconstruction;
use app\models\DecorationAdd;
use app\models\DecorationList;
use app\models\DecorationParticulars;
use app\models\Effect;
use app\models\EffectPicture;
use app\models\FixationFurniture;
use app\models\Goods;
use app\models\GoodsBrand;
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
     * 有资料
     * @return string
     */
    public function actionHaveInformation()
    {
        $post = \Yii::$app->request->post();
        if(!empty($post)){
            // 搜索框
            $search_condition = new Effect();
            $lists = $search_condition->districtSearch($post);
            foreach ($lists as $list){
                $search_picture = new EffectPicture();
                $list_picture = $search_picture->find()->where(['effect_id' =>$list['id']])->all();
            }

            // 系列列表
            $series = new Series();
            $series_list = $series->find()->all();

            // 风格列表
            $style = new Style();
            $style_list = $style ->find()->all();
            foreach ($style_list as $s){
                $style_picture = new StylePicture();
                $style_picture_list = $style_picture->find()->where(['style_id'=>$s['id']])->all();
            }
            return Json::encode([
                'code' => 200,
                'msg' => 'OK',
                'data' => [
                    'list' => $lists,
                    'list_picture' => $list_picture,
                    'series_list' => $series_list,
                    'style_list' => $style_list,
                    'style_picture_list' => $style_picture_list,
                ]
            ]);
        }else{
            $search_condition = new Effect();
            $lists = $search_condition->find()->where(['id' => 1])->one();

            $search_picture = new EffectPicture();
            $list_picture = $search_picture->find()->where(['effect_id' =>$lists['id']])->all();

            // 系列列表
            $series = new Series();
            $series_list = $series->find()->where(['id' => $lists['id']])->one();

            // 风格列表
            $style = new Style();
            $style_list = $style ->find()->where(['id' =>$lists['id']])->all();
            foreach ($style_list as $s){
                $style_picture = new StylePicture();
                $style_picture_list = $style_picture->find()->where(['style_id'=>$s['id']])->all();
            }

            return Json::encode([
                     'code' => 200,
                     'msg' => 'OK',
                     'data' => [
                         'list' => $lists,
                         'list_picture' => $list_picture,
                         'series_list' => $series_list,
                         'style_list' => $style_list,
                         'style_picture_list' => $style_picture_list,
                     ]
            ]);
        }

    }

    /**
     * 无资料
     * @return string
     */
    public function actionNullInformation()
    {
        $post = \Yii::$app->request->post();
        $null_information = new Effect();
        if($null_information->load($post) && $null_information->validate())
        {
            if(!$null_information->save()){
                $errors = $null_information->errors;
                return Json::encode([
                            'code'=> 500,
                            'msg' => '填写有误',
                            'data' =>[
                                'errors' => $errors
                            ]
                ]);
            }
        }else{
            $errors = $null_information->errors;
            return Json::encode([
                    'code' => 500,
                    'msg' => '填写有误',
                    'data' => [
                        'errors'=> $errors
                        ]
            ]);
        }
        $list = $null_information->find()->where(['and','toponymy' => $post['toponymy'],'street' => $post['street']])->one();
        // 系列列表
        $series = new Series();
        $series_list = $series->find()->all();

        // 风格列表
        $style = new Style();
        $style_list = $style ->find()->all();
        foreach ($style_list as $s){
            $style_picture = new StylePicture();
            $style_picture_list = $style_picture->find()->where(['style_id'=>$s['id']])->all();
        }

        return Json::encode([
           'code' => 200,
            'msg' => '成功',
            'data' => [
                'series_list' => $series_list,
                'style_list' => $style_list,
                'style_picture_list' => $style_picture_list,
                'list' => $list
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
//        $post = \Yii::$app->request->post();
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
        //每天水电完成点位
        $arr['day_standard'] = $post['0'] ?? 5;
        $arr['profit'] = $post['1'] ?? 0.7;
        $arr['worker_kind'] = '水电';
        //所有基础装修类型
        $basis_decoration = BasisDecoration::find()->all();
        //人工一天价格
        $arr['day_price'] = LaborCost::univalence($post['province'], $post['city'], $arr['worker_kind']);
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
//        $post = \Yii::$app->request->post();
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
        //每天水电完成点位
        $arr['day_standard'] = $post['0'] ?? 5;
        $arr['profit'] = $post['1'] ?? 0.7;
        $arr['worker_kind'] = '水电';
        //人工一天价格
        $arr['day_price'] = LaborCost::univalence($post['province'], $post['city'], $arr['worker_kind']);
        //查询弱电所需要材料
        if(empty($post['effect_id'])){
            $electric_wire = '电线';
            $strong_current = [];
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
//        $post = \Yii::$app->request->post();
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
        //每天水电完成点位
        $arr['day_standard'] = $post['0'] ?? 5;
        $arr['profit'] = $post['1'] ?? 0.7;
        $arr['worker_kind'] = '水电';
        //人工一天价格
        $arr['day_price'] = LaborCost::univalence($post['province'], $post['city'], $arr['worker_kind']);
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
//        $post = \Yii::$app->request->post();
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
        //每天水电完成点位
        $arr['day_standard'] = $post['0'] ?? 40;
        $arr['profit'] = $post['1'] ?? 0.7;
        $arr['worker_kind'] = '水电';
        //人工一天价格
        $arr['day_price'] = LaborCost::univalence($post['province'], $post['city'], $arr['worker_kind']);

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
        //        $post = \Yii::$app->request->post();
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
        //每天水电完成点位
        $arr['day_standard'] = $post['0'] ?? 40;
        $arr['profit'] = $post['1'] ?? 0.7;
        $arr['worker_kind'] = '木工';
        //人工一天价格
        $arr['day_price'] = LaborCost::univalence($post['province'], $post['city'], $arr['worker_kind']);

        if(!empty($post['effect_id']))
        {

        }
    }


    /**
     * 软装配套
     * @return string
     */
    public function actionSoftOutfitAssort()
    {
        $soft_outfit = new SoftOutfitAssort();
        $all_id = $soft_outfit->findById(1);
        $goods_price = Goods::priceDetail($all_id);
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' =>[
                'SoftOutfit_goods' =>$goods_price,
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
        $fixation_furniture = new FixationFurniture();
        $all_id = $fixation_furniture->findById(1);
        $goods_price = Goods::priceDetail($all_id);
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
        $move_furniture = new MoveFurniture();
        $all_id = $move_furniture->findById(1);
        $goods_price = Goods::priceDetail($all_id);
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
        $appliances = new AppliancesAssort();
        $all_id = $appliances->findById(1);
        $goods_price = Goods::priceDetail($all_id);
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
        $life = new LifeAssort();
        $all_id = $life->findById(1);
        $goods_price = Goods::priceDetail($all_id);
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' =>[
                'Life_goods' =>$goods_price,
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
        $Intelligence = new IntelligenceAssort();
        $all_id = $Intelligence->findById(1);
        $goods_price = Goods::priceDetail($all_id);
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' =>[
                'intelligence_goods' =>$goods_price,
                'quantity' => 1
            ]
        ]);
    }

}