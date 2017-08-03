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

    /**
     * Find user's role name(s) by user id
     *
     * @param int $userId user id
     * @return array
     */
    public static function findRoleNamesByUserId($userId)
    {
        $roleTbl = Role::tableName();
        $userRoleTbl = UserRole::tableName();
        $sql = "select r.name from {{%{$roleTbl}}} r, {{%{$userRoleTbl}}} ur";
        $sql .= " where r.id = ur.role_id and ur.user_id = {$userId} and review_status = " . Role::AUTHENTICATION_STATUS_APPROVED;
        return Yii::$app->db->createCommand($sql)->queryColumn();
    }

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'user_role';
    }
}