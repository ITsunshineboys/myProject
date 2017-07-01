<?php

namespace app\models;

use app\services\StringService;
use app\services\SmValidationService;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\Json;

class User extends ActiveRecord implements IdentityInterface
{
    const CACHE_PREFIX = 'user_';
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

        if (!$user->validate()) {
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
            [['identity_no', 'legal_person', 'identity_card_front_image', 'identity_card_back_image'], 'string']
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
     * Validates identity card no
     *
     * @return bool
     */
    public function validateIdentityNo()
    {
        return StringService::checkIdentityCardNo($this->identity_no);
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
     * @return bool
     */
    public function afterLogin(Role $role)
    {
        $this->login_time = time();
        $this->login_role_id = $role->id;
        return $this->save();
    }
}
