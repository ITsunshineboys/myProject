<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class GoodsOrder extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_order';
    }

    /**
     * Get total amount order
     *
     * @param string $where query conditions
     * @return int
     */
    public static function totalAmountOrder($where)
    {
        return (int)self::find()
            ->select('sum(amount_order) as total_amount_order')
            ->where($where)
            ->asArray()
            ->all()[0]['total_amount_order'];
    }
}