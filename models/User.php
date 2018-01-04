<?php

namespace app\models;

use app\services\StringService;
use app\services\SmValidationService;
use app\services\ModelService;
use app\services\ChatService;
use app\services\EventHandleService;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    const CACHE_PREFIX = 'user_';
    const CACHE_PREFIX_DAILY_FORGOT_PWD_CNT = 'daily_forgot_pwd_cnt_';
    const CACHE_PREFIX_DAILY_RESET_PWD_CNT = 'daily_reset_pwd_cnt_';
    const CACHE_PREFIX_SET_PAYPASSWORD = 'set_paypassword_';
    const CACHE_FREFIX_GET_PAY_PASSWORD = 'get_paypassword';
    const UNFIRST_SET_PAYPASSWORD = 'unfirstsetpaypassword';
    const FIRST_SET_PAYPASSWORD = 'firstsetpaypassword';
    const CACHE_PREFIX_RESET_MOBILE = 'reset_mobile_';
    const CACHE_PREFIX_GET_MOBILE = 'get_mobile_';
    const PASSWORD_MIN_LEN = 6;
    const PASSWORD_MAX_LEN = 25;
    const PREFIX_DEFAULT_MOBILE = '18';
    const DEFAULT_PWD = '888888';
    const FIELDS_VIEW_IDENTITY = [
        'legal_person',
        'identity_no',
        'identity_card_front_image',
        'identity_card_back_image',
    ];
    const FIELDS_VIEW_IDENTITY_EXTRA = [
        'review_status',
        'review_remark',
    ];
    const LEN_MAX_FIELDS = [
        'legal_person' => 15,
    ];
    const SEX_MALE = 0;
    const SEX_FEMALE = 1;
    const SEX_UNKOWN = 2;
    const SEXES = [
        self::SEX_MALE => '男',
        self::SEX_FEMALE => '女',
        self::SEX_UNKOWN => '保密',
    ];
    const NICKNAME_MIN_LEN = 2;
    const NICKNAME_MAX_LEN = 20;
    const SIGNATURE_MAX_LEN = 20;
    const FIELDS_USER_CENTER_MODEL = [
        'icon',
        'nickname',
        'gender',
        'birthday',
        'district_name',
        'signature',
        'aite_cube_no',
        'balance',
        'availableamount'
    ];
    const FIELDS_USER_CENTER_EXTRA = [
//        'address',
        'review_status',
    ];
    const BIRTHDAY_LEN = 8;
    const FIELDS_IDENTITY_LHZZ = [
        'legal_person',
        'identity_no',
        'identity_card_front_image',
        'identity_card_back_image',
    ];
    const FIELDS_IDENTITY_LHZZ_EXTRA = [
        'review_status',
        'review_time',
    ];
    const STATUS_OFFLINE = 0; // 关闭
    const STATUS_ONLINE = 1; // 正常
    const STATUSES = [
        self::STATUS_OFFLINE => '关闭',
        self::STATUS_ONLINE => '正常',
    ];
    const FIELDS_USER_DETAILS_MODEL_LHZZ = [
        'icon',
        'nickname',
        'gender',
        'birthday',
        'district_name',
        'signature',
        'mobile',
        'aite_cube_no',
        'create_time',
        'deadtime',
    ];
    const FIELDS_USER_DETAILS_MODEL_LHZZ_EXTRA = [
        'old_nickname',
        'role_names',
        'review_status',
        'status_operator',
        'status_remark',
        'review_time',
        'close_time',
    ];
    const FIELDS_USER_LIST_LHZZ = [
        'id',
        'icon',
        'nickname',
        'gender',
        'birthday',
        'district_name',
        'signature',
        'mobile',
        'aite_cube_no',
        'create_time',
        'deadtime',
        'old_nickname',
        'role_names',
        'review_status',
        'status_operator',
        'status_remark',
        'legal_person',
        'identity_no',
        'identity_card_front_image',
        'identity_card_back_image',
        'review_time',
        'close_time',
    ];
    const LOGIN_ORIGIN_ADMIN = 'login_origin_admin';
    const LOGIN_ORIGIN_APP = 'login_origin_app';
    const LOGIN_ROLE_ID = 'login_role_id';
    const PREFIX_SESSION_FILENAME = 'sess_';
    const RETYR_TIMES_CREATE_HUANXIN_USER = 3;

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        $key = self::CACHE_PREFIX . $id;
        $cache = Yii::$app->cache;
        $user = $cache->get($key);
        if (!$user) {
            $user = self::find()->where(['id' => $id])->one();
            if ($user) {
                $cache->set($key, $user);
            }
        }

        if (!$user) {
            return null;
        }

        return $user;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = self::find()->where(['accessToken' => $token])->one();

        if (!$user) {
            return null;
        }

        $key = self::CACHE_PREFIX . $user['id'];
        $cache = Yii::$app->cache;
        $cache->set($key, $user);

        return $user;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
