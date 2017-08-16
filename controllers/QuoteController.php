<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/23 0023
 * Time: 上午 11:48
 */
namespace app\controllers;
use app\models\AssortGoods;
use app\models\DecorationAdd;
use app\models\DecorationParticulars;
use app\models\Effect;
use app\models\EffectPicture;
use app\models\EngineeringStandardCraft;
use app\models\Goods;
use app\models\GoodsAttr;
use app\models\GoodsCategory;
use app\models\LaborCost;
use app\models\Series;
use app\models\Style;
use app\services\ExceptionHandleService;
use app\services\SmValidationService;
use Symfony\Component\Yaml\Tests\A;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;

class QuoteController extends Controller
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
     * labor list show
     * @return string
     */
    public function actionLaborCostList()
    {
        return Json::encode([
            'list' => LaborCost::LaborCostList(),
        ]);
    }

    /**
     * 做工标准添加
     * @return string
     */
    public function actionLaborCostAdd()
    {
        $quest = \Yii::$app->request;
        $worker_kind = $quest->post('worker_kind','');
        $province_code = $quest->post('province_code','');
        $city_code = $quest->post('city_code','');
        $rank = $quest->post('rank','');
        $univalence = $quest->post('univalence','');
        $weak_quantity = $quest->post('weak','');
        $strong_quantity = $quest->post('strong','');
        $user = \Yii::$app->user->identity;
        if (!$worker_kind || !$province_code || !$city_code || !$rank || !$univalence || !$weak_quantity || !$strong_quantity)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $res = (new LaborCost())->weakAdd($worker_kind,$province_code,$city_code,$rank,$univalence,$weak_quantity,$strong_quantity);
        if ($res[0] == true && $res[1] == true){
            $code=200;
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        }else{
            $code=1051;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /*
     * 电工、油漆工、木工、泥瓦工、杂工做工标准修改
     */
    public function actionMultitermEdit()
    {
        $code = 1000;
        $post = \Yii::$app->request->post();
        $labor_cost = new LaborCost();
        foreach ($post as $one_data) {
            $_labor_cost = clone $labor_cost;
            $a = $_labor_cost->findOne($one_data['id']);
            $a->univalence = $one_data['univalence'];
            $a->quantity = $one_data['points'];
            $a->setAttributes($one_data);
            if (!$a->validate()) {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$a->save()) {
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }
    }

    /**
     * 水路和防水做工标准修改
     * @return string
     */
    public function actionMonomialEdit()
    {
        $code = 1000;
        $post = \Yii::$app->request->post();
        $labor_cost = new LaborCost();
        $_labor_cost = $labor_cost->findOne($post['id']);
        $_labor_cost->univalence = $post['univalence'];
        $_labor_cost->quantity = $post['points'];
        if (!$_labor_cost->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$_labor_cost->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }
    }

    /**
     * 工程标准工艺标准修改
     * @return string
     */
    public function actionCraftEdit()
    {
        $code = 1000;
        $post = \Yii::$app->request->post();
        $engineering_standard_craft = new EngineeringStandardCraft();
        foreach ($post as $one_data) {
            $craft = clone $engineering_standard_craft;
            $_craft = $craft->findOne($one_data['id']);
            $_craft->material = $one_data['material'];
            $_craft->setAttributes($one_data);
            if (!$_craft->validate()) {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$_craft->save()) {
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }
    }

    /**
     * 工程标准添加材料项
     * @return string
     */
    public function actionMaterialAddInquire()
    {
        $post = \Yii::$app->request->post();
        $goods = Goods::newMaterialAdd(3, $post);
        $goods_attr = GoodsAttr::findByGoodsId($goods['id']);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'goods' => [
                    'goods' => $goods,
                    'goods_attr' => $goods_attr,
                ],
            ],
        ]);
    }

    /**
     * 工程标准添加材料项
     * @return string
     */
    public function actionMaterialAdd()
    {
        $code = 1000;
        $post = \Yii::$app->request->post();
        $decoration_add = new DecorationAdd();
        foreach ($post as $one_data) {
            $add = clone $decoration_add;
            if (array_key_exists('series_id', $one_data) == true) {
                $add->series_id = $one_data['series_id'];
            }
            if (array_key_exists('style_id', $one_data) == true) {
                $add->style_id = $one_data['style_id'];
            }
            if (array_key_exists('min_area', $one_data) == true) {
                $add->min_area = $one_data['min_area'];
                $add->max_area = $one_data['max_area'];
            }
            $add->project = $one_data['project'];
            $add->material = $one_data['material'];
            $add->sku = $one_data['sku'];
            $add->district_code = $one_data['district_code'];
            $add->quantity = $one_data['quantity'];
            $add->setAttributes($one_data);
            if (!$add->validate()) {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$add->save()) {
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }
    }

    /**
     * 工程标准修改材料项
     * @return string
     */
    public function actionMaterialEdit()
    {
        $code = 1000;
        $post = \Yii::$app->request->post();
        $decoration_add = new DecorationAdd();
        foreach ($post as $one_data) {
            $add = clone $decoration_add;
            $_add = $add->findOne($one_data['id']);
            if (array_key_exists('series_id', $one_data) == true) {
                $_add->series_id = $one_data['series_id'];
            }
            if (array_key_exists('style_id', $one_data) == true) {
                $_add->style_id = $one_data['style_id'];
            }
            if (array_key_exists('min_area', $one_data) == true) {
                $_add->min_area = $one_data['min_area'];
                $_add->max_area = $one_data['max_area'];
            }
            $_add->project = $one_data['project'];
            $_add->material = $one_data['material'];
            $_add->sku = $one_data['sku'];
            $_add->district_code = $one_data['district_code'];
            $_add->quantity = $one_data['quantity'];
            $_add->setAttributes($one_data);
            if (!$_add->validate()) {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$_add->save()) {
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }
    }

    /**
     * plot list and pages
     * @return string
     */
    public function actionPlotList()
    {
        $post = \Yii::$app->request->get('post');
        $page = (int)\Yii::$app->request->get('page', 1);
        $size = (int)\Yii::$app->request->get('size', Effect::PAGE_SIZE_DEFAULT);
        if (substr($post, 4) == 00) {
            $where = "city_code = $post";
            $effect = Effect::pagination($where,$page,$size);
            return Json::encode([
                'code' => 200,
                'msg' => 'OK',
                'model' => $effect
            ]);
        }
        else
        {
            $where = "district_code = $post";
            $effect = Effect::pagination($where,$page,$size);
            return Json::encode([
                'code' => 200,
                'msg' => 'OK',
                'model' => $effect
            ]);
        }

    }

    /**
     * time find grabble
     * @return string
     */
    public function actionPlotTimeGrabble()
    {
        $post = \Yii::$app->request->get();
        $page = (int)\Yii::$app->request->get('page', 1);
        $size = (int)\Yii::$app->request->get('size', Effect::PAGE_SIZE_DEFAULT);
        $where = "add_time >= {$post['min']} AND add_time <= {$post['max']} AND city_code = {$post['city']}";
        $effect = Effect::pagination($where,$page,$size);
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'model' => $effect
        ]);
    }

    /**
     * plot find grabble all
     * @return string
     */
    public function actionPlotGrabble()
    {
        $post = \Yii::$app->request->get();
        $page = (int)\Yii::$app->request->get('page', 1);
        $size = (int)\Yii::$app->request->get('size', Effect::PAGE_SIZE_DEFAULT);
        $where = "toponymy like '%{$post['toponymy']}%' and city_code = {$post['city']}";
        $effect = Effect::pagination($where,$page,$size);
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'model' => $effect
        ]);
    }

    public function actionLaborList()
    {
        return Json::encode([
            'labor_list'=>LaborCost::find()
                ->distinct()
                ->select('worker_kind')
                ->orderBy('worker_kind')
                ->all()
        ]);
    }

    /**
     * series and style show
     * @return string
     */
    public function actionSeriesAndStyle()
    {
        $series = Series::findBySeries();
        $style = Style::findByStyle();
        return Json::encode([
            'series' => $series,
            'style' => $style
        ]);
    }

    /**
     * plot add function
     * @return string
     */
    public function actionPlotAdd()
    {
//        $request = \Yii::$app->request->post();
        $request = [
            'house_name'=>'小区名称',
            'province_code'=>510000,
            'city_code'=>510100,
            'cur_county_id'=>5101066,
            'address'=>'小区详细地址',
            'house_informations'=>
             [
                [
                    'area'=>60,
                    'balcony_area'=>20,
                    'cur_hall'=>1,
                    'cur_imgSrc'=>1,
                    'cur_kitchen'=>1,
                    'cur_room'=>1,
                    'cur_toilet'=>1,
                    'flattop_area'=>1,
                    'hall_area'=>1,
                    'hall_girth'=>1,
                    'have_stair'=>1,
                    'high'=>1,
                    'house_type_name'=>1,
                    'is_ordinary'=>1,
                    'kitchen_area'=>1,
                    'kitchen_girth'=>1,
                    'other_length'=>1,
                    'room_area'=>1,
                    'room_girth'=>1,
                    'toilet_area'=>1,
                    'toilet_girth'=>1,
                    'window'=>1,
                    'cur_all_drawing'=>
                        [
                            'drawing_name'=>123,
                            'drawing_list'=>1,
                            'house_type_name'=>1,
                            'series'=>1,
                            'style'=>1,
                        ],
                ],
                 [
                     'area'=>60,
                     'balcony_area'=>20,
                     'cur_hall'=>1,
                     'cur_imgSrc'=>1,
                     'cur_kitchen'=>1,
                     'cur_room'=>1,
                     'cur_toilet'=>1,
                     'flattop_area'=>1,
                     'hall_area'=>1,
                     'hall_girth'=>1,
                     'have_stair'=>1,
                     'high'=>1,
                     'house_type_name'=>1,
                     'is_ordinary'=>1,
                     'kitchen_area'=>1,
                     'kitchen_girth'=>1,
                     'other_length'=>1,
                     'room_area'=>1,
                     'room_girth'=>1,
                     'toilet_area'=>1,
                     'toilet_girth'=>1,
                     'window'=>1,
                     'cur_all_drawing'=>
                         [
                             'drawing_name'=>1212313,
                             'drawing_list'=>1,
                             'house_type_name'=>1,
                             'series'=>1,
                             'style'=>1,
                         ],
                 ],
            ]
        ];
//        $user = \Yii::$app->user->identity();
//        if (!$request) {
//            $code = 1000;
//            return Json::encode([
//                'code' => $code,
//                'msg' => \Yii::$app->params['errorCodes'][$code]
//            ]);
//        }
//        if (!$user) {
//            $code = 1052;
//            return Json::encode([
//                'code' => $code,
//                'msg' => \Yii::$app->params['errorCodes'][$code]
//            ]);
//        }
        $effect = new Effect();
        foreach ($request['house_informations'] as $house)
        {
            $_effect = clone $effect;
            $_effect->series_id = $house['cur_all_drawing']['series'];
            $_effect->style_id = $house['cur_all_drawing']['style'];
            $_effect->bedroom = $house['cur_room'];
            $_effect->sittingRoom_diningRoom = $house['cur_hall'];
            $_effect->toilet = $house['cur_toilet'];
            $_effect->kitchen = $house['cur_kitchen'];
            $_effect->window = $house['window'];
            $_effect->area = $house['area'];
            $_effect->high = $house['high'];
            $_effect->province_code = $request['province_code'];
            $_effect->city_code = $request['city_code'];
            $_effect->district_code = $request['cur_county_id'];
            $_effect->toponymy = $request['house_name'];
            $_effect->street = $request['address'];
            $_effect->particulars = $house['house_type_name'];
            $_effect->stairway = $house['have_stair'];
            $_effect->house_image = $house['cur_imgSrc'];
            $_effect->effect_images = $house['cur_all_drawing']['drawing_list'];
            $_effect->images_name = $house['cur_all_drawing']['drawing_name'];
            $_effect->type = $house['is_ordinary'];
            $_effect->add_time = time();
            
            $_effect->setAttributes($request);
            $code = 1000;
            if (!$_effect->validate()) {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$_effect->save()) {
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }

        $all [] = (new DecorationParticulars())->plotAdd($request);
        if ($all) {
            $code = 200;
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        } else {
            $code = 1051;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /**
     * plot edit function
     * @return string
     */
    public function actionPlotEdit()
    {
        $post = \Yii::$app->request->post();
        $user = \Yii::$app->user->identity();
        if (!$post) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $all [] = (new Effect())->plotAdd($post);
        $all [] = (new EffectPicture())->plotAdd($post);
        $all [] = (new DecorationParticulars())->plotAdd($post);
        if ($all) {
            $code = 200;
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        } else {
            $code = 1051;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /**
     * assort goods statistics list port
     * @return string
     */
    public function actionAssortGoods()
    {
        $pid = (int)\Yii::$app->request->get('pid', 0);
        $categories = GoodsCategory::categoriesByPid(GoodsCategory::APP_FIELDS_CATEGORY, $pid);
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'categories' => $categories
            ],
        ]);
    }

    /**
     * assort goods list
     * @return string
     */
    public function actionAssortGoodsList()
    {
        $goods_list = AssortGoods::find()
            ->select(['title','category_id as id','pid','path'])
            ->asArray()
            ->all();
        $goods_classify = GoodsCategory::find()
            ->select(['id','title'])
            ->where(['in','level',[1,2]])
            ->asArray()
            ->all();
        return Json::encode([
           'list'=> $goods_list,
            'classify'=>$goods_classify
        ]);
    }

    /**
     * add assort goods administration
     * @return string
     */
    public function actionAssortGoodsAdd()
    {
        $post = \Yii::$app->request->post();
        $array = [];
        foreach ($post['assort'] as $one_post) {
            ksort($one_post);
            $array [] = $one_post;
        }
        $user = \Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $find = AssortGoods::findByCategoryId();

        if (!empty($find))
        {
            $id = [];
            foreach ($find as $find_one) {
                $id [] = $find_one['category_id'];
            }
            (new AssortGoods())->deleteAll(['category_id'=>$id]);
        }

        $assort = (new AssortGoods())->add($array);
        if ($assort) {
            $code = 200;
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        } else {
            $code = 1051;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

}