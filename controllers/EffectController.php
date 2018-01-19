<?php
namespace app\controllers;

use app\models\Effect;
use app\models\EffectEarnest;
use app\models\EffectMaterial;
use app\models\User;
use app\services\ExceptionHandleService;
use app\services\ModelService;
use app\services\StringService;
use app\services\AuthService;
use yii\base\Model;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use Yii;

class EffectController extends Controller
{

    const TYPE_EFFECT=2;
    const PAY_STATUS=1;
    const PAGE_SIZE = 10;
    const ACCESS_LOGGED_IN_USER = [
        'effect-list',
        'effect-view',
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AuthService::className(),
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
                ],
            ],
        ];
    }

    /**
     * H5新样板间申请
     * @return string
     *
     */
    public function actionAddEffect()
    {
        $request = \Yii::$app->request;
        $post=$request->post();
        $code=1000;
        $phone  = trim($request->post('phone', ''), '');
        if (!preg_match('/^[1][3,5,7,8]\d{9}$/', $phone)) {
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]

            ]);
        }
        $code=Effect::addneweffect($post);

        if(!$code){
            $code=1000;
            return Json::encode([
                'code' =>$code,
                'msg' =>\Yii::$app->params['errorCodes'][$code]

            ]);
        }
        return Json::encode([
            'code' => 200,
            'msg' =>'ok'

        ]);

    }


    /**
     * 查看样板间备注详情
     * @return string
     *
     */
    public function actionRemarkView(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=403;
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

            $model=Effectearnest::find()->where(['effect_id'=>$effect_id])->one();
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
            $code=403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code = 1000;
        $request = new Request();
        $effect_enst_id = (int)trim($request->post('id', ''));
        if(!$effect_enst_id){
            return Json::encode([
                'code'=>$code,
                'msg'=>Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $remark= $request->post('remark','');

        if ($effect_enst_id && !isset($remark)) {
            $effect_id=EffectEarnest::find()->asArray()->where(['id'=>$effect_enst_id])->select('effect_id')->one()['effect_id'];
            $data = Effect::geteffectdata($effect_id);
            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
                'data'=>$data
            ]);
        }elseif( ($remark==''|| isset($remark)) && isset($effect_enst_id)){

            $effect_view=Effectearnest::find()->where(['id'=>$effect_enst_id])->one();
            $effect_view->remark=$remark;
            if(!$effect_view->save(false)){
                $code=500;
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




//        if(!$effect_enst_id){
//            return json_encode([
//                'code' => $code,
//                'msg' => \Yii::$app->params['errorCodes'][$code],
//
//            ]);
//        }
//
//        if(!$effect_view){
//            return json_encode([
//                'code' => $code,
//                'msg' => \Yii::$app->params['errorCodes'][$code],
//            ]);
//        }
//
//        if(isset($res) && $effect_enst_id){
//



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
            $code=403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code = 1000;
        $timeType = trim(Yii::$app->request->get('time_type', ''));
        $keyword = trim(Yii::$app->request->get('keyword', ''));

        $where = 'type = 0';
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
                if($startTime==$endTime){
                    list($startTime, $endTime) =ModelService::timeDeal($startTime);
                }else{
                    $endTime && $endTime .= ' 23:59:59';
                }
            } else {

                list($startTime, $endTime) = StringService::startEndDate($timeType);


            }

            if ($startTime) {
                $startTime = (int)strtotime($startTime);
                $startTime && $where .= " and create_time >= {$startTime}";
            }
            if ($endTime) {
                $endTime = (int)strtotime($endTime);
                 $endTime && $where .= " and create_time <= {$endTime}";
            }

        }else{
            $where.= " and CONCAT(name,phone,transaction_no) like '%{$keyword}%'";

        }
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', ModelService::PAGE_SIZE_DEFAULT);
        $paginationData = EffectEarnest::pagination($where, EffectEarnest::FIELDS_ADMIN, $page, $size);
        $earnest=new EffectEarnest();
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' =>
                   ['all_apply'=>$earnest::getallapply(),
                    'today_apply'=>$earnest::gettodayapply(),
                    'today_earnest'=>$earnest::gettodayearnest(),
                    'all_earnest'=>$earnest::getallearnest(),
                     $paginationData,
                     ]


        ]);
    }
    /**
     * app 保存/申请样板间
     * @return string
     */
    public function actionAppApplyEffect(){
        $user = Yii::$app->user->identity;

        $post=Yii::$app->request->post();
        if(!isset($user) && $post['type']==1){
            $code=403;
            return Json::encode([
                'code' =>$code,
                'msg' =>\Yii::$app->params['errorCodes'][$code]

            ]);
        }
        if (!$user){
            $uid='';
            $item=0;
        }else{
            $uid=$user->getId();
            $item=1;
        }
        $code=EffectEarnest::appAddEffect($uid,$post,$item);
        if(!$code){
            $code=500;
            return Json::encode([
                'code' =>$code,
                'msg' =>\Yii::$app->params['errorCodes'][$code]

            ]);
        }
        return Json::encode([
            'code' => 200,
            'msg' =>'ok'

        ]);

    }
    /**
     * app 样板间保存/申请详情
     * @return string
     */
    public function actionAppEffectView(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=1000;
        $effect_enst_id = trim(Yii::$app->request->get('effect_enst_id', ''), '');
        if(!$effect_enst_id){
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=Effect::getAppeffectdata($effect_enst_id);
            return Json::encode([
                'code'=>200,
                'msg'=>'ok',
                'data'=>$data
            ]);
    }
    /**
     * app 保存方案 去装修
     * @return string
     */
    public function actionGoDecoration(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $save_id=(int)trim(Yii::$app->request->get('save_id'));
        if(!$save_id){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user_info=User::find()->where(['id'=>$user->getId()])->asArray()->one();
        $effect_earnet=EffectEarnest::find()->where(['id'=>$save_id])->one();
        $effect_earnet->create_time=time();
        $effect_earnet->name=$user_info['nickname'];
        $effect_earnet->phone=$user_info['mobile'];
        $effect_earnet->type=0;
        if(!$effect_earnet->save(false)){
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        return Json::encode([
            'code'=>200,
            'msg'=>'申请成功,我们会在3天内和您联系'
        ]);
    }
    /**
     * 保存/申请方案列表
     * @return string
     */
    public function actionEffectPlan(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $type=(int)Yii::$app->request->get('type',1);
        $data=EffectEarnest::PlanList($user->getId(),$type);
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$data
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
        if($request->isPost){
            $effect_id=trim($request->post('id',''),'');
            if(!$effect_id){
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
            $model=Effectearnest::findone(['id'=>$effect_id]);
            $model->remark=trim($request->post('remark',''),'');
            $model->save();
            return json_encode([
                'code' => 200,
                'msg' => 'ok',
            ]);
        }else{
            $code=1050;
            return json_encode([
                'code'=>$code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }


    }


}