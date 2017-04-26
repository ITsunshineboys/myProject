<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Role extends ActiveRecord
{
    const CACHE_KEY_ALL = 'all_roles';

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

    public static function appRoles()
    {
        return array_slice(self::allRoles(), 1);
    }
}
