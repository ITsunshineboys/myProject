<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/27/17
 * Time: 2:08 PM
 */

namespace app\models;

use app\services\StringService;
use Yii;
use yii\db\ActiveRecord;
use yii\log\Logger;

class RolePermission extends ActiveRecord
{
    const CACHE_KEY_ROLES_PERMISSIONS = 'roles_permissions';
    const CACHE_KEY_ROLES = 'roles';
    const CACHE_KEY_PREFIX_ROLE_PERMISSIONS = 'role_permissions_';

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'role_permission';
    }

    /**
     * Get all roles' permissions
     *
     * @return array
     */
    public static function all()
    {
        $cache = Yii::$app->cache;
        $rolesPermissions = $cache->get(self::CACHE_KEY_ROLES_PERMISSIONS);
        if (!$rolesPermissions) {
            $rolesPermissions = self::find()->asArray()->all();
            $cache->set(self::CACHE_KEY_ROLES_PERMISSIONS, $rolesPermissions);
        }
        return $rolesPermissions;
    }

    /**
     * Get all role ids
     *
     * @return array
     */
    public static function roles()
    {
        $cache = Yii::$app->cache;
        $roles = $cache->get(self::CACHE_KEY_ROLES);
        if (!$roles) {
            $roles = array_map(function ($value) {
                return $value['role_id'];
            }, self::find()->select(['role_id'])->asArray()->all());
            $cache->set(self::CACHE_KEY_ROLES, array_unique($roles));
        }
        return $roles;
    }

    /**
     * Get role permissions by roleid
     *
     * @param int $roleId role id
     * @return array
     */
    public static function rolePermissions($roleId)
    {
        $cache = Yii::$app->cache;
        $cacheKey = self::CACHE_KEY_PREFIX_ROLE_PERMISSIONS . $roleId;
        $rolePermissions = $cache->get($cacheKey);
        if (!$rolePermissions) {
            $rolePermissions = array_map(function ($value) {
                return $value['controller'] . '/' . $value['action'];
            }, self::find()->select(['controller', 'action'])->where(['role_id' => $roleId])->asArray()->all());
            $cache->set($cacheKey, $rolePermissions);
        }
        if (YII_DEBUG) {
            StringService::writeLog('role_permissions', json_encode($rolePermissions), '', Logger::LEVEL_INFO);
        }
        return $rolePermissions;
    }
}