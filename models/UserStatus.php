<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/27/17
 * Time: 2:08 PM
 */

namespace app\models;

use app\services\ModelService;
use yii\db\ActiveRecord;

class UserStatus extends ActiveRecord
{
    const FIELDS_STATUS_LOGS = [
        'mobile',
        'create_time',
        'op_username',
        'status',
        'remark',
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'user_status';
    }

    /**
     * Add user status by user, operator and/or remark
     *
     * @param User $user user
     * @param User|null $operator operator default null
     * @param string $remark remark default empty
     * @return bool
     */
    public static function addByUserAndOperator(User $user, User $operator = null, $remark = '')
    {
        $userStatus = new self;
        $userStatus->uid = $user->id;
        $userStatus->mobile = $user->mobile;
        $userStatus->create_time = time();
        $user->deadtime > 0 && $userStatus->remark = $remark;
        $userStatus->status = $user->deadtime > 0 ? User::STATUS_OFFLINE : User::STATUS_ONLINE;
        if ($operator) {
            $lhzz = Lhzz::find()->where(['uid' => $operator->id])->one();
            if ($lhzz) {
                $userStatus->op_uid = $lhzz->id;
                $userStatus->op_username = $lhzz->nickname;
            }
        }

        return $userStatus->save();
    }

    /**
     * Get pagination list
     *
     * @param  array $where search condition
     * @param  array $select select fields default all fields
     * @param  int $page page number default 1
     * @param  int $size page size default 12
     * @param  array $orderBy order by fields default id desc
     * @return array
     */
    public static function pagination($where = [], $select = [], $page = 1, $size = ModelService::PAGE_SIZE_DEFAULT, $orderBy = ModelService::ORDER_BY_DEFAULT)
    {
        $offset = ($page - 1) * $size;
        $list = self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();
        foreach ($list as &$row) {
            if (isset($row['create_time'])) {
                $row['create_time'] = date('Y-m-d H:i', $row['create_time']);
            }

            if (isset($row['op_username'])) {
                $row['op_username'] = $row['op_username'] ? $row['op_username'] : '用户';
            }

            if (isset($row['status'])) {
                $row['status'] = User::STATUSES[$row['status']];
            }
        }

        return [
            'total' => (int)self::find()->where($where)->asArray()->count(),
            'details' => $list
        ];
    }
}