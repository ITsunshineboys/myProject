<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        $key = 'user_' . $id;
        $cache = Yii::$app->cache;
        $user = $cache->get($key);
        if (!$user) {
            $user = self::find()->where(['id' => $id])->asArray()->one();
            if ($user) {
                $cache->set($key, $user);
            }
        }

        if (!$user) {
            return null;
        }

        return new static($user);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = self::find()->where(['accessToken' => $token])->asArray()->one();

        if (!$user) {
            return null;
        }

        $key = 'user_' . $user['id'];
        $cache = Yii::$app->cache;
        $cache->set($key, $user);

        return new static($user);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $user = self::find()->where(['username' => $username])->asArray()->one();

        if (!$user) {
            return null;
        }

        $key = 'user_' . $user['id'];
        $cache = Yii::$app->cache;
        $cache->set($key, $user);

        return new static($user);
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
}
