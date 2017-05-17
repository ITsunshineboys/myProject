<?php

namespace app\controllers;

use app\models\AppliancesAssort;
use app\models\BasisMaterial;
use app\models\Effect;
use app\models\EffectPicture;
use app\models\FixationFurniture;
use app\models\Goods;
use app\models\GoodsBrand;
use app\models\IntelligenceAssort;
use app\models\LaborCost;
use app\models\LifeAssort;
use app\models\MoveFurniture;
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

    public function actionBasisDecoration()
    {
        //基础装修
        $post = \Yii::$app->request->post();
        $arr = [];
        //每天水电完成点位
        $arr['day_standard'] = 5;
        $arr['profit'] = 0.7;
        $arr['worker_kind'] = '水电';
        //人工一天价格
//        $arr['day_price'] = LaborCost::univalence($post['province'],$post['city'],$arr['worker_kind']);
        //查询出材料单价
        $material_id = BasisMaterial::material(1);
        $goods_price = Goods::priceDetail($material_id);
        //电线单价
        foreach ($goods_price as $name){
            if($name['name'] == '电线'){
                $wire['wire_price'] = BasisDecorationService::wire($name['platform_price']);
                //所有商品单价
                $goods_price['0']['platform_price'] = $wire['wire_price'] ;
            }
        }
        //所有弱电的点位
        $weak_location = 1;
        //所有的强电点位
        $powerful_location =[5,5,5];
        //基础装修
        $weak_current = BasisDecorationService::formula($arr,$weak_location,$goods_price);
        $powerful_current = BasisDecorationService::formula($arr,$powerful_location,$goods_price);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'weak_current_price' => $weak_current,
                'powerful_current_price' => $powerful_current,
            ]
        ]);
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