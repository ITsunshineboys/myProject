<?php

namespace app\controllers;

use app\models\LaborCost;
use app\models\User;
use app\models\Worker;
use app\models\WorkerCraft;
use app\models\WorkerOrder;
use app\models\WorkerOrderItem;
use app\models\WorkerWorks;
use app\models\WorkerWorksReview;
use app\services\ExceptionHandleService;
use app\services\ModelService;
use app\services\StringService;
use yii\db\Exception;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;

class WorkerController extends Controller
{
    const STATUS_ALL = -1;
    const STAR_DEFAULT = 15;
    const VIEWS_PARENT=0;
    const STATUS_JDED=2;
    const SITE_STATUS=[2,3,4,5];
    const SITE_STATUS_WORKER=[0,4,5];
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
        $laborcost = Worker::getLaborByWorkerId($worker->id);
        if ($laborcost == null) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user_info = User::find()->where(['id' => $user])->one();
        if ($user_info == null) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $data = [];
        $data['aite_cube_no'] = $user_info->aite_cube_no;
        $data['worker_no'] = $user_info->aite_cube_no;
        $data['rank'] = $laborcost->rank;
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

        Worker::setSignature($user, $signature);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
        ]);
    }

    /**
     * 智管工地列表-工人
     * @return int|string
     */
    public function actionWorkerSiteOrderList()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;
        //智管工地 status (0,4,5)
        $status = (int)$request->get('status',self::STATUS_ALL);
        $page = (int)$request->get('page', 1);
        $page_size = (int)$request->get('size', ModelService::PAGE_SIZE_DEFAULT);
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

        $data = WorkerOrder::getWorkerOrderList($user, $status, $page, $page_size);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }
    /**
     * 智管工地详情--工人
     */
    public function actionWorkerSiteWorkerDetail(){
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }
        $code=1000;
        $order_no=\Yii::$app->request->get('order_no');
        if(!$order_no){
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=WorkerOrder::workersiteview($order_no,$user);
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    /**
     * 智管工地详情-用户
     * @return int|string
     */
    public function actionUserSiteWorkerDetail(){
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }
        $code=1000;
        $order_no=\Yii::$app->request->get('order_no');
        if(!$order_no){
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=WorkerOrder::usersiteview($order_no,$user);
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$data
        ]);
    }
    /**
     * 智管工地列表-用户
     * @return int|string
     */
    public function actionUserSiteOrderList(){
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;
        //智管工地 status (0,4,5)
        $status = (int)$request->get('status', self::STATUS_ALL);
        $page = (int)$request->get('page', 1);
        $page_size = (int)$request->get('size', ModelService::PAGE_SIZE_DEFAULT);
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

        $data = WorkerOrder::getUserOrderList($user, $status, $page, $page_size);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    /**
     * 工程订单列表-业主
     * @return int|string
     */
    public function actionUserWorkerOrderList()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;
        //工程订单-业主 status=(all 除了3)
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
     * 工程订单详情-业主
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
     * 工程订单列表-工人
     * @return int|string
     */
    public function actionWorkerWorkerOrderList(){
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }
        $request = \Yii::$app->request;
        //工程订单-工人 status=(2,5,6)
        $status = (int)$request->get('status', self::STATUS_ALL);
        $page = (int)$request->get('page', 1);
        $page_size = (int)$request->get('size', ModelService::PAGE_SIZE_DEFAULT);
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

        $data = WorkerOrder::getWorkerWorkerOrderList($user, $status, $page, $page_size);
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
}
    /**
     * 工程订单详情-工人
     * @return int|string
     */
    public function actionWorkerWorkerOrderDetail(){
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

        $data = WorkerOrder::getWorkerWorkerOrderDetail($order_id);
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
     * 得到订单图片分页
     *
     * @return int|string
     */
    public function actionGetOrderImg()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;

        $order_no = (int)$request->get('order_no', 0);

        $page_size = (int)$request->get('page_size', WorkerOrder::IMG_PAGE_SIZE_DEFAULT);

        $page = (int)$request->get('page', 1);

        $data = WorkerOrder::getOrderImg($order_no, $page_size, $page);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }


