<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use app\services\StringService;
use yii\helpers\Json;
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
     * Set viewd number by supplierId
     *
     * @param int $supplierId supplier id
     */
    public static function setViewdNumberBySupplierId($supplierId)
    {
        $cache = Yii::$app->cache;
        $key = self::CACHE_KEY_PREFIX_VIEWED_IPS . date('Ymd') . $supplierId;
        $number = $cache->get($key);
        $number++;
        $todayEnd = strtotime(StringService::startEndDate('today')[1]);
        $cache->set($key, $number, $todayEnd - time());
    }

    /**
     * Update by supplier id
     *
     * @param int $supplierId supplier id
     */
    public static function updateBySupplierId($supplierId)
    {
        $date = date('Ymd');

        $model = self::find()->where(['supplier_id' => $supplierId, 'create_date' => $date])->one();
        if ($model) {
            $model->viewed_number += self::getViewdNumberBySupplierId($supplierId);
        } else {
            $model = new self;
            $model->supplier_id = $supplierId;
            $model->viewed_number = 1;
            $model->create_date = $date;
        }

        $model->save();
    }

    /**
     * Get viewd number by supplierId
     *
     * @param int $supplierId supplier id
     * @return int
     */
    public static function getViewdNumberBySupplierId($supplierId)
    {
        $cache = Yii::$app->cache;
        $key = self::CACHE_KEY_PREFIX_VIEWED_IPS . date('Ymd') . $supplierId;
        return $cache->get($key);
    }
}