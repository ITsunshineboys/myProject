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
use app\services\ModelService;
use yii\db\Query;

class UserRole extends ActiveRecord
{
    const CACHE_KEY_PREFIX_ROLES_STATUS = 'roles_status_';
    const FIELDS_EXTRA = [];
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
            $userRole = self::find()->where(['user_id' => $userId, 'role_id' => $role['id']])->one();
            $userRole && $status = $userRole->review_status;
            $rolesStatus[] = [
                'role_id' => $role['id'],
                'role_name' => $role['name'],
                'status' => $status,
                'status_desc' => Yii::$app->params['reviewStatuses'][$status],
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
        $ownerRoleId = Yii::$app->params['ownerRoleId'];
        $sql = "select r.name from {{%{$roleTbl}}} r, {{%{$userRoleTbl}}} ur";
        $sql .= " where r.id = ur.role_id and ur.user_id = {$userId} and (ur.role_id = {$ownerRoleId} or review_status = " . Role::AUTHENTICATION_STATUS_APPROVED . ")";
        return Yii::$app->db->createCommand($sql)->queryColumn();
    }

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'user_role';
    }

    /**
     * Get total number of authorized users
     *
     * @return int
     */
    public static function totalAuthorizedUserNumber()
    {
        return (int)self::find()
            ->where([
//                'role_id' => Yii::$app->params['ownerRoleId'],
                'review_status' => Role::AUTHENTICATION_STATUS_APPROVED
            ])
            ->count();
    }

    /**
     * Get total number of users
     *
     * @return int
     */
    public static function totalNumber()
    {
        return (int)self::find()->count();
    }

    /**
     * Get role user by user model and role id
     *
     * @param User $user user model
     * @param int $roleId role id
     * @return ActiveRecord|null
     */
    public static function roleUser(User $user, $roleId)
    {
        if ($roleId == Yii::$app->params['ownerRoleId']) {
            return $user;
        }

        $role = Role::findOne($roleId);
        if ($role) {
            $detail = Yii::createObject(__NAMESPACE__ . '\\' . $role->detail_model);
            return $detail::find()->where(['uid' => $user->id])->one();
        }
    }

    /**
     * @param int $userId user id
     * @param int $reviewStatus review status default 2 anthorized
     * @return array
     */
    public static function findRoleIdsByUserIdAndReviewStatus($userId, $reviewStatus = Role::AUTHENTICATION_STATUS_APPROVED)
    {
        return self::find()
            ->select(['role_id'])
            ->where(['user_id' => $userId, 'review_status' => $reviewStatus])
            ->column();
    }

    /**
     * @param array $where
     * @param array $select
     * @param int $page
     * @param $size
     * @param string $orderBy
     * @return array
     */
    public static function paginationBySupplier($where = [], $select = [], $page = 1, $size = Supplier::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC')
    {

        $select = array_diff($select, self::FIELDS_EXTRA);
        $offset = ($page - 1) * $size;
        $supplierList = (new Query())
            ->from(self::tableName().' as U')
            ->leftJoin(Supplier::tableName(). ' as S','U.user_id=S.uid')
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->all();
        $total = self::find()->where($where)->count();
        return ModelService::pageDeal($supplierList, $total, $page, $size);
    }


}