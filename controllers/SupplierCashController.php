<?php

namespace app\controllers;


use app\models\Supplier;
use app\models\SupplierCashManager;
use app\models\User;
use app\models\UserBankInfo;
use app\models\UserCashregister;
use app\services\ExceptionHandleService;
use app\services\ModelService;
use yii\filters\AccessControl;
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
        'mall-view'
    ];


    const CASH_STATUS_ING = 1;
    const CASH_STATUS_DONE = 2;
    const CASH_STATUS_FAIL = 3;



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
    public function actionMallView(){
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }
        $supplier_id=Supplier::find()->where(['uid'=>$user])->asArray()->one()['id'];
        $data=Supplier::getsupplierdata($supplier_id,$user);
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
        $page = (int)$request->get('page', 1);
        $page_size = (int)$request->get('page_size', ModelService::PAGE_SIZE_DEFAULT);
        $time_type = trim(htmlspecialchars($request->get('time_type', 'all')), '');
        $time_start = trim(htmlspecialchars($request->get('time_start', '')), '');
        $time_end = trim(htmlspecialchars($request->get('time_end', '')), '');
        $status = (int)$request->get('status', '');

        if (($status && !array_key_exists($status, self::USER_CASH_STATUSES))
            || ($time_type == 'custom' && $time_end && $time_start > $time_end)
        ) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $data = SupplierCashManager::getCashList($user, $page, $page_size, $time_type, $time_start, $time_end, $status);


        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data,
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
        $supplier_id=Supplier::find()->asArray()->where(['uid'=>$user])->one()['id'];
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
        $page = (int)$request->get('page', 1);
        $page_size = (int)$request->get('page_size', ModelService::PAGE_SIZE_DEFAULT);
        $time_type = trim(htmlspecialchars($request->get('time_type', 'all')), '');
        $time_start = trim(htmlspecialchars($request->get('time_start', '')), '');
        $time_end = trim(htmlspecialchars($request->get('time_end', '')), '');
        $search = trim(htmlspecialchars($request->get('search', '')), '');

        if (!array_key_exists($time_type, \Yii::$app->params['timeTypes'])
            || ($time_type == 'custom' && $time_end && $time_start > $time_end)
        ) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $data = SupplierCashManager::getOrderList($page, $page_size, $time_type, $time_start, $time_end, $search);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
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
        $page = (int)$request->get('page', 1);
        $page_size = (int)$request->get('page_size', ModelService::PAGE_SIZE_DEFAULT);
        $time_type = trim(htmlspecialchars($request->get('time_type','all ')), '');
        $time_start = trim(htmlspecialchars($request->get('time_start', '')), '');
        $time_end = trim(htmlspecialchars($request->get('time_end', '')), '');
        $status = trim(htmlspecialchars($request->get('status', self::CASH_STATUS_ING)), '');
        $search = trim(htmlspecialchars($request->get('search', '')), '');
        if (($status && !array_key_exists($status, self::USER_CASH_STATUSES))
            || ($time_type == 'custom' && $time_end && $time_start > $time_end)
        ) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $data = SupplierCashManager::getCashListAll($page, $page_size, $time_type, $time_start, $time_end, $status, $search,$time_type);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }


    /**
     * 大后台提现详情操作页
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
            $real_money = (int)$request->post('real_money', '');
            if (($status == self::CASH_STATUS_DONE && $real_money <= 0)  || !$cash_id
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
            $code = 1052;
            return Json::encode([
                'code' => 1052,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        return $user->getId();
    }

    public function actionTest(){
//        var_dump(UserCashregister::find()->asArray()->where(['status'])->all());
    }
}
