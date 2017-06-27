<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class GoodsStat extends ActiveRecord
{
    const CACHE_KEY_PREFIX_VIEWED_IPS = 'viewed_ips_';

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_stat';
    }

    /**
     * Update viewed number by supplier id
     *
     * @param int $supplierId supplier id
     */
    public static function updateViewedNumberBySupplierId($supplierId)
    {
        $date = date('Ymd');

        $model = self::find()->where(['supplier_id' => $supplierId, 'create_date' => $date])->one();
        if ($model) {
            $model->viewed_number += 1;
        } else {
            $model = new self;
            $model->supplier_id = $supplierId;
            $model->viewed_number = 1;
            $model->create_date = $date;
        }

        $model->save();
    }
}