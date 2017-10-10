<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/23 0023
 * Time: 上午 11:48
 */
namespace app\controllers;

use app\models\Apartment;
use app\models\ApartmentArea;
use app\models\AssortGoods;
use app\models\BrainpowerInitalSupervise;
use app\models\CoefficientManagement;
use app\models\DecorationAdd;
use app\models\DecorationMessage;
use app\models\DecorationParticulars;
use app\models\District;
use app\models\Effect;
use app\models\EffectPicture;
use app\models\EngineeringStandardCarpentryCoefficient;
use app\models\EngineeringStandardCarpentryCraft;
use app\models\EngineeringStandardCraft;
use app\models\Goods;
use app\models\GoodsAttr;
use app\models\GoodsCategory;
use app\models\LaborCost;
use app\models\Points;
use app\models\ProjectView;
use app\models\Series;
use app\models\StairsDetails;
use app\models\Style;
use app\models\WorkerCraftNorm;
use app\models\WorksBackmanData;
use app\models\WorksData;
use app\models\WorksWorkerData;
use app\services\ExceptionHandleService;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;

class QuoteController extends Controller
{
    const CATEGORY_LEVEL = 3;
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
        $select = 'worker_kind';
        $group  = 'worker_kind';
        return Json::encode([
            'list' => LaborCost::LaborCostList($select,$group),
        ]);
    }

    /**
     * 做工标准修改列表
     * @return string
     */
    public function actionLaborCostEditList()
    {
        $request = \Yii::$app->request;
        $province = trim($request->get('province',''));
        $city = trim($request->get('city',''));
        $worker_kind = trim($request->get('worker_kind',''));
        $select = 'id,province,city,univalence,worker_kind';
        $labor_cost = LaborCost::workerKind($select,$province,$city,$worker_kind);
        $worker_craft_norm = WorkerCraftNorm::findById($labor_cost['id']);
        return Json::encode([
            'labor_cost'=> $labor_cost,
            'worker_craft_norm'=>$worker_craft_norm
        ]);
    }

    /**
     * 做工标准修改
     * @return string
     */
    public function actionLaborCostEdit()
    {
        $post = \Yii::$app->request->post();
        foreach ($post['else'] as $one_post){
            if ($one_post['quantity']){
                $worker_craft_norm = WorkerCraftNorm::findOne($one_post['id']);
                $worker_craft_norm->quantity = $one_post['quantity'];
                $worker_craft_norm->save();
            }
        }
        $labor_cost = LaborCost::findOne($post['id']);
        $labor_cost->univalence = $post['univalence'];
        if ($labor_cost->save()){
            return Json::encode([
               'code' =>200,
                'msg'=>'OK'
            ]);
        }
    }

    /**
     * 工程标准列表
     * @return string
     */
    public function actionProjectNormList(){
        return Json::encode([
            'list'=>EngineeringStandardCraft::findByList(),
        ]);
    }

    /**
     * 工程标准修改列表
     * @return string
     */
    public function actionProjectNormEditList(){
        $city = trim(\Yii::$app->request->get('city',''));
        $project = trim(\Yii::$app->request->get('project',''));
        return Json::encode([
           'list'=>EngineeringStandardCraft::findByAll($project,$city),
        ]);
    }

    /**
     * 工程标准修改
     * @return string
     */
    public function actionProjectNormEdit(){
        $post = \Yii::$app->request->post();
        foreach ($post['material'] as $one_material){
            $material = EngineeringStandardCraft::findOne($one_material['id']);
            $material->material = $one_material['material'];
            $material->save();
        }
        return Json::encode([
           'code'=>200,
            'msg'=>'ok'
        ]);
    }

    /**
     * 工程标准修改 木作列表
     * @return string
     */
    public function actionProjectNormWoodworkList()
    {
        $material = ['石膏板', '龙骨', '丝杆'];
        $goods['specification']  = GoodsCategory::GoodsAttrValue(3,$material);
        $series = Series::findBySeries();
        $style  = Style::findByStyle();
        $coefficient    = EngineeringStandardCarpentryCoefficient::findByAll();
        $goods['find_specification']   = EngineeringStandardCarpentryCraft::findByAll();

        return Json::encode([
           'specification'=>$goods,
            'series'=>$series,
            'style'=>$style,
            'coefficient'=>$coefficient
        ]);
    }

    /**
     * 工程标准修改 木作修改
     * @return string
     */
    public function actionProjectNormWoodworkEdit()
    {
        $post = \Yii::$app->request->post();
        foreach ($post['value'] as $one_post){
            if (isset($one_post['id'])){
                $value = EngineeringStandardCraft::findOne($one_post['id']);
                $value->material = $one_post['value'];
                $value->save();
            } else {
                $value = new EngineeringStandardCraft();
                $value->district_code   = $post['district_code'];
                $value->project         = $post['project'];
                $value->project_details = $one_post['name'];
                $value->material        = $one_post['value'];
                $value->save();
            }
        }

        foreach ($post['specification'] as $one_specification){
            $specification = EngineeringStandardCarpentryCraft::findOne($one_specification['id']);
            $specification->value = $one_specification['value'];
            $specification->save();
        }

        foreach ($post['coefficient'] as $one_coefficient){
            if (isset($one_coefficient['id'])){
                $coefficient = EngineeringStandardCarpentryCoefficient::findOne($one_coefficient['id']);
                $coefficient->value = $one_coefficient['value'];
                $coefficient->save();
            } else {
                $coefficient = new EngineeringStandardCarpentryCoefficient();
                $coefficient->project  = $one_coefficient['project'];
                $coefficient->value  = $one_coefficient['value'];
                $coefficient->coefficient  = $one_coefficient['coefficient'];
                $coefficient->series_or_style  = $one_coefficient['series_or_style'];
                $coefficient->save();
            }
        }
        return Json::encode([
           'code'=>200,
            'msg'=>'OK'
        ]);
    }

    /**
     * 系数管理 列表
     * @return string
     */
    public function actionCoefficientList()
    {
        return Json::encode([
            'coefficient'=>CoefficientManagement::findByAll(),
           'list'=> GoodsCategory::findByHeadTitle(),
        ]);
    }

    /**
     * 系数管理 修改
     * @return string
     */
    public function actionCoefficientAdd()
    {
        $post = \Yii::$app->request->post();
        CoefficientManagement::deleteAll();
        $rows = [];
        foreach ($post['value'] as $value){
            $rows [] = $value;
        }
        (new CoefficientManagement())->findByInsert($rows);
        return Json::encode([
            'code'=>200,
            'msg'=>'OK'
        ]);
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
        } else {
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
        $city = (int)\Yii::$app->request->get('city');
        $min_time = (int)strtotime(\Yii::$app->request->get('min'));
        $max_time = (int)strtotime(\Yii::$app->request->get('max'));
        $page = (int)\Yii::$app->request->get('page', '1');
        $size = (int)\Yii::$app->request->get('size', Effect::PAGE_SIZE_DEFAULT);
        $where = "add_time >= {$min_time} AND add_time <= {$max_time} AND city_code = {$city}";
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

    /**
     * plot list all
     * @return string
     */
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
        $stairs_details = StairsDetails::findByAll();
        $series         = Series::findBySeries();
        $style          = Style::findByStyle();

        return Json::encode([
            'series' => $series,
            'style' => $style,
            'stairs_details'=>$stairs_details,
        ]);
    }

    /**
     * plot add function
     * @return string
     */
    public function actionPlotAdd()
    {
        $request = \Yii::$app->request->post();
//        $user = \Yii::$app->user->identity();
        $province_chinese = District::findByCode((int)$request['province_code']);
        $city_chinese = District::findByCode((int)$request['city_code']);
        $district_chinese = District::findByCode((int)$request['cur_county_id']);
        foreach ($request['house_informations'] as $house) {
            if ($house['is_ordinary'] != 1 ){

                //普通户型添加
                $bedroom = $house['cur_room'];
                $sittingRoom_diningRoom = $house['cur_hall'];
                $toilet = $house['cur_toilet'];
                $kitchen = $house['cur_kitchen'];
                $window = $house['window'];
                $area = $house['area'];
                $high = $house['high'];
                $province = $province_chinese['name'];
                $province_code = $request['province_code'];
                $city = $city_chinese['name'];
                $city_code = $request['city_code'];
                $district = $district_chinese['name'];
                $district_code = $request['cur_county_id'];
                $toponymy = $request['house_name'];
                $street = $request['address'];
                $particulars = $house['house_type_name'];
                $stairway = $house['have_stair'];
                $house_image = $house['cur_imgSrc'];
                $type = $house['is_ordinary'];
                $sort_id = $house['sort_id'];

                $hall_area = $house['hall_area'];
                $hall_perimeter = $house['hall_girth'];
                $bedroom_area = $house['room_area'];
                $bedroom_perimeter = $house['room_girth'];
                $toilet_area = $house['toilet_area'];
                $toilet_perimeter = $house['toilet_girth'];
                $kitchen_area = $house['kitchen_area'];
                $kitchen_perimeter = $house['kitchen_girth'];
                $modelling_length = $house['other_length'];
                $flat_area = $house['flattop_area'];
                $balcony_area = $house['balcony_area'];

                $effect =(new Effect())->plotAdd($bedroom,$sittingRoom_diningRoom,$toilet,$kitchen,$window,$area,$high,$province,$province_code,$city,$city_code,$district,$district_code,$toponymy,$street,$particulars,$stairway,$house_image,$type,$sort_id,0);

                $effect_id = \Yii::$app->db->getLastInsertID();
                (new DecorationParticulars())->plotAdd($effect_id,$hall_area,$hall_perimeter,$bedroom_area,$bedroom_perimeter,$toilet_area,$toilet_perimeter,$kitchen_area,$kitchen_perimeter,$modelling_length,$flat_area,$balcony_area);

                if (!empty($house['drawing_list'])){
                    foreach ($house['drawing_list'] as $images){
                        $effect_images = $images['all_drawing'];
                        $series_id     = $images['series'];
                        $style_id      = $images['style'];
                        $images_user   = $images['drawing_name'];
                        ( new EffectPicture())->plotAdd($effect_id,$effect_images,$series_id,$style_id,$images_user);
                    }
                }
            }
            else {

                // 案例添加
                $bedroom = $house['cur_room'];
                $sittingRoom_diningRoom = $house['cur_hall'];
                $toilet = $house['cur_toilet'];
                $kitchen = $house['cur_kitchen'];
                $window = $house['window'];
                $area = $house['area'];
                $high = $house['high'];
                $province = $province_chinese['name'];
                $province_code = $request['province_code'];
                $city = $city_chinese['name'];
                $city_code = $request['city_code'];
                $district = $district_chinese['name'];
                $district_code = $request['cur_county_id'];
                $toponymy = $request['house_name'];
                $street = $request['address'];
                $particulars = $house['house_type_name'];
                $stairway = $house['have_stair'];
                $house_image = $house['cur_imgSrc'];
                $type = $house['is_ordinary'];
                $sort_id = $house['sort_id'];
                if ($stairway != 1){
                    $stair_id = 0;
                }else{
                    $stair_id = $house['stair'];
                }


                $effect =(new Effect())->plotAdd($bedroom,$sittingRoom_diningRoom,$toilet,$kitchen,$window,$area,$high,$province,$province_code,$city,$city_code,$district,$district_code,$toponymy,$street,$particulars,$stairway,$house_image,$type,$sort_id,$stair_id);

                $effect_id = \Yii::$app->db->getLastInsertID();
                if (!empty($house['drawing_list'])){
                        $effect_images = $house['drawing_list'];
                        $series_id     = $house['series'];
                        $style_id      = $house['style'];
                        $images_user   = '案例添加';
                        ( new EffectPicture())->plotAdd($effect_id,$effect_images,$series_id,$style_id,$images_user);
                }

                if (!empty($house['all_goods'])) {
                    foreach ($house['all_goods'] as $goods) {
                        $goods_id = $effect_id;
                        $goods_first = $goods['first_name'];
                        $goods_second = $goods['second_name'];
                        $goods_three = $goods['three_name'];
                        $goods_code = $goods['good_code'];
                        $goods_quantity = $goods['good_quantity'];
                        (new WorksData())->plotAdd($goods_id, $goods_first, $goods_second, $goods_three, $goods_code, $goods_quantity);
                    }
                }

                if (!empty($house['worker_list'])) {
                    foreach ($house['worker_list'] as $worker) {
                        $worker_id = $effect_id;
                        $worker_kind = $worker['worker_kind'];
                        $worker_price = $worker['price'];
                        (new WorksWorkerData())->plotAdd($worker_id, $worker_kind, $worker_price);
                    }
                }
                foreach ($house['backman_option'] as $backman){
                    $backman_id = $effect_id;
                    $backman_option = $backman['name'];
                    $backman_value  = $backman['num'];
                    (new WorksBackmanData())->plotAdd($backman_id,$backman_option,$backman_value);
                }
            }
        }
        if ($effect) {
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
     * plot edit page view
     * @return string
     */
    public function actionPlotEditView()
    {
        $post = \Yii::$app->request->post();
//        $user = \Yii::$app->user->identity();
        $public_message = [];
        $public_message['effect'] = Effect::condition($post['street'],$post['toponymy'],$post['district']);
        $public_message['street'] =  $public_message['effect'][0]['street'];
        $public_message['toponymy'] =  $public_message['effect'][0]['toponymy'];
        $public_message['district_code'] =  $public_message['effect'][0]['district_code'];
        $public_message['district'] = $public_message['effect'][0]['district'];
        foreach ($public_message['effect'] as $one_effect){
            $id[] = $one_effect['id'];
        }
        $public_message['images'] = EffectPicture::findById($id);
        $public_message['decoration_particulars'] = DecorationParticulars::findById($id);
        $public_message['works_data'] = WorksData::findById($id);
        $public_message['works_worker_data'] = WorksWorkerData::findById($id);
        $public_message['works_backman_data'] = WorksBackmanData::findById($id);

        return Json::encode([
            'effect'=>$public_message,
        ]);
    }

    /**
     * plot  edit find
     * @return string
     */
    public function actionPlotEdit()
    {
        $request = \Yii::$app->request->post();
//        $user = \Yii::$app->user->identity();
        $province_chinese = District::findByCode($request['province_code']);
        $city_chinese = District::findByCode($request['city_code']);
        $district_chinese = District::findByCode($request['cur_county_id']);
        foreach ($request['house_informations'] as $house) {
            //添加功能
            if (!isset($house['id'])) {
                if ($house['is_ordinary'] != 1 ){
                    //普通户型添加
                    $bedroom = $house['cur_room'];
                    $sittingRoom_diningRoom = $house['cur_hall'];
                    $toilet = $house['cur_toilet'];
                    $kitchen = $house['cur_kitchen'];
                    $window = $house['window'];
                    $area = $house['area'];
                    $high = $house['high'];
                    $province = $province_chinese['name'];
                    $province_code = $request['province_code'];
                    $city = $city_chinese['name'];
                    $city_code = $request['city_code'];
                    $district = $district_chinese['name'];
                    $district_code = $request['cur_county_id'];
                    $toponymy = $request['house_name'];
                    $street = $request['address'];
                    $particulars = $house['house_type_name'];
                    $stairway = $house['have_stair'];
                    $house_image = $house['cur_imgSrc'];
                    $type = $house['is_ordinary'];
                    $sort_id = $house['sort_id'];

                    $hall_area = $house['hall_area'];
                    $hall_perimeter = $house['hall_girth'];
                    $bedroom_area = $house['room_area'];
                    $bedroom_perimeter = $house['room_girth'];
                    $toilet_area = $house['toilet_area'];
                    $toilet_perimeter = $house['toilet_girth'];
                    $kitchen_area = $house['kitchen_area'];
                    $kitchen_perimeter = $house['kitchen_girth'];
                    $modelling_length = $house['other_length'];
                    $flat_area = $house['flattop_area'];
                    $balcony_area = $house['balcony_area'];

                    $effect = (new Effect())->plotAdd($bedroom,$sittingRoom_diningRoom,$toilet,$kitchen,$window,$area,$high,$province,$province_code,$city,$city_code,$district,$district_code,$toponymy,$street,$particulars,$stairway,$house_image,$type,$sort_id,0);

                    $effect_id = \Yii::$app->db->getLastInsertID();
                    (new DecorationParticulars())->plotAdd($effect_id,$hall_area,$hall_perimeter,$bedroom_area,$bedroom_perimeter,$toilet_area,$toilet_perimeter,$kitchen_area,$kitchen_perimeter,$modelling_length,$flat_area,$balcony_area);

                    if (!empty($house['drawing_list'])){
                        foreach ($house['drawing_list'] as $images){
                            $effect_images = $images['all_drawing'];
                            $series_id     = $images['series'];
                            $style_id      = $images['style'];
                            $images_user   = $images['drawing_name'];
                            ( new EffectPicture())->plotAdd($effect_id,$effect_images,$series_id,$style_id,$images_user);
                        }
                    }
                }
                else {

                    // 案例添加
                    $bedroom = $house['cur_room'];
                    $sittingRoom_diningRoom = $house['cur_hall'];
                    $toilet = $house['cur_toilet'];
                    $kitchen = $house['cur_kitchen'];
                    $window = $house['window'];
                    $area = $house['area'];
                    $high = $house['high'];
                    $province = $province_chinese['name'];
                    $province_code = $request['province_code'];
                    $city = $city_chinese['name'];
                    $city_code = $request['city_code'];
                    $district = $district_chinese['name'];
                    $district_code = $request['cur_county_id'];
                    $toponymy = $request['house_name'];
                    $street = $request['address'];
                    $particulars = $house['house_type_name'];
                    $stairway = $house['have_stair'];
                    $house_image = $house['cur_imgSrc'];
                    $type = $house['is_ordinary'];
                    $sort_id = $house['sort_id'];
                    if ($stairway != 1){
                        $stair_id = 0;
                    }else{
                        $stair_id = $house['stair'];
                    }


                    $effect = (new Effect())->plotAdd($bedroom,$sittingRoom_diningRoom,$toilet,$kitchen,$window,$area,$high,$province,$province_code,$city,$city_code,$district,$district_code,$toponymy,$street,$particulars,$stairway,$house_image,$type,$sort_id,$stair_id);

                    $effect_id = \Yii::$app->db->getLastInsertID();
                    if (!empty($house['drawing_list'])){
                            $effect_images = $house['drawing_list'];
                            $series_id     = $house['series'];
                            $style_id      = $house['style'];
                            $images_user   = '案例图片';
                            ( new EffectPicture())->plotAdd($effect_id,$effect_images,$series_id,$style_id,$images_user);
                    }

                    foreach ($house['all_goods'] as $goods){
                        $goods_id = $effect_id;
                        $goods_first = $goods['first_name'];
                        $goods_second = $goods['second_name'];
                        $goods_three = $goods['three_name'];
                        $goods_code = $goods['good_code'];
                        $goods_quantity = $goods['good_quantity'];
                        (new WorksData())->plotAdd($goods_id,$goods_first,$goods_second,$goods_three,$goods_code,$goods_quantity);
                    }

                    foreach ($house['worker_list'] as $worker){
                        $worker_id = $effect_id;
                        $worker_kind = $worker['worker_kind'];
                        $worker_price = $worker['price'];
                        (new WorksWorkerData())->plotAdd($worker_id,$worker_kind,$worker_price);
                    }

                    foreach ($house['backman_option'] as $backman){
                        $backman_id = $effect_id;
                        $backman_option = $backman['name'];
                        $backman_value  = $backman['num'];
                        (new WorksBackmanData())->plotAdd($backman_id,$backman_option,$backman_value);
                    }
                }
            } else{
                if ($house['is_ordinary'] != 1 ){
                    //普通户型修改
                    $house_id = $house['id'];
                    $bedroom = $house['cur_room'];
                    $sittingRoom_diningRoom = $house['cur_hall'];
                    $toilet = $house['cur_toilet'];
                    $kitchen = $house['cur_kitchen'];
                    $window = $house['window'];
                    $area = $house['area'];
                    $high = $house['high'];
                    $province = $province_chinese['name'];
                    $province_code = $request['province_code'];
                    $city = $city_chinese['name'];
                    $city_code = $request['city_code'];
                    $district = $district_chinese['name'];
                    $district_code = $request['cur_county_id'];
                    $toponymy = $request['house_name'];
                    $street = $request['address'];
                    $particulars = $house['house_type_name'];
                    $stairway = $house['have_stair'];
                    $house_image = $house['cur_imgSrc'];
                    $type = $house['is_ordinary'];
                    $sort_id = $house['sort_id'];

                    $other_id = $house['other_id'];
                    $hall_area = $house['hall_area'];
                    $hall_perimeter = $house['hall_girth'];
                    $bedroom_area = $house['room_area'];
                    $bedroom_perimeter = $house['room_girth'];
                    $toilet_area = $house['toilet_area'];
                    $toilet_perimeter = $house['toilet_girth'];
                    $kitchen_area = $house['kitchen_area'];
                    $kitchen_perimeter = $house['kitchen_girth'];
                    $modelling_length = $house['other_length'];
                    $flat_area = $house['flattop_area'];
                    $balcony_area = $house['balcony_area'];

                    $effect = (new Effect())->plotEdit($house_id,$bedroom,$sittingRoom_diningRoom,$toilet,$kitchen,$window,$area,$high,$province,$province_code,$city,$city_code,$district,$district_code,$toponymy,$street,$particulars,$stairway,$house_image,$type,$sort_id,0);
                    (new DecorationParticulars())->plotEdit($other_id,$hall_area,$hall_perimeter,$bedroom_area,$bedroom_perimeter,$toilet_area,$toilet_perimeter,$kitchen_area,$kitchen_perimeter,$modelling_length,$flat_area,$balcony_area);

                    if (!empty($house['drawing_list'])){
                        foreach ($house['drawing_list'] as $images){
                            if (!empty($images['id'])){
                                $images_id     = $images['id'];
                                $effect_images = $images['all_drawing'];
                                $series_id     = $images['series'];
                                $style_id      = $images['style'];
                                $images_user   = $images['drawing_name'];
                                ( new EffectPicture())->plotEdit($images_id,$effect_images,$series_id,$style_id,$images_user);
                            }
                        }
                    }
                }
                else {
                    // 案例修改
                    $house_id = $house['id'];
                    $bedroom = $house['cur_room'];
                    $sittingRoom_diningRoom = $house['cur_hall'];
                    $toilet = $house['cur_toilet'];
                    $kitchen = $house['cur_kitchen'];
                    $window = $house['window'];
                    $area = $house['area'];
                    $high = $house['high'];
                    $province = $province_chinese['name'];
                    $province_code = $request['province_code'];
                    $city = $city_chinese['name'];
                    $city_code = $request['city_code'];
                    $district = $district_chinese['name'];
                    $district_code = $request['cur_county_id'];
                    $toponymy = $request['house_name'];
                    $street = $request['address'];
                    $particulars = $house['house_type_name'];
                    $stairway = $house['have_stair'];
                    $house_image = $house['cur_imgSrc'];
                    $type = $house['is_ordinary'];
                    $sort_id = $house['sort_id'];
                    if ($stairway != 1){
                        $stair_id = 0;
                    }else{
                        $stair_id = $house['stair'];
                    }
                    $effect = (new Effect())->plotEdit($house_id,$bedroom,$sittingRoom_diningRoom,$toilet,$kitchen,$window,$area,$high,$province,$province_code,$city,$city_code,$district,$district_code,$toponymy,$street,$particulars,$stairway,$house_image,$type,$sort_id,$stair_id);

                    if (!empty($house['drawing_list'])){
                            if (!empty($house['id'])){
                                $images_id     = $house['id'];
                                $effect_images = $house['drawing_list'];
                                $series_id     = $house['series'];
                                $style_id      = $house['style'];
                                $images_user   = '案例图片';
                                ( new EffectPicture())->plotEdit($images_id,$effect_images,$series_id,$style_id,$images_user);
                            }
                    }

                    foreach ($house['all_goods'] as $goods){
                        if (!empty($goods['id'])){
                            $goods_id = $goods['id'];
                            $goods_first = $goods['first_name'];
                            $goods_second = $goods['second_name'];
                            $goods_three = $goods['three_name'];
                            $goods_code = $goods['good_code'];
                            $goods_quantity = $goods['good_quantity'];
                            (new WorksData())->plotEdit($goods_id,$goods_first,$goods_second,$goods_three,$goods_code,$goods_quantity);
                        }
                    }

                    foreach ($house['worker_list'] as $worker){
                        if (!empty($worker['id'])){
                            $worker_id = $worker['id'];
                            $worker_kind = $worker['worker_kind'];
                            $worker_price = $worker['price'];
                            (new WorksWorkerData())->plotEdit($worker_id,$worker_kind,$worker_price);
                        }
                    }

                    foreach ($house['backman_option'] as $backman){
                        if (!empty($backman['id'])){
                            $backman_id = $backman['id'];
                            $backman_option = $backman['name'];
                            $backman_value  = $backman['num'];
                            (new WorksBackmanData())->plotEdit($backman_id,$backman_option,$backman_value);
                        }
                    }
                    if (!empty($house['delete_goods'])){
                        WorksData::deleteAll(['id'=>$house['delete_goods']]);
                    }
                    if (!empty($house['delete_workers'])){
                        WorksWorkerData::deleteAll(['id'=>$house['delete_workers']]);
                    }
                    if (!empty($house['delete_backman'])){
                        WorksBackmanData::deleteAll(['id'=>$house['delete_backman']]);
                    }
                }
            }
            // 删除功能
            if (!empty($request['delete_house'])){
                Effect::deleteAll(['id'=>$request['delete_house']]);
                EffectPicture::deleteAll(['effect_id'=>$request['delete_house']]);
                WorksBackmanData::deleteAll(['effect_id'=>$request['delete_house']]);
                WorksWorkerData::deleteAll(['effect_id'=>$request['delete_house']]);
                WorksData::deleteAll(['effect_id'=>$request['delete_house']]);
            }

            if (!empty($request['delete_drawing'])){
                EffectPicture::deleteAll(['id'=>$request['delete_drawing']]);
            }
        }
        if ($effect) {
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
            ->where(['state'=>0])
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
        $find = AssortGoods::findAll([]);

        if (!empty($find)) {
            $id = [];
            foreach ($find as $find_one) {
                $id [] = $find_one['category_id'];
            }
            (new AssortGoods())->deleteAll(['and',['category_id'=>$id],['state'=> 0]]);
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

    /**
     * homepage list show
     * @return string
     */
    public function actionHomepageList(){
        $request = \Yii::$app->request;
        $province_code = trim($request->get('province',''));
        $city_code = trim($request->get('city',''));
        $code = 200;
        return Json::encode([
            'code' => $code,
            'msg'  => 'ok',
            'list' =>  (new BrainpowerInitalSupervise())->findByCode($province_code,$city_code)
        ]);
    }

    /**
     * 首页管理排序
     * @return string
     */
    public function actionHomepageSort(){
        $post = \Yii::$app->request->post();
        foreach ($post['sort'] as $one_post){
            $sort_message = BrainpowerInitalSupervise::findOne($one_post['id']);
            $sort_message->sort = $one_post['sort'];
            $sort_message->save();
        }
        return Json::encode([
            'code'=>200,
            'msg'=>'OK'
        ]);
    }

    /**
     * the area of the page where the page is added
     * @return string
     */
    public function actionHomepageDistrict()
    {
        $request = \Yii::$app->request;
        $province_code = trim($request->get('province',''));
        $city_code = trim($request->get('city',''));
        $code = 200;
        return Json::encode([
            'code' => $code,
            'msg'  => 'ok',
            'list' => (new Effect)->findCode($province_code,$city_code),
        ]);

    }

    /**
     * homepage toponymy find
     * @return string
     */
    public function actionHomepageToponymy()
    {
        $request    = \Yii::$app->request;
        $province   = trim($request->post('province',''));
        $city       = trim($request->post('city',''));
        $district   = trim($request->post('district',''));
        $code = 200;
        return Json::encode([
            'code' => $code,
            'msg'  => 'ok',
            'list' => (new Effect)->findToponymy($province,$city,$district),
        ]);
    }

    /**
     * homepage street find
     * @return string
     */
    public function actionHomepageStreet()
    {
        $request    = \Yii::$app->request;
        $province   = trim($request->post('province',''));
        $city       = trim($request->post('city',''));
        $district   = trim($request->post('district',''));
        $toponymy   = trim($request->post('toponymy',''));
        $code = 200;
        return Json::encode([
            'code' => $code,
            'msg'  => 'ok',
            'list' => (new Effect)->findStreet($province,$city,$district,$toponymy),
        ]);
    }

    /**
     * homepage case find
     * @return string
     */
    public function actionHomepageCase()
    {
        $request  = \Yii::$app->request;
        $province = trim($request->post('province',''));
        $city     = trim($request->post('city',''));
        $district = trim($request->post('district',''));
        $toponymy = trim($request->post('toponymy',''));
        $street   = trim($request->post('street',''));
        $code = 200;
        return Json::encode([
            'code' => $code,
            'msg'  => 'ok',
            'list' => (new Effect)->findCase($province,$city,$district,$toponymy,$street),
        ]);
    }

    /**
     * homepage add function
     * @return string
     */
    public function actionHomepageAdd()
    {
        $request = \Yii::$app->request;
        $province_code = District::findByCode(trim($request->post('province')));
        $city_code = District::findByCode(trim($request->post('city')));
        $district_code = District::findByCode(trim($request->post('district')));
        $add_item = new BrainpowerInitalSupervise();
        $add_item->recommend_name  = trim($request->post('recommend_name',''));
        $add_item->district_code   = trim($request->post('district',''));
        $add_item->toponymy        = trim($request->post('toponymy',''));
        $add_item->street          = trim($request->post('street',''));
        $add_item->house_type_name = trim($request->post('house_type_name',''));
        $add_item->image           = trim($request->post('image',''));
        $add_item->province_code   = trim($request->post('province',''));
        $add_item->city_code       = trim($request->post('city',''));
        $add_item->add_time        = time();
        $add_item->province        = $province_code['name'];
        $add_item->city            = $city_code['name'] ;
        $add_item->district        = $district_code['name'];
        $code = 1000;
        if (!$add_item->validate()){
            return Json::encode([
               'code' =>  $code,
                'msg' => '请求的参数不正确'
            ]);
        }

        if ($add_item->save()){
            return Json::encode([
               'code' => 200,
                'mag' => 'ok'
            ]);
        }
    }

    /**
     * homepage status edit function
     * @return string
     */
    public function actionHomepageStatus()
    {
        $code = 1000;
        $id       = trim(\Yii::$app->request->get('id',''));
        $status   = trim(\Yii::$app->request->get('status',''));
        $find_one = BrainpowerInitalSupervise::findOne(['id'=>$id]);
        $find_one->status = $status;
        if (!$find_one->validate()){
            return Json::encode([
                'code' =>  $code,
                'msg'  => '请求的参数不正确'
            ]);
        }

        if ($find_one->save()){
            return Json::encode([
                'code' => 200,
                'mag'  => 'ok'
            ]);
        }
    }

    /**
     * homepage edit function
     * @return string
     */
    public function actionHomepageEdit()
    {
        $request = \Yii::$app->request;
        $id = trim($request->post('id',''));
        $province_code = District::findByCode(trim($request->post('province')));
        $city_code = District::findByCode(trim($request->post('city')));
        $district_code = District::findByCode(trim($request->post('district')));

        $add_item = new BrainpowerInitalSupervise();
        $item = $add_item->findOne(['id'=>$id]);
        $item->recommend_name  = trim($request->post('recommend_name',''));
        $item->district_code   = trim($request->post('district',''));
        $item->toponymy        = trim($request->post('toponymy',''));
        $item->street          = trim($request->post('street',''));
        $item->house_type_name = trim($request->post('house_type_name',''));
        $item->image           = trim($request->post('image',''));
        $item->province_code   = trim($request->post('province',''));
        $item->city_code       = trim($request->post('city',''));
        $item->province        = $province_code['name'];
        $item->city            = $city_code['name'] ;
        $item->district        = $district_code['name'];
        $code = 1000;
        if (!$item->validate()){
            return Json::encode([
                'code' =>  $code,
                'msg' => '请求的参数不正确'
            ]);
        }

        if ($item->save()){
            return Json::encode([
                'code' => 200,
                'mag' => 'ok'
            ]);
        }
    }

    /**
     * homepage delete function
     * @return string
     */
    public function actionHomepageDelete()
    {
        $id = trim(\Yii::$app->request->post('id',''));
        BrainpowerInitalSupervise::findOne($id)->delete();
        return  Json::encode([
           'code'=>200,
            'msg'=>'OK'
        ]);

    }

    /**
     * apartment area list
     * @return string
     */
    public function actionApartmentAreaList()
    {
        $code = 200;
        $select = "id,min_area,max_area";
        return Json::encode([
            'code'=> $code,
            'msg'=>'ok',
            'list'=>ApartmentArea::findByAll($select),
        ]);
    }

    /**
     * apartment area add and edit
     * @return string
     */
    public function actionApartmentArea()
    {
        $post = \Yii::$app->request->post();
        foreach ($post['list'] as $one_post){
            if (isset($one_post['id'])){
                $apartment_area = ApartmentArea::findOne(['id'=>$one_post['id']]);
                $apartment_area->min_area = $one_post['min_area'];
                $apartment_area->max_area = $one_post['max_area'];
                $apartment_area->save();
            }
            else {
                $apartment = new ApartmentArea();
                $apartment->province_code  = $post['province'];
                $apartment->city_code      = $post['city'];
                $apartment->min_area       = $one_post['min_area'];
                $apartment->max_area       = $one_post['max_area'];
                $apartment->save();
            }
        }
        return Json::encode([
           'code' => 200,
            'msg' => 'ok',
        ]);
    }

    /**
     * Decoration list
     * @return string
     */
    public function actionDecorationList()
    {
        $page = (int)\Yii::$app->request->get('page', 1);
        $size = (int)\Yii::$app->request->get('size', DecorationAdd::PAGE_SIZE_DEFAULT);
        $where  = [];
        $select = [];
        $decoration_add = DecorationAdd::pagination($where,$select,$page,$size);
        return Json::encode([
            'code' => 200,
            'msg'  => 'ok',
            'list'  => $decoration_add,
        ]);
    }

    /**
     * decoration ass classify
     * @return string
     */
    public function actionDecorationAddClassify()
    {
        $one_goods = trim(\Yii::$app->request->post('classify',''));
        $select = "goods.id,goods.title,sku,supplier_price,platform_price,market_price,left_number,";
        $goods  = Goods::priceDetail(self::CATEGORY_LEVEL,$one_goods,$select);
        if (!isset($goods['0'])){
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }
        foreach ($goods as &$one_goods){
            $one_goods['supplier_price'] = $one_goods['supplier_price'] / 100;
            $one_goods['platform_price'] = $one_goods['platform_price'] / 100;
            $one_goods['market_price'] = $one_goods['market_price'] / 100;
        }
        $goods_attr = GoodsAttr::frontDetailsByGoodsId($goods['0']['id']);
        return Json::encode([
            'goods'=> $goods['0'],
            'goods_attr'=> $goods_attr,
        ]);
    }

    /**
     * apartment area list
     * @return string
     */
    public function actionHouseTypeList()
    {
        $select = 'min_area,max_area';
        return Json::encode([
           'list'=> ApartmentArea::findByAll($select),
        ]);
    }

    /**
     * decoration message style series and house type list
     * @return string
     */
    public function actionDecorationMessageList()
    {
        $id = trim(\Yii::$app->request->post('id',''));
        return Json::encode([
           'list'=>DecorationMessage::findAll(['decoration_add_id'=>$id]),
        ]);
    }

    /**
     * decoration add
     * @return string
     */
    public function actionDecorationAdd()
    {
        $post = \Yii::$app->request->post();
        $decoration_add = new DecorationAdd();
        $decoration_add->province_code = $post['province'];
        $decoration_add->city_code     = $post['city'];
        $decoration_add->one_materials = $post['one_name'];
        $decoration_add->two_materials = $post['two_name'];
        $decoration_add->three_materials = $post['three_name'];
        $decoration_add->correlation_message = $post['message'];
        $decoration_add->sku           = $post['code'];
        $decoration_add->add_time      = time();

        if (!$decoration_add->validate()){
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg'=>\Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$decoration_add->save()){
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg'=>\Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $id = $decoration_add->attributes['id'];
        $decoration_message = new DecorationMessage();
        foreach ($post['add'] as $k => &$one_post){
            $one_post ['id'] = $id;
            if (isset($one_post['min_area'])) {
                $add_decoration [] = $one_post;
            }elseif (isset($one_post['style'])) {
                $style[] = $one_post;
            }elseif (isset($one_post['series'])) {
                $series[] = $one_post;
            }
        }
        if (isset($add_decoration)){
            $columns = ['min_area','max_area','quantity','decoration_add_id'];
            $decoration_message->findByInsert($add_decoration,$columns);
        }
        if (isset($style)){
            $columns = ['style_id','quantity','decoration_add_id'];
            $decoration_message->findByInsert($style,$columns);
        }
        if (isset($series)){
            $columns = ['series_id','quantity','decoration_add_id'];
            $decoration_message->findByInsert($series,$columns);
        }
        return Json::encode([
           'code' => 200,
            'msg' => 'ok',
        ]);
    }

    /**
     * decoration delete
     * @return string
     */
    public function actionDecorationDel()
    {
        $id = trim(\Yii::$app->request->post('id',''));
        DecorationAdd::deleteAll(['id'=>$id]);
        DecorationMessage::deleteAll(['decoration_add_id'=>$id]);
        return Json::encode([
           'code' => 200,
            'msg' => 'OK'
        ]);
    }

    /**
     * decoration edit list
     */
    public function actionDecorationEditList()
    {
        $id = trim(\Yii::$app->request->post('id',''));
        $sku = DecorationAdd::findOne($id);
        $message_select = 'id,quantity,style_id,series_id,min_area,max_area';
        $where = 'decoration_add_id='.$sku['id'];
        $decoration_message = DecorationMessage::findById($message_select,$where);
        $select ='id,category_id,title,sku,supplier_price,platform_price,market_price,left_number';
        $goods = Goods::findBySku($sku,$select);
        $goods['supplier_price'] = $goods['supplier_price'] / 100;
        $goods['platform_price'] = $goods['platform_price'] / 100;
        $goods['market_price'] = $goods['market_price'] / 100;
        $goods_attr = GoodsAttr::frontDetailsByGoodsId($goods['id']);

        return Json::encode([
           'decoration_add'=>$sku,
           'decoration_message'=>$decoration_message,
           'goods'=>$goods,
           'goods_attr'=>$goods_attr,
        ]);
    }

    /**
     * decoration edit
     * @return string
     */
    public function actionDecorationEdit()
    {
        $post = \Yii::$app->request->post();
        $decoration_add = DecorationAdd::findOne($post['id']);
        $decoration_add->correlation_message = $post['message'];
        $decoration_add->sku           = $post['code'];
        if (!$decoration_add->validate()){
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg'=>\Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$decoration_add->save()){
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg'=>\Yii::$app->params['errorCodes'][$code],
            ]);
        }

        foreach ($post['add'] as $one_post){
            if (isset($one_post['id'])) {
                DecorationMessage::findByUpdate($one_post['quantity'],$one_post['id']);
            } else {
                $columns = ['series_id','quantity','decoration_add_id'];
                DecorationMessage::findByInsert($one_post,$columns);
            }

        }

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
        ]);
    }

    /**
     * commonality  list
     * @return string
     */
    public function actionCommonalityList()
    {
        $select = 'id,title';
        $where  = 'level = 1';
        return Json::encode([
           'post'=> Points::findByPid($select,$where),
        ]);
    }

    /**
     * commonality  one title list
     * @return string
     */
    public function actionCommonalityTitle()
    {
        $id = trim(\Yii::$app->request->post('id',''));
        $select = 'id,title';
        $where  = 'pid='.$id;
        $title['one_title'] = Points::findByPid($select,$where);
        foreach ($title['one_title'] as $one_title){
            $ids [] = $one_title['id'];
        }
        $string_ids = implode(',',$ids);
        $two_select = 'id,title,count,pid';
        $two_where  = 'pid in ('.$string_ids.')';
        $title['two_title'] = Points::findByPid($two_select,$two_where);

        return Json::encode([
           'list'=> $title,
        ]);
    }

    /**
     * commonality white fuel edit and add
     * @return string
     */
    public function actionCommonalityTitleAdd()
    {
        $post = \Yii::$app->request->post();
        $points = new Points();
        foreach ($post  as $value){
            if (isset($value['one_title'])){
                $one_title[] = $value;
            }
            if (isset($value['two_title'])){
                if (isset($value['two_title']['id'])){
                    $two_title_id [] = $value;
                    $one = Points::findByUpdate($value['count'],$value['id']);
                } else {
                    $two_title[] = $value;
                }
            }
        }
        if (isset($one_title)){
            $columns = ['pid','title','level'];
            $points->findByInsert($one_title,$columns);
        } elseif (isset($two_title)){
            $columns = ['pid','title','count','level'];
            $points->findByInsert($two_title,$columns);
        }

        return Json::encode([
           'code' => 200,
            'msg' => 'ok',
        ]);
    }

    /**
     * commonality area proportion list
     * @return string
     */
    public function actionCommonalityAreaProportionList()
    {
        $id = trim(\Yii::$app->request->post('id',''));
        $select = 'id,project,project_value';
        $where = 'points_id='.$id;
        $area_select = 'id,min_area,max_area,project_name,project_value';
        return Json::encode([
            'list' => ProjectView::findByAll($select,$where),
            'area' => Apartment::findByAll($area_select,$where),
        ]);

    }

    /**
     * commonality area proportion edit
     * @return string
     */
    public function actionCommonalityAreaProportionEdit()
    {
        $post = \Yii::$app->request->post();
        foreach ($post['value'] as $value){
            ProjectView::findByUpdate($value['coefficient'],$value['id']);
        }

        foreach ($post['area'] as $area_value){
            if (isset($area_value['id'])){
                Apartment::findByUpdate($area_value['value'],$area_value['id']);
            } else {
                $add[] = $area_value;
            }
        }
        if (isset($add)){
            $columns = ['min_area','max_area','project_name','project_value','points_id'];
            Apartment::findByInsert($add,$columns);
        }
        return Json::encode([
           'code' => 200,
           'msg'  => 'OK',
        ]);
    }

    /**
     * Goods management list
     * @return string
     */
    public function actionGoodsManagementList()
    {
        $select = 'title,pid,path,category_id';
        $where = 'state = 1';
        return Json::encode([
           'list'=> AssortGoods::findByAll($select,$where),
        ]);
    }

    public function actionGoodsManagementAdd()
    {
        $post = \Yii::$app->request->post();
        (new AssortGoods())->deleteAll(['state'=>1]);
        foreach($post as $management) {
            AssortGoods::findByInsert($management);
        }
        return Json::encode([
           'code'=>200,
           'msg'=>'ok',
        ]);
    }
}