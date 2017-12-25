<?php

namespace app\controllers;



use app\models\ChatRecord;
use app\models\EffectEarnest;
use app\models\OwnerCashManager;

use app\models\Supplier;
use app\models\SupplierCashManager;
use app\models\User;
use app\models\UserAccessdetail;
use app\models\UserCashregister;
use app\services\ExceptionHandleService;
use app\services\ModelService;
use app\services\StringService;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use app\services\AuthService;


class SupplierCashController extends Controller
{

    /**
     * Actions accessed by logged-in users
     */
    const ACCESS_LOGGED_IN_USER = [
        'get-cash-list',
        'get-cash',
        'cash-index',
        'order-list-today',
        'cash-list-today',
        'cash-action-detail',
        'cash-deal',
        'mall-view',
        'owner-cash-index',
        'owner-cashed-list',
        'owner-cashed-detail',
        'owner-do-cash-deal',

    ];


    const CASH_STATUS_ING = 1;
    const CASH_STATUS_DONE = 2;
    const CASH_STATUS_FAIL = 3;

    const CASHING='提现中';
    const CASHED='已提现';
    const CASHED_FILD='驳回';

    const ACCESS_TYPE_RECHARGE = 1;
    const ACCESS_TYPE_CHARGE = 2;
    const ACCESS_TYPE_CASH_DONE = 3;
    const ACCESS_TYPE_CASH_ING = 4;
    const ACCESS_TYPE_REJECT = 5;
    const ACCESS_TYPE_PAYMENT = 6;

    const USER_CASH_STATUSES = [
        self::CASH_STATUS_ING => '提现中',
        self::CASH_STATUS_DONE => '已提现',
        self::CASH_STATUS_FAIL => '驳回'
    ];

    const USER_CASH_SATE=[
      self::CASH_STATUS_ING=>self::CASHING,
      self::ACCESS_TYPE_CHARGE=>self::CASHED,
      self::ACCESS_TYPE_CASH_DONE=>self::CASHED_FILD,
    ];

