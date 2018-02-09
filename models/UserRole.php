<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/27/17
 * Time: 2:08 PM
 */

namespace app\models;

use Yii;
use yii\data\Pagination;
use yii\db\ActiveRecord;
use app\models\Role;
use app\services\ModelService;
use yii\db\Query;

class UserRole extends ActiveRecord
{
    const CACHE_KEY_PREFIX_ROLES_STATUS = 'roles_status_';
    const FIELDS_EXTRA = [
        'user_id',
        'review_time',
        'review_status',
        'review_remark',
        'reviewer_uid',
    ];
    const REVIEW_STATUS = [
        self::REVIEW_BE_AUDITED => '待审核',
        self::REVIEW_DISAGREE => '审核不通过',
        self::REVIEW_AGREE => '审核通过',
    ];
    const REVIEW_AGREE = 2;
    const REVIEW_DISAGREE = 1;
    const  REVIEW_BE_AUDITED = 0;

    /**
     * Get review status by user id and role id
     *
     * @param $userId user id
     * @param $roleId role id
     * @return array
     */
    public static function getReviewStatus($userId, $roleId)
    {
        $userRole = UserRole::find()
            ->where(['user_id' => $userId, 'role_id' => $roleId])
            ->one();
        $reviewStatus = $userRole ? $userRole->review_status : Role::AUTHENTICATION_STATUS_NO_APPLICATION;
        return [$reviewStatus, Yii::$app->params['reviewStatuses'][$reviewStatus]];
    }

