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

class Goods extends ActiveRecord
{
    const GOODS_DETAIL_URL_PREFIX = 'mall/goods?id=';
    const ORDERBY_SEPARATOR = ':';
    const PAGE_SIZE_DEFAULT = 12;
    const STATUS_OFFLINE = 0;
    const STATUS_WAIT_ONLINE = 1;
    const STATUS_ONLINE = 2;
    const STATUS_DELETED = 3;

    const CATEGORY_GOODS_APP = ['id', 'title', 'subtitle', 'platform_price', 'comment_number', 'favourable_comment_rate', 'image1'];

    /**
     * @var array online status list
     */
    public static $statuses = [
        self::STATUS_OFFLINE => '已下架',
        self::STATUS_WAIT_ONLINE => '等待上架',
        self::STATUS_ONLINE => '已上架',
        self::STATUS_DELETED => '已删除',
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * Get goods list by category id
     *
     * @param  int $categoryId category id
     * @param  array $select select fields default all fields
     * @param  int $page page number default 1
     * @param  int $size page size default 12
     * @param  array $orderBy order by fields default sold_number desc
     * @return array
     */
    public static function findByCategoryId($categoryId, $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = ['sold_number' => SORT_DESC])
    {
        $categoryId = (int)$categoryId;
        if ($categoryId <= 0) {
            return [];
        }

        return self::pagination(['category_id' => $categoryId], $select, $page, $size, $orderBy);
    }

    /**
     * Get goods list
     *
     * @param  array $where search condition
     * @param  array $select select fields default all fields
     * @param  int $page page number default 1
     * @param  int $size page size default 12
     * @param  array $orderBy order by fields default sold_number desc
     * @return array
     */
    public static function pagination($where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = ['sold_number' => SORT_DESC])
    {
        $offset = ($page - 1) * $size;
        $goodsList = self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();
        if (!$select
            || in_array('platform_price', $select)
            || in_array('supplier_price', $select)
            || in_array('market_price', $select)
            || in_array('purchase_price', $select)
        ) {
            foreach ($goodsList as &$goods) {
                isset($goods['platform_price']) && $goods['platform_price'] /= 100;
                isset($goods['supplier_price']) && $goods['supplier_price'] /= 100;
                isset($goods['market_price']) && $goods['market_price'] /= 100;
                isset($goods['purchase_price']) && $goods['purchase_price'] /= 100;
            }
        }
        return $goodsList;
    }

    /**
     * Get goods list by brand id
     *
     * @param  int $brandId brand id
     * @param  array $select select fields default all fields
     * @param  int $page page number default 1
     * @param  int $size page size default 12
     * @param  array $orderBy order by fields default sold_number desc
     * @return array
     */
    public static function findByBrandId($brandId, $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = ['sold_number' => SORT_DESC])
    {
        $brandId = (int)$brandId;
        if ($brandId <= 0) {
            return [];
        }

        return self::pagination(['brand_id' => $brandId], $select, $page, $size, $orderBy);
    }

    /**
     * Disable goods by category ids
     *
     * @param array $categoryIds
     */
    public static function disableGoodsByCategoryIds(array $categoryIds)
    {
        foreach ($categoryIds as $categoryId) {
            self::disableGoodsByCategoryId($categoryId);
        }
    }

    /**
     * Disable goods by category id
     *
     * @param int $categoryId category id
     */
    public static function disableGoodsByCategoryId($categoryId)
    {
        $goodsIds = self::findIdsByCategoryId($categoryId);
        if ($goodsIds) {
            $goodsIds = implode(',', $goodsIds);
            $where = 'id in(' . $goodsIds . ')';
            self::updateAll([
                'status' => self::STATUS_OFFLINE,
                'offline_time' => time()
            ], $where);
        }
    }

    /**
     * Get goods ids by category id
     *
     * @param  init  $categoryId category id
     * @return array
     */
    public static function findIdsByCategoryId($categoryId)
    {
        $categoryId = (int)$categoryId;
        if ($categoryId <= 0) {
            return [];
        }

        return Yii::$app->db
            ->createCommand("select id from {{%goods}} where category_id = {$categoryId}")
            ->queryColumn();
    }

    /**
     * Get recommend by sku
     *
     * @param int $sku sku
     * @param array $select recommend fields default all fields
     * @return mixed array|bool
     */
    public static function findBySku($sku, $select = [])
    {
        if (!$sku) {
            return false;
        }

        return self::find()->select($select)->where(['sku' => $sku])->one();
    }

    /**
     * @param array $arr
     * @return array|ActiveRecord[]
     */
    public static function priceDetail($level = '', $title = '')
    {
        if (empty($level) && empty($title)) {
            echo '请正确输入值';
            exit;
        } else {
            $db = \Yii::$app->db;
            $sql = "SELECT goods.id,goods.platform_price,goods.supplier_price,goods_brand. name,goods_category.title FROM goods,goods_brand,goods_category WHERE goods.brand_id = goods_brand.id AND goods.category_id = goods_category.id AND goods_category.`level` = " . $level . " AND goods_category.title LIKE " . "'%$title%'";
            $a = $db->createCommand($sql)->queryAll();
        }
        foreach ($a as $v => $k) {
            $c [] = ($k['platform_price'] - $k['supplier_price']) / $k['supplier_price'];
            $max = array_search(max($c), $c);
        }
        return $a[$max];
    }

    /**
     * @param array $id
     */
    public static function findQueryAll($all = [])
    {
        if($all){
            $id = [];
            foreach ($all as $single)
            {
                $id [] = $single['goods_id'];
            }
            $all_goods = self::find()->asArray()->where(['in','id',$id])->all();
        }
        return $all_goods;
    }

    /**
     * Convert price
     */
    public function afterFind()
    {
        parent::afterFind();

        isset($this->platform_price) && $this->platform_price /= 100;
        isset($this->supplier_price) && $this->supplier_price /= 100;
        isset($this->market_price) && $this->market_price /= 100;
        isset($this->purchase_price) && $this->purchase_price /= 100;
    }

    public function getOrders()
    {
        return $this->hasOne(GoodsBrand::className(), ['id' => 'brand_id']);
    }
}