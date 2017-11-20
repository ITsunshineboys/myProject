<?php

namespace app\controllers;

use app\models\ContactForm;
use app\models\LoginForm;
use app\models\User;
use app\models\Role;
use app\models\UserRole;
use app\models\LogisticsDistrict;
use app\models\Addressadd;
use app\models\Invoice;
use app\services\BasisDecorationService;
use app\services\FileService;
use app\services\ExceptionHandleService;
use app\services\StringService;
use app\services\SmValidationService;
use app\services\AuthService;
use app\services\ModelService;
use app\services\EventHandleService;
use Yii;
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
        'admin-logout',
        'roles',
        'roles-status',
        'time-types',
        'upload',
        'upload-delete',
        'review-statuses',
        'reset-password-check',
        'reset-password',
        'reset-nickname',
        'reset-signature',
        'reset-gender',
        'reset-birthday',
        'reset-district',
        'reset-icon',
        'user-view',
        'switch-role',
        'last-login-role',
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AuthService::className(),
                'denyCallback' => function ($rule, $action) {
                    new ExceptionHandleService(func_get_args()[0]);
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
                    'admin-logout' => ['post',],
                    'upload' => ['post',],
                    'upload-delete' => ['post',],
                    'reset-password' => ['post',],
                    'reset-nickname' => ['post',],
                    'reset-signature' => ['post',],
                    'reset-gender' => ['post',],
                    'reset-birthday' => ['post',],
                    'reset-district' => ['post',],
                    'reset-icon' => ['post',],
                    'switch-role' => ['post',],
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
        if ($model->load($postData)) {
            if ($model->login()) {
                if ($model->isUserBlocked()) {
                    $code = 1015;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code],
                    ]);
                }

                $user = Yii::$app->user->identity;

                $needResetHxPwd = false;
                if (!empty($postData[$modelName]['registration_id'])) {
                    if ($postData[$modelName]['registration_id'] != $user->registration_id) {
                        $needResetHxPwd = true;
                    }
                    $user->registration_id = $postData[$modelName]['registration_id'];
                }

                $user->afterLogin();

                echo Json::encode([
                    'code' => 200,
                    'msg' => '登录成功',
                    'data' => [
                        'last_login_role' => [
                            'id' => $user->last_role_id_app,
                        ],
                    ],
                ]);

                $user->hx_pwd_date != date('Ymd') && $needResetHxPwd = true;
                empty(Yii::$app->session[User::LOGIN_ORIGIN_APP]) && $needResetHxPwd = false;
                if ($needResetHxPwd) {
                    $events = Yii::$app->params['events'];
                    $event = $events['async'];
                    $data = [
                        'event' => [
                            'name' => $events['user']['login'],
                            'data' => [
                                'mobile' => $user->mobile,
                                'username' => $user->username,
                                'registrationId' => $user->registration_id,
                            ],
                        ],
                    ];
                    new EventHandleService($event, $data);
                    Yii::$app->trigger($event);
                }
            }
        } else {
            $code = 1001;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }
    }

    /**
     * Register action.
     *
     * @return string
     */
    public function actionRegister()
    {
        $res = User::register(Yii::$app->request->post());
        $code = is_array($res) ? 200 : $res;
        $msg = is_array($res) ? '注册成功' : Yii::$app->params['errorCodes'][$code];
        echo Json::encode(compact('code', 'msg'));
        Yii::$app->trigger(Yii::$app->params['events']['async']);
    }

    /**
     * Check if mobile has been registered action.
     *
     * @return string
     */
    public function actionCheckMobileRegistered()
    {
        $code = 1000;

        $mobile = (int)Yii::$app->request->get('mobile', 0);
        if (!StringService::isMobile($mobile)) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if (User::find()->where(['mobile' => $mobile])->exists()) {
            $code = 1019;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
        ]);
    }

    /**
     * Logout action(app).
     *
     * @return string
     */
    public function actionLogout()
    {
        $userIdentity = Yii::$app->user->getIdentity();
        $userIdentity->logout();

        return Json::encode([
            'code' => 200,
            'msg' => '登出成功',
        ]);
    }

    /**
     * Logout action(admin).
     *
     * @return string
     */
    public function actionAdminLogout()
    {
        $userIdentity = Yii::$app->user->getIdentity();
        $userIdentity->adminLogout();

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
                'roles' => Role::appRoles(true),
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
        if (!$postData || !isset($postData['role_id'])) {
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
        if ($model->load($postData)) {
            if ($model->login()) {
                $user = Yii::$app->user->identity;
                if (!$user) {
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code],
                    ]);
                }

                if ($model->isUserBlocked()) {
                    $code = 1015;
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code],
                    ]);
                }

                $userRole = UserRole::find()
                    ->where(['user_id' => $user->id, 'role_id' => $role->id, 'review_status' => Role::AUTHENTICATION_STATUS_APPROVED])
                    ->one();
                if (!$userRole) {
                    return Json::encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code],
                    ]);
                }

                $user->afterLogin($role->id);

                return Json::encode([
                    'code' => 200,
                    'msg' => '登录成功',
                    'data' => [
                        'toUrl' => Yii::$app->request->hostInfo . '/admin/' . $role->admin_module,
                    ],
                ]);
            }
        }

        return Json::encode([
            'code' => $code,
            'msg' => Yii::$app->params['errorCodes'][$code],
        ]);
    }

    /**
     * Forget password check action.
     *
     * @return string
     */
    public function actionForgetPasswordCheck()
    {
        $postData = Yii::$app->request->post();
        $code = 1000;

        if (empty($postData['mobile'])
            || !StringService::isMobile($postData['mobile'])
        ) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $user = User::find()->where(['mobile' => $postData['mobile']])->one();
        if (!$user || $user->deadtime > 0 || !$user->checkDailyForgotPwdCnt()) {
            $code = !$user ? 1010 : ($user->deadtime > 0 ? 1015 : 1016);
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
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
            || mb_strlen(($postData['new_password'])) < User::PASSWORD_MIN_LEN
            || mb_strlen(($postData['new_password'])) > User::PASSWORD_MAX_LEN
        ) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $user = User::find()->where(['mobile' => $postData['mobile']])->one();
        if (!$user || $user->deadtime > 0 || !$user->checkDailyForgotPwdCnt()) {
            $code = !$user ? 1010 : ($user->deadtime > 0 ? 1015 : 1016);
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $codeValidationRes = SmValidationService::validCode($postData['mobile'], $postData['validation_code']);
        if ($codeValidationRes !== true) {
            $code = is_int($codeValidationRes) ? $codeValidationRes : 1002;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($user->validatePassword($postData['new_password'])) {
            SmValidationService::deleteCode($user->mobile);
            $user->setDailyForgotPwdCnt();

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
        $user->setDailyForgotPwdCnt();

        return Json::encode([
            'code' => 200,
            'msg' => '重设密码成功',
        ]);
    }

    /**
     * Admin forget password action.
     *
     * @return string
     */
    public function actionAdminForgetPassword()
    {
        $postData = Yii::$app->request->post();
        $code = 1000;

        if (empty($postData['mobile'])
            || empty($postData['validation_code'])
            || empty($postData['new_password'])
            || mb_strlen(($postData['new_password'])) < User::PASSWORD_MIN_LEN
            || mb_strlen(($postData['new_password'])) > User::PASSWORD_MAX_LEN
        ) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $user = User::find()->where(['mobile' => $postData['mobile']])->one();
        if (!$user || $user->deadtime > 0 || !UserRole::roleUser($user, Yii::$app->params['supplierRoleId'])) {
            $code = !$user ? 1010 : ($user->deadtime > 0 ? 1015 : 1040);
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $codeValidationRes = SmValidationService::validCode($postData['mobile'], $postData['validation_code']);
        if ($codeValidationRes !== true) {
            $code = is_int($codeValidationRes) ? $codeValidationRes : 1002;
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
     * Reset password check action.
     *
     * @return string
     */
    public function actionResetPasswordCheck()
    {
        $res = Yii::$app->user->identity->checkDailyResetPwdCnt();
        $code = 200;
        if (!$res) {
            $code = 1024;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        return Json::encode([
            'code' => $code,
            'msg' => 'ok'
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
            || mb_strlen(($postData['new_password'])) < User::PASSWORD_MIN_LEN
            || mb_strlen(($postData['new_password'])) > User::PASSWORD_MAX_LEN
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


        $codeValidationRes = SmValidationService::validCode($user->mobile, $postData['validation_code']);
        if ($codeValidationRes !== true) {
            $code = is_int($codeValidationRes) ? $codeValidationRes : 1002;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        if ($user->validatePassword($postData['new_password'])) {
            SmValidationService::deleteCode($user->mobile);
            $user->setDailyResetPwdCnt();

            return Json::encode([
                'code' => 200,
                'msg' => '重设密码成功',
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
        $user->setDailyResetPwdCnt();

        return Json::encode([
            'code' => 200,
            'msg' => '重设密码成功',
        ]);
    }

    /**
     * Reset nickname action.
     *
     * @return string
     */
    public function actionResetNickname()
    {
        $code = 1000;

        $nickname = Yii::$app->request->post('nickname', '');

        if (mb_strlen($nickname) < User::NICKNAME_MIN_LEN
            || mb_strlen($nickname) > User::NICKNAME_MAX_LEN
        ) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $user = Yii::$app->user->identity;
        $code = $user->resetNickname($nickname);
        if (200 != $code) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => '修改昵称成功',
        ]);
    }

    /**
     * Reset signature action.
     *
     * @return string
     */
    public function actionResetSignature()
    {
        $code = 1000;

        $signature = Yii::$app->request->post('signature', '');

        if (!$signature
            || mb_strlen($signature) > User::SIGNATURE_MAX_LEN
        ) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $user = Yii::$app->user->identity;
        $code = $user->resetSignature($signature);
        if (200 != $code) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => '修改签名成功',
        ]);
    }

    /**
     * Reset district action.
     *
     * @return string
     */
    public function actionResetDistrict()
    {
        $districtCode = (int)Yii::$app->request->post('district_code', 0);
        $res = ModelService::resetDistrict(Yii::$app->user->identity, $districtCode);
        return Json::encode([
            'code' => $res,
            'msg' => 200 == $res ? '修改地区成功' : Yii::$app->params['errorCodes'][$res],
        ]);
    }

    /**
     * Reset gender action.
     *
     * @return string
     */
    public function actionResetGender()
    {
        $gender = (int)Yii::$app->request->post('gender', 0);

        $user = Yii::$app->user->identity;
        $code = $user->resetGender($gender);
        if (200 != $code) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => '修改成功',
        ]);
    }

    /**
     * Reset birthday action.
     *
     * @return string
     */
    public function actionResetBirthday()
    {
        $birthday = (int)Yii::$app->request->post('birthday', 0);

        $user = Yii::$app->user->identity;
        $code = $user->resetBirthday($birthday);
        if (200 != $code) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => '修改成功',
        ]);
    }

    /**
     * Reset icon action.
     *
     * @return string
     */
    public function actionResetIcon()
    {
        $icon = trim(Yii::$app->request->post('icon', ''));
        $res = ModelService::resetIcon(Yii::$app->user->identity, $icon);
        return Json::encode([
            'code' => $res,
            'msg' => 200 == $res ? 'OK' : Yii::$app->params['errorCodes'][$res],
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

        if (in_array(@$postData['type'], SmValidationService::$needAuthorizedTypes)) {
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
            $code = 1020;
            if ($code == $e->getCode()) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
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
        $ret = [
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'can_access' => false
            ],
        ];

        $user = Yii::$app->user->identity;
        if (!$user || !$user->checkAdminLogin()) {
            return Json::encode($ret);
        }

        $module = trim(Yii::$app->request->get('module', ''));
        if (!$module) {
            return Json::encode($ret);
        }

        $ret['data']['can_access'] = Role::find()->where(['id' => $user->login_role_id, 'admin_module' => $module])->exists();
        return Json::encode($ret);
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

    /**
     * View owner action
     *
     * @return string
     */
    public function actionUserView()
    {
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'user-view' => Yii::$app->user->identity->view(),
            ],
        ]);
    }

    /**
     * check Is it the first set pay_password
     * @return string
     */
    public function actionCheckisfirstsetpaypwd()
    {
        $postData = Yii::$app->request->post();
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (empty($postData['role_id'])) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        };
        if ($postData['role_id'] != 7) {
            $check_user = UserRole::find()
                ->select('user_id')
                ->where(['user_id' => $user->id])
                ->andWhere(['role_id' => $postData['role_id']])
                ->asArray()
                ->one();
            if (!$check_user) {
                $code = 1010;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        $model = Role::CheckUserRole($postData['role_id']);
        if ($postData['role_id'] == 7) {
            $pay_password = User::find()->select('pay_password')->where(['id' => $user->id])->asArray()->one()['pay_password'];
        } else {
            $pay_password = $model->select('pay_password')->where(['uid' => $user->id])->asArray()->one()['pay_password'];
        }
        $data['type'] = empty($pay_password) ? 'first' : 'unfirst';
        $data['key'] = empty($pay_password) ? \Yii::$app->getSecurity()->generatePasswordHash('firstsetpaypassword' . $user->id . date('Y-m-d', time())) : \Yii::$app->getSecurity()->generatePasswordHash('unfirstsetpaypassword' . $user->id . date('Y-m-d', time()));
        $users = User::find()->where(['id' => $user->id])->select('mobile')->one();
        $data['mobile'] = $users['mobile'];
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => $data
        ]);
    }

    /**
     * set  pay password  or Get set paypassword SMS code
     * @return string
     */
    public function actionSetPaypassword()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $postData = Yii::$app->request->post();
        if (!$postData) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $code = User::SetPaypassword($postData, $user);
        if ($code == 200) {
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        } else {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }


    /**
     * reset pay password
     * @return string
     */
    public function actionResetPaypassword()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $postData = Yii::$app->request->post();
        $code = User::ResetPaypassword($postData, $user);
        if ($code == 200) {
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        } else {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /**Getresetmobilesmscode
     * get sms code to reset mobile
     * @return string
     */
    public function actionGetresetmobilesmscode()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $cache = Yii::$app->cache;
        $data = $cache->get(User::CACHE_PREFIX_RESET_MOBILE . $user->id);
        if ($data != false) {
            if ($data > 3) {
                $code = 1027;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        $sms['mobile'] = $user->mobile;
        $sms['type'] = 'resetMobile';
        try {
            new SmValidationService($sms);
        } catch (\InvalidArgumentException $e) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        } catch (ServerErrorHttpException $e) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        } catch (\Exception $e) {
            $code = 1020;
            if ($code == $e->getCode()) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
        }
        $code = 200;
        return Json::encode(
            [
                'code' => $code,
                'msg' => 'ok'
            ]
        );
    }

    /**
     * check user reset mobile's sms code is correct
     * @return string
     */
    public function actionCheckresetmobilesmscode()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $SmsCode = trim(htmlspecialchars(Yii::$app->request->post('smscode', '')), '');
        if (!$SmsCode) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (!SmValidationService::validCode($user->mobile, $SmsCode)) {
            $code = 1002;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        SmValidationService::deleteCode($user->mobile);
        $cache = Yii::$app->cache;
        $cacheData = 'ResetmobileSmscode' . $user->id . date('Y-m-d H', time());
        $data = $cache->set(User::CACHE_PREFIX_GET_MOBILE . $user->id, $cacheData, 60 * 60);
        if ($data == true) {
            $code = 200;
            return Json::encode(
                [
                    'code' => $code,
                    'msg' => 'ok'
                ]
            );
        } else {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /**
     * reset mobile by user
     * @return string
     */
    public function actionResetMobileByUser()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $mobile = trim(Yii::$app->request->post('mobile', ''));
        if (!$mobile || !preg_match('/^[1][3,5,7,8]\d{9}$/', $mobile)) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $check_mobile = User::CheckMobileIsExists($mobile, $user);
        if ($check_mobile != 200) {
            return Json::encode([
                'code' => $check_mobile,
                'msg' => Yii::$app->params['errorCodes'][$check_mobile]
            ]);
        }
        $cache = Yii::$app->cache;
        $cacheData = 'ResetmobileSmscode' . $user->id . date('Y-m-d H', time());
        $data = $cache->get(User::CACHE_PREFIX_GET_MOBILE . $user->id);
        if ($cacheData != $data) {
            $code = 1020;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code = User::ResetMobileByUser($mobile, $user);
        if ($code == 200) {
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        } else {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /**
     * Switch user role
     *
     * @return string
     */
    public function actionSwitchRole()
    {
        $roleId = (int)Yii::$app->request->post('role_id', 0);
        $user = Yii::$app->user->identity;
        $res = $user->switchRole($roleId);
        $data = [
            'code' => $res,
            'msg' => 200 == $res ? 'OK' : Yii::$app->params['errorCodes'][$res],
        ];
        return Json::encode($data);
    }

    /**
     * Get last login role
     *
     * @return string
     */
    public function actionLastLoginRole()
    {
        return Json::encode(
            [
                'code' => 200,
                'msg' => 'OK',
                'data' => [
                    'last_login_role' => [
                        'id' => Yii::$app->user->identity->last_role_id_app,
                    ],
                ],
            ]
        );
    }


    /**
     * add receive user address
     * @return string
     */
    public function actionAddReceiveAddress()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $district_code = trim($request->post('district_code', ''));
        $region = trim($request->post('region', ''));
        $consignee = trim($request->post('consignee', ''));
        $mobile = trim($request->post('mobile', ''));
        if (!$district_code || !$region || !$consignee || !$mobile) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code = Addressadd::UserAddressAdd($district_code, $region, $consignee, $mobile, $user->id);
        if ($code == 200) {
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        } else {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /**
     * get receive  address by user  and  default
     * @return string
     */
    public function actionGetReceiveAddress()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $addressList = Addressadd::find()->where(['uid' => $user->id])->all();
        foreach ($addressList as $k => $v) {
            $addressList[$k]['district'] = LogisticsDistrict::getdistrict($addressList[$k]['district']);
        }
        $code = 200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data' => $addressList
        ]);
    }

    /**
     * select default address
     * @return string
     */
    public function actionSetDefaultAddress()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $address_id = trim($request->post('address_id', ''));
        if (!$address_id) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code = Addressadd::SetDefaultAddress($address_id, $user);
        if ($code == 200) {
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        } else {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }

    /**
     * @return string
     */
    public function actionUpdateReceiveAddress()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $address_id = trim($request->post('address_id', ''));
        $consignee = trim($request->post('consignee', ''));
        $district_code = trim($request->post('district_code'));
        $mobile = trim($request->post('mobile', ''));
        $region = trim($request->post('region', ''));
        if (!$consignee || !$address_id || !$district_code || !$mobile || !$region) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code = Addressadd::updateAddress($consignee, $address_id, $district_code, $mobile, $region);
        if ($code == 200) {
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        } else {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }


    /**
     * 删除收货地址
     * @return string
     */
    public  function actionDelReceiveAddress()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $address_id = trim($request->post('address_id'));
        if (!$address_id){
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $code = Addressadd::DelAddress($address_id);
        if ($code == 200) {
            return Json::encode([
                'code' => $code,
                'msg' => 'ok'
            ]);
        } else {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
    }


    /**
     *  get user invoice list
     * @return string
     */
    public function actionGetInvoice()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $invoiceList = Invoice::find()->where(['uid' => $user->id])->all();
        $code = 200;
        return Json::encode([
            'code' => $code,
            'msg' => 'ok',
            'data' => $invoiceList
        ]);
    }


    /**
     * @return string
     */
    public function actionAddInvoice()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $invoice_type = trim($request->post('invoice_type', ''));
        $invoice_header_type = trim($request->post('invoice_header_type', ''));
        $invoice_header = trim($request->post('invoice_header', ''));
        $invoicer_card = trim($request->post('invoicer_card'));
        $invoice_content = trim($request->post('invoice_content', ''));
        if ($invoicer_card) {
            $isMatched = preg_match('/^[0-9A-Z?]{18}$/', $invoicer_card, $matches);
            if ($isMatched == false) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        $code = Invoice::AddUserInvoice($invoice_type, $invoice_header_type, $invoice_header, $invoice_content, $invoicer_card, $user);
        if ($code == 200) {
            return Json::encode([
                'code' => 200,
                'msg' => 'ok'
            ]);
        } else {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
    }

    /**
     * set default invoice
     * @return string
     */
    public function actionSetDefaultInvoice()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $invoice_id = trim($request->post('invoice_id', ''));
        if (!$invoice_id) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
        $code = Invoice::setDefaultInvoice($invoice_id, $user);
        if ($code == 200) {
            return Json::encode([
                'code' => 200,
                'msg' => 'ok'
            ]);
        } else {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
    }

    /**
     *  update invoice by user
     * @return string
     */
    public function actionUpdateInvoice()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request = Yii::$app->request;
        $invoice_id = trim($request->post('invoice_id', ''));
        $invoice_type = trim($request->post('invoice_type', ''));
        $invoice_header_type = trim($request->post('invoice_header_type', ''));
        $invoice_header = trim($request->post('invoice_header', ''));
        $invoicer_card = trim($request->post('invoicer_card'));
        $invoice_content = trim($request->post('invoice_content', ''));
        if ($invoicer_card) {
            $isMatched = preg_match('/^[0-9A-Z?]{18}$/', $invoicer_card, $matches);
            if ($isMatched == false) {
                $code = 1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                    'data' => null
                ]);
            }
        }
        $code = Invoice::updateUserInvoice($invoice_type, $invoice_header_type, $invoice_header, $invoice_content, $invoicer_card, $user, $invoice_id);
        if ($code == 200) {
            return Json::encode([
                'code' => 200,
                'msg' => 'ok'
            ]);
        } else {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
                'data' => null
            ]);
        }
    }


    public function actionCheckSignature()
    {

        $signature = Yii::$app->request->get('signature');
        $timestamp = Yii::$app->request->get('timestamp');
        $nonce = Yii::$app->request->get('nonce');
        $echostr = Yii::$app->request->get('echostr');
        //        $tmpArr = array($timestamp, $nonce);
        //        sort($tmpArr, SORT_STRING);
        //        $tmpStr = implode( $tmpArr );
        //        $tmpStr = sha1( $tmpStr );
        if ($signature) {

            echo $echostr;
        } else {
            return false;
        }
    }
}