//    /**
//     * 得到订单的具体施工日期
//     *
//     * @return array|int|string
//     */
//    public function actionGetOrderDays()
//    {
//        $user = self::userIdentity();
//        if (!is_int($user)) {
//            return $user;
//        }
//
//        $request = \Yii::$app->request;
//
//        $order_id = (int)$request->get('order_id', 0);
//
//        if (!$order_id) {
//            $code = 1000;
//            return Json::encode([
//                'code' => $code,
//                'msg' => \Yii::$app->params['errorCodes'][$code]
//            ]);
//        }
//
//        $order_days = WorkerOrder::find()
//            ->where(['id' => $order_id, 'uid' => $user])
//            ->select('days')
//            ->one();
//
//        if (!$order_days) {
//            $code = 1000;
//            return Json::encode([
//                'code' => $code,
//                'msg' => \Yii::$app->params['errorCodes'][$code]
//            ]);
//        } elseif ($order_days->days) {
//            $order_days = explode(',', $order_days->days);
//        } else {
//            $order_days = [];
//        }
//
//
//        return Json::encode([
//            'code' => 200,
//            'msg' => 'ok',
//            'data' => $order_days
//        ]);
//    }

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
        $item_id = (int)$request->get('item_id',0);

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

            //TODO 传值的格式暂定 $post[order_id, items, new_amount, reason, days]
            //TODO 其中 items[   [id, craft_id, area], [id, area] ...]   work_days[day1, day2, day3, day...]

            $order_id = (int)$request->post('order_id', 0);
            $items = $request->post('items', '');
            $new_amount = (int)$request->post('new_amount', 0);
            $reason = trim($request->post('reason', ''));
            $need_time = (int)$request->post('need_time', '');
            $days = (int)$request->post('days', '');

            if (!$order_id
                || ($days && $need_time != count(explode(',', $days)))
            ) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]
                ]);
            }

            $query = WorkerOrder::find()
                ->where(['id' => $order_id]);

            $order_old = $query
                ->asArray()
                ->orderBy(['id' => SORT_DESC])
                ->one();

            if ($order_old == null) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]
                ]);
            }

            //得到之前的数据
            $data = $order_old;
            //分两种情况，
            //a. 没有修改过  新建一条数据         //b. 修改过的  修改第二条
            if ($query->count() == 1) {
                $order_new = new WorkerOrder();
                unset($data['id']);
            } else {
                $order_new = WorkerOrder::find()->where(['id' => $order_id])->orderBy(['id' => SORT_DESC])->one();
            }


            //alter amount data
            if ($new_amount) {
                if (!$reason) {
                    $code = 1000;
                    return Json::encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $data['amount'] = $new_amount * 100;
                $data['reason'] = $reason;
            }

            //alter work_days data  修改工作时间表  新加一条数据， 旧的改变is_old状态为1
            if ($need_time && $need_time != $order_old['need_time']) {
                $data['need_time'] = $need_time;
            }

            if ($days) {
                $data['days'] = $days;
                $days_arr = explode(',', $days);
                $data['start_time'] = strtotime($days_arr[0]);
                $data['end_time'] = strtotime($days_arr[count($days_arr)-1]);
            }

            $data['is_old'] = 0;
            $data['modify_time'] = time();

            $trans = \Yii::$app->db->beginTransaction();
            try {
                WorkerOrder::updateAll(['is_old' => 1], ['order_no' => $order_old['order_no']]);
                $order_new->setAttributes($data, false);
                $order_new->save(false);
                $trans->commit();
            } catch (Exception $e) {
                $trans->rollBack();
                $code = 1051;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]
                ]);
            }

            $order_id_new = (int)$order_new->id;
            $new_items = [];

            //worker_order_item表对应订单号的全部 数据查出来   $old_items
            $old_items = WorkerOrderItem::find()
                ->where(['worker_order_id' => $order_old['id']])
                ->asArray()
                ->all();

            if ($old_items) {
                foreach ($old_items as &$old_item) {
                    unset($old_item['id']);
                    $old_item['worker_order_id'] = $order_id_new;
                    $new_items[] = $old_item;
                }
            }

            $new_item_finals = [];

            //改变数据
            if ($items) {
                if (!is_array($items)) {
                    $code = 1000;
                    return Json::encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code]
                    ]);
                }

                $new_item_adds = [];
                $item_exists = [];

                //需要改动的内容
                foreach ($items as $item) {
                    if (!isset($item['id'])) {
                        $code = 1000;
                        return Json::encode([
                            'code' => $code,
                            'msg' => \Yii::$app->params['errorCodes'][$code]
                        ]);
                    }
                    //todo 还有其他字段需要加进来
                    foreach ($new_items as &$new_item) {
                        if ($item['id'] == $new_item['worker_item_id']) {
                            isset($item['craft_id']) && $new_item['worker_craft_id'] = $item['craft_id'];
                            isset($item['area']) && $new_item['area'] = $item['area'];
                            isset($item['status']) && $new_item['status'] = $item['status'];
                            isset($item['electricity']) && $new_item['electricity'] = $item['electricity'];
                            isset($item['count']) && $new_item['count'] = $item['count'];
                        }
                        $item_exists[] = $new_item['worker_item_id'];
                    }
                }

                //直接添加的内容
                foreach ($items as $item) {
                    if (!in_array($item['id'], $item_exists)) {
                        $new_item_add['worker_order_id'] = $order_id_new;
                        $new_item_add['worker_item_id'] = $item['id'];
                        isset($item['craft_id']) && $new_item_add['worker_craft_id'] = $item['craft_id'];
                        isset($item['area']) && $new_item_add['area'] = $item['area'];
                        isset($item['status']) && $new_item_add['status'] = $item['status'];
                        isset($item['electricity']) && $new_item_add['electricity'] = $item['electricity'];
                        isset($item['count']) && $new_item_add['count'] = $item['count'];
                        $new_item_adds[] = $new_item_add;
                    }
                }

                $new_item_finals = array_merge($new_items, $new_item_adds);

            }

            $worker_order_item = new WorkerOrderItem();
            foreach ($new_item_finals as $new_item_final) {
                $trans = \Yii::$app->db->beginTransaction();
                $new_worker_order_item = clone $worker_order_item;
                $new_worker_order_item->setAttributes($new_item_final, false);
                if (!$new_worker_order_item->save(false)) {
                    $trans->rollBack();
                }
                $trans->commit();
            }

            $code = 200;
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        }

        $code = 1050;
        return Json::encode([
            'code' => $code,
            'msg' => \Yii::$app->params['errorCodes'][$code]
        ]);
    }

    /**
     * 得到 订单历史记录
     *
     * @return int|string
     */
    public function actionGetOrderHistory()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;

        $order_no = (int)$request->get('order_no', 0);

        $code = 1000;

        $order = WorkerOrder::getOrderHistory($user, $order_no);

        if (!$order_no || !$order) {
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $order
        ]);
    }

    /**
     * 取消订单
     *
     * @return string
     */
    public function actionCancelOrder()
    {
        return self::changeOrderStatus(WorkerOrder::WORKER_ORDER_NO);
    }

    /**
     * 工人接单改变状态
     *
     * @return string
     */
    public function actionAcceptOrder()
    {
        return self::changeOrderStatus(WorkerOrder::WORKER_ORDER_READY);
    }

    /**
     * 工人申请开工
     *
     * @return string
     */
    public function actionReadyOrder()
    {
        return self::changeOrderStatus(WorkerOrder::WORKER_WORKS_AFTER);
    }

    /**
     * 同意开工
     *
     * @return string
     */
    public function actionBeginOrder()
    {
        return self::changeOrderStatus(WorkerOrder::WORKER_ORDER_NOT_BEGIN);
    }


    /**
     * 确定验收
     *
     * @return string
     */
    public function actionFinishOrder()
    {
        return self::changeOrderStatus(WorkerOrder::WORKER_ORDER_DONE);
    }

    /**
     * 改变订单状态
     *
     * @param $status
     * @return int|string
     */
    public function changeOrderStatus($status)
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;

        $order_no = (int)$request->get('order_no', 0);

        $code = 1000;

        if (!$order_no
            || !array_key_exists($status, WorkerOrder::USER_WORKER_ORDER_STATUS)
        ) {
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $order = WorkerOrder::find()
            ->where(['order_no' => $order_no, 'uid' => $user, 'is_old' => WorkerOrder::IS_NEW])
            ->one();
        if (!$order) {
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $worker_id = $order->worker_id;

        $trans = \Yii::$app->db->beginTransaction();

        if (WorkerOrder::updateAll(['status' => $status], ['order_no' => $order_no])) {

            if ($status == WorkerOrder::WORKER_ORDER_DONE) {

                $works = WorkerOrder::newWorkerWorks($worker_id, $order_no);

                if (!is_array($works)) {
                    return Json::encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code]
                    ]);
                }

                $works_id = $works[1];

                $works_detail = WorkerOrder::newWorkerWorksDetail($works_id);

                if ($works_detail != 200) {
                    $trans->rollBack();
                    return Json::encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code]
                    ]);
                }
            }

        } else {
            $trans->rollBack();
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $trans->commit();

        return Json::encode([
            'code' => 200,
            'msg' => 'ok'
        ]);
    }

    /**
     * 工人作品评论
     *
     * @return string
     */
    public function actionAddWorksReview()
    {
        $uid = self::userIdentity();

        if (!is_int($uid)) {
            return $uid;
        }
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $code = 1000;
            $works_id = (int)$request->post('works_id', 0);
            $star = (int)$request->post('star', self::STAR_DEFAULT);
            $review = trim($request->post('review', ''));

            $user = User::find()->where(['id' => $uid])->exists();
            $works = WorkerWorks::find()->where(['id' => $works_id])->exists();
            $role_id = User::find()->select('last_role_id_app')->where(['id'=>$uid])->one()->last_role_id_app;
            if (!$user
                || $role_id > 7
                || !$role_id
                || !$works
            ) {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]
                ]);
            }

            $works_review = new WorkerWorksReview();

            $works_review->uid = $uid;
            $works_review->role_id = $role_id;
            $works_review->works_id = $works_id;
            $works_review->star = $star;
            $works_review->review = $review;
            $works_review->pid=self::VIEWS_PARENT;
            if ($works_review->save(false)) {
                return Json::encode([
                    'code' => 200,
                    'msg' => 'ok'
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
    /**
     * 工人回复评论
     * @return int|string
     */
    public function actionWorkerReply(){
        $uid = self::userIdentity();

        if (!is_int($uid)) {
            return $uid;
        }
        $code=1000;
        $request=new Request();
        $view_id=(int)($request->post('view_id'));
        $works_id=(int)($request->post('works_id'));
        $review=trim($request->post('review',''));
        if(!$view_id || !$review ||!$works_id){
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=WorkerWorksReview::WorkerRelpy($uid,$view_id,$review,$works_id);
        return Json::encode([
            'code' => $code,
            'msg' => $code==200?'ok':\Yii::$app->params['errorCodes'][$code]
        ]);

    }

    /**
     * 获取工人所有作品评论+分页
     *
     */
    public function actionGetAllWorksReview()
    {
        $code=1000;
        $request=new Request();
        $worker_id=(int)trim($request->get('worker_id'));
        if(!$worker_id){
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $page = (int)$request->get('page', 1);
        $size = (int)$request->get('size', WorkerWorksReview::VIEW_SIZE);
        $data=WorkerWorksReview::getworkerallviews($worker_id,$page,$size);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    /**
     * 查询所有工人作品+分页
     * @return string
     */
    public function actionGetWorksByWorkerId()
    {
        $request = \Yii::$app->request;
        $code=1000;
        $worker_id = (int)$request->get('worker_id', 0);
        if(!$worker_id){
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $page = (int)$request->get('page', 1);
        $size = (int)$request->get('size', WorkerOrder::IMG_PAGE_SIZE_DEFAULT);

        $data = WorkerOrder::getWorksByWorkerIdAll($worker_id, $page, $size);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    /**
     * 工人某作品详情
     * @return string
     */
    public function actionGetWorksDetail()
    {
        $code=1000;
        $works_id=trim(\Yii::$app->request->get('works_id'));
        if(!$works_id){
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
            $data=WorkerWorks::GetWorksDetail($works_id);
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);

    }
    /**
     * 编辑工人作品详情
     */
    public function actionEditWorksView(){

    }

    /**
     * 工人排班日历
     *
     * @return string
     */
    public function actionGetWorkDaysByMonth()
    {
        //根据年月查出当前月的接单

        $request = \Yii::$app->request;

        $time = trim($request->get('month', ''));
        $worker_id = (int)$request->get('worker_id', 0);

        $start_time = $end_time = 0;

        if (!$time) {
            $time_type = 'month';
        } else {
            $time_type = 'custom';
            $yearMonth = $time;
            list($year, $month) = explode('-', $yearMonth);
            $start_time = date("Y-m-d H:i:s", mktime(0, 0, 0, $month, 1, $year));
            $end_time = date("Y-m-d H:i:s", mktime(23, 59, 59, $month, date('t'), $year));
        }

        $time_area = ModelService::timeDeal($time_type, $start_time, $end_time);

        $data = WorkerOrder::getWorkDaysByTimeArea($worker_id, $time_area);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    /**
     * 工人抢单
     * @return int|string
     */
    public function actionGrabsingle(){
        $uid = self::userIdentity();
        if (!is_int($uid)) {
            return $uid;
        }
        $code=1000;
        $order_id=(int)(\Yii::$app->request->get('order_id'));
        if(!$order_id){
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $worker_id=Worker::getWorkerByUid($uid)->id;
        $worker_order=WorkerOrder::find()
            ->where(['id'=>$order_id])
            ->one();

       $worker_order->worker_id=$worker_id;
       $worker_order->is_old=WorkerOrder::IS_OLD;
       $worker_order->status=self::STATUS_JDED;

       if(!$worker_order->update(false)){
           $code=500;
           return Json::encode([
               'code' => $code,
               'msg' => \Yii::$app->params['errorCodes'][$code]
           ]);
       }
       return Json::encode([
           'code'=>200,
           'msg'=>'ok',
           'data'=>[
               'order_id'=>$order_id
           ]
       ]);

    }
    public function actionGrabDetails(){


    }
}
