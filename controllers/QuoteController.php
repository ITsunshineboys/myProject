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
use app\models\ChatRecord;
use app\models\CoefficientManagement;
use app\models\DecorationAdd;
use app\models\DecorationMessage;
use app\models\DecorationParticulars;
use app\models\District;
use app\models\Effect;

use app\models\EffectEarnest;
use app\models\EffectMaterial;
use app\models\EffectPicture;
use app\models\EffectToponymy;
use app\models\EngineeringCraftName;
use app\models\EngineeringStandardCarpentryCoefficient;
use app\models\EngineeringStandardCarpentryCraft;
use app\models\EngineeringStandardCraft;
use app\models\FixedGrabbingGoods;
use app\models\Goods;
use app\models\GoodsAttr;
use app\models\GoodsCategory;
use app\models\LaborCost;
use app\models\Points;
use app\models\ProjectView;
use app\models\Series;
use app\models\StairsDetails;
use app\models\Style;
use app\models\Supplier;
use app\models\Toponymy;
use app\models\User;
use app\models\UserNewsRecord;
use app\models\WorkerCraftNorm;
use app\models\WorkerRank;
use app\models\WorkerType;
use app\models\WorksBackmanData;
use app\models\WorksData;
use app\models\WorksWorkerData;
use app\services\BasisDecorationService;
use app\services\ChatService;
use app\services\ExceptionHandleService;
use app\services\FileService;
use app\services\ModelService;
use app\services\StringService;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;

class QuoteController extends Controller
{

    /**
     * Actions accessed by logged-in users
     */
    const ACCESS_LOGGED_IN_USER = [
        'labor-cost-list',
        'labor-cost-edit-list',
        'labor-cost-edit',
        'project-norm-list',
        'project-norm-edit-list',
        'project-norm-edit' ,
        'project-norm-woodwork-list',
        'project-norm-woodwork-edit' ,
        'coefficient-list',
        'coefficient-add',
        'plot-list',
        'labor-list',
        'series-and-style',
        'plot-add' ,
        'plot-edit-view',
        'plot-edit',
        'plot-del',
        'assort-goods',
        'assort-goods-list',
        'assort-goods-add',
        'homepage-list',
        'homepage-sort',
        'homepage-district',
        'homepage-toponymy',
        'homepage-street',
        'homepage-case',
        'homepage-add',
        'homepage-status',
        'homepage-edit',
        'homepage-delete',
        'apartment-area-list',
        'apartment-area',
        'decoration-list',
        'decoration-add-classify',
        'house-type-list',
        'decoration-message-list',
        'decoration-add',
        'decoration-del' ,
        'decoration-edit-list' ,
        'decoration-edit' ,
        'commonality-list' ,
        'commonality-title',
        'commonality-title-add',
        'commonality-title-two-add',
        'commonality-else-list',
        'commonality-else-edit',
        'goods-management-list',
        'goods-management-add' ,
        'sku-fefer' ,
    ];

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
     *
     * labor list show
     * @return string
     */
    public function actionLaborCostList()
    {
//        $city = trim(\Yii::$app->request->get('city',''));
//        $data =LaborCost::LaborCostList('id,worker_kind_id',"city_code={$city}");
//        foreach ($data as &$v) {
//            $v['worker_kind'] = WorkerType::gettype($v['worker_kind_id']);
//            unset($v['worker_kind_id']);
//        }

        return Json::encode([
            'code'=> 200,
            'msg'=> 'ok',
            'list' =>WorkerType::find()
                ->where(['status'=>1,'pid'=>0])
                ->select('id,worker_name')
                ->asArray()
                ->all()
        ]);
    }

