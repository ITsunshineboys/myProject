<?php

namespace app\controllers;

use app\models\ContactForm;
use app\models\LoginForm;
use app\models\User;
use app\models\Role;
use app\services\FileService;
use app\services\ExceptionHandleService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\helpers\Json;

class SiteController extends Controller
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
                'only' => ['logout', 'roles'],
                'rules' => [
                    [
                        'actions' => ['logout', 'roles'],
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // todo: if api login, return json data
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        $userIdentity = Yii::$app->user->getIdentity();
        if ($userIdentity) {
            $cache = Yii::$app->cache;
            $keyPrefix = User::CACHE_PREFIX;
            $cache->delete($keyPrefix . $userIdentity->id);
        }

        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionDownload()
    {
        $filepath = Yii::$app->request->get('filepath');
        $fileService = new FileService($filepath);
        $fileService->download();
    }

    public function actionShow()
    {
        $filepath = Yii::$app->request->get('filepath');
        $fileService = new FileService($filepath);
        $fileService->show();
    }

    public function actionRoles()
    {
        $key = Role::CACHE_KEY_APP;
        $cache = Yii::$app->cache;
        $roles = $cache->get($key);
        if (!$roles) {
            $roles = Role::find()->where('id > 1')->asArray()->all();
            if ($roles) {
                $cache->set($key, $roles);
            }
        }
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'roles' => $roles,
            ],
        ]);
    }

    public function actionAllRoles()
    {
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'roles' => Role::allRoles(),
            ],
        ]);
    }

    public function actionAdminLogin()
    {
        $postData = Yii::$app->request->post();
        $code = 1000;
        if (!$postData) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $role = Role::findOne($postData['role_id']);
        if (!$role) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $modelName = 'LoginForm';
        if (!isset($postData[$modelName])) {
            $postData = [
                $modelName => $postData,
            ];
        }

        $model = new LoginForm;
        if ($model->load($postData) && $model->login()) {
            return Json::encode([
                'code' => 200,
                'msg' => 'OK',
                'data' => [
                    'toUrl' => Yii::$app->request->hostInfo . '/admin/' . $role->admin_module,
                ],
            ]);
        }

        $code = 1001;
        return Json::encode([
            'code' => $code,
            'msg' => Yii::$app->params['errorCodes'][$code],
        ]);
    }
}