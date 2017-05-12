<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class GoodsRecommend extends ActiveRecord
{
    const RECOMMEND_GOODS_TYPE_CAROUSEL = 0;
    const RECOMMEND_GOODS_TYPE_FIRST = 1;
    const RECOMMEND_GOODS_TYPE_SECOND = 2;
    const CACHE_KEY_FIRST = 'recommend_goods_first';
    const CACHE_KEY_SECOND = 'recommend_goods_second';
    const CACHE_KEY_CAROUSEL = 'recommend_goods_carousel';
    const PAGE_SIZE_DEFAULT_ADMIN_INDEX = 1000;
    const PAGE_SIZE_DEFAULT = 12;
    const FROM_TYPE_MALL = 1;
    const FROM_TYPE_LINK = 2;
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;
    const CACHE_KEY_PREFIX_VIEWED_NUMBER = 'recommend_goods_viewed_number_';
    const CACHE_KEY_PREFIX_SOLD_NUMBER = 'recommend_goods_sold_number_';

    /**
     * @var array app fields
     */
    private static $appFields = ['title', 'image', 'description', 'platform_price', 'url'];

    /**
     * @var array admin fields
     */
    public static $adminFields = ['id', 'sku', 'title', 'from_type', 'viewed_number', 'sold_number', 'status', 'create_time', 'image'];

    /**
     * @var array cache keys
     */
    private static $cacheKeys = [
        self::CACHE_KEY_CAROUSEL,
        self::CACHE_KEY_SECOND
    ];

    /**
     * @var array from types
     */
    public static $fromTypes = [
        self::FROM_TYPE_MALL => '商铺',
        self::FROM_TYPE_LINK => '链接',
    ];

    /**
     * @var array recommend types(banner|list)
     */
    public static $types = [
        self::RECOMMEND_GOODS_TYPE_CAROUSEL,
        self::RECOMMEND_GOODS_TYPE_SECOND,
    ];

    /**
     * @var array online status list
     */
    public static $statuses = [
        self::STATUS_OFFLINE => '停用',
        self::STATUS_ONLINE => '启用',
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_recommend';
    }

    /**
     * Get recommended goods for type first
     *
     * @return array
     */
    public static function first()
    {
        $key = self::CACHE_KEY_FIRST;
        $cache = Yii::$app->cache;
        $recommendGoods = $cache->get($key);
        if (!$recommendGoods) {
            $recommendGoods = self::_first();
            if ($recommendGoods) {
                $cache->set($key, $recommendGoods);
            }
        }

        return $recommendGoods;
    }

    /**
     * Get recommended goods for type first
     *
     * @access private
     * @return array
     */
    public static function _first()
    {
        $recommendGoods = [];

        $goodsRecommend = GoodsRecommend::find()->where(['type' => self::RECOMMEND_GOODS_TYPE_FIRST, 'status' => self::STATUS_ONLINE])->one();
        if ($goodsRecommend) {
            $goods = Goods::find()->where(['sku' => $goodsRecommend->sku])->one();
            if ($goods) {
                $goodsId = $goods->id;
                $platformPrice = $goods->platform_price / 100;
                $title = $goodsRecommend->title;
                $image = $goodsRecommend->image;
                $description = $goodsRecommend->description;
                $recommendGoods[] = [
                    'title' => $title,
                    'image' => $image,
                    'description' => $description,
                    'goods_id' => $goodsId,
                    'platform_price' => $platformPrice,
                ];
            }
        }

        return $recommendGoods;
    }

    /**
     * Get recommended goods for type second
     *
     * @param int $page page default 1
     * @param int $size page size default 12
     * @return array
     */
    public static function second($page = 1, $size = self::PAGE_SIZE_DEFAULT)
    {
        $page <= 0 && $page = 1;
        $size <= 0 && $size = self::PAGE_SIZE_DEFAULT;
        $offset = ($page - 1) * $size;
        return array_slice(self::secondAll(), $offset, $size);
    }

    /**
     * Get carousel
     *
     * @return array
     */
    public static function carousel()
    {
        $key = self::CACHE_KEY_CAROUSEL;
        $cache = Yii::$app->cache;
        $recommendGoods = $cache->get($key);
        if (!$recommendGoods) {
            $recommendGoods = self::_carousel(self::$appFields);
            if ($recommendGoods) {
                $cache->set($key, $recommendGoods);
            }
        }

        return $recommendGoods;
    }

    /**
     * Get carousel
     *
     * @access private
     * @param  array   $select  select fields default all fields
     * @param  array   $orderBy order by fields default sorting_number asc
     * @return array
     */
    private static function _carousel($select = [], $orderBy = ['sorting_number' => SORT_ASC])
    {
        return self::find()->select($select)->where(['type' => self::RECOMMEND_GOODS_TYPE_CAROUSEL, 'status' => self::STATUS_ONLINE])->orderBy($orderBy)->all();
    }

    /**
     * Get all recommended goods for type second
     *
     * @return array
     */
    public static function secondAll()
    {
        $key = self::CACHE_KEY_SECOND;
        $cache = Yii::$app->cache;
        $recommendGoods = $cache->get($key);
        if (!$recommendGoods) {
            $recommendGoods = self::_secondAll(self::$appFields);
            if ($recommendGoods) {
                $cache->set($key, $recommendGoods);
            }
        }

        return $recommendGoods;
    }

    /**
     * Get all recommended goods for type second
     *
     * @access private
     * @param  array   $select select fields default all fields
     * @return array
     */
    private static function _secondAll($select = [])
    {
        return self::find()->select($select)->where(['type' => self::RECOMMEND_GOODS_TYPE_SECOND, 'status' => self::STATUS_ONLINE])->all();
    }

    /**
     * Get banner list
     *
     * @param  array $where search condition
     * @param  array $select select fields default all fields
     * @param  int $page page number default 1
     * @param  int $size page size default 12
     * @param  array $orderBy order by fields default sold_number desc
     * @return array
     */
    public static function pagination($where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = ['id' => SORT_ASC])
    {
        if (in_array('from_type', $select)) {
            $select[] = 'supplier_name';
        }

        if (in_array('viewed_number', $select)) {
            $select[] = 'delete_time';
            $hasViewedNumber = true;
            unset($select[array_search('viewed_number', $select)]);
        }

        if (in_array('sold_number', $select)) {
            $field = 'delete_time';
            !in_array($field, $select) && $select[] = $field;
            $hasSoldNumber = true;
            unset($select[array_search('sold_number', $select)]);
        }

        $offset = ($page - 1) * $size;
        $bannerList = self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();
        if (!$select
            || in_array('create_time', $select)
            || in_array('delete_time', $select)
            || in_array('from_type', $select)
            || in_array('status', $select)
            || $hasViewedNumber
            || $hasSoldNumber
        ) {
            foreach ($bannerList as &$banner) {
                $hasViewedNumber && $banner['viewed_number'] = self::viewedNumber($banner['create_time'], $banner['delete_time']);
                $hasSoldNumber && $banner['sold_number'] = self::soldNumber($banner['create_time'], $banner['delete_time']);

                if (isset($banner['create_time'])) {
                    if (!empty($banner['create_time'])) {
                        $banner['create_time'] = date('Y-m-d H:i', $banner['create_time']);
                    }
                }

                if (isset($banner['delete_time'])) {
                    if (!empty($banner['delete_time'])) {
                        $banner['delete_time'] = date('Y-m-d H:i', $banner['delete_time']);
                    }
                }

                isset($banner['from_type']) && $banner['from_type'] = self::$fromTypes[$banner['from_type']];
                isset($banner['status']) && $banner['status'] = self::$statuses[$banner['status']];
            }
        }

        return $bannerList;
    }

    /**
     * Get viewed number
     *
     * @param int $createTime banner create time default 0
     * @param int $deleteTime banner delete time default 0
     * @return int
     */
    public static function viewedNumber($createTime = 0, $deleteTime = 0)
    {
        $createTime = (int)$createTime;
        $deleteTime = (int)$deleteTime;
        if (!$createTime || !$deleteTime) {
            return 0;
        }

        $key = self::CACHE_KEY_PREFIX_VIEWED_NUMBER . $createTime . '_' . $deleteTime;
        $cache = Yii::$app->cache;
        $viewedNumber = $cache->get($key);
        if ($viewedNumber === false) {
            $viewedNumber = self::_viewedNumber($createTime, $deleteTime);
            $cache->set($key, $viewedNumber);
        }

        return $viewedNumber;
    }

    /**
     * Get viewed number
     *
     * @access private
     * @param int $createTime banner create time
     * @param int $deleteTime banner delete time
     * @return int
     */
    public static function _viewedNumber($createTime, $deleteTime)
    {
        $where = "create_time >= {$createTime} and create_time <= {$deleteTime}";
        return (int)GoodsRecommendViewLog::find()->where($where)->asArray()->count();
    }

    /**
     * Get sold number
     *
     * @param int $createTime banner create time default 0
     * @param int $deleteTime banner delete time default 0
     * @return int
     */
    public static function soldNumber($createTime = 0, $deleteTime = 0)
    {
        $createTime = (int)$createTime;
        $deleteTime = (int)$deleteTime;
        if (!$createTime || !$deleteTime) {
            return 0;
        }

        $key = self::CACHE_KEY_PREFIX_SOLD_NUMBER . $createTime . '_' . $deleteTime;
        $cache = Yii::$app->cache;
        $viewedNumber = $cache->get($key);
        if ($viewedNumber === false) {
            $viewedNumber = self::_soldNumber($createTime, $deleteTime);
            $cache->set($key, $viewedNumber);
        }

        return $viewedNumber;
    }

    /**
     * Get sold number
     *
     * @access private
     * @param int $createTime banner create time
     * @param int $deleteTime banner delete time
     * @return int
     */
    public static function _soldNumber($createTime, $deleteTime)
    {
        $where = "create_time >= {$createTime} and create_time <= {$deleteTime}";
        return (int)GoodsRecommendSaleLog::find()->where($where)->asArray()->count();
    }

    /**
     * Check if can delete recommend records
     *
     * @param string $ids recommend record ids separated by commas
     * @return mixed bool|int
     */
    public static function canDelete($ids)
    {
        $ids = trim($ids);
        $ids = trim($ids, ',');

        if (!$ids) {
            return false;
        }

        $where = 'id in(' . $ids . ')';

        if (self::find()->where($where)->count() != count(explode(',', $ids))) {
            return false;
        }

        if (self::find()->where('delete_time > 0 and ' . $where)->count()) {
            return false;
        }

        if (self::find()->where('status = ' . self::STATUS_ONLINE . ' and ' . $where)->count()) {
            return -1;
        }

        return true;
    }

    /**
     * Check if can disable recommend records
     *
     * @param string $ids recommend record ids separated by commas
     * @return mixed bool
     */
    public static function canDisable($ids)
    {
        $ids = trim($ids);
        $ids = trim($ids, ',');

        if (!$ids) {
            return false;
        }

        $where = 'id in(' . $ids . ')';

        if (self::find()->where($where)->count() != count(explode(',', $ids))) {
            return false;
        }

        if (self::find()->where('status = ' . self::STATUS_OFFLINE . ' and ' . $where)->count()) {
            return false;
        }

        return true;
    }

    /**
     * Set cache after updated model
     *
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $cache = Yii::$app->cache;
        if ($this->type == self::RECOMMEND_GOODS_TYPE_CAROUSEL) {
            $cache->delete(self::CACHE_KEY_CAROUSEL);
        } elseif ($this->type == self::RECOMMEND_GOODS_TYPE_SECOND) {
            $cache->delete(self::CACHE_KEY_SECOND);
        }
    }
}