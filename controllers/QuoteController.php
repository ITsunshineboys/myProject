<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/23 0023
 * Time: 上午 11:48
 */
namespace app\controllers;
use app\models\AssortGoods;
use app\models\BrainpowerInitalSupervise;
use app\models\DecorationAdd;
use app\models\DecorationParticulars;
use app\models\District;
use app\models\Effect;
use app\models\EffectPicture;
use app\models\EngineeringStandardCraft;
use app\models\Goods;
use app\models\GoodsAttr;
use app\models\GoodsCategory;
use app\models\LaborCost;
use app\models\Series;
use app\models\StairsDetails;
use app\models\Style;
use app\models\WorksBackmanData;
use app\models\WorksData;
use app\models\WorksWorkerData;
use app\services\ExceptionHandleService;
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
        $series = Series::findBySeries();
        $style = Style::findByStyle();
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

    public function actionHomePageList(){
        $request = \Yii::$app->request;
        $province_code = trim($request->post('province',''));
        $city_code = trim($request->post('city',''));
        $code = 200;
        return Json::encode([
            'code' => $code,
            'msg'  => 'ok',
            'list' =>  (new BrainpowerInitalSupervise())->findByCode($province_code,$city_code)
        ]);
    }

    public function actionHomePageAdd()
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
        $add_item->sort            = trim($request->post('sort',''));
        $add_item->province        = $province_code;
        $add_item->city            = $city_code ;
        $add_item->district        = $district_code;
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

    public function actionHomePageStatus()
    {
        $code = 1000;
        $id = trim(\Yii::$app->request->get('id',''));
        $status = trim(\Yii::$app->request->get('status',''));
        $find_one = BrainpowerInitalSupervise::findOne(['id'=>$id]);
        $find_one->status = $status;
        if (!$find_one->validate()){
            return Json::encode([
                'code' =>  $code,
                'msg' => '请求的参数不正确'
            ]);
        }

        if ($find_one->save()){
            return Json::encode([
                'code' => 200,
                'mag' => 'ok'
            ]);
        }
    }
}