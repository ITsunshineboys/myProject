<?php
namespace app\controllers;

use app\models\Effect;
use app\models\EffectEarnst;
use app\models\EffectPicture;
use app\models\Series;
use app\models\Style;
use app\services\ExceptionHandleService;
use app\services\StringService;
use function PHPSTORM_META\elementType;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use Yii;

class EffectController extends Controller
{
    const HIGH=2.8;
    const WINDOW=2;
    const TYPE_EFFECT=2;
    const STATUST_ON=1;
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


    public function actionSrachToponymy(){
        $keyword=trim(Yii::$app->request->get('keyword'));
        $data=Effect::find()
            ->select('toponymy')
            ->asArray()
            ->distinct()
            ->where( "toponymy like '{$keyword}%'")
            ->andWhere(['type'=>1])
            ->all();
        var_dump($data);

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
            $high=$effect->high=self::HIGH;
            $window=$effect->window=self::WINDOW;
            $site_particulars=trim($request->post('site_particulars', ''), '');
            $effect->city = mb_substr($site_particulars,0,2,'utf8');
             $effect->district = mb_substr($site_particulars,2,3,'utf8');
            $effect->street = mb_substr($site_particulars,6,null,'utf8');
            $particulars= $effect->particulars = trim($request->post('particulars', ''), '');
            preg_match('/\d+/',$particulars,$area);
            $area=$effect->area=$area[0];
            $toponymy= $effect->toponymy = trim($request->post('toponymy', ''), '');
           $stairway= $effect->stairway = trim($request->post('stairway', ''), '');
            if (!$series_id || !$style_id ||!$area ||!$high || !$window  ||!$toponymy||  !$particulars || !$site_particulars) {
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
            if($stairway==1){

                 $stair_id=trim($request->post('stair_id',''),'');

                $effect->stair_id=$stair_id;

            }
            $transaction=Yii::$app->db->beginTransaction();
            $effect->type=self::TYPE_EFFECT;
            if(!$effect->save(false)){
                $transaction->rollBack();
                $code=500;
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
            $effect_picture->effect_id=$effect->id;
            if(!$effect_picture->save(false)){
                $transaction->rollBack();
                $code=500;
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
                return json_encode([
                    'code' => 200,
                    'msg' => 'ok',
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
     * 获取户型列表
     * @return string
     */
    public function actionGetparticulars(){
        $particulars=(new Query())->select('particulars')->from('effect')->distinct()->all();

        return json_encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$particulars
        ]);

    }
    /**
     * 获取风格
     * @return string
     */
    public function actionGetstyle(){

        return json_encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>Style::findByStyle()
        ]);
    }
    /**
     * 获取系列
     * @return string
     *
     */
    public function actionGetseries(){

        return json_encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>Series::findBySeries()
        ]);
    }
    /**
     * 获取楼梯材质
     * @return array
     *
     */
    public function actionGetstair(){

        $stairway=(int)trim(Yii::$app->request->get('stairway',''),'');
        if($stairway==1){

            $data=(new Query())->from('stairs_details')->select('*')->all();
            return json_encode([
                'code'=>200,
                'msg'=>'ok',
                'data'=>$data
            ]);
        }

    }
    /**
     * 支付定金/获取用户信息
     * @return array
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
                    'msg' => 'ok',

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


        if($request->isPost){
            $effect_id = trim($request->post('id', ''), '');
            if(!$effect_id){
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],

                ]);
            }
            $remark=EffectEarnst::findOne(['id'=>$effect_id]);
            if(!$remark){
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],

                ]);
            }
            $res=$remark->remark= trim($request->post('remark',''),'');
                if($res){
                    if(!$remark->save()){
                        $code=500;
                        return json_encode([
                            'code' => $code,
                            'msg' => \Yii::$app->params['errorCodes'][$code],
                        ]);
                }
                    return json_encode([
                    'code' => 200,
                    'msg' => 'ok',
                ]);

            }else{
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
                            'msg' => 'ok',
                            'data' => $data

                        ]);
                    }
                }

        }



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
        $earnest=new EffectEarnst();
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
            $model=EffectEarnst::findone(['id'=>$effect_id]);
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