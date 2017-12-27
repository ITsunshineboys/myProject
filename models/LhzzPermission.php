<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/27/17
 * Time: 2:08 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class LhzzPermission extends ActiveRecord
{
    const MODULES = [
        'site' => '通用',
        'distribution' => '分销',
        'effect' => '样板间',
        'mall' => '商城',
        'order' => '订单',
        'quote' => '智能报价',
        'supplier-cash' => '提现',
        'supplieraccount' => '账户',
        'withdrawals' => '钱包',
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'lhzz_permission';
    }

    /**
     * Get all permissions group by module
     *
     * @return array
     */
    public static function allGroupByModule()
    {
        $perms = self::find()
            ->select(['id', 'controller', 'desc'])
            ->orderBy('controller')
            ->asArray()
            ->all();

        $tmp = [];
        foreach ($perms as $perm) {
            $tmp[$perm['controller']][] = [
                'id' => $perm['id'],
                'name' => $perm['desc'],
            ];
        }

        $all = [];
        foreach ($tmp as $k => $v) {
            $all[] = [
                'module' => isset(self::MODULES[$k]) ? self::MODULES[$k] : $k,
                'permissions' => $v,
            ];
        }

        return $all;
    }
}