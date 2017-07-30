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

class UserMobile extends ActiveRecord
{
    const FIELDS_BINDING_LOGS = [
        'mobile',
        'create_time',
        'op_username',
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'user_mobile';
    }

    /**
     * Add user mobile by user and operator
     *
     * @param User $user user
     * @param User|null $operator operator default null
     * @return bool
     */
    public static function addByUserAndOperator(User $user, User $operator = null)
    {
        $userMobile = new self;
        $userMobile->uid = $user->id;
        $userMobile->mobile = $user->mobile;
        $userMobile->create_time = time();
        if ($operator) {
            $lhzz = Lhzz::find()->where(['uid' => $operator->id])->one();
            if ($lhzz) {
                $userMobile->op_uid = $lhzz->id;
                $userMobile->op_username = $lhzz->nickname;
            }
        }

        return $userMobile->save();
    }

    /**
     * Get list
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
        }

        return $list;
    }
}