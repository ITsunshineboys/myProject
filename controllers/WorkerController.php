<?php

namespace app\controllers;

use app\models\Worker;
use app\models\WorkerItem;
use app\models\WorkerOrder;
use app\models\WorkerOrderItem;
use app\services\ExceptionHandleService;
use app\services\ModelService;
use yii\db\Transaction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;

class WorkerController extends Controller
{
    const STATUS_ALL = 4;

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
     * 判断用户是否登陆
     * @return int|string
     */
    private function userIdentity()
    {
        $user = \Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        return $user->getId();
    }

    /**
     * 工人资料1  头像，名字，是否有签名
     * @return int|string
     */
    public function actionGetWorker()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $worker = Worker::find()->where(['uid' => $user])->one();
        if ($worker == null) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data = [];
        $data['icon'] = $worker->icon;
        $data['nickname'] = $worker->nickname;
        $data['signature'] = $worker->signature ? '已设置' : '未设置';
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    /**
     * 得到用户签名
     * @return int|string
     */
    public function actionGetSignature()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $worker = Worker::getWorkerByUid($user);
        $signature = $worker->signature;
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $signature
        ]);
    }

    /**
     * 修改工人签名
     * @return int|string
     */
    public function actionSetSignature()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $signature = trim(\Yii::$app->request->get('signature', ''), '');

        $code = Worker::setSignature($user, $signature);

        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
        ]);
    }

    /**
     * 智管工地列表
     * @return int|string
     */
    public function actionWorkerOrderList()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;
        $status = (int)$request->get('status', self::STATUS_ALL);
        $page = (int)$request->get('page', 1);
        $page_size = (int)$request->get('page_size', ModelService::PAGE_SIZE_DEFAULT);
        if ($status
            && $status != self::STATUS_ALL
            && !array_key_exists($status, WorkerOrder::WORKER_ORDER_STATUS)
        ) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $data = WorkerOrder::getWorkerOrderList($user, $status, $page, $page_size);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    /**
     * 工程订单列表
     * @return int|string
     */
    public function actionUserWorkerOrderList()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;
        $status = (int)$request->get('status', self::STATUS_ALL);
        $page = (int)$request->get('page', 1);
        $page_size = (int)$request->get('page_size', ModelService::PAGE_SIZE_DEFAULT);
        if ($status
            && $status != self::STATUS_ALL
            && !array_key_exists($status, WorkerOrder::USER_WORKER_ORDER_STATUS)
        ) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $data = WorkerOrder::getUserWorkerOrderList($user, $status, $page, $page_size);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    /**
     * 工程订单详情
     * @return string
     */
    public function actionUserWorkerOrderDetail()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;

        $order_id = (int)$request->get('order_id', 0);
        if (!$order_id) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $data = WorkerOrder::getUserWorkerOrderDetail($order_id);
        if (is_int($data)) {
            return Json::encode([
                'code' => $data,
                'msg' => \Yii::$app->params['errorCodes'][$data]
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    /**
     * 订单条目详情
     * @return string
     */
    public function actionOrderItemDetail()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;

        $order_id = (int)$request->get('order_id', 0);
        $item_id = (int)$request->get('item_id', 0);

        if (!$item_id || !$order_id) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $data = WorkerOrder::getOrderItemDetail($order_id, $item_id);
        if (is_int($data)) {
            return Json::encode([
                'code' => $data,
                'msg' => \Yii::$app->params['errorCodes'][$data]
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    /**
     * 工人修改订单
     * @return int|string
     */
    public function actionChangeWorkerOrder()
    {
        if (\Yii::$app->request->isPost) {
            $user = self::userIdentity();
            if (!is_int($user)) {
                return $user;
            }
            $request = \Yii::$app->request;
            $post = \Yii::$app->request->post();
            $order_no = (int)$request->get('order_no', 0);
            $order_id = (int)$request->get('order_id', 0);

            if (!$order_no
                || !$order_id
                || !isset($post['homeinfos'])
            ) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]
                ]);
            }


            $order_old = WorkerOrder::find()
                ->where(['order_no' => $order_no, 'id' => $order_id])
                ->asArray()
                ->one();


            if ($order_old == null) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]
                ]);
            }

            $order_new = new WorkerOrder();

            //得到之前的数据
            $data = $order_old;

            unset($data['id']);

            //改变数据

            $home_info = $post['homeinfos'];

            $need_time = FindworkerController::getOrderNeedTime($home_info);
            $home_info = WorkerOrderItem::addMudorderitem($home_info, $need_time);

            $order_old['is_old'] = 1;
            $data['is_old'] = 0;

            $trans = \Yii::$app->db->beginTransaction();
            $order_old->update();
        }

        $code = 1050;
        return Json::encode([
            'code' => $code,
            'msg' => \Yii::$app->params['errorCodes'][$code]
        ]);
    }
}
