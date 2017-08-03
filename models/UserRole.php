<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/27/17
 * Time: 2:08 PM
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\Role;

class UserRole extends ActiveRecord
{
    const CACHE_KEY_PREFIX_ROLES_STATUS = 'roles_status_';

    /**
     * Get roles status by user id
     *
     * @param $userId
     * @return array
     */
    public static function rolesStatus($userId)
    {
        $key = self::CACHE_KEY_PREFIX_ROLES_STATUS . $userId;
        $cache = Yii::$app->cache;
        $rolesStatus = $cache->get($key);
        if (!$rolesStatus) {
            $rolesStatus = self::_rolesStatus($userId);
            if ($rolesStatus) {
                $cache->set($key, $rolesStatus);
            }
        }

        return $rolesStatus;
    }

    /**
     * Get roles status by user id
     *
     * @param $userId
     * @return array
     */
    private static function _rolesStatus($userId)
    {
        foreach (Role::appRoles() as $role) {
            $status = Role::AUTHENTICATION_STATUS_NO_APPLICATION;
            $rolesStatus[] = [
                'role_id' => $role['id'],
                'role_name' => $role['name'],
                'status' => $status,
                'status_desc' => Role::$authenticationStatus[$status],
            ];
        }

        usort($rolesStatus, function ($a, $b) {
            return $a['role_id'] - $b['role_id'];
        });

        return $rolesStatus;
    }

    public static function findRolesByUserId($userId)
    {
//        self::find()
//            ->select([])
//            ->where(['user_id' => $user->id])->asArray()->all();
        $roleTbl = Role::tableName();
        $userRoleTbl = UserRole::tableName();
        $sql = "select role.name from {{%{$roleTbl}}} r, {{%{$userRoleTbl}}} ur";
//        $sql .= " where r.id = ur.role_id and ur.user.id = {$userId} and review_status = " . self::;
    }

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'user_role';
    }
}