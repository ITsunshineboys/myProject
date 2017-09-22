<?php

namespace app\controllers;


use app\models\Supplier;
use app\models\SupplierCashManager;
use app\services\ExceptionHandleService;
use app\services\ModelService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;

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
        'cash-deal'
    ];

    const CASH_STATUS_ING = 1;
    const CASH_STATUS_DONE= 2;
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
        self::CASH_STATUS_FAIL => '提现失败'
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
        $page = (int)$request->post('page', 1);
        $page_size = (int)$request->post('page_size', ModelService::PAGE_SIZE_DEFAULT);
        $time_type = trim(htmlspecialchars($request->post('time_type', 'all')), '');
        $time_start = trim(htmlspecialchars($request->post('time_start', '')), '');
        $time_end = trim(htmlspecialchars($request->post('time_end', '')), '');
        $status = (int)$request->post('status', '');

        if (($time_type == 'custom' && (!$time_start || !$time_end))
            || !array_key_exists($time_type, \Yii::$app->params['timeTypes'])
            || ($status && !array_key_exists($status, self::USER_CASH_STATUSES))
        ) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }

        $supplier = Supplier::find()
            ->select('id')->where(['uid' => $user])->one();
        $data = SupplierCashManager::getCashList($supplier['id'], $page, $page_size, $time_type, $time_start, $time_end, $status);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    /**
     * 获取商家提现详情
     * @return mixed
     */
    public function actionGetCash($admin = 0)
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;
        $cash_id = (int)$request->get('cash_id', '');
        if (!$cash_id) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }

        $supplier = Supplier::find()->select('id')->where(['uid' => $user])->one();
        if ($admin) {
            $supplier['id'] = 0;
        }
        $data = SupplierCashManager::GetCash($cash_id, $supplier['id']);

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
        $page = (int)$request->post('page', 1);
        $page_size = (int)$request->post('page_size', ModelService::PAGE_SIZE_DEFAULT);
        $time_type = trim(htmlspecialchars($request->post('time_type', 'today')), '');
        $time_start = trim(htmlspecialchars($request->post('time_start', '')), '');
        $time_end = trim(htmlspecialchars($request->post('time_end', '')), '');
        $search = trim(htmlspecialchars($request->post('search', '')), '');

        if (($time_type == 'custom' && (!$time_start || !$time_end))
            || !array_key_exists($time_type, \Yii::$app->params['timeTypes'])
        ) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
                'data' => null
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
        $page = (int)$request->post('page', 1);
        $page_size = (int)$request->post('page_size', ModelService::PAGE_SIZE_DEFAULT);
        $time_type = trim(htmlspecialchars($request->post('time_type', 'today')), '');
        $time_start = trim(htmlspecialchars($request->post('time_start', '')), '');
        $time_end = trim(htmlspecialchars($request->post('time_end', '')), '');
        $status = trim(htmlspecialchars($request->post('status', self::CASH_STATUS_DONE)), '');
        $search = trim(htmlspecialchars($request->post('search', '')), '');

        if (($time_type == 'custom' && (!$time_start || !$time_end))
            || !array_key_exists($time_type, \Yii::$app->params['timeTypes'])
            || ($status && !array_key_exists($status, self::USER_CASH_STATUSES))
        ) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }

        $data = SupplierCashManager::getCashListAll($page, $page_size, $time_type, $time_start, $time_end, $status, $search);

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
        return $this->actionGetCash(1);
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

            if (($status != self::CASH_STATUS_DONE && $status != self::CASH_STATUS_FAIL)
                || ($status == self::CASH_STATUS_DONE && $real_money <= 0)
                || !$cash_id
            ) {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]
                ]);
            }

            $data = SupplierCashManager::doCashDeal($cash_id, $status, $reason, $real_money);

            if ($data) {
                return Json::encode([
                    'code' => 200,
                    'msg' => 'ok',
                    'data' => $data
                ]);
            }

            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
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

}
