<?php

namespace app\controllers;


use app\models\DecorationCompany;
use app\models\Designer;
use app\models\Supplier;
use app\models\UserFollow;
use app\services\ExceptionHandleService;
use app\services\ModelService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;

class UserFollowController extends Controller
{
    const ALLOWED_ROLE_ID = [
        3 => '设计师',
        5 => '装修公司',
        6 => '店铺'
    ];
    /**
     * Actions accessed by logged-in users
     */
    const ACCESS_LOGGED_IN_USER = [
        'follow-list',
        'toggle-status'
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

    /**
     * get follow list
     * @return int|string
     */
    public function actionFollowList()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;
        $page = (int)$request->get('page', 1);
        $page_size = (int)$request->get('page_size', ModelService::PAGE_SIZE_DEFAULT);
        $role_id = (int)$request->get('role_id', '');

        if (!$role_id || !in_array($role_id, array_keys(self::ALLOWED_ROLE_ID))) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }

        $data = (new UserFollow())->getFollowList($user, $role_id, $page, $page_size);
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    /**
     * toggle status of follow
     * @return int|string
     */
    public function actionToggleStatus()
    {
        $user = self::userIdentity();
        if (!is_int($user)) {
            return $user;
        }

        $request = \Yii::$app->request;
        $id = (int)$request->get('id', '');
        if (!$id) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }

        $code = (new UserFollow())->toggleStatus($id);

        return Json::encode([
            'code' => $code,
            'msg' => 200 == $code ? 'OK' : \Yii::$app->params['errorCodes'][$code]
        ]);
    }

    //删除所有关注
    public function actionDeleteFollow()
    {
        if (UserFollow::deleteAll()){
            echo 'ok';
        }
    }

    //全部关注
    public function actionAddFollow()
    {
        $supplier_ids = Supplier::find()->select(['id'])->asArray()->all();
        foreach ($supplier_ids as $supplier_id) {
            $ids[] = $supplier_id['id'];
            $user_follow = new UserFollow();
            $user_follow->role_id = 6;
            $user_follow->user_id = 1;
            $user_follow->follow_id = $supplier_id['id'];
            $user_follow->save(false);
            $supplier = Supplier::find()->where(['id'=> $supplier_id['id']])->one();
            $supplier->follower_number = 1;
            $supplier->save(false);
        }
        //如果没有关注 add follow ,follower_number+1
        //
        $supplier_ids = Designer::find()->select(['id'])->asArray()->all();
        foreach ($supplier_ids as $supplier_id) {
            $ids[] = $supplier_id['id'];
            $user_follow = new UserFollow();
            $user_follow->role_id = 3;
            $user_follow->user_id = 1;
            $user_follow->follow_id = $supplier_id['id'];
            $user_follow->save(false);
            $supplier = Designer::find()->where(['id'=> $supplier_id['id']])->one();
            $supplier->follower_number = 1;
            $supplier->save(false);
        }

        $supplier_ids = DecorationCompany::find()->select(['id'])->asArray()->all();
        foreach ($supplier_ids as $supplier_id) {
            $ids[] = $supplier_id['id'];
            $user_follow = new UserFollow();
            $user_follow->role_id = 5;
            $user_follow->user_id = 1;
            $user_follow->follow_id = $supplier_id['id'];
            $user_follow->save(false);
            $supplier = DecorationCompany::find()->where(['id'=> $supplier_id['id']])->one();
            $supplier->follower_number = 1;
            $supplier->save(false);
        }

        echo "ok";
    }
}
