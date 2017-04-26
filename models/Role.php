<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Role extends ActiveRecord
{
    const CACHE_KEY_APP = 'app_roles';
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
        foreach ([self::CACHE_KEY_APP, self::CACHE_KEY_ALL] as $key) {
            $cache->delete($key);
        }
    }

    public static function allRoles()
    {
        $key = self::CACHE_KEY_ALL;
        $cache = Yii::$app->cache;
        $roles = $cache->get($key);
        if (!$roles) {
            $roles = self::find()->where([])->asArray()->all();
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

    public function appRoles()
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
}