//        $user = self::find()->where(['username' => $username])->one();
        $user = self::find()->where("mobile = '{$username}' or aite_cube_no = '{$username}'")->one();

        if (!$user) {
            return null;
        }

        $key = self::CACHE_PREFIX . $user->id;
        $cache = Yii::$app->cache;
        $cache->set($key, $user);

        return $user;
    }

    /**
     * Register user
     *
     * @param array $data data
     * @param bool $checkValidationCode if check validation code
     * @param bool $external if external user
     * @return int|array
     */
    public static function register(array $data, $checkValidationCode = true, $external = true)
    {
        $code = 1000;

        if (empty($data['mobile'])
            || empty($data['password'])
            || mb_strlen(($data['password'])) < self::PASSWORD_MIN_LEN
            || mb_strlen(($data['password'])) > self::PASSWORD_MAX_LEN
        ) {
            return $code;
        }

        if ($checkValidationCode) {
            if (empty($data['validation_code'])) {
                return $code;
            }

            $checkCodeRes = SmValidationService::validCode($data['mobile'], $data['validation_code']);
            if ($checkCodeRes === false) {
                return 1002;
            } elseif (is_int($checkCodeRes)) {
                return $checkCodeRes;
            }
        }

        $user = new self;
        $user->attributes = $data;
        $user->password = Yii::$app->security->generatePasswordHash($user->password);
        $user->create_time = $user->login_time = time();
        $user->login_role_id = Yii::$app->params['ownerRoleId'];
        $user->nickname = Yii::$app->params['user']['default_nickname'];
        $user->icon = Yii::$app->params['user']['deault_icon_path'];

        if (!$user->validate()) {
            ModelService::uniqueError($user, 'mobile') && $code = 1019;
            return $code;
        }

        $transaction = Yii::$app->db->beginTransaction();
        $code = 500;
        try {
            if (!$user->save()) {
                $transaction->rollBack();
                return $code;
            }

            $offset = $external ? Yii::$app->params['offsetAiteCubeNo'] : Yii::$app->params['offsetAiteCubeNoInternal'];
            $user->aite_cube_no = $user->id + $offset;
            if (!$user->save()) {
                $transaction->rollBack();
                return $code;
            }

            $userRole = new UserRole;
            $userRole->user_id = $user->id;
            $userRole->role_id = Yii::$app->params['ownerRoleId']; // owner
            if (!$userRole->save()) {
                $transaction->rollBack();
                return $code;
            }

            $operator = isset($data['operator']) ? $data['operator'] : null;
            if (!UserMobile::addByUserAndOperator($user, $operator)) {
                $transaction->rollBack();
                return $code;
            }

            if (!UserStatus::addByUserAndOperator($user, $operator)) {
                $transaction->rollBack();
                return $code;
            }

            $transaction->commit();

            if ($checkValidationCode && !empty($data['validation_code'])) {
                SmValidationService::deleteCode($data['mobile']);
            }

            $code = 200;
            return [
                'code' => $code,
                'id' => $user->id
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $code;
        }
    }

    /**
     * Reset user's new mobile and new password
     *
     * @param int $mobile mobile
     * @param int $newMobile new mobile
     * @param string $pwd password
     * @return bool
     */
    public static function resetMobileAndPwdByMobile($mobile, $newMobile, $pwd)
    {
        $user = self::find()->where(['mobile' => $mobile])->one();
        if (!$user) {
            return false;
        }

        $user->mobile = $newMobile;
        $user->password = Yii::$app->getSecurity()->generatePasswordHash($pwd);
        return $user->validate() && $user->save();
    }

    /**
     * Add user by mobile and password
     *
     * @param int $mobile mobile
     * @param string $pwd password
     * @return int
     */
    public static function addByMobileAndPwd($mobile, $pwd)
    {
        $code = 1000;

        if (!StringService::isMobile($mobile)) {
            return $code;
        }

        $user = new self;
        $user->mobile = $mobile;
        $user->password = Yii::$app->getSecurity()->generatePasswordHash($pwd);
        if (!$user->validate()) {
            return $code;
        }

        if (!$user->save()) {
            $code = 500;
            return $code;
        }

        $code = 200;
        return $code;
    }

    /**
     * Check role and get user identity
     *
     * @param int $mobile mobile
     * @return ActiveRecord
     */
    public static function checkRoleAndGetIdentityByMobile($mobile)
    {
        $user = self::find()->where(['mobile' => $mobile])->one();

        if (!$user) {
            $code = 1010;
            return $code;
        } else {
            if (UserRole::find()->where(['user_id' => $user->id])->count() >= Yii::$app->params['maxRolesNumber']) {
                $code = 1011;
                return $code;
            }
        }

        return $user;
    }

    /**
     * Disable users in batch
     *
     * @param array $userIds user id list
     * @param User $operator operator
     * @param string $remark remark
     * @return int
     */
    public static function disableInBatch(array $userIds, User $operator, $remark = '')
    {
        $tran = Yii::$app->db->beginTransaction();

        foreach ($userIds as $userId) {
            $user = self::findOne($userId);
            if ($user->deadtime > 0) {
                continue;
            }

            $toggleStatusRes = $user->toggleStatus($operator, $remark, true);
            if (200 !== $toggleStatusRes) {
                $tran->rollBack();
                return $toggleStatusRes;
            }
        }

        $tran->commit();
        return 200;
    }

    /**
     * Toggle user status
     *
     * @param User $operator operator
     * @param string $remark remark
     * @param bool $disableOnly if disable only
     * @param bool $enableOnly if enable only
     * @return int
     */
    public function toggleStatus(User $operator, $remark = '', $disableOnly = false, $enableOnly = false)
    {
        $code = 500;
        $tran = Yii::$app->db->beginTransaction();

        try {
            if ($disableOnly) {
                $this->deadtime = time();
            } elseif ($enableOnly) {
                $this->deadtime = 0;
            } else {
                $this->deadtime = $this->deadtime > 0 ? 0 : time();
            }
            if (!$this->save()) {
                $tran->rollBack();
                return $code;
            }

            if (!UserStatus::addByUserAndOperator($this, $operator, $remark)) {
                $tran->rollBack();
                return $code;
            }

            if ($this->deadtime) {
                $supplier = UserRole::roleUser($this, Yii::$app->params['supplierRoleId']);
                if ($supplier) {
                    $admin = UserRole::roleUser($operator, $operator->login_role_id);
                    $supplier->offline($admin);
                }
            }

            $tran->commit();
            $code = 200;
            return $code;
        } catch (\Exception $e) {
            $tran->rollBack();
            return $code;
        }
    }

    /**
     * Enable users in batch
     *
     * @param array $userIds user id list
     * @param User $operator operator
     * @return int
     */
    public static function enableInBatch(array $userIds, User $operator)
    {
        $tran = Yii::$app->db->beginTransaction();

        foreach ($userIds as $userId) {
            $user = self::findOne($userId);
            if ($user->deadtime == 0) {
                continue;
            }

            $toggleStatusRes = $user->toggleStatus($operator, '', false, true);
            if (200 !== $toggleStatusRes) {
                $tran->rollBack();
                return $toggleStatusRes;
            }
        }

        $tran->commit();
        return 200;
    }

    /**
     * Get pagination list
     *
     * @param  array $where search condition
     * @param  array $select select fields default all fields
     * @param  int $page page number default 1
     * @param  int $size page size default 12
     * @param  array $orderBy order by fields default id desc
     * @return array
     */
    public static function pagination($where = [], $select = [], $page = 1, $size = ModelService::PAGE_SIZE_DEFAULT, $orderBy = ModelService::ORDER_BY_DEFAULT)
    {
        $selectOld = $select;
        $select = array_diff($select, self::FIELDS_USER_DETAILS_MODEL_LHZZ_EXTRA);

        $offset = ($page - 1) * $size;
        $list = self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->all();

        $details = [];
        foreach ($list as &$row) {
            $detail = [];

            if (isset($row['create_time'])) {
                $row['create_time'] = date('Y-m-d H:i', $row['create_time']);
            }

            if (isset($row['op_username'])) {
                $row['op_username'] = $row['op_username'] ? $row['op_username'] : '用户';
            }

            if (isset($row['gender'])) {
                $row['gender'] = self::SEXES[$row['gender']];
            }

            if (isset($row['birthday'])) {
                $row['birthday'] = StringService::formatBirthday($row['birthday']);
            }

            if (in_array('old_nickname', $selectOld)) {
                $detail['old_nickname'] = $row->getOldNickname();
            }

            if (in_array('role_names', $selectOld)) {
                $detail['role_names'] = UserRole::findRoleNamesByUserId($row->id);
            }

            if (in_array('review_status', $selectOld)) {
                $userRole = UserRole::find()
                    ->where(['user_id' => $row->id, 'role_id' => Yii::$app->params['ownerRoleId']])
                    ->one();
                $reviewStatus = $userRole ? $userRole->review_status : Role::AUTHENTICATION_STATUS_NO_APPLICATION;
                $detail['review_status'] = $reviewStatus;
                $detail['review_status' . ModelService::SUFFIX_FIELD_DESCRIPTION] = Yii::$app->params['reviewStatuses'][$reviewStatus];
            }

            if (in_array('review_time', $selectOld)) {
                $userRole = UserRole::find()
                    ->where(['user_id' => $row->id, 'role_id' => Yii::$app->params['ownerRoleId']])
                    ->one();
                if ($userRole) {
                    $detail['review_time'] = $userRole->review_time
                        ? date('Y-m-d H:i', $userRole->review_time)
                        : '';
                }
            }

            if (in_array('status_operator', $selectOld)) {
                $userStatus = UserStatus::find()->where(['uid' => $row->id])->orderBy(['id' => SORT_DESC])->one();
                if ($userStatus) {
                    $detail['status_operator'] = $userStatus->op_username;
                } else {
                    $detail['status_operator'] = '';
                }
            }

            if (in_array('status_remark', $selectOld)) {
                $userStatus = UserStatus::find()->where(['uid' => $row->id])->orderBy(['id' => SORT_DESC])->one();
                if ($userStatus) {
                    $detail['status_remark'] = $userStatus->remark;
                } else {
                    $detail['status_remark'] = '';
                }
            }

            if (isset($row['deadtime'])) {
                $detail['status'] = self::STATUSES[$row['deadtime'] > 0 ? self::STATUS_OFFLINE : self::STATUS_ONLINE];
                $row['deadtime'] = date('Y-m-d H:i', $row['deadtime']);
            }

            if (in_array('close_time', $selectOld)) {
                $userStatus = UserStatus::find()
                    ->where(['uid' => $row->id, 'status' => self::STATUS_OFFLINE])
                    ->orderBy(['id' => SORT_DESC])
                    ->one();
                if ($userStatus) {
                    $detail['close_time'] = date('Y-m-d H:i', $userStatus->create_time);
                }
            }

            $detail = array_merge(array_filter($row->getAttributes()), $detail);
            foreach (array_diff($selectOld, array_keys($detail)) as $attrName) {
                $detail[$attrName] = '';
            }
            $details[] = $detail;
        }

        return [
            'total' => (int)self::find()->where($where)->asArray()->count(),
            'details' => $details
        ];
    }

    /**
     * Get statistics on the total number of users, workers etc.
     *
     * @return int
     */
    public static function totalNumberStat()
    {
        $totalOwner = self::totalNumber();
        $totalDesigner = Designer::totalNumber();
        $totalSupplier = Supplier::totalNumber();
        $totalManager = Manager::totalNumber();
        $totalWorker = Worker::totalNumber();
        $totalDecorationCompany = DecorationCompany::totalNumber();

        $totalUserNumber = $totalOwner
            + $totalDesigner
            + $totalSupplier
            + $totalManager
            + $totalWorker
            + $totalDecorationCompany;

        $totalAuthorizedUserNumber = UserRole::totalAuthorizedUserNumber();

        return [
            'total_number_user' => $totalUserNumber,
            'total_number_owner' => $totalOwner,
            'total_number_designer' => $totalDesigner,
            'total_number_supplier' => $totalSupplier,
            'total_number_manager' => $totalManager,
            'total_number_worker' => $totalWorker,
            'total_number_decoration_company' => $totalDecorationCompany,
            'total_number_authorized_user' => $totalAuthorizedUserNumber,
            'total_number_unauthorized_user' => $totalUserNumber - $totalAuthorizedUserNumber,
        ];
    }

    /**
     * Get total number of users
     *
     * @return int
     */
    public static function totalNumber()
    {
        return (int)self::find()->count();
    }

    /**
     * Check if kicked out
     *
     * @return bool
     */
    public static function checkKickedout()
    {
        if (Yii::$app->session->getHasSessionId()) {
            $sessId = Yii::$app->session->id;
            $user = self::find()
                ->where(['oldAuthKey' => $sessId])
                ->orWhere(['oldAuthKeyAdmin' => $sessId])
                ->one();
            if ($user) {
                if ($user->oldAuthKey
                    && $user->authKey
                    && $user->authKey != $user->oldAuthKey
                    && $sessId == $user->oldAuthKey
                ) {
                    return true;
                } elseif ($user->oldAuthKeyAdmin
                    && $user->authKeyAdmin
                    && $user->authKeyAdmin != $user->oldAuthKeyAdmin
                    && $sessId == $user->oldAuthKeyAdmin
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * set pay password
     * @param $postData
     * @param $user
     * @return int
     */
    public static function SetPaypassword($postData, $user)
    {
        if (!array_key_exists('key', $postData)) {
            $code = 1000;
            return $code;
        }
        $key = trim(htmlspecialchars($postData['key']), '');
        if ($key=='forget_pay_password')
        {
            $code = self::setPaypassword_secend($postData, $user);
        }else
        {
            if (\Yii::$app->getSecurity()->validatePassword(self::FIRST_SET_PAYPASSWORD . $user->id . date('Y-m-d', time()), $key) == true) {
                $code = self::setPaypassword_first($postData, $user);
            }
            if (\Yii::$app->getSecurity()->validatePassword(self::UNFIRST_SET_PAYPASSWORD . $user->id . date('Y-m-d', time()), $key) == true) {
                $code = self::setPaypassword_secend($postData, $user);
            }
        }
        return $code;
    }

    /**
     * set first pay password
     * @param $postData
     * @param $user
     * @return int
     */
    private static function setPaypassword_first($postData, $user)
    {

        if (!array_key_exists('pay_pwd_first', $postData) || !array_key_exists('pay_pwd_secend', $postData) || !array_key_exists('role_id', $postData)) {
            $code = 1000;
            return $code;
        }
        $pay_pwd_first = trim(htmlspecialchars($postData['pay_pwd_first']), '');
        $pay_pwd_secend = trim(htmlspecialchars($postData['pay_pwd_secend']), '');
        $role_id = trim(htmlspecialchars($postData['role_id']), '');
        if (!self::CheckPaypwdFormat($pay_pwd_first) || !self::CheckPaypwdFormat($pay_pwd_secend)) {
            $code = 1000;
            return $code;
        }
        if ($pay_pwd_first != $pay_pwd_secend) {
            $code = 1053;
            return $code;
        }
        if ($postData['role_id'] != 7) {
            $check_user = UserRole::find()
                ->select('user_id')
                ->where(['user_id' => $user->id])
                ->andWhere(['role_id' => $postData['role_id']])
                ->asArray()
                ->one();
            if (!$check_user) {
                $code = 1010;
                return $code;
            }
        }
        if ($postData['role_id'] == 7) {
            $userRole = self::find()->where(['id' => $user->id])->one();
        } else {
            $userRole = Role::CheckUserRole($role_id)->where(['uid' => $user->id])->one();
        }
        if (!$userRole) {
            $code = 1010;
            return $code;
        }
        $cache = Yii::$app->cache;
        $data = $cache->get(self::CACHE_PREFIX_SET_PAYPASSWORD . $user->id);
        if ($data === false) {
            $cacheData = 1;
            $cache->set(self::CACHE_PREFIX_SET_PAYPASSWORD . $user->id, $cacheData, 24 * 60 * 60);
        } else {
            $cacheData = $data + 1;
            if ($cacheData > 5) {
                $code = 1024;
                return $code;
            }
            $cache->set(self::CACHE_PREFIX_SET_PAYPASSWORD . $user->id, $cacheData, 24 * 60 * 60);
        }
        $tran = Yii::$app->db->beginTransaction();
        try {
            $psw = Yii::$app->getSecurity()->generatePasswordHash($pay_pwd_secend);
            $userRole->pay_password = $psw;
            $res = $userRole->save(false);
            if (!$res) {
                $code = 500;
                $tran->rollBack();
                return $code;
            }
            $tran->commit();
            $code = 200;
            return $code;
        } catch (Exception $e) {
            $tran->rollBack();
            $code = 500;
            return $code;
        }
    }

    /**
     * check pay password
     * @param $psw
     * @return int
     */
    public static function CheckPaypwdFormat($psw)
    {
        $res = preg_match('/\b\d{6}\b/', $psw, $matches);
        return $res;
    }

    /**
     * update pay password
     * @param $postData
     * @param $user
     * @return int
     */
    private static function setPaypassword_secend($postData, $user)
    {
        if (!array_key_exists('pay_pwd', $postData) || !array_key_exists('role_id', $postData)) {
            $code = 1000;
            return $code;
        }
        $pay_pwd = trim(htmlspecialchars($postData['pay_pwd']), '');
        $role_id = trim(htmlspecialchars($postData['role_id']), '');

        if ($postData['role_id'] != 7) {
            $check_user = UserRole::find()
                ->select('user_id')
                ->where(['user_id' => $user->id])
                ->andWhere(['role_id' => $postData['role_id']])
                ->asArray()
                ->one();
            if (!$check_user) {
                $code = 1010;
                return $code;
            }
        }
        if ($postData['role_id'] == 7) {
            $userRole = self::find()->where(['id' => $user->id])->one();
        } else {
            $userRole = Role::CheckUserRole($role_id)->where(['uid' => $user->id])->one();
        }
        if (!$userRole) {
            $code = 1010;
            return $code;
        }
        $cache = Yii::$app->cache;
        $data = $cache->get(self::CACHE_PREFIX_SET_PAYPASSWORD . $user->id);
        if ($data != false) {
            if ($data > 5) {
                $code = 1024;
                return $code;
            }
        }
        $users = self::find()->select('mobile')->where(['id' => $user->id])->asArray()->one();
        $psw = Yii::$app->getSecurity()->generatePasswordHash($pay_pwd);
        $cache->set(self::CACHE_FREFIX_GET_PAY_PASSWORD . $user->id, $psw, 60 * 60);
        $data = array();
        $data['mobile'] = 0;
        $data['mobile'] = $users['mobile'];
        $data['type'] = 'resetPayPassword';
        try {
            new SmValidationService($data);
        } catch (\InvalidArgumentException $e) {
            $code = 1000;
            return $code;
        } catch (\ServerErrorHttpException $e) {
            $code = 500;
            return $code;
        } catch (\Exception $e) {
            $code = 1020;
            if ($code == $e->getCode()) {
                return $code;
            }
        }
        $code = 200;
        return $code;
    }

    /**
     * update paypassword
     * @param $postData
     * @param $user
     * @return int
     */
    public static function ResetPaypassword($postData, $user)
    {
        if (!array_key_exists('role_id', $postData) || !array_key_exists('sms_code', $postData)) {
            $code = 1000;
            return $code;
        }
        $role_id = trim(htmlspecialchars($postData['role_id']), '');
        $smscode = trim(htmlspecialchars($postData['sms_code']), '');
        $cache = Yii::$app->cache;
        $psw = $cache->get(self::CACHE_FREFIX_GET_PAY_PASSWORD . $user->id);

        if ($postData['role_id'] != 7) {
            $check_user = UserRole::find()
                ->select('user_id')
                ->where(['user_id' => $user->id])
                ->andWhere(['role_id' => $postData['role_id']])
                ->asArray()
                ->one();
            if (!$check_user) {
                $code = 1010;
                return $code;
            }
        }
        $users = self::find()->select('mobile')->where(['id' => $user->id])->one();
        if (!SmValidationService::validCode($users['mobile'], $smscode)) {
            $code = 1002;
            return $code;
        }
        SmValidationService::deleteCode($users['mobile']);
        if ($postData['role_id'] == 7) {
            $userRole = self::find()->where(['id' => $user->id])->one();
        } else {
            $userRole = Role::CheckUserRole($role_id)->where(['uid' => $user->id])->one();
        }
        if (!$userRole) {
            $code = 1010;
            return $code;
        }
        $data = $cache->get(self::CACHE_PREFIX_SET_PAYPASSWORD . $user->id);
        if ($data === false) {
            $cacheData = 1;
            $cache->set(self::CACHE_PREFIX_SET_PAYPASSWORD . $user->id, $cacheData, 24 * 60 * 60);
        } else {
            $cacheData = $data + 1;
            if ($cacheData > 5) {
                $code = 1024;
                return $code;
            }
            $cache->set(self::CACHE_PREFIX_SET_PAYPASSWORD . $user->id, $cacheData, 24 * 60 * 60);
        }
        $userRole->pay_password = $psw;
        $res = $userRole->save(false);
        if ($res) {
            $code = 200;
            return $code;
        }
    }

    /**
     * check mobile by user
     * @param $mobile
     * @param $user
     * @return int
     */
    public static function ResetMobileByUser($mobile, $user)
    {
        $cache = Yii::$app->cache;
        $tran = Yii::$app->db->beginTransaction();
        $users = self::find()->where(['id' => $user->id])->one();
        $time = time();
        $e = 1;
        try {
            $users->mobile = $mobile;
            $res1 = $users->save();
            $UserMobile = new UserMobile();
            $UserMobile->uid = $user->id;
            $UserMobile->mobile = $mobile;
            $UserMobile->create_time = $time;
            $res2 = $UserMobile->save();
            if (!$res1 || !$res2) {
                $code = 500;
                $tran->rollBack();
                return $code;
            }
            $data = $cache->get(self::CACHE_PREFIX_RESET_MOBILE . $user->id);
            if ($data === false) {
                $cacheData = 1;
                $cache->set(self::CACHE_PREFIX_RESET_MOBILE . $user->id, $cacheData, strtotime(date('Y-m-d', time() + 23 * 60 * 60 + 59 * 60)) - time());
            } else {
                $cacheData = $data + 1;
                if ($cacheData > 3) {
                    $code = 1027;
                    $tran->rollBack();
                    return $code;
                }
                $cache->set(self::CACHE_PREFIX_RESET_MOBILE . $user->id, $cacheData, strtotime(date('Y-m-d', time() + 23 * 60 * 60 + 59 * 60)) - time());
            }

        } catch (Exception $e) {
            $tran->rollBack();
        }
        $tran->commit();
        if ($e) {
            $code = 200;
            return $code;
        }
    }

    /**
     * @param $mobile
     * @param $user
     * @return int
     */
    public static function CheckMobileIsExists($mobile, $user)
    {

        if ($mobile == $user->mobile) {
            $code = 1025;
            return $code;
        }
        $check = self::find()->select('mobile')->asArray()->where(['mobile' => $mobile])->andWhere("id!={$user->id}")->one();
        if ($check) {
            $code = 1019;
            return $code;
        }
        $code = 200;
        return $code;
    }

    /**
     * Check if identity card no existing
     *
     * @param string $identityNo identity card no
     * @return bool
     */
    public static function checkIdentityExisting($identityNo)
    {
        $res = self::find()->where(['identity_no' => $identityNo])->exists();
        return $res;
    }

    /**
     * @param $user
     * @param $postData
     * @return int
     */
    public static function CheckOrderJurisdiction($user, $postData)
    {
        if (!array_key_exists('order_no', $postData)) {
            $code = 1000;
            return $code;
        }
        $GoodsOrder = GoodsOrder::find()
            ->where(['order_no' => $postData['order_no']])
            ->one();
        if ($user->id != $GoodsOrder->user_id) {
            $code = 1034;
            return $code;
        }
        $code = 200;
        return $code;
    }

    /**
     * Create huan xin user by username
     *
     * @param string $username username
     * @param int $retryTimes retry times default 3
     * @return bool
     */
    public static function createHuanXinUser($username, $retryTimes = self::RETYR_TIMES_CREATE_HUANXIN_USER)
    {
        $success = false;

        $username = trim($username);
        if (!$username) {
            return $success;
        }

        $hxPwd = Yii::$app->params['chatOptions']['user_password_default']; // StringService::getUniqueStringBySalt(substr($username, 0, 11));
        for ($i = 0; $i < $retryTimes; $i++) {
            $res = (new ChatService)->createUser($username, $hxPwd);
            if (!array_key_exists('error', $res)) {
                $success = true;
                break;
            }
            usleep(10);
        }

        if (!$success) {
            $event = Yii::$app->params['events']['3rd']['failed']['createHuanxinUser'];
            new EventHandleService($event, $username);
            Yii::$app->trigger($event);
        }

        return $success;
    }

    /**
     * Reset huan xin user password
     *
     * @param string $username username
     * @param string $hxPwd user huan xin password
     * @param int $retryTimes retry times default 3
     * @return bool
     */
    public static function resetHuanXinUserPwd($username, $hxPwd, $retryTimes = self::RETYR_TIMES_CREATE_HUANXIN_USER)
    {
        $success = false;

        $username = trim($username);
        $hxPwd = trim($hxPwd);
        if (!$username || !$hxPwd) {
            return $success;
        }

        for ($i = 0; $i < $retryTimes; $i++) {
            $res = (new ChatService)->resetPassword($username, $hxPwd);
            if (!array_key_exists('error', $res)) {
                $success = true;
                break;
            }
            usleep(10);
        }

        if (!$success) {
            $event = Yii::$app->params['events']['3rd']['failed']['resetHuanxinUserPassword'];
            new EventHandleService($event, $username);
            Yii::$app->trigger($event);
        }

        return $success;
    }

    /**
     * Generate huan xin password
     *
     * @param string $mobile
     * @param string $restrationId
     * @param string $hxUsername default empty
     * @return string
     */
    public static function generateHxPwd($mobile, $restrationId, $hxUsername = '')
    {
        $salt = Yii::$app->params['security']['salt'];
        $w = date('w');
        $wCnt = substr_count($mobile, $w);
        $hour = 7 + $w;
        $minute = 13 + $wCnt;
        $time = strtotime($hour . ':' . $minute);
        $sum = $w + $wCnt + strlen($salt) + strlen($restrationId) + $time;
        return md5($mobile . md5(md5($restrationId) . $hxUsername) . $sum . $salt);
    }

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * Reset user's new mobile
     *
     * @param int $mobile mobile
     * @param User $operator operator
     * @return bool
     */
    public function resetMobile($mobile, User $operator)
    {
        if ($this->mobile == $mobile) {
            $code = 200;
            return $code;
        }

        $this->mobile = $mobile;
        if (!$this->validate()) {
            $code = ModelService::uniqueError($this, 'mobile') ? 1019 : 1000;
            return $code;
        }

        $code = 500;
        try {
            $tran = Yii::$app->db->beginTransaction();
            if (!$this->save()) {
                $tran->rollBack();
                return $code;
            }

            if (!UserMobile::addByUserAndOperator($this, $operator)) {
                $tran->rollBack();
                return $code;
            }

            $tran->commit();
            $code = 200;
            return $code;
        } catch (\Exception $e) {
            $tran->rollBack();
            return $code;
        }
    }

    /**
     * Logout(admin)
     */
    public function adminLogout()
    {
//        if (!empty(Yii::$app->session[self::LOGIN_ORIGIN_ADMIN])) {
//            unset(Yii::$app->session[self::LOGIN_ORIGIN_ADMIN]);
//        }
        Yii::$app->user->logout();
        $this->authKeyAdmin = '';
        $this->save();
    }

    /**
     * Logout(app)
     */
    public function logout()
    {
//        if (!empty(Yii::$app->session[self::LOGIN_ORIGIN_APP])) {
//            unset(Yii::$app->session[self::LOGIN_ORIGIN_APP]);
//        }
        Yii::$app->user->logout();
        $this->authKey = '';
        $this->save();
    }

    /**
     * Reset nickname
     *
     * @param string $nickname nickname
     * @return int
     */
    public function resetNickname($nickname)
    {
        if (!$nickname) {
            $code = 1000;
            return $code;
        }

        if ($this->nickname != Yii::$app->params['user']['default_nickname']) {
            $code = 1017;
            return $code;
        }

        if (User::find()->where(['nickname' => $nickname])->exists()) {
            $code = 1018;
            return $code;
        }

        $this->nickname = $nickname;
        if (!$this->save()) {
            $code = 500;
            return $code;
        }

        $code = 200;
        return $code;
    }

    /**
     * Reset signature
     *
     * @param string $signature nickname
     * @return int
     */
    public function resetSignature($signature)
    {
//        if (!$signature) {
//            $code = 1000;
//            return $code;
//        }

        if ($this->signature == $signature) {
            $code = 200;
            return $code;
        }

        $this->signature = $signature;
        if (!$this->save()) {
            $code = 500;
            return $code;
        }

        $code = 200;
        return $code;
    }

    /**
     * Reset district
     *
     * @param string $districtCode district code
     * @return int
     */
    public function resetDistrict($districtCode)
    {
        $district = District::validateDistrictCode($districtCode);
        if (!$district) {
            $code = 1000;
            return $code;
        }

        if ($this->district_code == $districtCode) {
            $code = 200;
            return $code;
        }

        $this->district_code = $districtCode;
        $this->district_name = District::fullNameByCode($districtCode);
        if (!$this->save()) {
            $code = 500;
            return $code;
        }

        $code = 200;
        return $code;
    }

    /**
     * Reset birthday
     *
     * @param string $birthday birthday
     * @return int
     */
    public function resetBirthday($birthday)
    {
        if (!StringService::isBirthday($birthday)) {
            $code = 1000;
            return $code;
        }

        if ($this->birthday == $birthday) {
            $code = 200;
            return $code;
        }

        $this->birthday = $birthday;
        if (!$this->save()) {
            $code = 500;
            return $code;
        }

        $code = 200;
        return $code;
    }

    /**
     * Reset gender
     *
     * @param string $gender gender
     * @return int
     */
    public function resetGender($gender)
    {
        if (!in_array($gender, array_keys(self::SEXES))) {
            $code = 1000;
            return $code;
        }

        if ($this->gender == $gender) {
            $code = 200;
            return $code;
        }

        $this->gender = $gender;
        if (!$this->save()) {
            $code = 500;
            return $code;
        }

        $code = 200;
        return $code;
    }

    /**
     * Reset icon
     *
     * @param string $icon icon
     * @return int
     */
    public function resetIcon($icon)
    {
        if ($this->icon == $icon) {
            $code = 200;
            return $code;
        }

        $this->icon = $icon;
        if (!$this->save()) {
            $code = 500;
            return $code;
        }

        $code = 200;
        return $code;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // mobile and password are both required
            [['mobile', 'password'], 'required'],
            ['mobile', 'match', 'pattern' => '/^1[34578]{1}\d{9}$/'],
            ['mobile', 'unique'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            [['identity_no', 'legal_person', 'identity_card_front_image', 'identity_card_back_image'], 'string'],
            ['legal_person', 'string', 'length' => [1, 15]],
            ['nickname', 'string', 'length' => [self::NICKNAME_MIN_LEN, self::NICKNAME_MAX_LEN]],
            ['signature', 'string', 'length' => [1, self::SIGNATURE_MAX_LEN]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    /**
     * Validates identity info
     *
     * @param ActiveRecord $operator operator default null
     * @return bool
     */
    public function validateIdentity(ActiveRecord $operator = null)
    {
        if ($operator) {
            return true;
        }

        if (!$this->identity_card_front_image
            || !$this->identity_card_back_image
            || !$this->validateIdentityNo()
            || !$this->validateLegalPerson()
        ) {
            return false;
        }

        return true;
    }

    /**
     * Validates identity card no
     *
     * @return bool
     */
    public function validateIdentityNo()
    {
        $attr = 'identity_no';
        if (!$this->$attr) {
            return false;
        }

        return StringService::checkIdentityCardNo($this->$attr);
    }

    /**
     * Validates legal person
     *
     * @return bool
     */
    public function validateLegalPerson()
    {
        $attr = 'legal_person';
        if (!$this->$attr) {
            return false;
        }

        return mb_strlen($this->$attr) <= self::LEN_MAX_FIELDS[$attr];
    }

    /**
     * Check login
     *
     * @return bool
     */
    public function checkLogin()
    {
        if ($this->deadtime > 0
            || !$this->authKey
            || empty(Yii::$app->session[self::LOGIN_ORIGIN_APP])
        ) {
            return false;
        }

        return true;
    }

    /**
     * Check admin login
     *
     * @return bool
     */
    public function checkAdminLogin()
    {
        if ($this->deadtime > 0
            || !$this->authKeyAdmin
            || empty(Yii::$app->session[self::LOGIN_ORIGIN_ADMIN])
        ) {
            return false;
        }

        return true;
    }

    /**
     * Set daily forgot password count
     */
    public function setDailyForgotPwdCnt()
    {
        $cache = Yii::$app->cache;
        $key = self::CACHE_PREFIX_DAILY_FORGOT_PWD_CNT . $this->id;
        $cnt = (int)$cache->get($key);
        $todayEnd = strtotime(StringService::startEndDate('today')[1]);
        $cache->set($key, ++$cnt, $todayEnd - time());
    }

    /**
     * Set daily reset password count
     */
    public function setDailyResetPwdCnt()
    {
        $cache = Yii::$app->cache;
        $key = self::CACHE_PREFIX_DAILY_RESET_PWD_CNT . $this->id;
        $cnt = (int)$cache->get($key);
        $todayEnd = strtotime(StringService::startEndDate('today')[1]);
        $cache->set($key, ++$cnt, $todayEnd - time());
    }

    /**
     * Check daily forgot password count
     *
     * @return bool
     */
    public function checkDailyForgotPwdCnt()
    {
        return $this->getDailyForgotPwdCnt() < Yii::$app->params['user']['daily_forgot_pwd_cnt_max'];
    }

    /**
     * Get daily forgot password count
     *
     * @return int
     */
    public function getDailyForgotPwdCnt()
    {
        $cache = Yii::$app->cache;
        $key = self::CACHE_PREFIX_DAILY_FORGOT_PWD_CNT . $this->id;
        return (int)$cache->get($key);
    }

    /**
     * Check daily reset password count
     *
     * @return bool
     */
    public function checkDailyResetPwdCnt()
    {
        return $this->getDailyResetPwdCnt() < Yii::$app->params['user']['daily_reset_pwd_cnt_max'];
    }

    /**
     * Get daily reset password count
     *
     * @return int
     */
    public function getDailyResetPwdCnt()
    {
        $cache = Yii::$app->cache;
        $key = self::CACHE_PREFIX_DAILY_RESET_PWD_CNT . $this->id;
        return (int)$cache->get($key);
    }

    /**
     * Certificate user
     *
     * @param string $identityNo
     * @param string $legalPerson
     * @param string $identityCardFrontImage
     * @param string $identityCardBackImage
     * @param ActiveRecord $operator operator default null
     * @return int
     */
    public function certificate($identityNo, $legalPerson, $identityCardFrontImage, $identityCardBackImage, ActiveRecord $operator = null)
    {
        $this->refresh();

//        if ($this->identity_no) {
//            return 200;
//        }

        $this->legal_person = $legalPerson;
        $this->identity_no = $identityNo;
        $this->identity_card_front_image = $identityCardFrontImage;
        $this->identity_card_back_image = $identityCardBackImage;
        if (!$this->validateIdentityNo() || !$this->validateLegalPerson()) {
            return 1000;
        }

        $tran = Yii::$app->db->beginTransaction();
        $code = 500;
        try {
            if (!$this->save()) {
                $tran->rollBack();
                return $code;
            }

            $userRole = UserRole::find()
                ->where([
                    'user_id' => $this->id,
                    'role_id' => Yii::$app->params['ownerRoleId'],
                ])
                ->one();
            if ($userRole) {
                $userRole->review_apply_time = time();
                if ($operator) {
                    $userRole->review_status = Role::AUTHENTICATION_STATUS_APPROVED;
                    $userRole->reviewer_uid = $operator->id;
                } else {
                    $userRole->review_status = Role::AUTHENTICATION_STATUS_IN_PROCESS;
                }

                if (!$userRole->save()) {
                    $tran->rollBack();
                    return $code;
                }
            }

            $tran->commit();
            return 200;
        } catch (\Exception $e) {
            $tran->rollBack();
            return $code;
        }
    }

    /**
     * View identity(lhzz)
     *
     * @return array
     */
    public function viewIdentityLhzz()
    {
        $modelData = ModelService::selectModelFields($this, self::FIELDS_IDENTITY_LHZZ);
        $viewData = $modelData
            ? array_merge($modelData, $this->_extraData(self::FIELDS_IDENTITY_LHZZ_EXTRA))
            : $modelData;
        self::_formatData($viewData);
        return $viewData;
    }

    /**
     * Get extra fields
     *
     * @access private
     * @param array $extraFields extra fields
     * @return array
     */
    private function _extraData(array $extraFields)
    {
        $extraData = [];

        foreach ($extraFields as $extraField) {
            switch ($extraField) {
                case 'address':
                    $extraData[$extraField] = $this->getFullAddress();
                    break;
                case 'review_status':
                    $userRole = UserRole::find()
                        ->where(['user_id' => $this->id, 'role_id' => Yii::$app->params['ownerRoleId']])
                        ->one();
                    if ($userRole) {
                        $extraData[$extraField] = $userRole->review_status;
                        $extraData[$extraField . ModelService::SUFFIX_FIELD_DESCRIPTION] = Yii::$app->params['reviewStatuses'][$userRole->review_status];
                    }
                    break;
                case 'review_remark':
                    $userRole = UserRole::find()
                        ->where(['user_id' => $this->id, 'role_id' => Yii::$app->params['ownerRoleId']])
                        ->one();
                    if ($userRole) {
                        $extraData[$extraField] = $userRole->review_remark;
                    }
                    break;
                case 'review_time':
                    $userRole = UserRole::find()
                        ->where(['user_id' => $this->id, 'role_id' => Yii::$app->params['ownerRoleId']])
                        ->one();
                    if ($userRole) {
                        $extraData[$extraField] = $userRole->review_time
                            ? date('Y-m-d H:i', $userRole->review_time)
                            : '';
                    }
                    break;
                case 'old_nickname':
                    $extraData[$extraField] = $this->getOldNickname();
                    break;
                case 'role_names':
                    $extraData[$extraField] = UserRole::findRoleNamesByUserId($this->id);
                    break;
                case 'status_operator':
                    $userStatus = UserStatus::find()->where(['uid' => $this->id])->orderBy(['id' => SORT_DESC])->one();
                    if ($userStatus) {
                        $extraData[$extraField] = $userStatus->op_username;
                    }
                    break;
                case 'status_remark':
                    $userStatus = UserStatus::find()->where(['uid' => $this->id])->orderBy(['id' => SORT_DESC])->one();
                    if ($userStatus) {
                        $extraData[$extraField] = $userStatus->remark;
                    }
                    break;
                case 'close_time':
                    $userStatus = UserStatus::find()
                        ->where(['uid' => $this->id, 'status' => self::STATUS_OFFLINE])
                        ->orderBy(['id' => SORT_DESC])
                        ->one();
                    if ($userStatus) {
                        $extraData[$extraField] = date('Y-m-d H:i', $userStatus->create_time);
                    }
                    break;
            }
        }

        return $extraData;
    }

    /**
     * Get user full address
     *
     * @return string
     */
    public function getFullAddress()
    {
        $fullAddress = '';
        $userAddress = UserAddress::find()->where(['uid' => $this->id])->one();
        if ($userAddress) {
            $fullAddress = District::fullNameByCode($userAddress->district) . $userAddress->region;
        }
        return $fullAddress;
    }

    /**
     * Get old nickname
     *
     * @return string
     */
    public function getOldNickname()
    {
        return $this->nickname != Yii::$app->params['user']['default_nickname']
            ? Yii::$app->params['user']['default_nickname']
            : '';
    }

    /**
     * Format data
     *
     * @param array $data data to format
     */
    private static function _formatData(array &$data)
    {
        if (isset($data['gender'])) {
            $data['gender'] = self::SEXES[$data['gender']];
        }

        if (isset($data['birthday'])) {
            $data['birthday'] = StringService::formatBirthday($data['birthday']);
        }

        if (isset($data['balance'])) {
            $data['balance'] = StringService::formatPrice($data['availableamount']/100);
            unset( $data['availableamount']);
        }

        if (isset($data['create_time'])) {
            $data['create_time'] = date('Y-m-d H:i', $data['create_time']);
        }

        if (isset($data['deadtime'])) {
            $data['status'] = self::STATUSES[$data['deadtime'] > 0 ? self::STATUS_OFFLINE : self::STATUS_ONLINE];
            $data['deadtime'] = date('Y-m-d H:i', $data['deadtime']);
        }
    }

    /**
     * View identity(app)
     *
     * @return array
     */
    public function viewIdentity()
    {
        $modelData = ModelService::selectModelFields($this, self::FIELDS_VIEW_IDENTITY);
        $viewData = $modelData
            ? array_merge($modelData, $this->_extraData(self::FIELDS_VIEW_IDENTITY_EXTRA))
            : $modelData;
        self::_formatData($viewData);
        return $viewData;
    }

    /**
     * Get view data
     *
     * @return array
     */
    public function view()
    {
        $modelData = ModelService::selectModelFields($this, self::FIELDS_USER_CENTER_MODEL);
        $viewData = $modelData
            ? array_merge($modelData, $this->_extraData(self::FIELDS_USER_CENTER_EXTRA))
            : $modelData;
        self::_formatData($viewData);
        return $viewData;
    }

    /**
     * Get view data(lhzz)
     *
     * @return array
     */
    public function viewLhzz()
    {
        $modelData = ModelService::selectModelFields($this, self::FIELDS_USER_DETAILS_MODEL_LHZZ);
        $viewData = $modelData
            ? array_merge($modelData, $this->_extraData(self::FIELDS_USER_DETAILS_MODEL_LHZZ_EXTRA))
            : $modelData;
        self::_formatData($viewData);
        return $viewData;
    }

    /**
     * Set cache after updated user model
     *
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $events = Yii::$app->params['events'];
            $event = $events['async'];
            $data = [
                'event' => [
                    'name' => $events['user']['register'],
                    'data' => $this->mobile,
                ],
            ];
            new EventHandleService($event, $data);
        }

        $key = self::CACHE_PREFIX . $this->id;
        $cache = Yii::$app->cache;
        $cache->set($key, $this);
    }

    /**
     * Do some ops after login
     *
     * @param int $roleId role id default 0
     * @return bool
     */
    public function afterLogin($roleId = 0)
    {
        $this->login_time = time();
        $roleId && $this->login_role_id = $roleId;

        $sessionId = Yii::$app->session->id;

        if (!YII_DEBUG) {
            $appOrAdminAuthKey = $roleId ? $this->authKeyAdmin : $this->authKey;
            $sessFile = Yii::$app->fileCache->cachePath . '/' . self::PREFIX_SESSION_FILENAME . $appOrAdminAuthKey;
            if ($appOrAdminAuthKey != $sessionId && file_exists($sessFile)) {
                @unlink($sessFile);
            }
        }

        if ($roleId) {
            Yii::$app->session[self::LOGIN_ORIGIN_ADMIN] = $this->id;
            $this->oldAuthKeyAdmin = $this->authKeyAdmin;
            $this->authKeyAdmin = $sessionId;
        } else {
            Yii::$app->session[self::LOGIN_ORIGIN_APP] = $this->id;
            $this->oldAuthKey = $this->authKey;
            $this->authKey = $sessionId;
        }
        Yii::$app->session[self::LOGIN_ROLE_ID] = $roleId ? $roleId : Yii::$app->params['ownerRoleId'];

        return $this->save();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * Switch user role
     *
     * @param int $roleId role id
     * @return int
     */
    public function switchRole($roleId)
    {
        $roleId = (int)$roleId;

        if ($this->last_role_id_app == $roleId) {
            return 200;
        }

        if (!$roleId
            || in_array($roleId, [Yii::$app->params['lhzzRoleId']])
            || !in_array($roleId, UserRole::findRoleIdsByUserIdAndReviewStatus($this->id))
        ) {
            return 1000;
        }

        $this->last_role_id_app = $roleId;
        return $this->save() ? 200 : 500;
    }
}
