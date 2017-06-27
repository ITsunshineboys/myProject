<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use app\services\StringService;
use Yii;
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
     * Update daily viewed number and ip number by supplier id
     *
     * @param int $supplierId supplier id
     * @param string $ip ip
     */
    public static function updateDailyViewedNumberAndIpNumberBySupplierId($supplierId, $ip)
    {
        $date = date('Ymd');

        $model = self::find()->where(['supplier_id' => $supplierId, 'create_date' => $date])->one();
        if ($model) {
            $model->viewed_number += 1;
            if (!in_array($ip, self::getDailyViewedIpsBySupplierId($supplierId))) {
                $model->ip_number += 1;
                self::setDailyViewedIpsBySupplierId($supplierId, $ip);
            }
        } else {
            $model = new self;
            $model->supplier_id = $supplierId;
            $model->viewed_number = 1;
            $model->create_date = $date;
            $model->ip_number = 1;
            self::setDailyViewedIpsBySupplierId($supplierId, $ip);
        }

        $model->save();
    }

    /**
     * Get daily ip list by supplierId
     *
     * @param int $supplierId supplier id
     * @return array
     */
    public static function getDailyViewedIpsBySupplierId($supplierId)
    {
        $cache = Yii::$app->cache;
        $key = date('Ymd') . self::CACHE_KEY_PREFIX_VIEWED_IPS . $supplierId;
        $ips = $cache->get($key);
        return $ips ? Json::decode($ips) : [];
    }

    /**
     * Set daily ip list by supplierId
     *
     * @param int $supplierId supplier id
     * @param string $ip ip
     */
    public static function setDailyViewedIpsBySupplierId($supplierId, $ip)
    {
        $cache = Yii::$app->cache;
        $now = time();
        $key = date('Ymd', $now) . self::CACHE_KEY_PREFIX_VIEWED_IPS . $supplierId;

        $ips = $cache->get($key);
        $ips = $ips ? Json::decode($ips) : [];
        if (!in_array($ip, $ips)) {
            $ips[] = $ip;
        }

        $todayEnd = strtotime(StringService::startEndDate('today')[1]);
        $cache->set($key, Json::encode($ips), $todayEnd - $now);
    }
}