<?php
namespace app\controllers;

use app\models\Effect;
use app\models\EffectEarnst;
use app\models\EffectPicture;
use app\models\GoodsCategory;
use app\models\StairsDetails;
use app\services\AdminAuthService;
use app\services\ExceptionHandleService;
use app\services\StringService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use Yii;

class EffectController extends Controller
{

    const PAGE_SIZE = 10;
    const ACCESS_LOGGED_IN_USER = [
        'logout',
        'roles',
        'reset-password',
        'roles-status',
        'time-types',
        'upload',
        'upload-delete',
        'review-statuses',
    ];

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
                    'logout' => ['post',],
                    'reset-password' => ['post',],
                    'upload' => ['post',],
                    'upload-delete' => ['post',]
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
            $effect_picture=new EffectPicture();
           $series_id= $effect_picture->series_id = trim($request->post('series_id', ''), '');
           $style_id= $effect_picture->style_id = trim($request->post('style_id', ''), '');
            $effect = new Effect();
            $bedroom=$effect->bedroom = trim($request->post('bedroom', ''), '');

            $sittingRoom_diningRoom= $effect->sittingRoom_diningRoom = trim($request->post('sittingRoom_diningRoom', ''), '');
            $effect->toilet = trim($request->post('toilet', ''), '');
            $window =$effect->window = trim($request->post('window', ''), '');
            $kitchen=$effect->kitchen = trim($request->post('kitchen', ''), '');
            $area= $effect->area = trim($request->post('area', ''), '');
            $high= $effect->high = trim($request->post('high', ''), '');
            $province=$effect->province = trim($request->post('province', ''), '');
            $city =$effect->city = trim($request->post('city', ''), '');
           $district= $effect->district = trim($request->post('district', ''), '');
            $toponymy= $effect->toponymy = trim($request->post('toponymy', ''), '');
            $street=$effect->street = trim($request->post('street', ''), '');
            $particulars= $effect->particulars = trim($request->post('particulars', ''), '');
            $site_particulars= $effect->site_particulars = trim($request->post('site_particulars', ''), '');
           $stairway= $effect->stairway = trim($request->post('stairway', ''), '');
            if (!$series_id || !$style_id ||!$sittingRoom_diningRoom||!$kitchen||!$bedroom || !$window || !$high ||!$area || !$province || !$city ||!$district ||!$toponymy|| !$street|| !$particulars || !$site_particulars) {
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
            if($stairway==1){

                 $stair_id=trim($request->post('stair_id',''),'');

                $effect->stair_id=$stair_id;

            }

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
            $effect->save(false);
            $effect_picture->effect_id=$effect->id;
            $effect_picture->save(false);
                return json_encode([
                    'code' => 200,
                    'msg' => '样板间添加成功!',
                    'data'=>[
                        'id'=>$effect->id
                    ]
                ]);

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
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
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
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
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
     * 后台样板间获取申请总金额
     * @return string
     *
     */

    public function actionApplyEarnestNum(){

        $user = \Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $earnest=new EffectEarnst();
        $data=[];
        $data['all_apply']=$earnest::getallapply();

        $data['today_apply']=$earnest::gettodayapply();
        $data['today_earnest']=$earnest::gettodayearnest();
        $data['all_earnest']=$earnest::getallearnest();
        return json_encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    /**
     * 后台样板间列表搜索
     * @return string
     *
     */



    public function actionEffectList()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code = 1000;
        $timeType = trim(Yii::$app->request->get('time_type', ''));
        $keyword = trim(Yii::$app->request->get('keyword', ''));

        $where = '1';
        if(!$keyword){


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
                $startTime = (int)strtotime($startTime);
                $startTime && $where .= " and create_time >= {$startTime}";
            }
            if ($endTime) {
                $endTime = (int)strtotime($endTime);
                $endTime && $where .= " and create_time <= {$endTime}";
            }

        }else{
            $where=" name like '%{$keyword}%' or phone like '%{$keyword}%'";
        }



        $page = (int)Yii::$app->request->get('page', 1);

        $size = (int)Yii::$app->request->get('size', EffectEarnst::PAGE_SIZE_DEFAULT);
        $paginationData = EffectEarnst::pagination($where, EffectEarnst::FIELDS_ADMIN, $page, $size);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' =>

                $paginationData

        ]);
    }



    /**
     * 后台样板间列表查看或编辑备注
     * @return string
     *
     */
    public function actionEditRemark(){

        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
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
        if(!$model){
            return json_encode([
                'code' => 200,
                'msg' => 'ok',
                'data'=>null
            ]);
        }
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