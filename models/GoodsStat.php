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
    const PAGE_SIZE_DEFAULT = 12;
    const FIELDS_ADMIN = [
        'create_date',
        'sold_number',
        'amount_sold',
        'ip_number',
        'viewed_number',
    ];
    const FIELDS_EXTRA = [];

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

    /**
     * Get goods statistics list
     *
     * @param  array $where search condition
     * @param  array $select select fields default all fields
     * @param  int $page page number default 1
     * @param  int $size page size default 12
     * @param  string $orderBy order by fields default id desc
     * @return array
     */
    public static function pagination($where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC')
    {
        $select = array_diff($select, self::FIELDS_EXTRA);

        $offset = ($page - 1) * $size;
        $goodsStatList = self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();

        foreach ($goodsStatList as &$goodsStat) {
            if (isset($goodsStat['create_date'])) {
                $goodsStat['create_date'] = substr($goodsStat['create_date'], 0, 4) . '-'
                    . substr($goodsStat['create_date'], 4, 2) . '-'
                    . substr($goodsStat['create_date'], 6);
            }
        }

        return $goodsStatList;
    }
}