    /**
     * 做工标准修改列表 (已优化)
     * @return string
     */
    public function actionLaborCostEditList()
    {
        $id = (int)trim(\Yii::$app->request->get('id',''));
        $city_code = (int)trim(\Yii::$app->request->get('city_code',''));
        $province_code = (int)trim(\Yii::$app->request->get('province_code',''));
//        $where = "id = $id and city_code = $city_code";
//        $select = 'id,city_code,province_code,univalence,worker_kind_id,unit';
        $labor_cost = LaborCost::workerKind($id,$city_code,$province_code);
        if($labor_cost['univalence']==''){
            $worker_craft_norm = WorkerType::findPidbyid($id);
        }else{
            $worker_craft_norm = WorkerCraftNorm::findById($labor_cost['id']);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'labor_cost'=> $labor_cost,
            'worker_craft_norm'=>$worker_craft_norm
        ]);
    }

    /**
     * 做工标准修改（已优化)
     * @return string
     */
    public function actionLaborCostEdit()
    {
        $post = \Yii::$app->request->post();
        //修改
        if(isset($post['id'])){
            foreach ($post['else'] as $one_post){
                if ($one_post['quantity']){
                    $worker_craft_norm = WorkerCraftNorm::findOne($one_post['id']);
//                    var_dump($worker_craft_norm);
                    $worker_craft_norm->quantity = $one_post['quantity'] * 100;
                    $worker = $worker_craft_norm->save();
                }
            }
            $labor_cost = LaborCost::findOne($post['id']);
            $labor_cost->univalence = $post['univalence'] * 100;
            $labor_cost=$labor_cost->save();
        }elseif(isset($post['worker_id'])){
            $labor_cost =new LaborCost();
            $labor_cost->univalence = $post['univalence'] * 100;
            $labor_cost->worker_kind_id = $post['worker_id'];
            $labor_cost->province_code = $post['province_code'];
            $labor_cost->city_code = $post['city_code'];
            $labor_cost=$labor_cost->save();

            $id=\Yii::$app->db->getLastInsertID();

            foreach ($post['else'] as $one_post){
                if ($one_post['quantity']){

                    $worker_craft_norm =new WorkerCraftNorm();
                    $worker_craft_norm->worker_type_id = $one_post['id'];
                    $worker_craft_norm->quantity = $one_post['quantity'] * 100;
                    $worker_craft_norm->labor_cost_id = $id;
                    $worker = $worker_craft_norm->save();
                }
            }

        }

        if (!$worker){
            $code = 500;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code],
            ]);
        }
        if (!$labor_cost){
            $code = 500;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code],
            ]);
        }



        return Json::encode([
            'code' =>200,
            'msg'=>'OK'
        ]);
    }

    /**
     * 工程标准列表 ok
     * @return string
     */
    public function actionProjectNormList(){
//        $city = trim(\Yii::$app->request->get('city',''));
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'list'=>WorkerType::WorkerCraft3(),
        ]);
    }

    /**
     * 工程标准修改列表 ok
     * @return string
     */
    public function actionProjectNormEditList(){
        $city = (int)trim(\Yii::$app->request->get('city_code',''));
        $project_id = trim(\Yii::$app->request->get('id',''));
        $data=EngineeringStandardCraft::findALLByid($project_id,$city);
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
           'list'=>$data
        ]);
    }

    /**
     * 工程标准修改 ok
     * @return string
     */
    public function actionProjectNormEdit(){
        $post = \Yii::$app->request->post();

        foreach ($post['material'] as $one_material){

            if(isset($one_material['id'])){
                $material = EngineeringStandardCraft::findallbycity($post['city_code'],$one_material['id']);
                $material->material = $one_material['value'] * 100;
                $edit_material = $material->save();
            }elseif(isset($one_material['project_id'])){
                $model =new EngineeringStandardCraft();
                $model->city_code=$post['city_code'];
                $model->material=$one_material['value']*100;
                $model->project_id=$one_material['project_id'];
                $edit_material = $model->save();
            }

        }
        if (!$edit_material){
            $code = 1000;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code],
            ]);
        }
        return Json::encode([
           'code'=>200,
            'msg'=>'ok'
        ]);
    }

    /**
     * 工程标准修改 木作列表 ok
     * @return string
     */
    public function actionProjectNormWoodworkList()
    {
        $city = (int)trim(\Yii::$app->request->get('city',''));

        $material = [22,9,12,13]; // 龙骨 丝杆 细木工板 石膏板分类 id   13
        $goods_ = Goods::priceDetail(3,$material);



        $category = array_values(Effect::array_group_by($goods_,'title'));

        foreach ($category as $v){
            $goods_c [] = BasisDecorationService::profitMargin($v);
        }

        $id = [];
        foreach ($goods_c as $one){
            $id [] = $one['id'];
        }

        $goods['specification'] = GoodsCategory::attrValue($id);

        $series = Series::findBySeries();
        $style  = Style::findByStyle();
        $where = 'city_code='.$city;
        $coefficient = EngineeringStandardCarpentryCoefficient::findByAll($where);
        $goods['find_specification'] = EngineeringStandardCarpentryCraft::findByAll($where);
//        $a =[];
//        var_dump($goods);die;
//        if($goods['specification'] &&  $goods['find_specification']){
//            foreach ($goods['specification'] as $k1=>$v1){
//                $a[$k1]=$v1['value'];
//            }
//            foreach ($goods['find_specification'] as $k2=>&$v2){
//                if($v2['value']==''){
//                    $v2['value']=$a[$k2];
//                }
//            }
//        }




        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
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
                $value = EngineeringStandardCraft::findallbycity($post['city_code'],$one_post['id']);
                $value->material = $one_post['value'] * 100;
                $value->save();
            }elseif (isset($one_post['project_id'])){
                $model =new EngineeringStandardCraft();
                $model->city_code=$post['city_code'];
                $model->material=$one_post['value']*100;
                $model->project_id=$one_post['project_id'];
                $model->save();
            }
        }


        foreach ($post['specification'] as $one_specification){
            if(isset($one_specification['type_id'])){
                $specification = EngineeringStandardCarpentryCraft::find()
                    ->where(['type_id'=>$one_specification['type_id'],'city_code'=>$post['city_code']])
                    ->one();

                if(is_numeric($specification['value'])){
                    $specification->value = $one_specification['value'] * 100;
                }else{
                    $specification->value =$one_specification['value'];
                }
            }elseif (isset($one_specification['id'])){
                $specification = new EngineeringStandardCarpentryCraft();
                $specification->type_id=$one_specification['id'];
                $specification->value=$one_specification['value']*100;
                $specification->city_code=$post['city_code'];
                if($one_specification['id']==71){
                    $specification->unit=2;
                }else{
                    $specification->unit=1;
                }

            }


            $specification->save();
        }

        foreach ($post['coefficient'] as $one_coefficient){
            if (isset($one_coefficient['id'])){
                $coefficient = EngineeringStandardCarpentryCoefficient::findOne($one_coefficient['id']);
                $coefficient->value = $one_coefficient['value'] * 100;
                $coefficient->save();
            }
        }

        foreach ($post['coefficient'] as $one_coefficient){
            if (isset($one_coefficient['add_id'])){
                $coefficient = new EngineeringStandardCarpentryCoefficient();
                $coefficient->city_code  = $post['city_code'];
                $coefficient->project  = $one_coefficient['add_id'];
                $coefficient->value  = $one_coefficient['value'] * 100;
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
        $city = (int)trim(\Yii::$app->request->get('city',''));
        $where = 'city_code = '.$city;
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'coefficient'=>CoefficientManagement::findByAll('category_id,coefficient',$where),
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

        $tr = \Yii::$app->db->beginTransaction();
        try{
             $del = CoefficientManagement::deleteAll();
            if (!$del){
                $tr->rollBack();
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg'  => \Yii::$app->params['errorCodes'][$code]
                ]);
            }
            foreach ($post['value'] as $value){
                $row = (new CoefficientManagement())->findByInsert($value,$post['city']);
            }

            if (!$row){
                $tr->rollBack();
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg'  => \Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $tr->commit();
        }catch (\Exception $e) {
            $tr->rollBack();
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg'  => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        return Json::encode([
            'code'=>200,
            'msg'=>'OK'
        ]);
    }

    /**
     * 小区列表
     * @return string
     */
    public function actionPlotList()
    {
        $page = (int)\Yii::$app->request->get('page', 1);
        $size = (int)\Yii::$app->request->get('size', Effect::PAGE_SIZE_DEFAULT);

        $post = \Yii::$app->request->get('post');
        $min_time = (\Yii::$app->request->get('min'));
        $max_time = (\Yii::$app->request->get('max'));
        $toponymy = \Yii::$app->request->get('toponymy');


        switch ($post || $min_time || $max_time || $toponymy){
            case $post && !$min_time && !$max_time && !$toponymy:
                if (substr($post, 4) == 00) {
                    $where = 'city_code = '.$post . ' and type !=  2';
                    $effect = Effect::pagination($where,$page,$size);
                } else {
                    $where = 'district_code = '.$post. ' and type !=  2';
                    $effect = Effect::pagination($where,$page,$size);
                }
                break;
            case $post && $min_time && !$max_time && !$toponymy:
                $where = "add_time >=" . strtotime($min_time) . " AND city_code = ".$post. ' and type !=  2';
                $effect = Effect::pagination($where,$page,$size);
                break;
            case $post && !$min_time && $max_time && !$toponymy:
                $where = " add_time <=". strtotime($max_time)." AND city_code = ".$post. ' and type !=  2';
                $effect = Effect::pagination($where,$page,$size);
                break;
            case  $post  && $min_time && $max_time && !$toponymy:
                if (strtotime($min_time) == strtotime($max_time)){
                    $timeType = ModelService::timeDeal($min_time);
                    $where = "add_time >=" . strtotime($timeType[0]) ." and add_time <=". strtotime($timeType[1]) ." AND city_code = ".$post. ' and type !=  2';
                    $effect = Effect::pagination($where,$page,$size);
                } else {
                    $where = "add_time >=" . strtotime($min_time) ." and add_time <=". strtotime($max_time) ." AND city_code = ".$post. ' and type !=  2';
                    $effect = Effect::pagination($where,$page,$size);
                }
                break;
            case  $post && !$min_time && !$max_time && $toponymy:
                $where = "toponymy like '%{$toponymy}%' and city_code = ".$post. ' and type !=  2';
                $effect = Effect::pagination($where,$page,$size);
                break;
        }

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
        $data= WorkerType::Laborlist();

        return Json::encode([
            'code' => 200,
            'msg'=> 'ok',
            'labor_list' => $data,

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
            'code' => 200,
            'msg' => 'ok',
            'series' => $series,
            'style' => $style,
            'stairs_details'=>$stairs_details,
        ]);
    }

    /**
     * 小区列表
     * @return string
     */

    public function actionEffectPlotList(){
        $request= new Request();
        $city_code = (int)$request->get('district_code','');
        $code = 1000;
        if(!$city_code){

            return Json::encode([
                'code' => $code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $start_time=trim($request->get('start_time',''));
        $end_time= trim($request->get('end_time',''));
//        $district_code = (int)$request->get('$district_code','');
        $keyword = trim($request->get('keyword',''));
        $where = 1;
        if(!$keyword) {
            if (($start_time && !StringService::checkDate($start_time))
                || ($end_time && !StringService::checkDate($end_time))
            ) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
            if ($start_time == $end_time) {
                list($start_time, $end_time) = ModelService::timeDeal($start_time);
            } else {
                $start_time && $end_time .= ' 23:59:59';
            }
            if ($start_time) {
                $startTime = (int)strtotime($start_time);
                $startTime && $where .= " and add_time >= {$startTime}";
            }
            if ($end_time) {
                $end_time = (int)strtotime($end_time);
                $end_time && $where .= " and add_time <= {$end_time}";
            }
            if($city_code ){
                $where .= " and  (district_code = {$city_code} or city_code = {$city_code})";
            }
        }else{
            $where .=" and  toponymy like '%{$keyword}%'";
        }
        $page = (int)\Yii::$app->request->get('page', 1);
        $size = (int)\Yii::$app->request->get('size', ModelService::PAGE_SIZE_DEFAULT);

        $paginationData = EffectToponymy::pagination($where, EffectToponymy::FIELDS_EXTRA, $page, $size);
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$paginationData
        ]);

    }
    /**
     * 小区添加
     * @return string
     */

    public function actionEffectPlotAdd(){
        $request = \Yii::$app->request->post();
        $province_chinese = District::findByCode((int)$request['province_code']);
        $city_chinese = District::findByCode((int)$request['city_code']);
        $district_chinese = District::findByCode((int)$request['district_code']);

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $ids=[];
                foreach ($request['house_informations'] as $house) {
                    if ($house['is_ordinary'] == 0) {
                        //普通户型添加
                        $bedroom                = $house['cur_room'];
                        $sittingRoom_diningRoom = $house['cur_hall'];
                        $toilet                 = $house['cur_toilet'];
                        $kitchen                = $house['cur_kitchen'];
                        $window                 = $house['window'];
                        $area                   = $house['area'];
                        $high                   = $house['high'];
                        $province               = $province_chinese['name'];
                        $province_code          = $request['province_code'];
                        $city                   = $city_chinese['name'];
                        $city_code              = $request['city_code'];
                        $district               = $district_chinese['name'];
                        $district_code          = $request['district_code'];
                        $toponymy               = $request['house_name'];
                        $street                 = $request['address'];
                        $particulars            = $house['house_type_name'];
                        $stairway               = $house['have_stair'];
                        $house_image            = $house['cur_imgSrc'];
                        $type                   = $house['is_ordinary'];
                        $sort_id                = $house['sort_id'];
                        $effect_                = (new Effect())->plotAdd($bedroom, $sittingRoom_diningRoom, $toilet, $kitchen, $window, $area, $high, $province, $province_code, $city, $city_code, $district, $district_code, $toponymy, $street, $particulars, $stairway, $house_image, $type, $sort_id, 0);

                        if (!$effect_) {
                            $transaction->rollBack();
                            $code = 500;
                            return Json::encode([
                                'code' => $code,
                                'msg' => \Yii::$app->params['errorCodes'][$code]
                            ]);
                        }

                        $hall_area         = $house['hall_area'];
                        $hall_perimeter    = $house['hall_girth'];
                        $bedroom_area      = $house['room_area'];
                        $bedroom_perimeter = $house['room_girth'];
                        $toilet_area       = $house['toilet_area'];
                        $toilet_perimeter  = $house['toilet_girth'];
                        $kitchen_area      = $house['kitchen_area'];
                        $kitchen_perimeter = $house['kitchen_girth'];
                        $modelling_length  = $house['other_length'];
                        $flat_area         = $house['flattop_area'];
                        $balcony_area      = $house['balcony_area'];
                        $effect_id         = \Yii::$app->db->getLastInsertID();
                        $ids[]=$effect_id;
                        $decoration = (new DecorationParticulars())->plotAdd($effect_id, $hall_area, $hall_perimeter, $bedroom_area, $bedroom_perimeter, $toilet_area, $toilet_perimeter, $kitchen_area, $kitchen_perimeter, $modelling_length, $flat_area, $balcony_area);

                        if (!$decoration) {
                            $transaction->rollBack();
                            $code = 500;
                            return Json::encode([
                                'code' => $code,
                                'msg' => \Yii::$app->params['errorCodes'][$code]
                            ]);
                        }

                        if (!empty($house['drawing_list'])) {
                            foreach ($house['drawing_list'] as $images) {
                                $effect_images  = $images['all_drawing'];
                                $series_id      = $images['series'];
                                $style_id       = $images['style'];
                                $images_user    = $images['drawing_name'];
                                $effect_picture = (new EffectPicture())->plotAdd($effect_id, $effect_images, $series_id, $style_id, $images_user);
                            }
                            if (!$effect_picture) {
                                $transaction->rollBack();
                                $code = 500;
                                return Json::encode([
                                    'code' => $code,
                                    'msg' => \Yii::$app->params['errorCodes'][$code]
                                ]);
                            }
                        }


                    }
                    // 案列添加
                    if ($house['is_ordinary'] == 1) {
                        $bedroom                = $house['cur_room'];
                        $sittingRoom_diningRoom = $house['cur_hall'];
                        $toilet                 = $house['cur_toilet'];
                        $kitchen                = $house['cur_kitchen'];
                        $window                 = $house['window'];
                        $area                   = $house['area'];
                        $high                   = $house['high'];
                        $province               = $province_chinese['name'];
                        $province_code          = $request['province_code'];
                        $city                   = $city_chinese['name'];
                        $city_code              = $request['city_code'];
                        $district               = $district_chinese['name'];
                        $district_code          = $request['district_code'];
                        $toponymy               = $request['house_name'];
                        $street                 = $request['address'];
                        $particulars            = $house['house_type_name'];
                        $stairway               = $house['have_stair'];
                        $house_image            = $house['cur_imgSrc'];
                        $type                   = $house['is_ordinary'];
                        $sort_id                = $house['sort_id'];
                        if ($stairway != 1) {
                            $stair_id = 0;
                        } else {
                            $stair_id = $house['stair'];
                        }
                        $effect = (new Effect())->plotAdd($bedroom, $sittingRoom_diningRoom, $toilet, $kitchen, $window, $area, $high, $province, $province_code, $city, $city_code, $district, $district_code, $toponymy, $street, $particulars, $stairway, $house_image, $type, $sort_id, $stair_id);
                        if (!$effect) {
                            $transaction->rollBack();
                            $code = 500;
                            return Json::encode([
                                'code' => $code,
                                'msg' => \Yii::$app->params['errorCodes'][$code]
                            ]);
                        }

                        $effect_id = \Yii::$app->db->getLastInsertID();
                        $ids[]=$effect_id;
                        $effect_images  = $house['drawing_list'];
                        $series_id      = $house['series'];
                        $style_id       = $house['style'];
                        $images_user    = '案例添加';
                        $effect_picture = (new EffectPicture())->plotAdd($effect_id, $effect_images, $series_id, $style_id, $images_user);
                        if (!$effect_picture) {
                            $transaction->rollBack();
                            $code = 500;
                            return Json::encode([
                                'code' => $code,
                                'msg' => \Yii::$app->params['errorCodes'][$code]
                            ]);
                        }

                        if (!empty($house['all_goods'])) {
                            foreach ($house['all_goods'] as $goods) {
                                $goods_id          = $effect_id;
                                $goods_first       = $goods['first_name'];
                                $goods_second      = $goods['second_name'];
                                $goods_three       = $goods['three_name'];
                                $goods_code        = $goods['good_code'];
                                $goods_quantity    = $goods['good_quantity'];
                                $three_category_id = $goods['three_id'];
                                $works_data        = (new WorksData())->plotAdd($goods_id, $goods_first, $goods_second, $goods_three, $goods_code, $goods_quantity, $three_category_id);
                            }
                            if (!$works_data) {
                                $transaction->rollBack();
                                $code = 500;
                                return Json::encode([
                                    'code' => $code,
                                    'msg' => \Yii::$app->params['errorCodes'][$code]
                                ]);
                            }
                        }

                        if (!empty($house['worker_list'])) {
                            foreach ($house['worker_list'] as $worker) {
                                $worker_id         = $effect_id;
                                $worker_kind       = $worker['worker_kind'];
                                $worker_price      = $worker['price']*100;
                                $works_worker_data = (new WorksWorkerData())->plotAdd($worker_id, $worker_kind, $worker_price);
                            }

                            if (!$works_worker_data) {
                                $transaction->rollBack();
                                $code = 500;
                                return Json::encode([
                                    'code' => $code,
                                    'msg' => \Yii::$app->params['errorCodes'][$code]
                                ]);
                            }
                        }

//                    if (!empty($house['backman_option'])) {
//                        foreach ($house['backman_option'] as $backman) {
//                            $backman_id         = $effect_id;
//                            $backman_option     = $backman['name'];
//                            $backman_value      = $backman['num'];
//                            $works_backman_data = (new WorksBackmanData())->plotAdd($backman_id, $backman_option, $backman_value);
//                        }
//                        if (!$works_backman_data) {
//                            $transaction->rollBack();
//                            return 500;
//                        }
//                    }
                    }
                }

                if(is_array($ids)){
                    $ids = implode(',',$ids);
                }
                $effect_plot = new EffectToponymy();
                $effect_plot->effect_id=$ids;
                $effect_plot->toponymy=$request['house_name'];
                $effect_plot->province_code=$request['province_code'];
                $effect_plot->city_code=$request['city_code'];
                $effect_plot->district_code=$request['district_code'];
                $effect_plot->street=$request['address'];
                $effect_plot->add_time=time();
                if(!$effect_plot->save(false)){
                    $transaction->rollBack();
                    $code = 500;
                    return Json::encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]
                ]);
            }

            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
            ]);

    }

    /**
     * 小区详情
     * @return string
     */
    public function actionEffectPlotEditView()
    {
        $plot_id= (int)\Yii::$app->request->get('plot_id','');
        $public_message = [];
        $topnymy_datas=EffectToponymy::find()->where(['id'=>$plot_id])->asArray()->one();

        $data= EffectToponymy::PlotView($plot_id);
        $a=[];
        foreach ($data as $v){
            if($v!=null){
                $a[]=$v;
            }
        }
        $data=EffectToponymy::arraySequence($a,'sort_id');
        $public_message['street'] =  $topnymy_datas['street'];
        $public_message['toponymy'] =  $topnymy_datas['toponymy'];
        $public_message['district_code'] =  $topnymy_datas['district_code'];
        $public_message['district'] = District::findByCode($topnymy_datas['district_code'])['name'];
        $public_message['effect']=$data;
        $id=[];
        foreach ($data as $one_effect){

            $id[]=$one_effect['id'];

        }
        $public_message['images']= EffectPicture::findByIds($id);

        $public_message['decoration_particulars'] = DecorationParticulars::findByIds($id);
        $public_message['goods_data'] = WorksData::findByIds($id);
        $public_message['worker_data'] = WorksWorkerData::findByIds($id);
//        $public_message['works_backman_data'] = WorksBackmanData::findById($id);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'effect'=>$public_message,
        ]);
    }

    /**
     * 删除小区
     * @return string
     */
    public function actionEffectDelPlot(){
        $del_id=(int)\Yii::$app->request->post('del_id');
        $effect_plot=EffectToponymy::find()->where(['id'=>$del_id])->asArray()->select('effect_id')->one();
        if(!$effect_plot){
            $code =1000;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $effect_ids= explode(',',$effect_plot['effect_id']);

        EffectToponymy::deleteAll(['id'=>$del_id]);
        Effect::deleteAll(['id'=>$effect_ids]);
        EffectPicture::deleteAll(['effect_id'=>$effect_ids]);
        WorksWorkerData::deleteAll(['effect_id'=>$effect_ids]);
        WorksData::deleteAll(['effect_id'=>$effect_ids]);

       return Json::encode([
           'code'=>200,
           'msg'=>'ok'
       ]);

    }
    /**
     * 小区编辑
     * @return string
     */
    public function actionEffectEditPlot(){
        $request = \Yii::$app->request->post();

        $province_chinese = District::findByCode((int)$request['province_code']);
        $city_chinese = District::findByCode((int)$request['city_code']);
        $district_chinese = District::findByCode((int)$request['district_code']);

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $ids=[];
            foreach ($request['house_informations'] as $house) {
                //添加功能

                if (!isset($house['id'])) {
                    if ($house['is_ordinary'] == 0) {
                        //普通户型添加
                        $bedroom                = $house['cur_room'];
                        $sittingRoom_diningRoom = $house['cur_hall'];
                        $toilet                 = $house['cur_toilet'];
                        $kitchen                = $house['cur_kitchen'];
                        $window                 = $house['window'];
                        $area                   = $house['area'];
                        $high                   = $house['high'];
                        $province               = $province_chinese['name'];
                        $province_code          = $request['province_code'];
                        $city                   = $city_chinese['name'];
                        $city_code              = $request['city_code'];
                        $district               = $district_chinese['name'];
                        $district_code          = $request['district_code'];
                        $toponymy               = $request['house_name'];
                        $street                 = $request['address'];
                        $particulars            = $house['house_type_name'];
                        $stairway               = $house['have_stair'];
                        $house_image            = $house['cur_imgSrc'];
                        $type                   = $house['is_ordinary'];
                        $sort_id                = $house['sort_id'];
                        $effect_                = (new Effect())->plotAdd($bedroom, $sittingRoom_diningRoom, $toilet, $kitchen, $window, $area, $high, $province, $province_code, $city, $city_code, $district, $district_code, $toponymy, $street, $particulars, $stairway, $house_image, $type, $sort_id, 0);
                        if (!$effect_) {
                            $transaction->rollBack();
                            return 500;
                        }


                        $hall_area         = $house['hall_area'];
                        $hall_perimeter    = $house['hall_girth'];
                        $bedroom_area      = $house['room_area'];
                        $bedroom_perimeter = $house['room_girth'];
                        $toilet_area       = $house['toilet_area'];
                        $toilet_perimeter  = $house['toilet_girth'];
                        $kitchen_area      = $house['kitchen_area'];
                        $kitchen_perimeter = $house['kitchen_girth'];
                        $modelling_length  = $house['other_length'];
                        $flat_area         = $house['flattop_area'];
                        $balcony_area      = $house['balcony_area'];
                        $effect_id         = \Yii::$app->db->getLastInsertID();
                        $ids[]=$effect_id;
                        $decoration        = (new DecorationParticulars())->plotAdd($effect_id, $hall_area, $hall_perimeter, $bedroom_area, $bedroom_perimeter, $toilet_area, $toilet_perimeter, $kitchen_area, $kitchen_perimeter, $modelling_length, $flat_area, $balcony_area);

                        if (!$decoration) {
                            $transaction->rollBack();
                            return 500;
                        }

                        if (!empty($house['drawing_list'])) {
                            if(empty($images['id'])) {
                                foreach ($house['drawing_list'] as $images) {
                                    $effect_images = $images['all_drawing'];
                                    $series_id = $images['series'];
                                    $style_id = $images['style'];
                                    $images_user = $images['drawing_name'];
                                    $effect_picture = (new EffectPicture())->plotAdd($effect_id, $effect_images, $series_id, $style_id, $images_user);
                                }
                            }


                            if (!$effect_picture) {
                                $transaction->rollBack();
                                return 500;
                            }
                        }
                    }

                    // 案列添加
                    if ($house['is_ordinary'] == 1) {
                        $bedroom                = $house['cur_room'];
                        $sittingRoom_diningRoom = $house['cur_hall'];
                        $toilet                 = $house['cur_toilet'];
                        $kitchen                = $house['cur_kitchen'];
                        $window                 = $house['window'];
                        $area                   = $house['area'];
                        $high                   = $house['high'];
                        $province               = $province_chinese['name'];
                        $province_code          = $request['province_code'];
                        $city                   = $city_chinese['name'];
                        $city_code              = $request['city_code'];
                        $district               = $district_chinese['name'];
                        $district_code          = $request['district_code'];
                        $toponymy               = $request['house_name'];
                        $street                 = $request['address'];
                        $particulars            = $house['house_type_name'];
                        $stairway               = $house['have_stair'];
                        $house_image            = $house['cur_imgSrc'];
                        $type                   = $house['is_ordinary'];
                        $sort_id                = $house['sort_id'];
                        if ($stairway != 1) {
                            $stair_id = 0;
                        } else {
                            $stair_id = $house['stair'];
                        }
                        $effect = (new Effect())->plotAdd($bedroom, $sittingRoom_diningRoom, $toilet, $kitchen, $window, $area, $high, $province, $province_code, $city, $city_code, $district, $district_code, $toponymy, $street, $particulars, $stairway, $house_image, $type, $sort_id, $stair_id);
                        if (!$effect) {
                            $code = 500 ;
                            $transaction->rollBack();
                            return Json::encode([
                                'code'=>$code,
                                'msg' => \Yii::$app->params['errorCodes'][$code]
                            ]);
                        }

                        $effect_id      = \Yii::$app->db->getLastInsertID();
                        $ids[]=$effect_id;
                        $effect_images  = $house['drawing_list'];
                        $series_id      = $house['series'];
                        $style_id       = $house['style'];
                        $images_user    = '案例添加';
                        $effect_picture = (new EffectPicture())->plotAdd($effect_id, $effect_images, $series_id, $style_id, $images_user);
                        if (!$effect_picture) {
                            $code = 500 ;
                            $transaction->rollBack();
                            return Json::encode([
                                'code'=>$code,
                                'msg' => \Yii::$app->params['errorCodes'][$code]
                            ]);
                        }

                        if (!empty($house['all_goods'])) {

                            foreach ($house['all_goods'] as $goods) {
                                $goods_id          = $effect_id;
                                $goods_first       = $goods['first_name'];
                                $goods_second      = $goods['second_name'];
                                $goods_three       = $goods['three_name'];
                                $goods_code        = $goods['good_code'];
                                $goods_quantity    = $goods['good_quantity'];
                                $three_category_id = $goods['three_id'];
                                $works_data        = (new WorksData())->plotAdd($goods_id, $goods_first, $goods_second, $goods_three, $goods_code, $goods_quantity, $three_category_id);
                            }
                            if (!$works_data) {
                                $code = 500 ;
                                $transaction->rollBack();
                                return Json::encode([
                                    'code'=>$code,
                                    'msg' => \Yii::$app->params['errorCodes'][$code]
                                ]);
                            }
                        }

                        if (!empty($house['worker_list'])) {

                            foreach ($house['worker_list'] as $worker) {
                                $worker_id         = $effect_id;
                                $worker_kind       = $worker['worker_kind'];
                                $worker_price      = $worker['price'];
                                $works_worker_data = (new WorksWorkerData())->plotAdd($worker_id, $worker_kind, $worker_price);
                            }

                            if (!$works_worker_data) {
                                $transaction->rollBack();
                                $code = 500 ;
                                $transaction->rollBack();
                                return Json::encode([
                                    'code'=>$code,
                                    'msg' => \Yii::$app->params['errorCodes'][$code]
                                ]);
                            }
                        }
//
//                        if (!empty($house['backman_option'])) {
//                            foreach ($house['backman_option'] as $backman) {
//                                $backman_id         = $effect_id;
//                                $backman_option     = $backman['name'];
//                                $backman_value      = $backman['num'];
//                                $works_backman_data = (new WorksBackmanData())->plotAdd($backman_id, $backman_option, $backman_value);
//                            }
//                            if (!$works_backman_data) {
//                                $transaction->rollBack();
//                                return 500;
//                            }
//                        }
                    }

                }

                //  案例修改
                if (isset($house['id'])) {

                    if ($house['is_ordinary'] == 0) {
                        //普通户型修改
                        $ids[]=$house['id'];
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
                        $district_code = $request['district_code'];
                        $toponymy = $request['house_name'];
                        $street = $request['address'];
                        $particulars = $house['house_type_name'];
                        $stairway = $house['have_stair'];
                        $house_image = $house['cur_imgSrc'];
                        $type = $house['is_ordinary'];
                        $sort_id = $house['sort_id'];
                        (new Effect())->plotEdit($house_id, $bedroom, $sittingRoom_diningRoom, $toilet, $kitchen, $window, $area, $high, $province, $province_code, $city, $city_code, $district, $district_code, $toponymy, $street, $particulars, $stairway, $house_image, $type, $sort_id, 0);
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
                        (new DecorationParticulars())->plotEdit($other_id, $hall_area, $hall_perimeter, $bedroom_area, $bedroom_perimeter, $toilet_area, $toilet_perimeter, $kitchen_area, $kitchen_perimeter, $modelling_length, $flat_area, $balcony_area);
                        if (!empty($house['drawing_list'])) {
                            foreach ($house['drawing_list'] as $images) {
                                if (!empty($images['id'])) {
                                    $images_id = $images['id'];
                                    $effect_images = $images['all_drawing'];
                                    $series_id = $images['series'];
                                    $style_id = $images['style'];
                                    $images_user = $images['drawing_name'];
                                    (new EffectPicture())->plotEdit($images_id, $effect_images, $series_id, $style_id, $images_user);
                                } else {
                                    $effect_images = $images['all_drawing'];
                                    $series_id = $images['series'];
                                    $style_id = $images['style'];
                                    $images_user = $images['drawing_name'];
                                    (new EffectPicture())->plotAdd($house_id, $effect_images, $series_id, $style_id, $images_user);
                                }
                            }

                        }
                    }

                    if ($house['is_ordinary'] == 1){

                        // 案例修改
                        $ids[]=$house['id'];
                        $house_id               = $house['id'];
                        $bedroom                = $house['cur_room'];
                        $sittingRoom_diningRoom = $house['cur_hall'];
                        $toilet                 = $house['cur_toilet'];
                        $kitchen                = $house['cur_kitchen'];
                        $window                 = $house['window'];
                        $area                   = $house['area'];
                        $high                   = $house['high'];
                        $province               = $province_chinese['name'];
                        $province_code          = $request['province_code'];
                        $city                   = $city_chinese['name'];
                        $city_code              = $request['city_code'];
                        $district               = $district_chinese['name'];
                        $district_code          = $request['district_code'];
                        $toponymy               = $request['house_name'];
                        $street                 = $request['address'];
                        $particulars            = $house['house_type_name'];
                        $stairway               = $house['have_stair'];
                        $house_image            = $house['cur_imgSrc'];
                        $type                   = $house['is_ordinary'];
                        $sort_id                = $house['sort_id'];

                        if ($stairway != 1) {
                            $stair_id = 0;
                        } else {
                            $stair_id = $house['stair'];
                        }

                        (new Effect())->plotEdit($house_id, $bedroom, $sittingRoom_diningRoom, $toilet, $kitchen, $window, $area, $high, $province, $province_code, $city, $city_code, $district, $district_code, $toponymy, $street, $particulars, $stairway, $house_image, $type, $sort_id, $stair_id);

                        $images_id     = $house['drawing_id'];
                        $effect_images = $house['drawing_list'];
                        $series_id     = $house['series'];
                        $style_id      = $house['style'];
                        $images_user   = '案例图片';
                        (new EffectPicture())->plotEdit($images_id, $effect_images, $series_id, $style_id, $images_user);
                        WorksData::deleteAll(['effect_id'=>$house_id]);
                        if (!empty($house['all_goods'])) {
                            foreach ($house['all_goods'] as $goods) {
                                if (!empty($goods['id'])) {
                                    $goods_id       = $goods['id'];
                                    $goods_first    = $goods['first_name'];
                                    $goods_second   = $goods['second_name'];
                                    $goods_three    = $goods['three_name'];
                                    $goods_code     = $goods['good_code'];
                                    $goods_quantity = $goods['good_quantity'];
                                    $three_category_id = $goods['three_id'];
                                    (new WorksData())->plotEdit($goods_id, $goods_first, $goods_second, $goods_three, $goods_code, $goods_quantity,$three_category_id);
                                } else {
                                    $goods_id       = $house['id'];
                                    $goods_first    = $goods['first_name'];
                                    $goods_second   = $goods['second_name'];
                                    $goods_three    = $goods['three_name'];
                                    $goods_code     = $goods['good_code'];
                                    $goods_quantity = $goods['good_quantity'];
                                    $three_category_id = $goods['three_id'];
                                    (new WorksData())->plotAdd($goods_id, $goods_first, $goods_second, $goods_three, $goods_code, $goods_quantity,$three_category_id);
                                }
                            }
                        }
                        WorksWorkerData::deleteAll(['effect_id'=>$house_id]);
                        if (!empty($house['worker_list'])) {
                            foreach ($house['worker_list'] as $worker) {
                                if (!empty($worker['id'])) {
                                    $worker_id    = $worker['id'];
                                    $worker_kind  = $worker['worker_kind'];
                                    $worker_price = $worker['price']*100;
                                    (new WorksWorkerData())->plotEdit($worker_id, $worker_kind, $worker_price);
                                } else {
                                    $worker_id    = $house['id'];
                                    $worker_kind  = $worker['worker_kind'];
                                    $worker_price = $worker['price']*100;
                                    $works_worker_data = (new WorksWorkerData())->plotAdd($worker_id, $worker_kind, $worker_price);
                                }
                            }
                        }

//                        if (!empty($house['backman_option'])) {
//                            foreach ($house['backman_option'] as $backman) {
//                                if (!empty($backman['id'])) {
//                                    $backman_id     = $backman['id'];
//                                    $backman_option = $backman['name'];
//                                    $backman_value  = $backman['num'];
//                                    (new WorksBackmanData())->plotEdit($backman_id, $backman_option, $backman_value);
//                                } else {
//                                    $backman_id     = $house['id'];
//                                    $backman_option = $backman['name'];
//                                    $backman_value  = $backman['num'];
//                                    (new WorksBackmanData())->plotAdd($backman_id, $backman_option, $backman_value);
//                                }
//                            }
//                        }

                        if (!empty($house['delete_goods'])) {
                            WorksData::deleteAll(['id' => $house['delete_goods']]);
                        }

                        if (!empty($house['delete_workers'])) {
                            WorksWorkerData::deleteAll(['id' => $house['delete_workers']]);
                        }

//                        if (!empty($house['delete_backman'])) {
//                            WorksBackmanData::deleteAll(['id' => $house['delete_backman']]);
//                        }
                    }
                }

            }

                $ids = implode(',',$ids);

                $toponymy_edit=EffectToponymy::find()->where(['id'=>$request['effect_id']])->one();
                $toponymy_edit->effect_id=$ids;
                $toponymy_edit->province_code=$request['province_code'];
                $toponymy_edit->city_code=$request['city_code'];
                $toponymy_edit->district_code=$request['district_code'];
                $toponymy_edit->street=$request['address'];
                $toponymy_edit->toponymy=$request['house_name'];

                if(!$toponymy_edit->save(false)){
                    $transaction->rollBack();
                    $code = 500;
                    return json_encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code]
                    ]);
                }

            $transaction->commit();
        }catch (\Exception $e) {
            $transaction->rollBack();
            $code = 500;
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        // 删除功能
        if (!empty($request['delete_house'])) {
            Effect::deleteAll(['id' => $request['delete_house']]);
            EffectPicture::deleteAll(['effect_id' => $request['delete_house']]);
            WorksBackmanData::deleteAll(['effect_id' => $request['delete_house']]);
            WorksWorkerData::deleteAll(['effect_id' => $request['delete_house']]);
            WorksData::deleteAll(['effect_id' => $request['delete_house']]);
        }

        if (!empty($request['delete_drawing'])) {
            EffectPicture::deleteAll(['id' => $request['delete_drawing']]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
        ]);
    }

    /**
     * plot add function
     * @return string
     */
    public function actionPlotAdd()
    {
        $request = \Yii::$app->request->post();

        $province_chinese = District::findByCode((int)$request['province_code']);
        $city_chinese = District::findByCode((int)$request['city_code']);
        $district_chinese = District::findByCode((int)$request['cur_county_id']['id']);


        $transaction = \Yii::$app->db->beginTransaction();
        try {
            foreach ($request['house_informations'] as $house) {
                if ($house['is_ordinary'] == 0) {
                    //普通户型添加
                    $bedroom                = $house['cur_room'];
                    $sittingRoom_diningRoom = $house['cur_hall'];
                    $toilet                 = $house['cur_toilet'];
                    $kitchen                = $house['cur_kitchen'];
                    $window                 = $house['window'];
                    $area                   = $house['area'];
                    $high                   = $house['high'];
                    $province               = $province_chinese['name'];
                    $province_code          = $request['province_code'];
                    $city                   = $city_chinese['name'];
                    $city_code              = $request['city_code'];
                    $district               = $district_chinese['name'];
                    $district_code          = $request['cur_county_id']['id'];
                    $toponymy               = $request['house_name'];
                    $street                 = $request['address'];
                    $particulars            = $house['house_type_name'];
                    $stairway               = $house['have_stair'];
                    $house_image            = $house['cur_imgSrc'];
                    $type                   = $house['is_ordinary'];
                    $sort_id                = $house['sort_id'];
                    $effect_                = (new Effect())->plotAdd($bedroom, $sittingRoom_diningRoom, $toilet, $kitchen, $window, $area, $high, $province, $province_code, $city, $city_code, $district, $district_code, $toponymy, $street, $particulars, $stairway, $house_image, $type, $sort_id, 0);

                    if (!$effect_) {
                        $transaction->rollBack();
                        $code = 500;
                        return Json::encode([
                            'code' => $code,
                            'msg'  => \Yii::$app->params['errorCodes'][$code]
                        ]);
                    }

                    $hall_area         = $house['hall_area'];
                    $hall_perimeter    = $house['hall_girth'];
                    $bedroom_area      = $house['room_area'];
                    $bedroom_perimeter = $house['room_girth'];
                    $toilet_area       = $house['toilet_area'];
                    $toilet_perimeter  = $house['toilet_girth'];
                    $kitchen_area      = $house['kitchen_area'];
                    $kitchen_perimeter = $house['kitchen_girth'];
                    $modelling_length  = $house['other_length'];
                    $flat_area         = $house['flattop_area'];
                    $balcony_area      = $house['balcony_area'];
                    $effect_id         = \Yii::$app->db->getLastInsertID();
                    $decoration        = (new DecorationParticulars())->plotAdd($effect_id, $hall_area, $hall_perimeter, $bedroom_area, $bedroom_perimeter, $toilet_area, $toilet_perimeter, $kitchen_area, $kitchen_perimeter, $modelling_length, $flat_area, $balcony_area);

                    if (!$decoration) {
                        $transaction->rollBack();
                        $code = 500;
                        return Json::encode([
                            'code' => $code,
                            'msg'  => \Yii::$app->params['errorCodes'][$code]
                        ]);
                    }

                    if (!empty($house['drawing_list'])) {
                        foreach ($house['drawing_list'] as $images) {
                            $effect_images  = $images['all_drawing'];
                            $series_id      = $images['series'];
                            $style_id       = $images['style'];
                            $images_user    = $images['drawing_name'];
                            $effect_picture = (new EffectPicture())->plotAdd($effect_id, $effect_images, $series_id, $style_id, $images_user);
                        }
                        if (!$effect_picture) {
                            $transaction->rollBack();
                            $code = 500;
                            return Json::encode([
                                'code' => $code,
                                'msg'  => \Yii::$app->params['errorCodes'][$code]
                            ]);
                        }
                    }


                }
                // 案列添加
                if ($house['is_ordinary'] == 1) {
                    $bedroom                = $house['cur_room'];
                    $sittingRoom_diningRoom = $house['cur_hall'];
                    $toilet                 = $house['cur_toilet'];
                    $kitchen                = $house['cur_kitchen'];
                    $window                 = $house['window'];
                    $area                   = $house['area'];
                    $high                   = $house['high'];
                    $province               = $province_chinese['name'];
                    $province_code          = $request['province_code'];
                    $city                   = $city_chinese['name'];
                    $city_code              = $request['city_code'];
                    $district               = $district_chinese['name'];
                    $district_code          = $request['cur_county_id']['id'];
                    $toponymy               = $request['house_name'];
                    $street                 = $request['address'];
                    $particulars            = $house['house_type_name'];
                    $stairway               = $house['have_stair'];
                    $house_image            = $house['cur_imgSrc'];
                    $type                   = $house['is_ordinary'];
                    $sort_id                = $house['sort_id'];
                    if ($stairway != 1) {
                        $stair_id = 0;
                    } else {
                        $stair_id = $house['stair'];
                    }
                    $effect = (new Effect())->plotAdd($bedroom, $sittingRoom_diningRoom, $toilet, $kitchen, $window, $area, $high, $province, $province_code, $city, $city_code, $district, $district_code, $toponymy, $street, $particulars, $stairway, $house_image, $type, $sort_id, $stair_id);
                    if (!$effect) {
                        $transaction->rollBack();
                        $code = 500;
                        return Json::encode([
                            'code' => $code,
                            'msg'  => \Yii::$app->params['errorCodes'][$code]
                        ]);
                    }

                    $effect_id      = \Yii::$app->db->getLastInsertID();
                    $effect_images  = $house['drawing_list'];
                    $series_id      = $house['series'];
                    $style_id       = $house['style'];
                    $images_user    = '案例添加';
                    $effect_picture = (new EffectPicture())->plotAdd($effect_id, $effect_images, $series_id, $style_id, $images_user);
                    if (!$effect_picture) {
                        $transaction->rollBack();
                        $code = 500;
                        return Json::encode([
                            'code' => $code,
                            'msg'  => \Yii::$app->params['errorCodes'][$code]
                        ]);
                    }

                    if (!empty($house['all_goods'])) {
                        foreach ($house['all_goods'] as $goods) {
                            $goods_id          = $effect_id;
                            $goods_first       = $goods['first_name'];
                            $goods_second      = $goods['second_name'];
                            $goods_three       = $goods['three_name'];
                            $goods_code        = $goods['good_code'];
                            $goods_quantity    = $goods['good_quantity'];
                            $three_category_id = $goods['three_id'];
                            $works_data        = (new WorksData())->plotAdd($goods_id, $goods_first, $goods_second, $goods_three, $goods_code, $goods_quantity, $three_category_id);
                        }
                        if (!$works_data) {
                            $transaction->rollBack();
                            $code = 500;
                            return Json::encode([
                                'code' => $code,
                                'msg'  => \Yii::$app->params['errorCodes'][$code]
                            ]);
                        }
                    }

                    if (!empty($house['worker_list'])) {
                        foreach ($house['worker_list'] as $worker) {
                            $worker_id         = $effect_id;
                            $worker_kind       = $worker['worker_kind'];
                            $worker_price      = $worker['price'];
                            $works_worker_data = (new WorksWorkerData())->plotAdd($worker_id, $worker_kind, $worker_price);
                        }

                        if (!$works_worker_data) {
                            $transaction->rollBack();
                            $code = 500;
                            return Json::encode([
                                'code' => $code,
                                'msg'  => \Yii::$app->params['errorCodes'][$code]
                            ]);
                        }
                    }

//                    if (!empty($house['backman_option'])) {
//                        foreach ($house['backman_option'] as $backman) {
//                            $backman_id         = $effect_id;
//                            $backman_option     = $backman['name'];
//                            $backman_value      = $backman['num'];
//                            $works_backman_data = (new WorksBackmanData())->plotAdd($backman_id, $backman_option, $backman_value);
//                        }
//                        if (!$works_backman_data) {
//                            $transaction->rollBack();
//                            return 500;
//                        }
//                    }
                }
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return Json::encode([
                'code' => 1000,
                'msg' => '请求参数错误',
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
        ]);
    }

    /**
     * plot edit page view
     * @return string
     */
    public function actionPlotEditView()
    {
        $post = \Yii::$app->request->post();

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
//        $public_message['works_backman_data'] = WorksBackmanData::findById($id);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
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

        $province_chinese = District::findByCode($request['province_code']);
        $city_chinese = District::findByCode($request['city_code']);
        $district_chinese = District::findByCode($request['cur_county_id']['id']);

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            foreach ($request['house_informations'] as $house) {
                //添加功能
                if (!isset($house['id'])) {
                    if ($house['is_ordinary'] == 0) {
                        //普通户型添加
                        $bedroom                = $house['cur_room'];
                        $sittingRoom_diningRoom = $house['cur_hall'];
                        $toilet                 = $house['cur_toilet'];
                        $kitchen                = $house['cur_kitchen'];
                        $window                 = $house['window'];
                        $area                   = $house['area'];
                        $high                   = $house['high'];
                        $province               = $province_chinese['name'];
                        $province_code          = $request['province_code'];
                        $city                   = $city_chinese['name'];
                        $city_code              = $request['city_code'];
                        $district               = $district_chinese['name'];
                        $district_code          = $request['cur_county_id']['id'];
                        $toponymy               = $request['house_name'];
                        $street                 = $request['address'];
                        $particulars            = $house['house_type_name'];
                        $stairway               = $house['have_stair'];
                        $house_image            = $house['cur_imgSrc'];
                        $type                   = $house['is_ordinary'];
                        $sort_id                = $house['sort_id'];
                        $effect_                = (new Effect())->plotAdd($bedroom, $sittingRoom_diningRoom, $toilet, $kitchen, $window, $area, $high, $province, $province_code, $city, $city_code, $district, $district_code, $toponymy, $street, $particulars, $stairway, $house_image, $type, $sort_id, 0);
                        if (!$effect_) {
                            $transaction->rollBack();
                            return 500;
                        }


                        $hall_area         = $house['hall_area'];
                        $hall_perimeter    = $house['hall_girth'];
                        $bedroom_area      = $house['room_area'];
                        $bedroom_perimeter = $house['room_girth'];
                        $toilet_area       = $house['toilet_area'];
                        $toilet_perimeter  = $house['toilet_girth'];
                        $kitchen_area      = $house['kitchen_area'];
                        $kitchen_perimeter = $house['kitchen_girth'];
                        $modelling_length  = $house['other_length'];
                        $flat_area         = $house['flattop_area'];
                        $balcony_area      = $house['balcony_area'];
                        $effect_id         = \Yii::$app->db->getLastInsertID();
                        $decoration        = (new DecorationParticulars())->plotAdd($effect_id, $hall_area, $hall_perimeter, $bedroom_area, $bedroom_perimeter, $toilet_area, $toilet_perimeter, $kitchen_area, $kitchen_perimeter, $modelling_length, $flat_area, $balcony_area);

                        if (!$decoration) {
                            $transaction->rollBack();
                            return 500;
                        }

                        if (!empty($house['drawing_list'])) {
                            foreach ($house['drawing_list'] as $images) {
                                $effect_images  = $images['all_drawing'];
                                $series_id      = $images['series'];
                                $style_id       = $images['style'];
                                $images_user    = $images['drawing_name'];
                                $effect_picture = (new EffectPicture())->plotAdd($effect_id, $effect_images, $series_id, $style_id, $images_user);
                            }
                            if (!$effect_picture) {
                                $transaction->rollBack();
                                return 500;
                            }
                        }
                    }

                    // 案列添加
                    if ($house['is_ordinary'] == 1) {
                        $bedroom                = $house['cur_room'];
                        $sittingRoom_diningRoom = $house['cur_hall'];
                        $toilet                 = $house['cur_toilet'];
                        $kitchen                = $house['cur_kitchen'];
                        $window                 = $house['window'];
                        $area                   = $house['area'];
                        $high                   = $house['high'];
                        $province               = $province_chinese['name'];
                        $province_code          = $request['province_code'];
                        $city                   = $city_chinese['name'];
                        $city_code              = $request['city_code'];
                        $district               = $district_chinese['name'];
                        $district_code          = $request['cur_county_id']['id'];
                        $toponymy               = $request['house_name'];
                        $street                 = $request['address'];
                        $particulars            = $house['house_type_name'];
                        $stairway               = $house['have_stair'];
                        $house_image            = $house['cur_imgSrc'];
                        $type                   = $house['is_ordinary'];
                        $sort_id                = $house['sort_id'];
                        if ($stairway != 1) {
                            $stair_id = 0;
                        } else {
                            $stair_id = $house['stair'];
                        }
                        $effect = (new Effect())->plotAdd($bedroom, $sittingRoom_diningRoom, $toilet, $kitchen, $window, $area, $high, $province, $province_code, $city, $city_code, $district, $district_code, $toponymy, $street, $particulars, $stairway, $house_image, $type, $sort_id, $stair_id);
                        if (!$effect) {
                            $transaction->rollBack();
                            return 500;
                        }

                        $effect_id      = \Yii::$app->db->getLastInsertID();
                        $effect_images  = $house['drawing_list'];
                        $series_id      = $house['series'];
                        $style_id       = $house['style'];
                        $images_user    = '案例添加';
                        $effect_picture = (new EffectPicture())->plotAdd($effect_id, $effect_images, $series_id, $style_id, $images_user);
                        if (!$effect_picture) {
                            $transaction->rollBack();
                            return 500;
                        }

                        if (!empty($house['all_goods'])) {
                            foreach ($house['all_goods'] as $goods) {
                                $goods_id          = $effect_id;
                                $goods_first       = $goods['first_name'];
                                $goods_second      = $goods['second_name'];
                                $goods_three       = $goods['three_name'];
                                $goods_code        = $goods['good_code'];
                                $goods_quantity    = $goods['good_quantity'];
                                $three_category_id = $goods['three_id'];
                                $works_data        = (new WorksData())->plotAdd($goods_id, $goods_first, $goods_second, $goods_three, $goods_code, $goods_quantity, $three_category_id);
                            }
                            if (!$works_data) {
                                $transaction->rollBack();
                                return 500;
                            }
                        }

                        if (!empty($house['worker_list'])) {
                            foreach ($house['worker_list'] as $worker) {
                                $worker_id         = $effect_id;
                                $worker_kind       = $worker['worker_kind'];
                                $worker_price      = $worker['price'];
                                $works_worker_data = (new WorksWorkerData())->plotAdd($worker_id, $worker_kind, $worker_price);
                            }

                            if (!$works_worker_data) {
                                $transaction->rollBack();
                                return 500;
                            }
                        }
//
//                        if (!empty($house['backman_option'])) {
//                            foreach ($house['backman_option'] as $backman) {
//                                $backman_id         = $effect_id;
//                                $backman_option     = $backman['name'];
//                                $backman_value      = $backman['num'];
//                                $works_backman_data = (new WorksBackmanData())->plotAdd($backman_id, $backman_option, $backman_value);
//                            }
//                            if (!$works_backman_data) {
//                                $transaction->rollBack();
//                                return 500;
//                            }
//                        }
                    }
                }

                //  案例修改
                if (isset($house['id'])) {
                    if ($house['is_ordinary'] == 0) {
                        //普通户型修改
                        $house_id               = $house['id'];
                        $bedroom                = $house['cur_room'];
                        $sittingRoom_diningRoom = $house['cur_hall'];
                        $toilet                 = $house['cur_toilet'];
                        $kitchen                = $house['cur_kitchen'];
                        $window                 = $house['window'];
                        $area                   = $house['area'];
                        $high                   = $house['high'];
                        $province               = $province_chinese['name'];
                        $province_code          = $request['province_code'];
                        $city                   = $city_chinese['name'];
                        $city_code              = $request['city_code'];
                        $district               = $district_chinese['name'];
                        $district_code          = $request['cur_county_id']['id'];
                        $toponymy               = $request['house_name'];
                        $street                 = $request['address'];
                        $particulars            = $house['house_type_name'];
                        $stairway               = $house['have_stair'];
                        $house_image            = $house['cur_imgSrc'];
                        $type                   = $house['is_ordinary'];
                        $sort_id                = $house['sort_id'];
                        (new Effect())->plotEdit($house_id, $bedroom, $sittingRoom_diningRoom, $toilet, $kitchen, $window, $area, $high, $province, $province_code, $city, $city_code, $district, $district_code, $toponymy, $street, $particulars, $stairway, $house_image, $type, $sort_id, 0);
                        $other_id          = $house['other_id'];
                        $hall_area         = $house['hall_area'];
                        $hall_perimeter    = $house['hall_girth'];
                        $bedroom_area      = $house['room_area'];
                        $bedroom_perimeter = $house['room_girth'];
                        $toilet_area       = $house['toilet_area'];
                        $toilet_perimeter  = $house['toilet_girth'];
                        $kitchen_area      = $house['kitchen_area'];
                        $kitchen_perimeter = $house['kitchen_girth'];
                        $modelling_length  = $house['other_length'];
                        $flat_area         = $house['flattop_area'];
                        $balcony_area      = $house['balcony_area'];
                        (new DecorationParticulars())->plotEdit($other_id, $hall_area, $hall_perimeter, $bedroom_area, $bedroom_perimeter, $toilet_area, $toilet_perimeter, $kitchen_area, $kitchen_perimeter, $modelling_length, $flat_area, $balcony_area);

                        if (!empty($house['drawing_list'])) {
                            foreach ($house['drawing_list'] as $images) {
                                if (!empty($images['id'])) {
                                    $images_id     = $images['id'];
                                    $effect_images = $images['all_drawing'];
                                    $series_id     = $images['series'];
                                    $style_id      = $images['style'];
                                    $images_user   = $images['drawing_name'];
                                    (new EffectPicture())->plotEdit($images_id, $effect_images, $series_id, $style_id, $images_user);
                                }
                            }
                        }
                    }

                    if ($house['is_ordinary'] == 1){
                        // 案例修改
                        $house_id               = $house['id'];
                        $bedroom                = $house['cur_room'];
                        $sittingRoom_diningRoom = $house['cur_hall'];
                        $toilet                 = $house['cur_toilet'];
                        $kitchen                = $house['cur_kitchen'];
                        $window                 = $house['window'];
                        $area                   = $house['area'];
                        $high                   = $house['high'];
                        $province               = $province_chinese['name'];
                        $province_code          = $request['province_code'];
                        $city                   = $city_chinese['name'];
                        $city_code              = $request['city_code'];
                        $district               = $district_chinese['name'];
                        $district_code          = $request['cur_county_id']['id'];
                        $toponymy               = $request['house_name'];
                        $street                 = $request['address'];
                        $particulars            = $house['house_type_name'];
                        $stairway               = $house['have_stair'];
                        $house_image            = $house['cur_imgSrc'];
                        $type                   = $house['is_ordinary'];
                        $sort_id                = $house['sort_id'];
                        if ($stairway != 1) {
                            $stair_id = 0;
                        } else {
                            $stair_id = $house['stair'];
                        }

                        (new Effect())->plotEdit($house_id, $bedroom, $sittingRoom_diningRoom, $toilet, $kitchen, $window, $area, $high, $province, $province_code, $city, $city_code, $district, $district_code, $toponymy, $street, $particulars, $stairway, $house_image, $type, $sort_id, $stair_id);

                        $images_id     = $house['drawing_id'];
                        $effect_images = $house['drawing_list'];
                        $series_id     = $house['series'];
                        $style_id      = $house['style'];
                        $images_user   = '案例图片';
                        (new EffectPicture())->plotEdit($images_id, $effect_images, $series_id, $style_id, $images_user);

                        if (!empty($house['all_goods'])) {
                            foreach ($house['all_goods'] as $goods) {
                                if (!empty($goods['id'])) {
                                    $goods_id       = $goods['id'];
                                    $goods_first    = $goods['first_name'];
                                    $goods_second   = $goods['second_name'];
                                    $goods_three    = $goods['three_name'];
                                    $goods_code     = $goods['good_code'];
                                    $goods_quantity = $goods['good_quantity'];
                                    $three_category_id = $goods['three_id'];
                                    (new WorksData())->plotEdit($goods_id, $goods_first, $goods_second, $goods_three, $goods_code, $goods_quantity,$three_category_id);
                                } else {
                                    $goods_id       = $house['id'];
                                    $goods_first    = $goods['first_name'];
                                    $goods_second   = $goods['second_name'];
                                    $goods_three    = $goods['three_name'];
                                    $goods_code     = $goods['good_code'];
                                    $goods_quantity = $goods['good_quantity'];
                                    $three_category_id = $goods['three_id'];
                                    (new WorksData())->plotAdd($goods_id, $goods_first, $goods_second, $goods_three, $goods_code, $goods_quantity,$three_category_id);
                                }
                            }
                        }

                        if (!empty($house['worker_list'])) {
                            foreach ($house['worker_list'] as $worker) {
                                if (!empty($worker['id'])) {
                                    $worker_id    = $worker['id'];
                                    $worker_kind  = $worker['worker_kind'];
                                    $worker_price = $worker['price']*100;
                                    (new WorksWorkerData())->plotEdit($worker_id, $worker_kind, $worker_price);
                                } else {
                                    $worker_id    = $house['id'];
                                    $worker_kind  = $worker['worker_kind'];
                                    $worker_price = $worker['price']*100;
                                    $works_worker_data = (new WorksWorkerData())->plotAdd($worker_id, $worker_kind, $worker_price);
                                }
                            }
                        }

//                        if (!empty($house['backman_option'])) {
//                            foreach ($house['backman_option'] as $backman) {
//                                if (!empty($backman['id'])) {
//                                    $backman_id     = $backman['id'];
//                                    $backman_option = $backman['name'];
//                                    $backman_value  = $backman['num'];
//                                    (new WorksBackmanData())->plotEdit($backman_id, $backman_option, $backman_value);
//                                } else {
//                                    $backman_id     = $house['id'];
//                                    $backman_option = $backman['name'];
//                                    $backman_value  = $backman['num'];
//                                    (new WorksBackmanData())->plotAdd($backman_id, $backman_option, $backman_value);
//                                }
//                            }
//                        }

                        if (!empty($house['delete_goods'])) {
                            WorksData::deleteAll(['id' => $house['delete_goods']]);
                        }

                        if (!empty($house['delete_workers'])) {
                            WorksWorkerData::deleteAll(['id' => $house['delete_workers']]);
                        }

//                        if (!empty($house['delete_backman'])) {
//                            WorksBackmanData::deleteAll(['id' => $house['delete_backman']]);
//                        }
                    }
                }
            }
            $transaction->commit();
        }catch (\Exception $e) {
            $transaction->rollBack();
            $code = 1000;
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        // 删除功能
        if (!empty($request['delete_house'])) {
            Effect::deleteAll(['id' => $request['delete_house']]);
            EffectPicture::deleteAll(['effect_id' => $request['delete_house']]);
            WorksBackmanData::deleteAll(['effect_id' => $request['delete_house']]);
            WorksWorkerData::deleteAll(['effect_id' => $request['delete_house']]);
            WorksData::deleteAll(['effect_id' => $request['delete_house']]);
        }

        if (!empty($request['delete_drawing'])) {
            EffectPicture::deleteAll(['id' => $request['delete_drawing']]);
        }

        return Json::encode([
           'code' => 200,
           'msg' => 'ok',
        ]);
    }

    /**
     * 小区案例添加  删除功能
     * @return string
     */
    public function actionPlotDel()
    {
        $del_id = (int)trim(\Yii::$app->request->post('del_id',''));
        $effect = Effect::find()->asArray()->where(['id'=>$del_id])->one();
        // 删除功能
//        $delete ='district_code = '.$effect['district_code']. ' and toponymy = '.$effect['toponymy'] . ' and street = '.$effect['street'];
        //  ['and',['district_code'=>$effect['district_code']],['toponymy'=>$effect['toponymy']],['street'=>$effect['street']]]
        Effect::deleteAll(['and',['district_code'=>$effect['district_code']],['toponymy'=>$effect['toponymy']],['street'=>$effect['street']]]);
        EffectPicture::deleteAll(['effect_id' => $del_id]);
//        WorksBackmanData::deleteAll(['effect_id' => $del_id]);
        WorksWorkerData::deleteAll(['effect_id' => $del_id]);
        WorksData::deleteAll(['effect_id' => $del_id]);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
        ]);

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
     * 案例/社区商品配套管理 列表
     * @return string
     */
    public function actionAssortGoodsList()
    {

        $city = (int)trim(\Yii::$app->request->get('city',''));
        $where = 'city_code='.$city;

        $goods_list = AssortGoods::find()
            ->select(['title','category_id as id','pid','path','quantity'])
            ->where(['state'=>0])
            ->andWhere($where)
            ->asArray()
            ->all();
        foreach ($goods_list as $cate){
            $catagory=GoodsCategory::find()->asArray()->where(['id'=>$cate['id']])->one();
            if($catagory['deleted']==1){
                AssortGoods::deleteAll(['category_id'=>$cate['id']]);
            }
        }
        $goods_classify = GoodsCategory::find()
            ->select(['id','title'])
            ->where(['in','level',[1,2]])
            ->asArray()
            ->all();
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'list'=> $goods_list,
            'classify'=>$goods_classify
        ]);
    }

    /**
     * 案例/社区商品配套管理 添加
     * @return string
     */
    public function actionAssortGoodsAdd()
    {
        $post = \Yii::$app->request->post();
        (new AssortGoods())->deleteAll(['and',['state'=>0],['city_code'=>$post['city']]]);
        if(isset($post['assort'])){
            foreach($post['assort'] as $management) {
                $add = AssortGoods::add($management,$post['city']);
            }

            if (!$add){
                $code=500;
                return Json::encode([
                    'code' => $code,
                    'mag' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }

        }
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
        ]);

    }

    /**
     * homepage list show
     * @return string
     */
    public function actionHomepageList(){
        $request = \Yii::$app->request;
        $district_code = trim($request->get('city_code',''));

        $brainpower =BrainpowerInitalSupervise::codeStatus($district_code);
        foreach ($brainpower as $value){
            $effect = Effect::find()
                ->asArray()
                ->where(['district_code'=>$value['district_code']])
                ->andWhere(['toponymy'=>$value['toponymy']])
                ->andWhere(['street'=>$value['street']])
                ->andWhere(['type'=>1])
                ->one();

            if (!$effect){
                BrainpowerInitalSupervise::deleteAll(['id'=>$value['id']]);
            }
        }

        $code = 200;
        return Json::encode([
            'code' => $code,
            'msg'  => 'ok',
            'list' =>  BrainpowerInitalSupervise::findByCode($district_code)
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
        $city_code = trim($request->get('city',''));
        $code = 200;

        return Json::encode([
            'code' => $code,
            'msg'  => 'ok',
            'list' => Effect::findCode($city_code),
        ]);

    }

    /**
     * homepage toponymy find
     * @return string
     */
    public function actionHomepageToponymy()
    {
        $request    = \Yii::$app->request;
//        $province   = trim($request->post('province',''));
//        $city       = trim($request->post('city',''));
        $district   = trim($request->get('district',''));
        $code = 200;
        return Json::encode([
            'code' => $code,
            'msg'  => 'ok',
            'list' => Effect::findToponymy($district),
        ]);
    }

    /**
     * @return string
     */
    public function actionHomepageEditView()
    {
        $id = (int)\Yii::$app->request->get('id');

        $effect= BrainpowerInitalSupervise::find()
            ->asArray()
            ->where(['id'=>$id])
            ->one();
        if(!$effect){
            $code=1000;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $topnymy_id=EffectToponymy::find()
            ->select('id')
            ->asArray()
            ->where(['district_code'=>$effect['district_code'],'toponymy'=>$effect['toponymy'],'street'=>$effect['street']])
            ->one();
       $effect['toponymy_id']=$topnymy_id['id'];

        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'list'=>$effect
        ]);

    }
//    /**
//     * homepage street find
//     * @return string
//     */
//    public function actionHomepageStreet()
//    {
//        $request    = \Yii::$app->request;
//        $province   = trim($request->post('province',''));
//        $city       = trim($request->post('city',''));
//        $district   = trim($request->post('district',''));
//        $toponymy   = trim($request->post('toponymy',''));
//        $code = 200;
//        return Json::encode([
//            'code' => $code,
//            'msg'  => 'ok',
//            'list' => (new Effect)->findStreet($province,$city,$district,$toponymy),
//        ]);
//    }

    /**
     * homepage case find
     * @return string
     */
    public function actionHomepageCase()
    {
        $request  = \Yii::$app->request;
        $toponymy_id= trim($request->get('toponymy_id'));
        $effect_ids = EffectToponymy::find()
            ->select('effect_id')
            ->where(['id'=>$toponymy_id])
            ->asArray()
            ->one()['effect_id'];
        $effect_id=explode(',',$effect_ids);
        $data=[];
        foreach ($effect_id as $item){
            $data[]=Effect::find()
               ->select('id,particulars')
               ->asArray()
               ->where(['id'=>$item,'type'=>1])
               ->one();
            $a = [];
            foreach ($data as $value){
                if ($value != null){
                    $a[] = $value;
                }
            }

       }

        return Json::encode([
            'code' => 200,
            'msg'  => 'ok',
            'list' => $a,
        ]);
    }

    /**
     * homepage add function
     * @return string
     */
    public function actionHomepageAdd()
    {
        $request = \Yii::$app->request;
        $effect = Effect::find()
            ->asArray()
            ->where(['id'=>(int)trim($request->post('effect_id',''))])
//            ->andWhere(['toponymy'=>trim($request->post('toponymy',''))])
//            ->andWhere(['street'=>trim($request->post('street',''))])
//            ->andWhere(['!=','type',2])
            ->one();


        $add_item = new BrainpowerInitalSupervise();
        $add_item->recommend_name  = trim($request->post('recommend_name',''));
        $add_item->image           = trim($request->post('image',''));
        $add_item->house_type_name = $effect['particulars'];
        $add_item->city_code       = $effect['city_code'];
        $add_item->district_code   = $effect['district_code'];
        $add_item->toponymy         = $effect['toponymy'];
        $add_item->street          = $effect['street'];
        $add_item->effect_id       = $effect['id'];
        $add_item->add_time        = time();

        $code = 500;
        if (!$add_item->save()){
            return Json::encode([
               'code' => $code,
                'mag' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'mag' => 'ok',
        ]);
    }

    /**
     * homepage status edit function
     * @return string
     */
    public function actionHomepageStatus()
    {
        $code = 1000;
        $id       = (int)trim(\Yii::$app->request->get('id',''));
        $status   = (int)trim(\Yii::$app->request->get('status',''));
        $find_one = BrainpowerInitalSupervise::findOne(['id'=>$id]);
        $find_one->status = $status;
        if (!$find_one->validate()){
            return Json::encode([
                'code' =>  $code,
                'mag' => \Yii::$app->params['errorCodes'][$code]
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
        $effect = Effect::find()
            ->asArray()
            ->where(['id'=>(int)trim($request->post('effect_id',''))])
            ->one();

        $add_item = new BrainpowerInitalSupervise();
        $item = $add_item->findOne(['id'=>$id]);
        $item->recommend_name  = trim($request->post('recommend_name',''));
        $item->image           = trim($request->post('image',''));
        $item->district_code   = $effect['district_code'];
        $item->toponymy        = $effect['toponymy'];
        $item->street          = $effect['street'];
        $item->house_type_name = $effect['particulars'];
        $item->city_code       = $effect['city_code'];
        $item->effect_id       = $effect['id'];


        $code = 500;
        if (!$item->save()){
            return Json::encode([
                'code' => $code,
                'mag' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'mag' => 'ok',
        ]);
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
            } else {
                $apartment = new ApartmentArea();
                $apartment->min_area  = $one_post['min_area'];
                $apartment->max_area  = $one_post['max_area'];
                $apartment->save();
            }
        }
        return Json::encode([
           'code' => 200,
            'msg' => 'ok',
        ]);
    }

    /**
     * 材料添加项 列表
     * @return string
     */
    public function actionDecorationList()
    {

        $page = (int)trim(\Yii::$app->request->get('page', 1));
        $size = (int)trim(\Yii::$app->request->get('size', DecorationAdd::PAGE_SIZE_DEFAULT));
        $city = (int)trim(\Yii::$app->request->get('city', 510100));
        $keyword=trim(\Yii::$app->request->get('keyword'));
        $data=DecorationAdd::find()->asArray()->select('sku')->all();

        foreach ($data as $v){
            $goods_status=Goods::find()
                ->select('status')
                ->where(['sku'=>$v['sku']])
                ->asArray()
                ->one()['status'];
            if($goods_status!=2){
                DecorationAdd::deleteAll(['sku'=>$v['sku']]);
            }
        }
        $where  = 'da.city_code = '.$city;
        if($keyword){
            $where.=" and gc.title like '%{$keyword}%'";
        }

        $select = 'da.id,da.c_id,da.add_time,da.correlation_message,da.sku,gc.title';
        $decoration_add = DecorationAdd::pagination($where,$select,$page,$size);


        return Json::encode([
            'code' => 200,
            'msg'  => 'ok',
            'list'  => $decoration_add,
        ]);
    }

    /**
     * 材料添加项 材料抓取
     * @return string
     */
    public function actionDecorationAddClassify()
    {
        $get=\Yii::$app->request->get();
        $category_id = (int)trim(\Yii::$app->request->get('category_id',''));
        if(!isset($get['id'])){
        $d_add = DecorationAdd::find()->where(['c_id'=>$category_id])->one();
        if ($d_add){
            $code=1087;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }
    }

        $goods  = Goods::priceDetail(self::CATEGORY_LEVEL,$category_id);

        if ( isset($goods['0'])) {
            $max        = BasisDecorationService::profitMargin($goods);
            $goods_attr = GoodsAttr::frontDetailsByGoodsId($max['id']);
        } else {
            $code=1080;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
                'goods'=> [],
                'goods_attr'=> [],
            ]);
        }



        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'goods'=> $max,
            'goods_attr'=> $goods_attr,
        ]);

    }

    /**
     * apartment area list
     * @return string
     */
    public function actionHouseTypeList()
    {
        $select = 'id,min_area,max_area';
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
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
            'code' => 200,
            'msg' => 'ok',
           'list'=>DecorationMessage::findAll(['decoration_add_id'=>$id]),
        ]);
    }

    /**
     * 材料添加项 添加
     * @return string
     */
    public function actionDecorationAdd()
    {
        $post = \Yii::$app->request->post();
        $decoration_add = new DecorationAdd();
        $decoration_add->city_code = $post['city'];
        $decoration_add->correlation_message = $post['message'];
        $decoration_add->c_id = $post['category_id'];
        $decoration_add->sku  = $post['sku'];
        $decoration_add->add_time = time();

        if (!$decoration_add->save()){
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg'=>\Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $id = $decoration_add->attributes['id'];
        foreach ($post['add'] as $one_post){
            switch ($one_post){
                case isset($one_post['series']) != null:
                    $dm = \Yii::$app->db->createCommand()
                        ->insert(DecorationMessage::tableName(), [
                            'series_id' => $one_post['series'],
                            'quantity' => $one_post['quantity'],
                            'decoration_add_id' => $id,
                        ])->execute();
                    break;
                case isset($one_post['style']) != null:
                    $dm = \Yii::$app->db->createCommand()
                        ->insert(DecorationMessage::tableName(), [
                            'style_id' => $one_post['style'],
                            'quantity' => $one_post['quantity'],
                            'decoration_add_id' => $id,
                        ])->execute();
                    break;
                case isset($one_post['min_area']) != null:
                    $dm = \Yii::$app->db->createCommand()
                        ->insert(DecorationMessage::tableName(), [
                            'min_area' => $one_post['min_area'],
                            'max_area' => $one_post['max_area'],
                            'quantity' => $one_post['quantity'],
                            'decoration_add_id' => $id,
                        ])->execute();
                    break;
            }
        }

        if (!$dm){
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg'=>\Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
           'code' => 200,
            'msg' => 'ok',
        ]);
    }

    /**
     * 材料添加项  一键抓取sku
     * @return string
     */
    public function actionDecorationUp()
    {
        $decoration = DecorationAdd::find()->asArray()->All();
        $c_id = [];
        foreach ($decoration as $one){
            $c_id [] = $one['c_id'];
        }

        $goods  = Goods::priceDetail(self::CATEGORY_LEVEL,$c_id);
        $g_c = array_values(Effect::array_group_by($goods,'title'));
        foreach ($g_c as $v){
            $goods_c [] = BasisDecorationService::profitMargin($v);
        }


        foreach ($decoration as $value){
            foreach ($goods_c as $one_goods){
                if ($value['c_id'] ==  $one_goods['category_id']){
                    DecorationAdd::findByUpdate($one_goods['sku'],$value['id']);
                }
            }
        }

        return Json::encode([
           'code'=> 200,
           'msg'=> 'ok',
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
        $sku = DecorationAdd::findOne($id)->toArray();//TODO 修改
        $goods_cate=GoodsCategory::GetCategory($sku['c_id']);//TODO 新增分类 1 2 3 级
        $sku['goods_cate']=$goods_cate;
        $message_select = 'id,quantity,style_id,series_id,min_area,max_area';
        $where = 'decoration_add_id='.$sku['id'];
        $decoration_message = DecorationMessage::findById($message_select,$where);
        $select =['id','category_id','title','sku','supplier_price','platform_price','market_price','left_number'];
        $goods = Goods::findBySku($sku,$select);
        $goods['supplier_price'] = $goods['supplier_price'] / 100;
        $goods['platform_price'] = $goods['platform_price'] / 100;
        $goods['market_price'] = $goods['market_price'] / 100;
        $goods_attr = GoodsAttr::frontDetailsByGoodsId($goods['id']);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
           'decoration_add'=>$sku,
           'decoration_message'=>$decoration_message,
           'goods'=>$goods,
           'goods_attr'=>$goods_attr,
        ]);
    }

    /**
     *  材料添加项 修改
     * @return string
     */
    public function actionDecorationEdit()
    {
        $post                                = \Yii::$app->request->post();
        $decoration_add                      = DecorationAdd::findOne($post['id']);
        $decoration_add->correlation_message = $post['message'];
        $decoration_add->sku                 = $post['sku'];
        if (!$decoration_add->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }
        foreach ($post['add'] as $one_post) {
            if (isset($one_post['id'])) {
                $rows=DecorationMessage::find()->where(['id'=>$one_post['id']])->one();
                $rows->quantity=$one_post['quantity'];
                $dm=$rows->save(false);

            } elseif (isset($one_post['series'])) {
                $dm = \Yii::$app->db->createCommand()
                    ->insert(DecorationMessage::tableName(), [
                        'decoration_add_id'=>$post['id'],
                        'series_id' => $one_post['series'],
                        'quantity' => $one_post['quantity'],
                    ])->execute();
            } elseif (isset($one_post['style'])) {
                $dm = \Yii::$app->db->createCommand()
                    ->insert(DecorationMessage::tableName(), [
                        'decoration_add_id'=>$post['id'],
                        'style_id' => $one_post['style'],
                        'quantity' => $one_post['quantity'],
                    ])->execute();
            } elseif (isset($one_post['min_area'])) {
                $dm = \Yii::$app->db->createCommand()
                    ->insert(DecorationMessage::tableName(), [
                        'decoration_add_id' => $post['id'],
                        'min_area' => $one_post['min_area'],
                        'max_area' => $one_post['max_area'],
                        'quantity' => $one_post['quantity'],
                    ])->execute();
            }

        }
            if (!$dm) {
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
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
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
           'post'=> Points::findByPid('id,title','level = 1'),
        ]);
    }

    /**
     * commonality  one title list
     * @return string
     */
    public function actionCommonalityTitle()
    {
        $id = trim(\Yii::$app->request->get('id',''));
        $count = Points::findOne(['id'=>$id]);
        $select = 'id,title,differentiate,count';
        $where  = 'pid='.$id;
        $title['one_title'] = Points::findByPid($select,$where);
        foreach ($title['one_title'] as $one_title){
            $ids [] = $one_title['id'];
        }
        if (isset($ids)){
            $string_ids = implode(',',$ids);
            $two_select = 'id,title,count,pid,differentiate';
            $two_where  = 'pid in ('.$string_ids.')';
            $title['two_title'] = Points::findByPid($two_select,$two_where);
        }
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'list' => $title,
            'count' => $count
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
        if (isset($post['one_title']['id'])){
            $points->title = $post['one_title']['title'];
            $points->pid = $post['one_title']['id'];
            $points->level = 2;
            $points->differentiate = 1;
            if (!$points->save()){
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
            ]);
        }


        if (isset($post['one_title']['edit_id'])){
            $edit_points = $points->findOne(['id'=>$post['one_title']['edit_id']]);
            $edit_points->title = $post['one_title']['title'];
            if (!$edit_points->save()){
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
            ]);
        }


        if (isset($post['del_id'])) {
            $points_delete = $points->deleteAll(['and',['differentiate'=>1],['id'=>$post['del_id']]]);
            $points->deleteAll(['and',['differentiate'=>1],['pid'=>$post['del_id']]]);
            if ($points_delete == 0){
                return Json::encode([
                    'code'=> 1081,//TODO 1055 ->1081
                    'msg' => '删除失败,请确认'
                ]);
            }
            return Json::encode([
                'code'=> 200,
                'msg' => 'ok',
            ]);
        }
    }

    /**
     * commonality two title add
     * @return string
     */
    public function actionCommonalityTitleTwoAdd()
    {
        $post = \Yii::$app->request->post();
        $points = new Points();
        foreach ($post['two_title'] as $value){
            if (isset($value['id'])) {
                $points->findByInsert($value);
            }
            if (isset($value['edit_id'])){
                $points->findByUpdate($value['count'],$value['edit_id'],$value['title']);
            }
        }

        if (isset($post['del_id'])) {
            $del_points = $points->deleteAll(['and',['differentiate'=>1],['id'=>$post['del_id']]]);
            if (!$del_points){
                $code = 1000;
                return Json::encode([
                   'code' => $code,
                   'msg' => '删除'.\Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }

        if (isset($post['count'])){
            $points->_findByUpdate($post['count']['count'],$post['count']['id']);
        }
        if (isset($post['two_count'])){
            foreach ($post['two_count'] as $two){
                $points->findByUpdate($two['count'],$two['two_id'],$two['title']);
            }

        }


        return Json::encode([
           'code' => 200,
           'msg' => 'OK',
        ]);
    }

    /**
     * commonality area proportion list
     * @return string
     */
    public function actionCommonalityElseList()
    {
        $id = trim(\Yii::$app->request->get('id',''));
        $select = 'id,project,project_value,unit';
        $where = 'points_id  ='.$id;
        $area_select = 'id,points_id,min_area,max_area,project_name,project_value';
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'list' => ProjectView::findByAll($select,$where),
            'area' => Apartment::findByAll($area_select,$where),
            'apartment_area' => ApartmentArea::findCondition([],$where),
            'else_area'=> ApartmentArea::findByAll('min_area,max_area'),
        ]);
    }

    /**
     * commonality area proportion edit
     * @return string
     */
    public function actionCommonalityElseEdit()
    {
        $post = \Yii::$app->request->post();

        if (isset($post['apartment_area'])) {
            ApartmentArea::deleteAll([]);
            foreach ($post['apartment_area'] as $apartment_area){
                $add_apartment_area = ApartmentArea::findInset($apartment_area);
            }
            if (!$add_apartment_area) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }elseif (isset($post['value']) || isset($post['area'])) {

            if (isset($post['value'])){
                //上半部分
                foreach ($post['value'] as $one_value) {
                    ProjectView::findByUpdate($one_value['coefficient'], $one_value['id']);
                }
            }

            if(isset($post['area'])){
                //下半部分
                foreach ($post['area'] as $one_else){

                    if (isset($one_else['id'])){
                        Apartment::findByUpdate($one_else['value'],$one_else['id']);

                    }
                    if (isset($one_else['min_area'])) {
                        Apartment::findByInsert($one_else);

                    }
                }
            }


        }
        return Json::encode([
                'code' => 200,
                'msg' => 'ok',
            ]);
        }



    /**
     * 智能报价配套商品管理 列表
     * @return string
     */
    public function actionGoodsManagementList()
    {
        $city = (int)trim(\Yii::$app->request->get('city',''));
        $select = 'title,pid,path,category_id as id,quantity';
        $where = 'state = 1  and city_code = '.$city;
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
           'list'=> AssortGoods::findByAll($select,$where),
        ]);
    }

    /**
     * 智能报价配套商品管理 添加
     * @return string
     */
    public function actionGoodsManagementAdd()
    {
        $post = \Yii::$app->request->post();
        $tr = \Yii::$app->db->beginTransaction();
        try {
            $data=AssortGoods::find()->where(['state'=>1])->all();
            if($data){
                $del = (new AssortGoods())->deleteAll(['and',['state'=>1],['city_code'=>$post['city']]]);
                if (!$del){
                    $tr->rollBack();
                    $code=500;
                    return Json::encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code]
                    ]);
                }
            }

            //(new AssortGoods())->deleteAll(['and',['state'=>0],['city_code'=>$post['city']]]);


            if(isset($post['add_item'])){
                foreach($post['add_item'] as $management) {
                    $add = AssortGoods::findByInsert($management,$post['city']);
                }

                if (!$add){
                    $tr->rollBack();
                    $code=500;
                    return Json::encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code]
                    ]);
                }
            }

            $tr->commit();
        } catch (Exception $e) {
            $tr->rollBack();
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
        ]);
    }

    /**
     * 检测商品编码
     * @return string
     */
    public function actionSkuFefer()
    {
        $category = trim(\Yii::$app->request->get('cate_id',''));
        $sku = (int)trim(\Yii::$app->request->get('sku',''));

        $goods_category = GoodsCategory::find()->where(['id'=>$category])->andwhere(['level'=>3])->one();
        if (!$goods_category){
            $code = 1043;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }


        $goods = Goods::find()
            ->where(['sku'=>$sku])
            ->andWhere(['category_id'=>$goods_category['id']])
            ->andWhere(['status'=>2])
            ->one();
        if (!$goods){
            $code = 1043;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }


        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
        ]);

    }

    /**
     * 固定抓取 添加详情
     */
    public function actionFixedAddView(){
        $category_id=(int)\Yii::$app->request->get('category_id');
        $category=GoodsCategory::find()
            ->where(['id'=>$category_id])
            ->asArray()
            ->select('level,path,parent_title,title')
            ->one();
        if (isset($category['level']) && isset($category['path'])) {
            $category['titles'] = '';
            if ($category['level'] == GoodsCategory::LEVEL3) {
                $path = trim($category['path'], ',');
                list($rootId, $parentId, $id) = explode(',', $path);
                $rootCategory = GoodsCategory::findOne($rootId);
                $category['titles'] = $rootCategory->title
                    . GoodsCategory::SEPARATOR_TITLES
                    . $category['parent_title']
                    . GoodsCategory::SEPARATOR_TITLES
                    . $category['title'];
            } elseif ($category['level'] == GoodsCategory::LEVEL2) {
                $category['titles'] = $category['parent_title']
                    . GoodsCategory::SEPARATOR_TITLES
                    . $category['title'];
            } elseif ($category['level'] == GoodsCategory::LEVEL1) {
                $category['titles'] = $category['title'];
            }
            $category['level'] = GoodsCategory::$levels[$category['level']];
        }
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'list'=>$category
        ]);
    }

    /**
     * 固定抓取 添加商品
     * @return string
     */
    public function actionFixedGrabbingAdd(){
        $user_id=\Yii::$app->user->identity->getId();
        if(!$user_id){
            $code=403;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $path=trim(\Yii::$app->request->post('path'));
        $sku=trim(\Yii::$app->request->post('sku'));
        $start_time=trim(\Yii::$app->request->post('start_time'));
        $end_time=trim(\Yii::$app->request->post('end_time'));
        $city_code=trim(\Yii::$app->request->post('city_code'));

        if(!$path || !$sku){
            $code=1000;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);

        }
        $path=explode(',',$path);
        $goods_category=Goods::find()->where(['sku'=>$sku])->select('category_id')->asArray()->one()['category_id'];

        if($goods_category!=$path[2]){
            $code=1043;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=FixedGrabbingGoods::add($path,$sku,$start_time,$end_time,$city_code,$user_id);
        return Json::encode([
            'code'=>$code,
            'msg'=>$code==200?'ok':\Yii::$app->params['errorCodes'][$code]
        ]);
    }
    /**
     * 固定抓取 商品详情
     * @return string
     */
    public function actionFixedGoodsView(){
        $sku=trim(\Yii::$app->request->get('sku'));
        if(!$sku){
            $code=1000;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $goods=FixedGrabbingGoods::goodsview($sku);
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'goods'=>$goods
        ]);

    }

    /**
     * 固定抓取 总计商品数量
     * @return string
     */
    public function actionFirstGoodsList(){
        $city_code=(int)\Yii::$app->request->get('city_code',510010);
        $first_list=GoodsCategory::find()
            ->select('id,title')
            ->where(['pid'=>0])
            ->asArray()
            ->all();

        foreach ($first_list as &$list){
            $list['count']=FixedGrabbingGoods::find()
                ->where(['city_code'=>$city_code,'first_cate_id'=>$list['id']])
                ->count('first_cate_id');
        }
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'list'=>$first_list
        ]);

    }

    /**
     * 固定抓取 商品列表
     * @return string
     */
    public function actionFixedGrabbingList(){


        $vaue_all=\Yii::$app->params['value_all'];
        $first_cate_id=(int)\Yii::$app->request->get('first_cate_id');
        $status=(int)\Yii::$app->request->get('status',$vaue_all);
        $keyword=trim(\Yii::$app->request->get('keyword',''));
        $category_id=(int)(\Yii::$app->request->get('category_id',''));
        if(!$first_cate_id){
            $code=1000;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $where='fg.first_cate_id='.$first_cate_id;

        if(!$keyword){
            if ($category_id) {

                $cate_ids=Supplier::getcategory($category_id);
                if(is_array($cate_ids)){
                    $ids=  implode(',',$cate_ids);
                    $where.=" and fg.three_cate_id in ({$ids})";

                }else{
                    $where.=" and fg.three_cate_id ={$category_id}";
                }
            }
            if ($status != $vaue_all){
                $where.= " and fg.status ={$status} ";
            }else{
                $keys=implode(',',array_keys(FixedGrabbingGoods::FIXED_GOODS_STATUS));
                $where.= " and fg.status in ({$keys}) ";

            }

        }else{
            $where=" gc.title like '%{$keyword}%' or gc.parent_title  like '%{$keyword}%'";
        }
        $page = (int)\Yii::$app->request->get('page', 1);
        $size = (int)\Yii::$app->request->get('size', Supplier::PAGE_SIZE_DEFAULT);
        $paginationData = FixedGrabbingGoods::pagination($where, FixedGrabbingGoods::FIXED_GOODS_SEACRH, $page, $size);
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' =>
                $paginationData

        ]);
    }


    /**固定抓取商品 编辑详情
     * @return string
     */
    public function actionFixedGoodsEditView(){
        $id=(int)\Yii::$app->request->get('id');
        if(!$id){
            $code=1000;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=FixedGrabbingGoods::find()
            ->where(['id'=>$id])
            ->asArray()
            ->one();
        $data['start_time']=date('Y-m-d',$data['start_time']);
        $data['end_time']=date('Y-m-d',$data['end_time']);
        $data['operator']=User::find()->where(['id'=>$data['operator_id']])->one()->nickname;
        $data['operat_time']=date('Y-m-d H:i:s',$data['operat_time']);
        $data['status']=FixedGrabbingGoods::FIXED_GOODS_STATUS[$data['status']];
        $one_title=GoodsCategory::find()->where(['id'=>$data['first_cate_id']])->select('title')->one()->title;
        $two_title=GoodsCategory::find()->where(['id'=>$data['two_cate_id']])->select('title')->one()->title;
        $data['title']=GoodsCategory::find()->where(['id'=>$data['three_cate_id']])->select('title')->one()->title;

        $data['titles']=$one_title.'-'.$two_title.'-'.$data['title'];

        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$data
        ]);

    }

    /**
     * 固定抓取商品 修改
     * @return string
     */
    public function actionFixedGrabbingEdit(){
        $user_id=\Yii::$app->user->identity->getId();
        if(!$user_id){
            $code=403;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $id=(int)(\Yii::$app->request->post('id'));
        $sku=trim(\Yii::$app->request->post('sku'));
        $start_time=trim(\Yii::$app->request->post('start_time'));
        $end_time=trim(\Yii::$app->request->post('end_time'));


        if(!$sku){
            $code=1000;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);

        }
        $code=FixedGrabbingGoods::edit($id,$sku,$start_time,$end_time,$user_id);
        return Json::encode([
            'code'=>$code,
            'msg'=>$code==200?'ok':\Yii::$app->params['errorCodes'][$code]
        ]);
    }

    /**
     * 优化:小区添加
     * @return string
     */
    public function actionVillageAdd(){
        $province_code=(int)\Yii::$app->request->post('province_code',510000);
        $city_code=(int)\Yii::$app->request->post('city_code',510100);
        $district_code=(int)\Yii::$app->request->post('district_code','');
        $village_name=trim(\Yii::$app->request->post('village_name',''));
        $street=trim(\Yii::$app->request->post('street',''));

        $village=new EffectToponymy();

        $village->province_code=$province_code;
        $village->city_code=$city_code;
        $village->district_code=$district_code;
        $village->toponymy=$village_name;
        $village->add_time=time();
        $village->street=$street;

        if(!$village->save(false)){
            $code=500;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        return Json::encode([
            'code'=>200,
            'msg'=>'ok'
        ]);

    }

    /**
     * 优化:小区编辑
     * @return string
     */
    public function actionVillageEdit(){
        $village_id=(int)\Yii::$app->request->post('village_id');
        $district_code=(int)\Yii::$app->request->post('district_code');
        $village_name=trim(\Yii::$app->request->post('village_name',''));
        $street=trim(\Yii::$app->request->post('street',''));
        if(!$village_id){
            $code=1000;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $res=EffectToponymy::find()->where(['id'=>$village_id])->one();
        $res->district_code=$district_code;
        $res->toponymy=$village_name;
        $res->street=$street;
        if(!$res->save(false)){
            $code=500;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }

        return Json::encode([
            'code'=>200,
            'msg'=>'ok'
        ]);
    }

    /**
     * 优化:小区户型 列表
     * @return string
     */
    public function actionVillageHomeList(){
        $village_id=(int)\Yii::$app->request->get('village_id');
        if(!$village_id){
            $code=1000;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $houselist=Effect::HoseList($village_id);
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'list'=>$houselist
        ]);
    }

    /**
     * 测试功能
     */
    public function actionTest()
    {
        $res=new ChatService();
       $a= $res->getUser('18101795837304455113979583');
       var_dump($a);die;
    }
}