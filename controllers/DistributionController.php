<?php

namespace app\controllers;

use app\models\Addressadd;
use app\models\Distribution;
use app\models\GoodsOrder;
use app\models\LoginForm;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\helpers\Json;
use yii\db\Exception;
use app\services\ExceptionHandleService;
use yii\web\ServerErrorHttpException;

use app\services\SmValidationService;
use app\services\StringService;

class DistributionController extends Controller
{

    /**
     * Actions accessed by logged-in users
     */
    const ACCESS_LOGGED_IN_USER = [
        'logout',
        'roles',
        'reset-password',
        'roles-status',
        'time-types',
        'upload',
        'upload-delete',
        'review-statuses',
        'reset-nickname',
        'reset-signature',
        'reset-gender',
        'reset-birthday',
        'reset-district',
        'user-view',
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
                    'upload-delete' => ['post',],
                    'reset-nickname' => ['post',],
                    'reset-signature' => ['post',],
                    'reset-gender' => ['post',],
                    'reset-birthday' => ['post',],
                    'reset-district' => ['post',],
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
     * 输入手机号获取验证码
     * 登录状态判断并保存手机号
     * @return string
     */
    public function actionDistributionLoginMobile(){
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $mobile=trim($request->post('mobile',''));
        $ismobile = preg_match('/^1[34578]\d{9}$/',$mobile);
        if ($ismobile!=1 || !$mobile){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg'  => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
        $user=Distribution::findByMobile($mobile);
        $time=time();
        if($user)
        {
            $sms['mobile']=$mobile;
            $sms['type']='register';
        }else{
            $sms['mobile']=$mobile;
            $sms['type']='register';
        }
        try {
            new SmValidationService($sms);
        } catch (\InvalidArgumentException $e) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        } catch (ServerErrorHttpException $e) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }catch (\Exception $e) {
            $code = 1020;
            if ($code == $e->getCode()) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        $code=200;
        $session['distribution_mobile']=$mobile;
        return Json::encode(
            [
                'code'=>$code,
                'msg'=>'ok'
            ]
        );
    }

   /**
     * 登录
     * @return string
     */
    public function actionDistributionLogin(){
        $session = Yii::$app->session;
        $mobile=$session['distribution_mobile'];
        if (!$mobile)
        {
            $code = 403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $request = Yii::$app->request;
        $code=trim($request->post('code',''));
        if (!$code)
        {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        if (!SmValidationService::validCode($mobile, $code)) {
            $code = 1002;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
        SmValidationService::deleteCode($mobile);
        $time=time();
        $user=Distribution::findByMobile($mobile);
        if (!$user)
        {
            $tran = Yii::$app->db->beginTransaction();
            try{
                $Distribution=new Distribution();
                $Distribution->mobile=$mobile;
                $Distribution->create_time=$time;
                $res=$Distribution->save(false);
                if (!$res)
                {
                    $tran->rollBack();
                    $code=500;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $session['distribution_token']=urlencode($mobile.'&'.$time);
                if (!$session['distribution_token'])
                {
                    $tran->rollBack();
                }
                $code=200;
                return Json::encode([
                    'code' => $code,
                    'msg' =>'ok'
                ]);
            }catch (Exception $e){
                $tran->rollBack();
                $code=500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }else{
                $session['distribution_token']=urlencode($user->mobile.'&'.$user->create_time);
                $code=200;
                return Json::encode([
                    'code' => $code,
                    'msg' =>'ok'
                ]);
        }
    }


    /**
     * 分销个人中心
     * @return string
     */
    public function actionDistributionUserCenter(){
        $session = Yii::$app->session;
        $data=explode('&', urldecode($session['distribution_token']));
        if (!$data)
        {
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);

        }
        $mobile=$data[0];
        $create_time=$data[1];
        $Distribution=Distribution::find()
            ->where(['mobile'=>$mobile,'create_time'=>$create_time])
            ->one();
        if (!$Distribution){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=Distribution::DistributionUserCenter($mobile);
        $code=200;
        return Json::encode([
            'code' => $code,
            'msg' =>'ok',
            'data'=>$data
        ]);
    }


   /**
     * 分销绑定手机号
     * @return string
     */
    public function actionDistributionBindingMobile(){
        $session = Yii::$app->session;
        $data=explode('&', urldecode($session['distribution_token']));
        if (!$data)
        {
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $mobile=$data[0];
        $create_time=$data[1];
        $Distribution=Distribution::find()
            ->where(['mobile'=>$mobile,'create_time'=>$create_time])
            ->one();
        if (!$Distribution){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $binding_mobile=$request = Yii::$app->request->post('mobile','');
        $ismobile = preg_match('/^1[34578]\d{9}$/',$binding_mobile);
        if (!$binding_mobile || $ismobile!=1)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user=Distribution::find()
            ->where(['mobile'=>$binding_mobile])
            ->one();
        if (!$user)
        {
            $code=1010;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            $Distribution->parent_id=$user->id;
            $res=$Distribution->save(false);
            if (!$res)
            {
                $code=500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $code=200;
            return Json::encode([
                'code' => $code,
                'msg' =>'ok'
            ]);
        }catch (Exception $e){
            $tran->rollBack();
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /**
     * 大后台获取分销列表订单
     * @return array|string
     */
    public function actionGetdistributionlist1(){
        $request = Yii::$app->request;
        $page = (int)trim($request->get('page', 1));
        $page_size = (int)trim($request->get('page_size', 15));
        $time_type = trim($request->get('time_type', 'all'));
        $time_start = trim($request->get('time_start', ''));
        $time_end = trim($request->get('time_end', ''));
        if ($time_type == 'custom') {
            if (!$time_start || !$time_end) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        $search = trim($request->post('search', ''));

        if (!$page_size){
            $page_size=15;
        }
        $list=Distribution::Getdistributionlist($page,$page_size,$time_type,$time_start,$time_end,$search);
        return Json::encode([
            'code' => 200,
            'msg' =>'ok',
            'data' =>$list
        ]);
    }

   public  function  actionGetdistributionlist()
    {
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $page=trim($request->get('page',1));
        $size=trim($request->get('size',Distribution::PAGE_SIZE_DEFAULT));
        $keyword = trim($request->get('keyword', ''));
        $timeType = trim($request->get('time_type', ''));
        $where='';
        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));
            if (($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime))
            ){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }else{
            list($startTime, $endTime) = StringService::startEndDate($timeType);
            $startTime = explode(' ', $startTime)[0];
            $endTime = explode(' ', $endTime)[0];
        }
        if ($startTime) {
            $startTime = (int)strtotime($startTime);
            $startTime && $where .= "  create_time >= {$startTime}";
            if ($endTime) {
                $endTime = (int)strtotime($endTime);
                $endTime && $where .= " and  create_time <= {$endTime}";
            }

        }
        if ($endTime) {
            $endTime = (int)strtotime($endTime);
            $endTime && $where .= "  create_time <= {$endTime}";
        }
        $sort_time=trim($request->get('sort_time','2'));
        switch ($sort_time)
        {
            case 1:
                $sort='create_time asc';
                break;
            case 2:
                $sort='create_time desc';
                break;
        }
        if ($where!='')
        {
            if($keyword){
                $where .=" and mobile like '%{$keyword}%'";
            }
        }else{
            if($keyword){
                $where .="mobile like '%{$keyword}%'";
            }
        }
        $count=Distribution::find()->count();
        $data=Distribution::pagination($where,[],$page,$size,$sort);
        if ($data['list'])
        {
            foreach ($data['list'] as &$list)
            {
                $total_amount=0;
                $user=User::find()
                    ->where(['mobile'=>$list['mobile']])
                    ->one();
                $order_subsetnum=0;
                if ($user)
                {
                    $UserOrders=GoodsOrder::find()
                        ->select('order_no,amount_order,paytime,remarks')
                        ->where(['user_id'=>$user->id,'order_refer'=>2])
                        ->asArray()
                        ->all();
                    $order_subsetnum+=count($UserOrders);

                    foreach ($UserOrders as &$UserOrder)
                    {
                        $total_amount+=$UserOrder['amount_order']*0.01;
                    }
                }
                $consigneeOrders=GoodsOrder::find()
                    ->select('order_no,amount_order,paytime,remarks')
                    ->where(['consignee_mobile'=>$list['mobile'],'order_refer'=>1])
                    ->asArray()
                    ->all();
                $order_subsetnum+=count($consigneeOrders);
                foreach ($consigneeOrders as &$consigneeOrder)
                {

                    $total_amount+=$consigneeOrder['amount_order']*0.01;
                }
                $list['subset_amount']=GoodsOrder::switchMoney($total_amount);
                $list['order_subsetnum']=$order_subsetnum;
                unset($list['profit']);
                unset($list['parent_id']);
                unset($list['id']);
                $list['applydis_time']=$list['create_time'];
                unset($list['create_time']);
            }
        }

        $time=strtotime(date('Y-m-d',time()));
        $nowday_user=Distribution::find()->asArray()->where('create_time>'.$time)->count();
        $data['total_add']=$count;
        $data['nowday_add']=$nowday_user;
        return Json::encode([
            'code'=>200,
            'msg' =>'ok',
            'data' => $data
        ]);
    }

    /**
     * 分销详情页
     */
    public function actionGetdistributiondetail(){
        $request = Yii::$app->request;
        $mobile= trim(htmlspecialchars($request->post('mobile', 'all')), '');
        $data=Distribution::find()->where(['mobile'=>$mobile])->one();
        $subset=Distribution::find()->select('mobile,applydis_time')->where(['parent_id'=>$data['id']])->limit(10)->asArray()->all();
        foreach ($subset as $k =>$v){
            $subset[$k]['add_time']=date('Y-m-d',$subset[$k]['applydis_time']);
            unset($subset[$k]['applydis_time']);
        }
        $fatherset=Distribution::find()->where(['id'=>$data['parent_id']])->one();
        $data=[
            'myself'=>[
                'mobile'=>$data['mobile'],
                'add_time'=>date('Y-m-d',$data['create_time']),
            ],
            'fatherset'=>[
                'mobile'=>$fatherset['mobile'],
                'add_time'=>date('Y-m-d',$fatherset['applydis_time'])
             ],
            'subset'=>$subset
        ];
         return Json::encode([
            'code' => 200,
            'msg' =>'ok',
            'data' =>$data
        ]);
    }

    /**
     *  分销详情页-查看全部
     * @return string
     */
    public function actionSearchmore(){
        $request = Yii::$app->request;
        $mobile= trim(htmlspecialchars($request->post('mobile', 'all')), '');
        $data=Distribution::find()->where(['mobile'=>$mobile])->one();
        $subset=Distribution::find()->select('mobile,applydis_time')->where(['parent_id'=>$data['id']])->asArray()->all();
        return Json::encode([
            'code' => 200,
            'msg' =>'ok',
            'data' =>$subset
        ]);
    }

}