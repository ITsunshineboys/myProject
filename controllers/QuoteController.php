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
    public  function actionLaborCostList()
    {
        return Json::encode([
            'list'=> LaborCost::LaborCostList(),
        ]);
    }

    /**
     * 做工标准添加
     * @return string
     */
    public function actionLaborCostAdd()
    {
        $code = 1000;
//        $data = \Yii::$app->request->post();
        $post = [
        [
            'profession'=>'电工',
            'province'=>'510000',
            'city'=>'510100',
            'grade'=>'白银',
            'univalence'=>'300',
            'points'=>'5',
            'worker_kind_details' => '强电',
            'unit'=>'个'
        ],
        [
            'profession'=>'电工',
            'province'=>'510000',
            'city'=>'510100',
            'grade'=>'白银',
            'univalence'=>'300',
            'points'=>'6',
            'worker_kind_details' => '弱电',
            'unit'=>'个'
        ]
        ];
        $labor_cost =  new LaborCost;
        foreach ($post as $one_data)
        {
            $_labor_cost = clone $labor_cost;
            $_labor_cost->province_code = $one_data['province'];
            $_labor_cost->city_code = $one_data['city'];
            $_labor_cost->rank = $one_data['grade'];
            $_labor_cost->worker_kind = $one_data['profession'];
            $_labor_cost->univalence = $one_data['univalence'];
            $_labor_cost->quantity = $one_data['points'];
            $_labor_cost->worker_kind_details = $one_data['worker_kind_details'];
            $_labor_cost->unit = $one_data['unit'];
            $_labor_cost->setAttributes($one_data);
            if (!$_labor_cost->validate())
            {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$_labor_cost->save())
            {
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
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
        foreach ($post as $one_data)
        {
            $_labor_cost = clone $labor_cost;
            $a = $_labor_cost->findOne($one_data['id']);
            $a->univalence = $one_data['univalence'];
            $a->quantity = $one_data['points'];
            $a->setAttributes($one_data);
            if (!$a->validate())
            {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$a->save())
            {
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
        if (!$_labor_cost->validate())
        {
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
        foreach ($post as $one_data)
        {
            $craft = clone $engineering_standard_craft;
            $_craft = $craft->findOne($one_data['id']);
            $_craft->material = $one_data['material'];
            $_craft->setAttributes($one_data);
            if (!$_craft->validate())
            {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$_craft->save())
            {
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
        $goods = Goods::newMaterialAdd(3,$post);
        $goods_attr = GoodsAttr::findByGoodsId($goods['id']);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'goods' => [
                   'goods'=> $goods,
                   'goods_attr'=> $goods_attr,
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
        foreach ($post as $one_data)
        {
            $add = clone $decoration_add;
            if (array_key_exists('series_id',$one_data) == true)
            {
                $add->series_id = $one_data['series_id'];
            }
            if (array_key_exists('style_id',$one_data) == true)
            {
                $add->style_id = $one_data['style_id'];
            }
            if (array_key_exists('min_area',$one_data) == true)
            {
                $add->min_area = $one_data['min_area'];
                $add->max_area = $one_data['max_area'];
            }
            $add->project = $one_data['project'];
            $add->material = $one_data['material'];
            $add->sku = $one_data['sku'];
            $add->district_code = $one_data['district_code'];
            $add->quantity = $one_data['quantity'];
            $add->setAttributes($one_data);
            if (!$add->validate())
            {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$add->save())
            {
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
        foreach ($post as $one_data)
        {
            $add = clone $decoration_add;
            $_add = $add->findOne($one_data['id']);
            if (array_key_exists('series_id',$one_data) == true)
            {
                $_add->series_id = $one_data['series_id'];
            }
            if (array_key_exists('style_id',$one_data) == true)
            {
                $_add->style_id = $one_data['style_id'];
            }
            if (array_key_exists('min_area',$one_data) == true)
            {
                $_add->min_area = $one_data['min_area'];
                $_add->max_area = $one_data['max_area'];
            }
            $_add->project = $one_data['project'];
            $_add->material = $one_data['material'];
            $_add->sku = $one_data['sku'];
            $_add->district_code = $one_data['district_code'];
            $_add->quantity = $one_data['quantity'];
            $_add->setAttributes($one_data);
            if (!$_add->validate())
            {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }

            if (!$_add->save())
            {
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
        if (substr($post,4) ==00)
        {
            $effect = Effect::find()->where(['city_code'=>$post]);
            $pages = new Pagination(['totalCount'=>$effect->count(),'pageSize'=>12]);
            $model = $effect->offset($pages->offset)
                ->limit($pages->limit)
                ->asArray()
                ->select('effect.toponymy,effect.add_time,effect.district')
                ->groupBy('district')
                ->orderBy(['add_time'=>SORT_ASC])
                ->all();
            $list = [];
            foreach ($model as $one_model)
            {
                $one_model['add_time'] = date('Y-m-d H:i',$one_model['add_time']);
                $list [] = $one_model;
            }
            return Json::encode([
                'model' => $list,
                'pages'=> $pages
            ]);
        }else
        {
            $effect = Effect::find()->where(['district_code'=>$post]);
            $pages = new Pagination(['totalCount'=>$effect->count(),'pageSize'=>12]);
            $model = $effect->offset($pages->offset)
                ->limit($pages->limit)
                ->asArray()
                ->select('effect.toponymy,effect.add_time,effect.district')
                ->groupBy('district')
                ->orderBy(['add_time'=>SORT_ASC])
                ->all();
            $list = [];
            foreach ($model as $one_model)
            {
                $one_model['add_time'] = date('Y-m-d H:i',$one_model['add_time']);
                $list [] = $one_model;
            }
            return Json::encode([
                'model'=>$list,
                'pages'=>$pages
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
        $effect = Effect::find()->where(['and',['>=','add_time',$post['min']],['<=','add_time',$post['max']],['city_code'=>$post['city']]]);
        $pages = new Pagination(['totalCount'=>$effect->count(),'pageSize'=>12]);
        $model = $effect->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->select('effect.toponymy,effect.add_time,effect.district')
            ->groupBy('district')
            ->orderBy(['add_time'=>SORT_ASC])
            ->all();
        $list = [];
        foreach ($model as $one_model)
        {
            $one_model['add_time'] = date('Y-m-d H:i',$one_model['add_time']);
            $list [] = $one_model;
        }
        return Json::encode([
            'model'=>$list,
            'pages'=>$pages
        ]);
    }

    /**
     * plot find grabble all
     * @return string
     */
    public function actionPlotGrabble()
    {
        $post = \Yii::$app->request->get();
        $effect = Effect::find()->where(['and',['like','toponymy',$post['toponymy']],['city_code'=>$post['city']]]);
        $pages = new Pagination(['totalCount'=>$effect->count(),'pageSize'=>12]);
        $model = $effect->offset($pages->offset)
            ->limit($pages->limit)
            ->asArray()
            ->select('effect.toponymy,effect.add_time,effect.district')
            ->groupBy('district')
            ->orderBy(['add_time'=>SORT_ASC])
            ->all();
        $list = [];
        foreach ($model as $one_model)
        {
            $one_model['add_time'] = date('Y-m-d H:i',$one_model['add_time']);
            $list [] = $one_model;
        }
        return Json::encode([
            'model'=>$list,
            'pages'=>$pages
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
           'series'=>$series,
            'style'=>$style
        ]);
    }

    /**
     * plot add function
     * @return string
     */
    public function actionPlotAdd()
    {
        $request = \Yii::$app->request->post();
        $user = \Yii::$app->user->identity();
        if (!$request)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (!$user)
        {
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $all [] =(new Effect())->plotAdd($request);
        $all [] = (new EffectPicture())->plotAdd($request);
        $all [] = (new DecorationParticulars())->plotAdd($request);
        if ($all){
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

    /**
     * plot edit function
     * @return string
     */
    public function actionPlotEdit()
    {
        $post = \Yii::$app->request->post();
        $user = \Yii::$app->user->identity();
        if (!$post)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (!$user)
        {
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $all [] =(new Effect())->plotAdd($post);
        $all [] = (new EffectPicture())->plotAdd($post);
        $all [] = (new DecorationParticulars())->plotAdd($post);
        if ($all){
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

    /**
     * assort goods statistics list port
     * @return string
     */
    public function actionAssortGoods()
    {
        return Json::encode([
            'goods_category' => GoodsCategory::find()
                ->select('title,path,id')
                ->asArray()
                ->all(),
        ]);
    }

    /**
     * add assort goods administration
     * @return string
     */
    public function actionAssortGoodsAdd()
    {
        $post = \Yii::$app->request->post();
        $user = \Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $assort = (new AssortGoods())->add($post);
        if ($assort){
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
    
}