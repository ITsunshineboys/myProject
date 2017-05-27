<?php

namespace app\controllers;

use app\models\AppliancesAssort;
use app\models\BasisDecoration;
use app\models\BasisMaterial;
use app\models\DecorationList;
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

    public function actionWeakCurrent()
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
        //所有基础装修类型
        $basis_decoration = BasisDecoration::find()->all();
        //人工一天价格
        $arr['day_price'] = LaborCost::univalence($post['province'], $post['city'], $arr['worker_kind']);
        //查询弱电所需要材料
        $electric_wire = '电线';
        $weak_current = [];
        $wire = Goods::priceDetail(3, $electric_wire);
        $weak_current['wire'] = BasisDecorationService::wire($wire['platform_price']);
        $pipe = '线管';
        $wire_pipe = Goods::priceDetail(3, $pipe);
        $weak_current['pipe'] = BasisDecorationService::wire($wire_pipe['platform_price']);
        $box = '底盒';
        $wire_box = Goods::priceDetail(3, $box);
        $weak_current['box'] = $wire_box['platform_price'];

        //弱电 价格
        if (!empty($post['effect_id'])) {
            //查询所有弱电点位
            $effect_id = Effect::find()->where(['id' => $post['effect_id']])->all();
            $points = Points::weakLocation($effect_id[0]['id']);
        } else {
            $effect = Effect::conditionQuery($post);
            //查询所有弱电点位
            $points = Points::weakLocation($effect['id']);
        }

        //基础弱电总价格
        $weak_current_price = ceil(BasisDecorationService::formula($arr, $points, $weak_current));

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'weak_current_price' => $weak_current_price,
            ]
        ]);
    }

    public function actionStrongCurrent()
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
        //查询弱电所需要材料
        if(empty($post['effect_id'])){
            $electric_wire = '电线';
            $strong_current = [];
            $strong = Goods::priceDetail(3, $electric_wire);
            $strong_current['wire'] = BasisDecorationService::wire($strong['platform_price']);
            $pipe = '线管';
            $wire_pipe = Goods::priceDetail(3, $pipe);
            $strong_current['pipe'] = BasisDecorationService::wire($wire_pipe['platform_price']);
            $box = '底盒';
            $strong_box = Goods::priceDetail(3, $box);
            $strong_current['box'] = $strong_box['platform_price'];
        }else{
            $basis_decoration = BasisDecoration::find()->all();
            $decoration_list = DecorationList::findByIds($post['series'],$post['style']);
            $basis_material  = BasisMaterial::material($decoration_list['id'],$basis_decoration[1]['id']);
            $strong_current = Goods::findQueryAll($basis_material,$post['city']);
            foreach ($strong_current as $unit_price)
            {
                    if($unit_price['title'] == '电线' ){
                        $electric_wire = BasisDecorationService::wire($unit_price['platform_price']);
                        $strong_current[1]['platform_price'] = $electric_wire;
                    }elseif($unit_price['title'] == '线管' ){
                        $electric_wire = BasisDecorationService::wire($unit_price['platform_price']);
                        $strong_current[2]['platform_price'] = $electric_wire;
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
        $powerful_current = ceil(BasisDecorationService::formula($arr, $powerful_location, $strong_current));
        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'strong_current_price' => $powerful_current,
            ]
        ]);
    }

    public function actionWaterproof()
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
        $arr['day_standard'] = $post['0'] ?? 40;
        $arr['profit'] = $post['1'] ?? 0.7;
        $arr['worker_kind'] = '水电';
        //人工一天价格
        $arr['day_price'] = LaborCost::univalence($post['province'], $post['city'], $arr['worker_kind']);

//        //水路 价格
//        if(!empty($post['effect_id'])){
//            //查询水路所需要材料
//            $decoration_list = DecorationList::findByIds($post['series'],$post['style']);
//            $basis_material  = BasisMaterial::material($decoration_list['id'],$basis_decoration[3]['id']);
//
//            //查询水路所需点位
//            $effect = Effect::find()->where(['id'=>$post['effect_id']])->all();
//            $points = Points::waterwayPoints($effect[0]['id']);
//        }else{
//            //查询水路所需要材料
//            $effect = Effect::conditionQuery($post);
//            $decoration_list = DecorationList::findByIds($effect['series_id'],$effect['style_id']);
//            $basis_material  = BasisMaterial::material($decoration_list['id'],$basis_decoration[3]['id']);
//
//            //查询水路所需点位
//            $points = Points::waterwayPoints($effect['id']);
//        }
//            $water_goods = Goods::priceDetail($basis_material);
//            $water_total = ceil(BasisDecorationService::formula($arr,$points,$water_goods));
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