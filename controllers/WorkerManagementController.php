<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/27 0027
 * Time: 下午 15:14
 */

namespace app\controllers;

use app\models\User;
use app\models\Worker;
use app\models\WorkerOrder;
use app\models\WorkerRank;
use app\models\WorkerType;
use app\services\ExceptionHandleService;
use app\services\StringService;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;

class WorkerManagementController extends Controller
{

    const ORDER_STATUS_ACCOMPLISH = 5;
    const DEFAULT_PAGE = 1;
    const DEFAULT_SIZE = 12;

    /**
     * Actions accessed by logged-in users
     */
    const ACCESS_LOGGED_IN_USER = [
        'homepage-list',
        'worker-type-list',
        'worker-type-add',
        'worker-type-edit',
        'worker-list',
        'worker-phone',
        'worker-add',
        'worker-order-list' ,
        'worker-order-status',
        'worker-order-status-list'
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
     * 找工人首页 页面
     */
    public function actionHomepageList()
    {
        // 工人总数
        $worker_count = Worker::find()->count('id');
        //工人订单
        $worker_order = WorkerOrder::find()->groupBy('order_no')->count('id');
        //工人总金额
        $worker_price = WorkerOrder::find()->select('amount')->where(['status' => self::ORDER_STATUS_ACCOMPLISH])->all();
        $cost         = 0;
        foreach ($worker_price as $one_cost) {
            $cost += $one_cost['amount'];
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => [
                'worker_count' => $worker_count,
                'worker_order' => $worker_order,
                'worker_cost' => sprintf("%.2f", (float)$cost * 0.01),
            ]
        ]);
    }

