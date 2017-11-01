<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/27 0027
 * Time: 下午 15:14
 */

namespace app\controllers;

use app\models\Worker;
use app\models\WorkerOrder;
use app\models\WorkerType;
use app\models\workType;
use app\services\ExceptionHandleService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;

class WorkerManagementController extends Controller
{
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
//        $worker_price = WorkerOrder::
        var_dump($worker_order);exit;
    }

    /**
     * 工种类型列表
     * @return string
     */
    public function actionWorkerTypeList()
    {
        $id = (int)trim(\Yii::$app->request->get('id',''));
        $status = trim(\Yii::$app->request->get('status',''));
        $del_id = (int)trim(\Yii::$app->request->get('del_id',''));

        //  状态修改
        if ($status != null){
            $worker_type = (new WorkerType())->findOne(['id'=>$id]);
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
        if ($del_id != null){
            $where = 'worker_type.id = '.$del_id;
            $del_ = WorkerType::findByListOne($where);
            if ($del_ != null){
                $code = 1069;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            } else {
                (new WorkerType())->deleteAll(['id'=>$del_id]);
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
        $post = \Yii::$app->request->post();
        foreach ($post['level'] as $one_post)
        {
            $worker_type = (new WorkerType())->ByInsert($one_post);
        }
        if (!$worker_type)
        {
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

    /**
     * 工种类型修改
     * @return string
     */
    public function actionWorkerTypeEdit()
    {
        $post = \Yii::$app->request->post();
        //  修改工种类型
        if (isset($post['edit'])){
            foreach ($post['edit'] as $one_post){
                (new WorkerType())->ByUpdate($one_post);
            }
        }

        //  添加工种类型
        if (isset($post['add'])){
            foreach ($post['add'] as $one_post){
                (new WorkerType())->ByInsert($one_post);
            }
        }

        return Json::encode([
           'code' => 200,
           'msg' => 'OK',
        ]);
    }

    public function actionWorkerAdd()
    {

    }

}