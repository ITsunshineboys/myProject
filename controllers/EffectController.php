<?php
namespace app\controllers;

use app\models\Effect;
use app\models\EffectEarnst;
use app\services\AdminAuthService;
use app\services\ExceptionHandleService;
use app\services\StringService;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use Yii;

class EffectController extends Controller
{

    const PAGE_SIZE = 10;
    const ACCESS_LOGGED_IN_USER = [
        'effect-view',
        'effect-list',
        'remark-view',
        'edit-remark',

    ];
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AdminAuthService::className(),
                'denyCallback' => function ($rule, $action) {
                    $code = 403;
                    new ExceptionHandleService($code);
                    exit;
                },
                'only' => self::ACCESS_LOGGED_IN_USER,
                'rules' => [
                    [
                        'actions' => self::ACCESS_LOGGED_IN_USER,
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [

                    'effect-view'=>['post'],
                    'effect-list'=>['get'],
                    'remark-view'=>['get'],
                     'edit-remark'=>['post']
                    ],
                ],
            ];

    }

    /**
     * 前台样板间申请
     * @return string
     *
     */
    public function actionAddEffect()
    {
        $code = 1000;
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $effect = new Effect();
            $effect->series_id = trim($request->post('series_id', ''), '');
            $effect->style_id = trim($request->post('style_id', ''), '');
            $effect->bedroom = trim($request->post('bedroom', ''), '');


            $effect->sittingRoom_diningRoom = trim($request->post('sittingRoom_diningRoom', ''), '');
            $effect->toilet = trim($request->post('toilet', ''), '');
            $window =$effect->window = trim($request->post('window', ''), '');
            $effect->kitchen = trim($request->post('kitchen', ''), '');
            $area= $effect->area = trim($request->post('area', ''), '');
           $effect->high = trim($request->post('high', ''), '');
            $effect->province = trim($request->post('province', ''), '');
            $effect->city = trim($request->post('city', ''), '');
            $effect->district = trim($request->post('district', ''), '');
            $effect->toponymy = trim($request->post('toponymy', ''), '');
            $effect->street = trim($request->post('street', ''), '');
            $effect->particulars = trim($request->post('particulars', ''), '');
            $effect->site_particulars = trim($request->post('site_particulars', ''), '');
            $effect->stairway = trim($request->post('stairway', ''), '');

            if($area>180){

                return json_encode([
                    'code' => $code,
                    'msg' => '面积不能超过180',
                ]);
            }
            if($window>20){
                return json_encode([
                    'code' => $code,
                    'msg' => '飘窗不能超过20',
                ]);
            }
            if (!$effect->validate()) {
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            } else {
                $effect->save();
                return json_encode([
                    'code' => 200,
                    'msg' => '样板间添加成功!',
                    'data' => $effect->toArray()
                ]);
            }

        } else {
            $code=500;
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]

            ]);
        }
    }

    /**
     * 支付定金/获取用户信息
     * @return string
     *
     */
    public function actionEffectEarnest()
    {
        $code = 1000;
        $request = new Request();

        if ($request->isPost) {
            $model = new EffectEarnst();
            //保存样板Id
            $model->effect_id = trim($request->post('effect_id', ''), '');
            $name = $model->name = trim($request->post('name', ''), '');
            $phone = $model->phone = trim($request->post('phone', ''), '');
            if (!preg_match('/^[1][3,5,7,8]\d{9}$/', $phone)) {
                return json_encode([
                    'code' => $code,
                    'msg' => '电话号码不正确!'

                ]);
            }

            if (!$model->validate()) {
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]


                ]);
            } else {
                $model->save();
                return json_encode([
                    'code' => 200,
                    'msg' => '申请成功!',
                    'data' => $model->toArray(),

                ]);

            }
        } else {
            $code=500;
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]

            ]);
        }

    }
    /**
     * 查看样板间备注详情
     * @return string
     *
     */
    public function actionRemarkView(){

        $code=1000;
        $request=new Request();

        $effect_id = trim($request->get('id', ''), '');
        if (!$effect_id) {
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],


            ]);
        }else{

            $model=EffectEarnst::find()->where(['effect_id'=>$effect_id])->one();
            return json_encode([
                'code' => 200,
                'msg' => 'ok',
                'data'=>$model->remark

            ]);
        }


    }

    /**
     * 查看样板间详情
     * @return string
     *
     */
    public function actionEffectView()
    {
        $code = 1000;
        $request = new Request();

            $effect_id = trim($request->get('id', ''), '');
                if(!$effect_id){
                    return json_encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code],

                    ]);
                }
        if($request->isPost){
            $remark=EffectEarnst::findOne(['effect_id'=>$effect_id]);

          $res=$remark->remark= trim($request->post('remark',''),'');

        $remark->save();


            return json_encode([
                'code' => 200,
                'msg' => 'ok',
                'data' => $res
                ]);



        }
            $model = new Effect();


            $data = $model->geteffectdata($effect_id);
//            var_dump($data);exit;
            if (!$effect_id) {
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],



                ]);
            }else{
                return json_encode([
                    'code' => 200,
                    'msg' => '成功!',
                    'data' => $data

                ]);
            }


    }

    /**
     * 后台样板间列表搜索+分页
     * @return string
     *
     */



    public function actionEffectList()
    {
        $code = 1000;

        $timeType = trim(Yii::$app->request->get('time_type', ''));
        $phone = trim(Yii::$app->request->get('phone', ''));



        $where = '1';
        if($phone){
            $where='phone';
            $where .= " like $phone";



        }
        if (!$timeType || !in_array($timeType, array_keys(Yii::$app->params['timeTypes']))) {

            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }


        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));



            if (($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime))
            ) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        } else {
            list($startTime, $endTime) = StringService::startEndDate($timeType);
            $startTime = explode(' ', $startTime)[0];
            $endTime = explode(' ', $endTime)[0];
        }
//
        if ($startTime) {
            $startTime = str_replace('-', '', $startTime);
            $startTime && $where .= " and create_time >= {$startTime}";
        }
        if ($endTime) {
            $endTime = str_replace('-', '', $endTime);
            $endTime && $where .= " and create_time <= {$endTime}";
        }

        $page = (int)Yii::$app->request->get('page', 1);

        $size = (int)Yii::$app->request->get('size', EffectEarnst::PAGE_SIZE_DEFAULT);
        $paginationData = EffectEarnst::pagination($where, EffectEarnst::FIELDS_ADMIN, $page, $size);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' =>

                $paginationData['details']



        ]);
    }



    /**
     * 后台样板间列表查看或编辑备注
     * @return string
     *
     */
    public function actionEditRemark(){
        $code=1000;
        $request=new Request();
        $effect_id=trim($request->get('id',''),'');

        if(!$effect_id){
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $model=EffectEarnst::findone(['id'=>$effect_id]);
        if($request->isPost){
            $model->remark=trim($request->post('remark',''),'');
            $model->save();
            return json_encode([
                'code' => 200,
                'msg' => 'ok',
            ]);
        }else{

            return json_encode([
                'code' => $code,
                'msg' => 'ok',
                'data'=>$model->remark
            ]);
        }
    }


}