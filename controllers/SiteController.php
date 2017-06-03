<?php

namespace app\controllers;

use app\models\ContactForm;
use app\models\LoginForm;
use app\models\User;
use app\models\Role;
use app\models\UserRole;
use app\services\BasisDecorationService;
use app\services\FileService;
use app\services\ExceptionHandleService;
use app\services\StringService;
use app\services\SmValidationService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ServerErrorHttpException;

class SiteController extends Controller
{
    /**
     * Actions accessed by logged-in users
     */
    const ACCESS_LOGGED_IN_USER = [
        'logout',
        'roles',
        'reset-password',
        'roles-status',
        'time-types',
        'upload',
        'upload-delete',
        'review-statuses',
        'check-access-admin'
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
            $user->login_role_id = Yii::$app->params['ownerRoleId'];
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

        if (empty($postData['mobile'])
            || empty($postData['validation_code'])
            || empty($postData['password'])
            || strlen(($postData['password'])) < User::PASSWORD_MIN_LEN
            || strlen(($postData['password'])) > User::PASSWORD_MAX_LEN
        ) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $user = new User;
        $user->attributes = $postData;
        $user->password = Yii::$app->security->generatePasswordHash($user->password);
        $user->create_time = $user->login_time = time();
        $user->login_role_id = Yii::$app->params['ownerRoleId'];

        if (!$user->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!SmValidationService::validCode($postData['mobile'], $postData['validation_code'])) {
            $code = 1002;
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

            $user->aite_cube_no = $user->id + Yii::$app->params['offsetAiteCubeNo'];
            if (!$user->save()) {
                $transaction->rollBack();

                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            $userRole = new UserRole;
            $userRole->user_id = $user->id;
            $userRole->role_id = Yii::$app->params['ownerRoleId']; // owner
            if (!$userRole->save()) {
                $transaction->rollBack();

                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            $transaction->commit();

            SmValidationService::deleteCode($postData['mobile']);

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

        $code = 1001;
        $model = new LoginForm;
        if ($model->load($postData) && $model->login()) {
            $user = Yii::$app->user->identity;
            if (!$user) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            $userRole = UserRole::find()->where(['user_id' => $user->id, 'role_id' => $role->id])->one();
            if (!$userRole) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }

            $user->afterLogin($role);

            return Json::encode([
                'code' => 200,
                'msg' => '登录成功',
                'data' => [
                    'toUrl' => Yii::$app->request->hostInfo . '/admin/' . $role->admin_module,
                ],
            ]);
        }

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

        if (empty($postData['mobile'])
            || empty($postData['validation_code'])
            || empty($postData['new_password'])
            || strlen(($postData['new_password'])) < User::PASSWORD_MIN_LEN
            || strlen(($postData['new_password'])) > User::PASSWORD_MAX_LEN
        ) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!SmValidationService::validCode($postData['mobile'], $postData['validation_code'])) {
            $code = 1002;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $user = User::find()->where(['mobile' => $postData['mobile']])->one();
        if (!$user) {
            $code = 403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($user->validatePassword($postData['new_password'])) {
            SmValidationService::deleteCode($user->mobile);

            return Json::encode([
                'code' => 200,
                'msg' => '重设密码成功',
            ]);
        }

        $user->attributes = $postData;
        if (!$user->validate()) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $user->password = Yii::$app->security->generatePasswordHash($postData['new_password']);
        if (!$user->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        SmValidationService::deleteCode($postData['mobile']);

        return Json::encode([
            'code' => 200,
            'msg' => '重设密码成功',
        ]);
    }

    /**
     * Reset password action.
     *
     * @return string
     */
    public function actionResetPassword()
    {
        $postData = Yii::$app->request->post();
        $code = 1000;

        if (empty($postData['new_password'])
            || empty($postData['validation_code'])
            || strlen(($postData['new_password'])) < User::PASSWORD_MIN_LEN
            || strlen(($postData['new_password'])) > User::PASSWORD_MAX_LEN
        ) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($user->validatePassword($postData['new_password'])) {
            SmValidationService::deleteCode($user->mobile);

            return Json::encode([
                'code' => 200,
                'msg' => '重设密码成功',
            ]);
        }

        if (!SmValidationService::validCode($user->mobile, $postData['validation_code'])) {
            $code = 1002;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $user->password = Yii::$app->security->generatePasswordHash($postData['new_password']);
        if (!$user->save()) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        SmValidationService::deleteCode($user->mobile);

        return Json::encode([
            'code' => 200,
            'msg' => '重设密码成功',
        ]);
    }

    /**
     * Get validation code action.
     *
     * @return string
     */
    public function actionValidationCode()
    {
        $postData = Yii::$app->request->post();

        if (in_array($postData['type'], SmValidationService::$needAuthorizedTypes)) {
            if (!Yii::$app->user->identity) {
                $code = 403;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }

        try {
            new SmValidationService($postData);
        } catch (\InvalidArgumentException $e) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        } catch (ServerErrorHttpException $e) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        } catch (\Exception $e) {
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
        ]);
    }

    /**
     * Get daily sent num action.
     *
     * @return string
     */
    public function actionSmSendNum()
    {
        $getData = Yii::$app->request->get();
        $code = 1000;

        if (empty($getData['mobile']) || empty($getData['type'])) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (in_array($getData['type'], SmValidationService::$needAuthorizedTypes)) {
            if (!Yii::$app->user->identity) {
                $code = 403;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        }

        $sendNum = SmValidationService::sendNum($getData['mobile'], $getData['type']);
        $leftNum = Yii::$app->params['sm']['maxSendNumPerDay'] - $sendNum;
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'sendNum' => $sendNum,
                'leftNum' => $leftNum >= 0 ? $leftNum : 0,
            ],
        ]);
    }

    /**
     * Get roles status action.
     *
     * @return string
     */
    public function actionRolesStatus()
    {
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'roles_status' => UserRole::rolesStatus(Yii::$app->user->identity->id),
            ],
        ]);
    }

    /**
     * Get time types action
     *
     * @return string
     */
    public function actionTimeTypes()
    {
        $timeTypes = [];
        foreach (Yii::$app->params['timeTypes'] as $value => $name) {
            $timeTypes[] = [
                'value' => $value,
                'name' => $name,
            ];
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'time_types' => $timeTypes,
            ],
        ]);
    }

    /**
     * Get review statuses action
     *
     * @return string
     */
    public function actionReviewStatuses()
    {
        $reviewStatuses = [];
        foreach (Yii::$app->params['reviewStatuses'] as $value => $name) {
            $reviewStatuses[] = [
                'value' => $value,
                'name' => $name,
            ];
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'review_statuses' => $reviewStatuses,
            ],
        ]);
    }

    /**
     * Upload file action
     */
    public function actionUpload()
    {
        $uploadRet = FileService::upload();

        if (is_int($uploadRet)) {
            return Json::encode([
                'code' => $uploadRet,
                'msg' => Yii::$app->params['errorCodes'][$uploadRet],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'file_path' => $uploadRet,
            ],
        ]);
    }

    /**
     * Delete upload file action
     */
    public function actionUploadDelete()
    {
        $code = 1000;

        $filePath = trim(Yii::$app->request->post('file_path', ''));
        if (!$filePath || !FileService::existUploadFile($filePath)) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (!@unlink($filePath)) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK'
        ]);
    }

    /**
     * Check admin module access
     *
     * @return string
     */
    public function actionCheckAccessAdmin()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 403;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $code = 1000;

        $module = trim(Yii::$app->request->get('module', ''));
        if (!$module) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'can_access' => Role::find()->where(['id' => $user->login_role_id, 'admin_module' => $module])->exists()
            ],
        ]);
    }

    public function actionTest()
    {
        $quantity = array(
            1, 2, 3, 4, 5);
        $unitPrice = array(1, 2, 3, 4, 5);
        $arr = array(
            'day_price' => 300,
            'day_standard' => 5,
            'profit' => 15,
            'total_standard' => 300
        );
        $b = BasisDecorationService::formula($arr, $quantity, $unitPrice);

        var_dump($b);
    }
}