    /**
     * Get review remark by user id and role id
     *
     * @param $userId user id
     * @param $roleId role id
     * @return mixed|string
     */
    public static function getReviewRemark($userId, $roleId)
    {
        $userRole = UserRole::find()
            ->where(['user_id' => $userId, 'role_id' => $roleId])
            ->one();

        if ($userRole
            && in_array($userRole->review_status, [self::REVIEW_AGREE, self::REVIEW_DISAGREE])
        ) {
            return $userRole->review_remark;
        }

        return '';
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
        foreach (Role::appRoles() as $role) {
            $status = Role::AUTHENTICATION_STATUS_NOT_ONLINE;
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

        $roleNames = Yii::$app->db->createCommand($sql)->queryColumn();
        if (!self::find()->where(['user_id' => $userId])->exists()) {
            if (User::find()->where(['id' => $userId])->exists()) {
                $role = Role::findOne(Yii::$app->params['ownerRoleId']);
                if ($role) {
                    return array_merge($roleNames, [$role->name]);
                }
            }
        }

        return $roleNames;
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
     * Add user role
     *
     * @param int $userId user id
     * @param int $roleId role id
     * @param ActiveRecord|null $operator operator
     * @param int $reviewStatus review status default 0(meaning "in process")
     * @return bool
     */
    public static function addUserRole($userId, $roleId, ActiveRecord $operator = null, $reviewStatus = Role::AUTHENTICATION_STATUS_IN_PROCESS)
    {
        UserRole::deleteAll(['user_id' => $userId, 'role_id' => $roleId]);
        $userRole = new self;
        $userRole->user_id = $userId;
        $userRole->role_id = $roleId;
        $now = time();
        $userRole->review_apply_time = $now;
        $userRole->review_status = $reviewStatus;
        if ($operator) {
            $userRole->review_time = $now;
            $userRole->reviewer_uid = $operator->id;
        }
        return $userRole->save();
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
    public static function paginationBySupplier($where = [], $select = [], $page = 1, $size = Supplier::PAGE_SIZE_DEFAULT, $orderBy = 'L.id DESC')
    {
        $offset = ($page - 1) * $size;
        $List = (new Query())
            ->from(self::tableName() . ' as L')
            ->leftJoin(Supplier::tableName() . ' as S', 'L.user_id=S.uid')
            ->leftJoin(User::tableName() . ' as U', 'U.id=L.user_id')
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->all();
        foreach ($List as &$list) {
            $list['category'] = GoodsCategory::GetCateGoryById($list['category_id']);

            if ($list['review_time'] != 0) {
                $list['review_time'] = date('Y-m-d H:i', $list['review_time']);
            }
            $list['review_apply_time'] = date('Y-m-d H:i', $list['review_apply_time']);
            $list['type_shop'] = Supplier::TYPE_SHOP[$list['type_shop']];
            $list['supplier_id'] = $list['id'];
            unset($list['id']);
        }
        $total = (new Query())
            ->from(self::tableName() . ' as L')
            ->leftJoin(Supplier::tableName() . ' as S', 'L.user_id=S.uid')
            ->leftJoin(User::tableName() . ' as U', 'U.id=L.user_id')
            ->where($where)->count();
        return ModelService::pageDeal($List, $total, $page, $size);
    }

    /**
     * @param array $where
     * @param int $page
     * @param int $size
     * @param $orderBy
     * @return array
     */
    public static function pagination($where = [], $page = 1, $size = ModelService::PAGE_SIZE_DEFAULT, $orderBy)
    {

        $select = 'ur.*,u.nickname,u.mobile,u.aite_cube_no';
        $UserRoleList = (new Query())
            ->from('user_role as ur')
            ->leftJoin('user as u', 'u.id=ur.user_id')
            ->select($select)
            ->where($where)
            ->orderBy($orderBy);
        $total = (int)$UserRoleList->count();
        $pagination = new Pagination(['totalCount' => $total, 'pageSize' => $size, 'pageSizeParam' => false]);
        $arr = $UserRoleList->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        foreach ($arr as &$UserRole) {
            $UserRole['review_apply_time'] = date('Y-m-d H:i:s', $UserRole['review_apply_time']);
            $UserRole['review_time'] = date('Y-m-d H:i:s', $UserRole['review_time']);
            $UserRole['review_status'] = self::REVIEW_STATUS[$UserRole['review_status']];
            $UserRole['handle_name'] = User::find()->asArray()->select('nickname')->where(['id' => $UserRole['reviewer_uid']])->one()['nickname'];
        }

        return ModelService::pageDeal($arr, $total, $page, $size);

    }

    public static function userauditview($id)
    {
        $audits = (new Query())
            ->from('user_role as ur')
            ->leftJoin('user as u', 'u.id = ur.user_id')
            ->select('ur.*,u.legal_person as nickname,u.aite_cube_no,u.identity_no,u.identity_card_front_image,u.identity_card_back_image,u.mobile')
            ->where(['ur.id' => $id])
            ->one();

        if ($audits) {
            $audits['review_apply_time'] = date('Y-m-d H:i:s', $audits['review_apply_time']);
            $audits['review_time'] = date('Y-m-d H:i:s', $audits['review_time']);
            $audits['review_status'] = self::REVIEW_STATUS[$audits['review_status']];
            $audits['handle_name'] = User::find()->asArray()->select('nickname')->where(['id' => $audits['reviewer_uid']])->one()['nickname'];

            return $audits;
        } else {
            return null;
        }

    }


    /**
     * 验证角色
     * @param $role_id
     * @return array
     */
    public static function VerifyRolePermissions($role_id)
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            return [
                'code' => 403,
                'data' => ''
            ];
        }
        switch ($role_id) {
            case \Yii::$app->params['ownerRoleId']:
                $role = $user;
                break;
            case \Yii::$app->params['supplierRoleId']:
                $role = Supplier::find()->select('id')->where(['uid' => $user->id])->one();
                break;
            case \Yii::$app->params['lhzzRoleId']:
                $role = Lhzz::find()->select('id')->where(['uid' => $user->id])->one();
                break;
        }
        if (!$role) {
            return [
                'code' => 1034,
                'data' => ''
            ];
        }
        return [
            'code' => 200,
            'data' => $role->id
        ];
    }

    /**
     * Do some ops after updated model
     *
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (isset($changedAttributes['review_status'])) {
            Yii::$app->cache->delete(self::CACHE_KEY_PREFIX_ROLES_STATUS . $this->user_id);
        }
    }


    /**
     * @return int
     */
    public function checkIsAuthentication()
    {
        switch ($this->review_status) {
            case self::REVIEW_AGREE:
                $code = 200;
                break;
            case self::REVIEW_DISAGREE:
                $code = 1092;
                break;
            case self::REVIEW_BE_AUDITED:
                $code = 1091;
                break;
        }
        return $code;
    }
}