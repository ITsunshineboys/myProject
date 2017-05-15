<?php

namespace app\controllers;

use app\models\Effect;
use app\models\EffectPicture;
use app\models\Goods;
use app\models\GoodsBrand;
use app\models\GoodsCategory;
use app\models\LaborCost;
use app\models\Series;
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

    public function actionMaterialChoice()
    {
        //基础装修
        $post = [
            'room' => 3,
            'hall' => 1,
            'toilet' => 1,
            'kitchen' => 1,
            'area' => 80,
            'high' =>2.8,
            'window'=> 2,
            'style' =>'欧式',
            'series' =>'齐家',
            'province' => '四川',
            'city' => '成都',
            'district' => '成华',
        ];
        $arr = [];
        //每天水电完成点位
        $arr['day_standard'] = 5;
        $arr['profit'] = 0.7;
        $arr['worker_kind'] = '水电';
        //人工一天价格
        $arr['day_price'] = LaborCost::univalence($post['province'],$post['city'],$arr['worker_kind']);
        $arr['weak_current'] = '';
        //一个房间弱电价格
        $weak_current = BasisDecorationService::formula($arr);
        //主材料
        //固定家具
        //移动家具
        //家电配套
        //软装配套
        //智能配套 noopsyche_assort
        $noopsyche_assort = [];
        $quantity = 1;

        $goods_category = new GoodsCategory();
        $category = $goods_category->find()->where(['title' => '智能配套'])->one();
        $noopsyche_assort['category'] = $category['title'];

        $goods = new Goods();
        $price = $goods->find()->where(['category_id' => $category['id']])->all();
        $noopsyche_assort['platform_price'] = $price['platform_price'];

        $goods_brand = new GoodsBrand();
        $brand = $goods_brand->find()->where(['id' => $price['brand_id'] ])->all();
        $noopsyche_assort['brand'] = $brand['name'];
        $noopsyche_assort['quantity'] = $quantity;

        var_dump($noopsyche_assort);
        exit;
        //生活配套
    }
}