    const USER_ACCESS_TYPE = [
        self::ACCESS_TYPE_RECHARGE => '充值',
        self::ACCESS_TYPE_CHARGE => '扣款',
        self::ACCESS_TYPE_CASH_DONE => '已提现',
        self::ACCESS_TYPE_CASH_ING => '提现中',
        self::ACCESS_TYPE_REJECT => '驳回',
        self::ACCESS_TYPE_PAYMENT => '货款'
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


    //---------------------------商家后台---------------------------

    /**
     * 商家详情
     * @return int|string
     */
    public function actionMallView()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }
        $supplier_id = Supplier::find()->where(['uid' => $user])->asArray()->one()['id'];
        $data = Supplier::getsupplierdata($supplier_id, $user);
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data,
        ]);
    }

    /**
     * 获取商家提现列表
     * @return mixed
     */
    public function actionGetCashList()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;
        $timeType = trim(htmlspecialchars($request->get('time_type', '')), '');;
        $status = (int)$request->get('status', '');
        $where = "uid=$user and role_id=6";
        $code=1000;
            if ($timeType == 'custom') {
                $time_start = trim(htmlspecialchars($request->get('time_start', '')), '');
                $time_end = trim(htmlspecialchars($request->get('time_end', '')), '');
                if (($time_start && !StringService::checkDate($time_start))
                    || ($time_end && !StringService::checkDate($time_end))
                ) {
                    return json_encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code],
                    ]);
                }
                if ($time_start == $time_end) {
                    list($time_start, $time_end) = ModelService::timeDeal($time_start);
                }else{
                    $time_end && $time_end = ' 23:59:59';
                }
            } else {
                list($time_start, $time_end) = StringService::startEndDate($timeType);
            }

            if ($time_start) {
                $startTime = (int)strtotime($time_start);
                $startTime && $where .= " and apply_time >= {$startTime}";
            }
            if ($time_end) {
                $time_end = (int)strtotime($time_end);
                $time_end && $where .= " and apply_time <= {$time_end}";
            }
            if ($status!=0){
                $where.= " and status ={$status} ";
            }else{

                $keys=implode(',',array_keys(self::USER_CASH_SATE));
                $where.= " and  status in ({$keys}) ";

            }

        $page = (int)$request->get('page', 1);
        $page_size = (int)$request->get('page_size', ModelService::PAGE_SIZE_DEFAULT);

        $paginationData = SupplierCashManager::getCashList($user,$where,$page, $page_size);
        return json_encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $paginationData
        ]);

    }

    /**
     * 获取商家提现详情
     * @return mixed
     */
    public function actionGetCash()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;
        $transaction_no = (int)$request->get('transaction_no', '');
        if (!$transaction_no) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier_id = Supplier::find()->asArray()->where(['uid' => $user])->one()['id'];
        if (!$supplier_id) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data = SupplierCashManager::GetCash($transaction_no, $supplier_id);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }


    //-----------------------------------大后台---------------------------------

    /**
     * 大后台商家财务中心首页
     * @return mixed [金额和数量的统计]
     */
    public function actionCashIndex()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $cash_manager = new SupplierCashManager();
        $data = [];
        $data['orders_all'] = $cash_manager->getPayedOrdersAll();
        $data['orders_today'] = $cash_manager->getPayedOrdersToday();
        $data['cashes_all'] = $cash_manager->getPayedCashesAll();
        $data['cashes_today'] = $cash_manager->getPayedCashesToday();
        $data['payed_cashes_count'] = $cash_manager->getPayedCashesCountAll();
        $data['not_payed_cashes_count'] = $cash_manager->getNotPayedCashesCountAll();

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }
    /**
     * 大后台业主财务中心
     * @return int|string
     */
    public function actionOwnerCashIndex(){
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }
        $data=[];
        $data['cashes_all'] = OwnerCashManager::getOwnerCashedAll();
        $data['cashes_today'] = OwnerCashManager::getOwnerCashedToday();
        $data['payed_cashes_count'] = OwnerCashManager::getOwnerCashed();
        $data['not_payed_cashes_count'] = OwnerCashManager::getOwnerCashing();

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }
    /**
     * 业主提现管理
     * @return int|string
     */
    public function actionOwnerCashedList(){
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;

        $timeType = trim(htmlspecialchars($request->get('time_type', '')), '');

        $status = trim(htmlspecialchars($request->get('status', '')), '');
        $search = trim(htmlspecialchars($request->get('keyword', '')), '');
        $code = 1000;

        if ($status==1 || $status==0){
            $time_s='g.apply_time';
        }else{
            $time_s='g.handle_time';
        }
        $where = "g.role_id=7";
        if (!$search) {
            if ($timeType == 'custom') {
                $time_start = trim(htmlspecialchars($request->get('time_start', '')), '');
                $time_end = trim(htmlspecialchars($request->get('time_end', '')), '');
                if (($time_start && !StringService::checkDate($time_start))
                    || ($time_end && !StringService::checkDate($time_end))
                ) {
                    return json_encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code],
                    ]);
                }
                if ($time_start == $time_end) {
                    list($time_start, $time_end) = ModelService::timeDeal($time_start);
                }else{
                    $time_end && $time_end .= ' 23:59:59';
                }

            } else {
                list($time_start, $time_end) = StringService::startEndDate($timeType);
            }

            if ($time_start) {
                $startTime = (int)strtotime($time_start);
                $startTime && $where .= " and {$time_s} >= {$startTime}";
            }
            if ($time_end) {
                $time_end = (int)strtotime($time_end);
                $time_end && $where .= " and {$time_s} <= {$time_end}";
            }

            if ($status!=0){
                $where.= " and g.status =$status ";
            }
        } else {
            $where.= " and CONCAT(u.nickname,u.aite_cube_no) like '%{$search}%'";
        }

        $page = (int)$request->get('page', 1);
        $page_size = (int)$request->get('size', ModelService::PAGE_SIZE_DEFAULT);
        $paginationData = OwnerCashManager::getCashListAll($where,$page, $page_size);
        return json_encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $paginationData
        ]);
    }

    /**
     * 业主提现详情
     * @return int|string
     */
    public function actionOwnerCashedDetail(){
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;
        $transaction_no = (int)$request->get('transaction_no', '');
        if (!$transaction_no) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user_uid=UserCashregister::find()
            ->asArray()
            ->where(['transaction_no'=>$transaction_no])
            ->one()['uid'];
        $user_id=User::find()->where(['id'=>$user_uid])->asArray()->one()['id'];
        if(!$user_id){
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data = OwnerCashManager::GetCashView($transaction_no,$user_id);
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }


    /**
     * 处理业主提现
     * @return int|string
     */
    public function actionOwnerDoCashDeal(){
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }
        $code = 1000;
        $request = \Yii::$app->request;
        $cash_id = (int)$request->post('cash_id', '');
        $status = (int)$request->post('status', '');
        $reason = trim(htmlspecialchars($request->post('reason', '')), '');
        $real_money = $request->post('real_money', '');
        if (($status == self::CASH_STATUS_DONE && $real_money < 0)  || !$cash_id
        ) {
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $code=OwnerCashManager::doCash($cash_id, $status, $reason, $real_money);
        return Json::encode([
            'code' => $code,
            'msg' => $code==200?'ok':\Yii::$app->params['errorCodes'][$code]
        ]);


    }
    /**
     * 获取今日入账列表
     * @return int|string
     */
    public function actionOrderListToday()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;
        $timeType = trim(htmlspecialchars($request->get('time_type', 'all')), '');
        $search = trim(htmlspecialchars($request->get('search', '')), '');
        $where = "g.pay_status= 1";
        $code=1000;
        if (!$search) {
            if ($timeType == 'custom') {
                $time_start = trim(htmlspecialchars($request->get('time_start', '')), '');
                $time_end = trim(htmlspecialchars($request->get('time_end', '')), '');
                if (($time_start && !StringService::checkDate($time_start))
                    || ($time_end && !StringService::checkDate($time_end))
                ) {
                    return json_encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code],
                    ]);
                }
                if ($time_start == $time_end) {
                    list($time_start, $time_end) = ModelService::timeDeal($time_start);
                }else{
                    $time_end && $time_end .= ' 23:59:59';
                }
            } else {
                list($time_start, $time_end) = StringService::startEndDate($timeType);
            }

            if ($time_start) {
                $startTime = (int)strtotime($time_start);
                $startTime && $where .= " and g.paytime >= {$startTime}";
            }
            if ($time_end) {
                $time_end = (int)strtotime($time_end);
                $time_end && $where .= " and g.paytime <= {$time_end}";

            }

        } else {
            $where .=" and CONCAT(s.shop_no,s.shop_name, g.order_no) like '%{$search}%'";

        }

        $page = (int)$request->get('page', 1);
        $page_size = (int)$request->get('page_size', ModelService::PAGE_SIZE_DEFAULT);

        $paginationData = SupplierCashManager::getOrderList($where,$page, $page_size);
        return json_encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $paginationData
        ]);
    }

    /**
     * 获取今日提现列表
     * @return int|string
     */
    public function actionCashListToday()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;

        $timeType = trim(htmlspecialchars($request->get('time_type', '')), '');

        $status = trim(htmlspecialchars($request->get('status', '')), '');
        $search = trim(htmlspecialchars($request->get('search', '')), '');
        $code = 1000;

        if ($status==1 || $status==0){
          $time_s='g.apply_time';
        }else{
            $time_s='g.handle_time';
        }
        $where = "g.role_id=6";
        if (!$search) {
            if ($timeType == 'custom') {
                $time_start = trim(htmlspecialchars($request->get('time_start', '')), '');
                $time_end = trim(htmlspecialchars($request->get('time_end', '')), '');
                if (($time_start && !StringService::checkDate($time_start))
                    || ($time_end && !StringService::checkDate($time_end))
                ) {
                    return json_encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code],
                    ]);
                }
                if ($time_start == $time_end) {
                    list($time_start, $time_end) = ModelService::timeDeal($time_start);
                }else{
                    $time_end && $time_end .= ' 23:59:59';
                }

            } else {
                list($time_start, $time_end) = StringService::startEndDate($timeType);
            }

            if ($time_start) {
                $startTime = (int)strtotime($time_start);
                $startTime && $where .= " and {$time_s} >= {$startTime}";
            }
            if ($time_end) {
                $time_end = (int)strtotime($time_end);
                $time_end && $where .= " and {$time_s} <= {$time_end}";
            }

            if ($status!=0){
                $where.= " and g.status =$status ";
            }
        } else {
            $where.= " and CONCAT(s.shop_no,s.shop_name) like '%{$search}%'";
        }

        $page = (int)$request->get('page', 1);
        $page_size = (int)$request->get('page_size', ModelService::PAGE_SIZE_DEFAULT);
        $paginationData = SupplierCashManager::getCashListAll($where,$page, $page_size);
        return json_encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $paginationData
        ]);
    }


    /**
     * 大后台商家提现详情操作页
     * @return mixed
     */
    public function actionCashActionDetail()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;
        $transaction_no = (int)$request->get('transaction_no', '');
        if (!$transaction_no) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier_uid=UserCashregister::find()
            ->asArray()
            ->where(['transaction_no'=>$transaction_no])
            ->one()['uid'];
            $supplier_id=Supplier::find()->where(['uid'=>$supplier_uid])->asArray()->one()['id'];
        if(!$supplier_id){
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data = SupplierCashManager::GetCash($transaction_no,$supplier_id);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }


    /**
     * 提交和审核提现
     * @return mixed
     */
    public function actionCashDeal()
    {
        if (\Yii::$app->request->isPost) {
            $user = self::userIdentity();
            if (!is_int($user)) {
                return $user;
            }

            $code = 1000;
            $request = \Yii::$app->request;
            $cash_id = (int)$request->post('cash_id', '');
            $status = (int)$request->post('status', '');
            $reason = trim(htmlspecialchars($request->post('reason', '')), '');
            $real_money = $request->post('real_money', '');
            if (($status == self::CASH_STATUS_DONE && $real_money < 0)  || !$cash_id
            ) {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]
                ]);
            }

            $code = SupplierCashManager::doCashDeal($cash_id, $status, $reason, $real_money);
                return Json::encode([
                    'code' => $code,
                    'msg' => $code==200?'ok':\Yii::$app->params['errorCodes'][$code]
                ]);

            }
        $code = 1050;
        return Json::encode([
            'code' => $code,
            'msg' => \Yii::$app->params['errorCodes'][$code]
        ]);
    }

    //判断用户是否登陆
    private function userIdentity()
    {
        $user = \Yii::$app->user->identity;
        if (!$user) {
            $code = 403;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        return $user->getId();
    }

    public function actionTest()
    {
//       $user=User::find()->where(['id'=>8])->one();
//       $user->availableamount=99889865;
//       $user->save(false);
//       var_dump($user);die;
      var_dump(UserAccessdetail::find()->asArray()->all());

    }

}
