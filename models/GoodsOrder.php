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
     * @param int $startTime start time
     * @param int $endTime end time
     * @return int
     */
    public static function totalAmountOrder($startTime, $endTime)
    {
        return (int)self::find()
            ->select('sum(amount_order) as total_amount_order')
            ->where(['>=', 'create_time', $startTime])
            ->andWhere(['<=', 'create_time', $endTime])
            ->asArray()
            ->all()[0]['total_amount_order'];
    }

    /**
     * Get total order number
     *
     * @param int $startTime start time
     * @param int $endTime end time
     * @return int
     */
    public static function totalOrderNumber($startTime, $endTime)
    {
        return (int)self::find()
            ->where(['>=', 'create_time', $startTime])
            ->andWhere(['<=', 'create_time', $endTime])
            ->count();
    }
}