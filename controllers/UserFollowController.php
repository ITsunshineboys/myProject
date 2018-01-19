<?php

namespace app\controllers;


use app\models\User;
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
                'code' => $code,
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
        $user = \Yii::$app->user->identity;
        if (!$user)
        {
            $code = 403;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = \Yii::$app->request;
        $page = (int)$request->get('page', 1);
        $page_size = (int)$request->get('page_size', ModelService::PAGE_SIZE_DEFAULT);
        $role_id = (int)$request->get('role_id', \Yii::$app->params['supplierRoleId']);
        if (!$role_id || !in_array($role_id, array_keys(self::ALLOWED_ROLE_ID)))       {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
        $data = UserFollow::getFollowList($user, $role_id, $page, $page_size);
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public function actionToggleStatus()
    {
        $user = \Yii::$app->user->identity;
        if (!$user)
        {
            $code = 403;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $request = \Yii::$app->request;
        $id = (int)$request->get('id', '');
        if (!$id) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code = UserFollow::toggleStatus($id);

        return Json::encode
        ([
            'code' => $code,
            'msg' => 200 == $code ? 'OK' : \Yii::$app->params['errorCodes'][$code]
        ]);
    }


    /**
     * @return string
     * @throws \yii\db\Exception
     */
    public  function  actionUserFollowShop()
    {
        $user = \Yii::$app->user->identity;
        $user=User::find()->where('mobile=18108088021')->one();
        var_dump($user);die;
//        if (!$user)
//        {
//            $code=403;
//            return Json::encode([
//                'code' => $code,
//                'msg' => 200 == $code ? 'OK' : \Yii::$app->params['errorCodes'][$code]
//            ]);
//        }

        $supplier_id=\Yii::$app->request->post('supplier_id');
        $status=\Yii::$app->request->post('status',0);
        if (!$supplier_id)
        {
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => 200 == $code ? 'OK' : \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=UserFollow::UserFlowShop($user,$supplier_id,$status);
        return Json::encode([
            'code' => $code,
            'msg' => 200 == $code ? 'OK' : \Yii::$app->params['errorCodes'][$code]
        ]);
    }


}
