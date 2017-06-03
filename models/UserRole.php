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
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'user_role';
    }

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
        $rolesStatus = [];

        $userRoles = self::find()->where("user_id = {$userId} and role_id <> " . Yii::$app->params['ownerRoleId'])->all();
        $db = Yii::$app->db;
        $roleIds = [];
        foreach ($userRoles as $userRole) {
            $role = Role::findOne($userRole->role_id);
            if (!$role) {
                continue;
            }

            $detail = $db->createCommand("select * from {{%" . $role->detail_table . "}} where uid = {$userRole->user_id}")->queryOne();
            $status = Role::AUTHENTICATION_STATUS_NO_APPLICATION;
            if ($detail) {
                $detail = (object)$detail;
                if (empty($detail->approve_reason) && empty($detail->reject_reason)) {
                    $status = Role::AUTHENTICATION_STATUS_IN_PROCESS;
                } elseif ($detail->approve_reason) {
                    $status = Role::AUTHENTICATION_STATUS_APPROVED;
                } else {
                    $status = Role::AUTHENTICATION_STATUS_REJECTED;
                }
            }

            $rolesStatus[] = [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'status' => $status,
                'status_desc' => Role::$authenticationStatus[$status],
            ];

            $roleIds[] = $role->id;
        }

        foreach (Role::appRoles() as $role) {
            if ($role['id'] != Yii::$app->params['ownerRoleId'] && !in_array($role['id'], $roleIds)) {
                $status = Role::AUTHENTICATION_STATUS_NO_APPLICATION;
                $rolesStatus[] = [
                    'role_id' => $role['id'],
                    'role_name' => $role['name'],
                    'status' => $status,
                    'status_desc' => Role::$authenticationStatus[$status],
                ];
            }
        }

        usort($rolesStatus, function ($a, $b) {
            return $a['role_id'] - $b['role_id'];
        });

        return $rolesStatus;
    }
}