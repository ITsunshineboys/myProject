<?php

namespace app\controllers;

use app\models\ContactForm;
use app\models\LoginForm;
use app\models\User;
use app\models\Role;
use app\models\UserRole;
use app\services\FileService;
use app\services\ExceptionHandleService;
use app\services\StringService;
use app\services\SmValidationService;
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

    public function actionLogina()
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
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $postData = Yii::$app->request->post();
        $code = 1000;
        if (!$postData) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $modelName = StringService::classBasename(LoginForm::className());
        if (!isset($postData[$modelName])) {
            $postData = [
                $modelName => $postData,
            ];
        }

        $model = new LoginForm;
        if ($model->load($postData) && $model->login()) {
            $user = Yii::$app->user->identity;
            $user->login_time = time();
            if (!$user->save()) {
                $code = 500;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            return Json::encode([
                'code' => 200,
                'msg' => '登录成功',
            ]);
        }

        $code = 1001;
        return Json::encode([
            'code' => $code,
            'msg' => Yii::$app->params['errorCodes'][$code],
        ]);
    }

    /**
     * Register action.
     *
     * @return string
     */
    public function actionRegister()
    {
        $postData = Yii::$app->request->post();
        $code = 1000;

        if (!$postData || empty($postData['mobile'])
            || strlen(($postData['password'])) < User::PASSWORD_MIN_LEN
            || strlen(($postData['password'])) > User::PASSWORD_MAX_LEN
        ) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        // todo: validation code check

        $user = new User;
        $user->attributes = $postData;
        $user->password = Yii::$app->security->generatePasswordHash($user->password);
        $user->create_time = $user->login_time = time();
        $user->login_role_id = Yii::$app->params['owner_role_id'];

        if (!$user->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        $code = 500;
        try {
            if (!$user->save()) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            $user->aite_cube_no = $user->id + Yii::$app->params['offset_aite_cube_no'];
            if (!$user->save()) {
                $transaction->rollBack();

                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            $userRole = new UserRole;
            $userRole->user_id = $user->id;
            $userRole->role_id = Yii::$app->params['owner_role_id']; // owner
            if (!$userRole->save()) {
                $transaction->rollBack();

                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            $transaction->commit();

            return Json::encode([
                'code' => 200,
                'msg' => '注册成功',
            ]);
        } catch (\Exception $e) {
            $transaction->rollBack();
        }

        return Json::encode([
            'code' => $code,
            'msg' => Yii::$app->params['errorCodes'][$code],
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

        return Json::encode([
            'code' => 200,
            'msg' => '登出成功',
        ]);
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
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'roles' => Role::appRoles(),
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

        $modelName = StringService::classBasename(LoginForm::className());
        if (!isset($postData[$modelName])) {
            $postData = [
                $modelName => $postData,
            ];
        }

        $model = new LoginForm;
        if ($model->load($postData) && $model->login()) {
            return Json::encode([
                'code' => 200,
                'msg' => '登录成功',
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

    /**
     * Forget password action.
     *
     * @return string
     */
    public function actionForgetPassword()
    {
        $postData = Yii::$app->request->post();
        $code = 1000;

        if (!$postData || empty($postData['mobile'])
            || strlen(($postData['new_password'])) < User::PASSWORD_MIN_LEN
            || strlen(($postData['new_password'])) > User::PASSWORD_MAX_LEN
        ) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        // todo: validation code check

        $user = User::find()->where(['mobile' => $postData['mobile']])->one();
        if (!$user) {
            $code = 403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $user->attributes = $postData;
        $user->password = Yii::$app->security->generatePasswordHash($user->password);

        if (!$user->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $user->password = Yii::$app->security->generatePasswordHash($postData['new_password']);
        if (!$user->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!$user->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => '重设密码成功',
        ]);
    }

    public function actionValidationCode()
    {
        $getData = Yii::$app->request->get();
        $postData = Yii::$app->request->post();
        $code = 1000;

        if (empty($getData['type']) || empty($postData['mobile'])) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        try {
            new SmValidationService($getData['type'], $postData['mobile']);
        } catch (\Exception $e) {}

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
        ]);
    }
}