    /**
     * 工种类型列表
     * @return string
     */
    public function actionWorkerTypeList()
    {
        $id     = (int)trim(\Yii::$app->request->get('id', ''));
        $status = trim(\Yii::$app->request->get('status', ''));
        $del_id = (int)trim(\Yii::$app->request->get('del_id', ''));

        //  状态修改
        if ($status != null) {
            $worker_type         = (new WorkerType())->findOne(['id' => $id]);
            $worker_type->status = $status;
            if (!$worker_type->save()) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }

        //  删除功能
        if ($del_id != null) {
            $where = 'worker_type.id = ' . $del_id;
            $del_  = WorkerType::findByListOne($where);
            if ($del_ != null) {
                $code = 1069;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            } else {
                (new WorkerType())->deleteAll(['id' => $del_id]);
                (new WorkerRank())->deleteAll(['worker_type_id' => $del_id]);
            }
        }
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => WorkerType::findByList(),
        ]);
    }

    /**
     * 工种类型添加
     * @return string
     */
    public function actionWorkerTypeAdd()
    {
        $worker_type                 = new  WorkerType();
        $worker_type->worker_name    = \Yii::$app->request->post('worker', '');
        $worker_type->establish_time = time();
        $worker_type->status         = WorkerType::PARENT;
        if (!$worker_type->save()) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $id   = $worker_type->attributes['id'];
        $post = \Yii::$app->request->post();
        foreach ($post['rank'] as $one_post) {
            $worker_rank = (new WorkerRank())->ByInsert($id, $one_post['rank'], $one_post['min'], $one_post['max']);
        }
        if (!$worker_rank) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
        ]);

    }

    /**
     * 工种类型修改
     * @return string
     */
    public function actionWorkerTypeEdit()
    {
        $post = \Yii::$app->request->post();
        //  修改工种类型
        if (isset($post['edit'])) {
            $worker              = WorkerType::findOne(['id' => $post['edit']['id']]);
            $worker->worker_name = $post['edit']['worker_name'];
            if (!$worker->save()) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
            foreach ($post['edit']['level'] as $one_post) {
                $rank = (new WorkerRank())->ByUpdate($one_post);
            }
            if (!$rank) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }

        //  添加工种类型
        if (isset($post['add'])) {
            $worker_type                 = new  WorkerType();
            $worker_type->worker_name    = $post['add']['worker'];
            $worker_type->establish_time = time();
            $worker_type->status         = WorkerType::PARENT;
            if (!$worker_type->save()) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
            $id = $worker_type->attributes['id'];
            foreach ($post['add']['rank'] as $one_post) {
                $worker_rank = (new WorkerRank())->ByInsert($id, $one_post['rank'], $one_post['min'], $one_post['max']);
            }
            if (!$worker_rank) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
        ]);
    }

    /**
     *  添加工人列表
     * @return string
     */
    public function actionAddWorkerList()
    {
        $worker_     = trim(\Yii::$app->request->get('id', ''));
        $worker_type = WorkerType::find()
            ->select('id,worker_name')
            ->where(['and', ['status' => 1], ['pid' => 0]])
            ->asArray()
            ->all();

        if ($worker_ != null) {
            $worker_rank = WorkerRank::find()
                ->select('id,rank_name')
                ->where(['worker_type_id' => $worker_])
                ->asArray()
                ->all();

            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
                'data' => [
                    'worker' => $worker_type,
                    'level' => $worker_rank,
                ],
            ]);
        }


        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => [
                'worker' => $worker_type,
            ],
        ]);
    }

    /**
     * 添加工人 手机号验证
     * @return string
     */
    public function actionAddWorkerPhone()
    {
        $phone = (int)trim(\Yii::$app->request->post('phone', ''));
        //  手机号是否正确
        if (!preg_match('/^[1][3,5,7,8]\d{9}$/', $phone)) {
            $code = 1070;
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]

            ]);
        }

        // 该用户是否注册
        $user = User::find()->select('id')->where(['mobile' => $phone])->asArray()->one();
        if ($user == null) {
            $code = 1010;
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]

            ]);
        }

        // 该手机号是否注册工人
        $worker = Worker::find()->where(['uid' => $user])->one();
        if ($worker != null) {
            $code = 1071;
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]

            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => User::find()
                ->select(['identity_card_front_image,identity_card_back_image,legal_person,identity_no'])
                ->where(['mobile' => $phone])
                ->one(),
        ]);

    }

    /**
     * 添加工人 保存页面
     * @return string
     */
    public function actionAddWorkerAdd()
    {
        // 身份证号码验证
        $identity_no = trim(\Yii::$app->request->post('identity_no', ''));
        if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $identity_no)) {
            $code = 1072;
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]

            ]);
        }

        // 检测是否注册
        $identity_no_ = User::find()->where(['identity_no' => $identity_no])->one();
        if ($identity_no_) {
            $code = 1073;
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]

            ]);
        }


        $transaction = \Yii::$app->db->beginTransaction();
        $uid         = User::find()
            ->where(['mobile' => (int)trim(\Yii::$app->request->post('phone', ''))])
            ->one();
        try {
            $worker                 = new Worker();
            $worker->uid            = $uid->id;
            $worker->worker_type_id = (int)trim(\Yii::$app->request->post('worker_type_id', ''));
            $worker->province_code  = (int)trim(\Yii::$app->request->post('province', ''));
            $worker->city_code      = (int)trim(\Yii::$app->request->post('city', ''));
            $worker->level          = (int)trim(\Yii::$app->request->post('worker_rank_id', ''));
            $worker->create_time    = time();

            if (!$worker->save()) {
                $transaction->rollBack();
                $code = 1000;
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]

                ]);
            }

            $uid->legal_person              = trim(\Yii::$app->request->post('legal_person', ''));
            $uid->identity_no               = $identity_no;
            $uid->identity_card_front_image = trim(\Yii::$app->request->post('front_image', ''));
            $uid->identity_card_back_image  = trim(\Yii::$app->request->post('back_image', ''));

            if (!$uid->save()) {
                $transaction->rollBack();
                $code = 1000;
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]
                ]);
            }

            $transaction->commit();
            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
            ]);


        } catch (\Exception $e) {
            $transaction->rollBack();
            $code = 1000;
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /**
     * 工程订单列表
     */
    public function actionWorkerOrderList()
    {

        $page     = (int)trim(\Yii::$app->request->post('page', self::DEFAULT_PAGE));
        $size     = (int)trim(\Yii::$app->request->post('size', self::DEFAULT_SIZE));

        $status   = (int)trim(\Yii::$app->request->post('status', ''));
        $timeType = trim(\Yii::$app->request->post('time', ''));
        $worker   = trim(\Yii::$app->request->post('worker', ''));
        $other    = trim(\Yii::$app->request->post('other', ''));

        switch ($status || $timeType  || $worker || $other)
        {
            case !$status && !$timeType  && !$worker  && !$other:
                $where = "is_old = 0";
                break;
            case $status && !$timeType && !$worker  && !$other:
                $where = 'worker_order.status = ' . $status .' and is_old = 0';
                break;
            case !$status && $timeType && !$worker  && !$other:
                $times = StringService::startEndDate($timeType);
                $where = "worker_order.create_time >=".strtotime($times['0'])." and worker_order.create_time <= ".strtotime($times['1']).' and is_old = 0';
                break;
            case $status && $timeType && !$worker  && !$other:
                $times = StringService::startEndDate($timeType);
                $where = "worker_order.create_time >=".strtotime($times['0'])." and worker_order.create_time <=".strtotime($times['1']). " and worker_order.status = ".$status.'and is_old = 0';
                break;
            case !$status && $timeType = 'custom' && !$worker  && !$other:
                $min_time = strtotime((int)trim(\Yii::$app->request->post('min_time', '')));
                $max_time = strtotime((int)trim(\Yii::$app->request->post('max_time', '')));
                $where = "worker_order.create_time >=".$min_time." and worker_order.create_time <=".$max_time.' and is_old = 0';
                break;
            case $status && $timeType = 'custom' && !$worker  && !$other:
                $min_time = strtotime((int)trim(\Yii::$app->request->post('min_time', '')));
                $max_time = strtotime((int)trim(\Yii::$app->request->post('max_time', '')));
                $where = "worker_order.create_time >=".$min_time." and worker_order.create_time <=".$max_time." and worker_order.status = ".$status.' and is_old = 0';
                break;
            case !$status && !$timeType && $worker  && !$other:
                $where = "worker_order.worker_type_id = " .$worker.' and is_old = 0';
                break;
            case $status && !$timeType && $worker  && !$other:
                $where = "worker_order.worker_type_id = " .$worker."worker_order.status = ".$status.' and is_old = 0';
                break;
            case !$status && !$timeType  && !$worker  && $other:
                $where = " worker_order.con_tel like '%{$other}%'  or worker_order.con_people like '%{$other}%' or worker_order.order_no like '%{$other}%  or user.aite_cube_no like '%{$other}%' and is_old = 0'";
                break;
            case $status && !$timeType && !$worker  && $other:
                $where = " worker_order.con_tel like '%{$other}%'  or worker_order.con_people like '%{$other}%' or worker_order.order_no like '%{$other}%  or user.aite_cube_no like '%{$other}%' and worker_order.status = " .$status.' and is_old = 0';
                break;

        }

        $worker_order = WorkerOrder::orderList($where, $size, $page);
        return Json::encode([
            'list' => $worker_order
        ]);
    }

    /**
     * 工程订单状态修改列表
     * @return string
     */
    public function actionWorkerOrderStatusList()
    {
        $order_no = (int)trim(\Yii::$app->request->get('order_no'));
        $where = "order_no = ".$order_no ." and is_old = 0";
        $worker = WorkerOrder::find()->select('amount')->where($where)->One();
        return Json::encode([
            'code' => 200,
            'msg'  => 'OK',
            'data' => sprintf('%.2f',$worker['amount'] * 0.01)
        ]);
    }

    /**
     * 工程订单状态和备注
     * @return string
     */
    public function actionWorkerOrderStatus()
    {
        $amount = trim(\Yii::$app->request->post('amount',''));
        //  取消订单
        if ($amount){
            $user_price = trim(\Yii::$app->request->post('user_price',''));
            $worker_price = trim(\Yii::$app->request->post('worker_price',''));

            if (($user_price + $worker_price) > $amount) {
                return Json::encode([
                   'code' => 1050,
                   'msg' => '退还业主金额和工人金额之和超过了订单金额',
                ]);
            }

            $tr = \Yii::$app->db->beginTransaction();
            try{
                $id = (int)trim(\Yii::$app->request->post('id',''));

                if ($user_price && $worker_price){
                    $worker_order = WorkerOrder::findOne(['id'=>$id]);
                    $user = User::findOne(['id'=>$worker_order['uid']]);
                    $user->balance = $user['balance'] + $user_price;
                    if (!$user->save()){
                        $tr->rollBack();
                        $code = 1000;
                        return json_encode([
                            'code' => $code,
                            'msg' => \Yii::$app->params['errorCodes'][$code]

                        ]);
                    }

                    $worker = Worker::findOne(['id'=>$worker_order['worker_id']]);
                    $worker_info = User::findOne(['id'=>$worker['uid']]);
                    $worker_info->balance = $worker_info['balance'] + $worker_price;
                    if (!$worker_info->save()){
                        $tr->rollBack();
                        $code = 1000;
                        return json_encode([
                            'code' => $code,
                            'msg' => \Yii::$app->params['errorCodes'][$code]

                        ]);
                    }

                    $edit_status = WorkerOrder::findOne(['id'=>$id]);
                    $edit_status->amount = $user_price + $worker_price;
                    $edit_status->status =  WorkerOrder::WORKER_ORDER_NO;
                    if (!$edit_status->save()){
                        $tr->rollBack();
                        $code = 1000;
                        return json_encode([
                            'code' => $code,
                            'msg' => \Yii::$app->params['errorCodes'][$code]

                        ]);
                    }
                }

                $tr->commit();
                return Json::encode([
                    'code' => 200,
                    'msg' => 'ok',
                ]);
            }catch (Exception $e) {
                $tr->rollBack();
            }

        }

        //  备注消息
        $comment_id = (int)trim(\Yii::$app->request->post('comment_id',''));
        if ($comment_id){
            $edit_status = WorkerOrder::findOne(['id'=>$comment_id]);
            $edit_status->describe = trim(\Yii::$app->request->post('comment',''));
            if (!$edit_status->save()){
                $code = 1000;
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
    }

    /**
     * 工程订单详情列表
     * @return string
     */
    public function actionWorkerOrderDetails()
    {
        $id = (int)trim(\Yii::$app->request->get('id',''));
        $worker_order = WorkerOrder::MangeOrderView($id);
        return Json::encode($worker_order);
    }

    /**
     * 工人列表 列表
     * @return string
     */
    public function actionWorkerList()
    {
        $code = (int)trim(\Yii::$app->request->get('code',''));
        $size = (int)trim(\Yii::$app->request->get('size',self::DEFAULT_SIZE));
        $page = (int)trim(\Yii::$app->request->get('page',self::DEFAULT_PAGE));
        $status   = (int)trim(\Yii::$app->request->post('status', ''));
        $timeType = trim(\Yii::$app->request->post('time', ''));
        $worker   = trim(\Yii::$app->request->post('worker', ''));
        $other    = trim(\Yii::$app->request->post('other', ''));


        switch ($code || $status || $timeType || $worker || $other){
            case $code && !$status && !$timeType && !$worker && !$other:
                $where = "worker.city_code = ".$code;
                break;
            case !$code && $status && !$timeType && !$worker && !$other:
                $where = "worker.status = ".$status;
                break;
            case !$code && !$status && $timeType && !$worker && !$other:
                $times = StringService::startEndDate($timeType);
                $where = "worker.create_time >= ".strtotime($times['0']) . "and worker.create_time <=".strtotime($times['1']);
                break;
            case !$code && !$status && $timeType == 'custom' && !$worker && !$other:
                $min_time = strtotime((int)trim(\Yii::$app->request->post('min_time', '')));
                $max_time = strtotime((int)trim(\Yii::$app->request->post('max_time', '')));
                $where = "worker.create_time >= ".$min_time . "and worker.create_time <=".$max_time;
                break;
            case !$code && !$status && !$timeType && $worker && !$other:
                $where = "worker_type.worker_name = ".$worker;
                break;
            case !$code && !$status && !$timeType && !$worker && $other:
                $where = " user.mobile like '%{$other}%'  or user.aite_cube like '%{$other}%' or user.legal_person like '%{$other}%'";
                break;
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'list' => Worker::findByCode($where,$size,$page),
        ]);
    }

    /**
     * 工人列表 状态和备注修改
     * @return string
     */
    public function actionWorkerStatus()
    {
        $id = (int)trim(\Yii::$app->request->post('id',''));

        $worker = Worker::findOne(['id'=>$id]);

        $status = (int)trim(\Yii::$app->request->post('status',''));
        $comment = (int)trim(\Yii::$app->request->post('comment',''));
        if ($status) {
            $worker->status = $status;
            if (!$worker->save()){
                $code = 1000;
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

        if ($comment) {
            $worker->comment = $comment;
            if (!$worker->save()){
                $code = 1000;
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
    }

    public function actionWorkerDetails()
    {
        $id = (int)trim(\Yii::$app->request->get('id',''));
        $status = trim(\Yii::$app->request->get('status',''));
        switch ($status){
            case $status == 'basic':     // 基本信息
                $where = "worker.id = ".$id." and worker.status = 1 and user_role.review_status = 2";
                $select = "worker.icon,worker.native_place,worker.status,user.mobile,user.aite_cube_no,user.username,user.create_time,user_role.review_time";
                $worker = Worker::basicMessage($select,$where);
                break;
            case $status == 'role':    // 角色信息
                $where = "";
                $select = "";
                break;
            case $status == 'production':    // 作品信息
                break;
            case $status == 'engineering':    // 工程订单
                break;
            case $status == 'construction_site':    // 工地信息
                break;

        }
        var_dump($worker);exit;
    }
}