<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Role extends ActiveRecord
{
    const CACHE_KEY_ALL = 'all_roles';
    const AUTHENTICATION_STATUS_NO_APPLICATION = 1;
    const AUTHENTICATION_STATUS_IN_PROCESS = 2;
    const AUTHENTICATION_STATUS_APPROVED = 3;
    const AUTHENTICATION_STATUS_REJECTED = 4;

    public static $authenticationStatus = [
        self::AUTHENTICATION_STATUS_NO_APPLICATION => '未申请',
        self::AUTHENTICATION_STATUS_IN_PROCESS => '审核中',
        self::AUTHENTICATION_STATUS_APPROVED => '已通过',
        self::AUTHENTICATION_STATUS_REJECTED => '不通过',
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'role';
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

        $cache = Yii::$app->cache;
        $cache->delete(self::CACHE_KEY_ALL);
    }

    /**
     * Get all roles
     *
     * @return array|mixed|ActiveRecord[]
     */
    public static function allRoles()
    {
        $key = self::CACHE_KEY_ALL;
        $cache = Yii::$app->cache;
        $roles = $cache->get($key);
        if (!$roles) {
            $roles = self::find()->where([])->asArray()->orderBy('id')->all();
            if ($roles) {
                $cache->set($key, $roles);
            }
        }
        return $roles;
    }

    /**
     * Check if available role id
     *
     * @param int $roleId role id
     * @return bool
     */
    public static function activeRole($roleId)
    {
        $roleId = (int) $roleId;
        if ($roleId <= 0) {
            return false;
        }

        foreach (self::allRoles() as $role) {
            if ($role['id'] == $roleId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get roles for app
     *
     * @return array
     */
    public static function appRoles()
    {
        return array_slice(self::allRoles(), 1);
    }
}
