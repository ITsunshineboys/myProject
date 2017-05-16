<?php

namespace app\controllers;

use app\models\BasisMaterial;
use app\models\Effect;
use app\models\EffectPicture;
use app\models\Goods;
use app\models\GoodsBrand;
use app\models\GoodsCategory;
use app\models\IntelligenceAssort;
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
        $arr['day_price'] = LaborCost::univalence($post['province'],$post['city'],$arr['worker_kind']);
        //查询出材料单价
        $material_id = BasisMaterial::material(1);
        $goods_price = Goods::priceDetail($material_id);
        $goods_brand = GoodsBrand::findById($goods_price);
        //电线单价
        $wire['wire_price'] = BasisDecorationService::wire($goods_price[0]['platform_price']);
        //所有材料单价
        $all_material = [];
        $all_material[] = $wire['wire_price'];
        $all_material[] = $goods_price[0]['platform_price'];

        //所有弱电的点位
        $weak_location =[5,5,5];
        //所有的强电点位
        $powerful_location =[5,5,5];
        //基础装修
        $weak_current = BasisDecorationService::formula($arr,$weak_location,$all_material);
        $powerful_current = BasisDecorationService::formula($arr,$powerful_location,$all_material);

        return Json::encode([
            'code' => 200,
            'msg' => '成功',
            'data' => [
                'weak_current_price' => $weak_current,
                'powerful_current_price' => $powerful_current,
            ]
        ]);
    }

    public function actionMainMaterials()
    {

    }

    public function actionIntelligenceAssort()
    {
        $Intelligence = new IntelligenceAssort();
        $all = $Intelligence->find()->all();
//        $orders = Customer::find()->joinWith('orders')->where(['customer.id' => '1'])->all();
        $goods = Goods::find()->joinWith('orders')->where(['goods.brand_id'=> 1])->all();
        var_dump($goods);
        exit;
    }

}