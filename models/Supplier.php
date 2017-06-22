<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class Supplier extends ActiveRecord
{
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;
    const TYPE_ORG = [
        '个体工商户',
        '企业',
    ];
    const TYPE_SHOP = [
        '旗舰店',
        '自营店',
        '专营店',
        '专卖店',
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'supplier';
    }
}