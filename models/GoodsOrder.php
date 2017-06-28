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
     * @param int $supplierId supplier id default 0
     * @return int
     */
    public static function totalAmountOrder($startTime, $endTime, $supplierId = 0)
    {
        $query = self::find()
            ->select('sum(amount_order) as total_amount_order')
            ->where(['>=', 'create_time', $startTime])
            ->andWhere(['<=', 'create_time', $endTime]);

        $supplierId > 0 && $query->andWhere(['supplier_id' => $supplierId]);

        return (int)$query->asArray()->all()[0]['total_amount_order'];
    }

    /**
     * Get total order number
     *
     * @param int $startTime start time
     * @param int $endTime end time
     * @param int $supplierId supplier id default 0
     * @return int
     */
    public static function totalOrderNumber($startTime, $endTime, $supplierId = 0)
    {
        $query = self::find()
            ->where(['>=', 'create_time', $startTime])
            ->andWhere(['<=', 'create_time', $endTime]);

        $supplierId > 0 && $query->andWhere(['supplier_id' => $supplierId]);

        return (int)$query->count();
    }
}