<?php

namespace app\models;

use app\services\StringService;
use app\services\SmValidationService;
use app\services\ModelService;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    const CACHE_PREFIX = 'user_';
    const CACHE_PREFIX_DAILY_FORGOT_PWD_CNT = 'daily_forgot_pwd_cnt_';
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
    const NICKNAME_MIN_LEN = 4;
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
    ];
    const FIELDS_USER_CENTER_EXTRA = [
        'address',
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
    const STATUS_ONLINE = 1; // 开启

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
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'user';
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
            || strlen(($data['password'])) < self::PASSWORD_MIN_LEN
            || strlen(($data['password'])) > self::PASSWORD_MAX_LEN
        ) {
            return $code;
        }

        if ($checkValidationCode) {
            if (empty($data['validation_code'])) {
                return $code;
            }

            if (!SmValidationService::validCode($data['mobile'], $data['validation_code'])) {
                $code = 1002;
                return $code;
            }
        }

        $user = new self;
        $user->attributes = $data;
        $user->password = Yii::$app->security->generatePasswordHash($user->password);
        $user->create_time = $user->login_time = time();
        $user->login_role_id = Yii::$app->params['ownerRoleId'];
        $user->nickname = Yii::$app->params['user']['default_nickname'];

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
        $this->authKeyAdmin = '';
        $this->save();
    }

    /**
     * Logout(app)
     */
    public function logout()
    {
        $this->authKey = '';
        $this->save();
    }

    /**
     * Reset nickname action
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
     * Reset signature action
     *
     * @param string $signature nickname
     * @return int
     */
    public function resetSignature($signature)
    {
        if (!$signature) {
            $code = 1000;
            return $code;
        }

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
     * Reset district action
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
        $this->district_name = $district->name;
        if (!$this->save()) {
            $code = 500;
            return $code;
        }

        $code = 200;
        return $code;
    }

    /**
     * Reset birthday action
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
     * Reset gender action
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
    public function getAuthKey()
    {
        return $this->authKey;
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
     * @return bool
     */
    public function validateIdentity()
    {
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
     * Check login by authentication key
     *
     * @return bool
     */
    public function checkLogin()
    {
        $this->refresh();

        if ($this->deadtime > 0 ||
            (!$this->authKey && !$this->authKeyAdmin)
        ) {
            return false;
        }

        if ('other' == StringService::userAgent()) { // from pc
            return !empty($this->authKeyAdmin);
        } else { // from app
            return !empty($this->authKey);
        }
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
     * Check daily forgot password count
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
        $this->_formatData($viewData);
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
                    $userAddress = UserAddress::find()->where(['uid' => $this->id])->one();
                    if ($userAddress) {
                        $extraData[$extraField] = District::fullNameByCode($userAddress->district) . $userAddress->region;
                    }
                    break;
                case 'review_status':
                    $userRole = UserRole::find()
                        ->where(['user_id' => $this->id, 'role_id' => Yii::$app->params['ownerRoleId']])
                        ->one();
                    if ($userRole) {
                        $extraData[$extraField] = Yii::$app->params['reviewStatuses'][$userRole->review_status];
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
            }
        }

        return $extraData;
    }

    /**
     * Format data
     *
     * @param array $data data to format
     */
    private function _formatData(array &$data)
    {
        if (isset($data['gender'])) {
            $data['gender'] = self::SEXES[$data['gender']];
        }

        if (isset($data['birthday'])) {
            $data['birthday'] = StringService::formatBirthday($data['birthday']);
        }

        if (isset($data['balance'])) {
            $data['balance'] /= 100;
        }
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
        $this->_formatData($viewData);
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
        if ($roleId) {
            $this->authKeyAdmin = $sessionId;
        } else {
            $this->authKey = $sessionId;
        }

        return $this->save();
    }
}
