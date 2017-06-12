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
use yii\helpers\HtmlPurifier;

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

    const AFTER_SALE_SERVICES = [
        '提供发票',
        '退货',
        '换货',
        '上门维修',
        '上门安装'
    ];

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
     * @param  init $categoryId category id
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
     * Disable goods by brand ids
     *
     * @param array $brandIds
     */
    public static function disableGoodsByBrandIds(array $brandIds)
    {
        foreach ($brandIds as $brandId) {
            self::disableGoodsByBrandId($brandId);
        }
    }

    /**
     * Disable goods by brand id
     *
     * @param int $brandId brand id
     */
    public static function disableGoodsByBrandId($brandId)
    {
        $goodsIds = self::findIdsByBrandId($brandId);
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
     * Get goods ids by brand id
     *
     * @param  init $brandId brand id
     * @return array
     */
    public static function findIdsByBrandId($brandId)
    {
        $brandId = (int)$brandId;
        if ($brandId <= 0) {
            return [];
        }

        return Yii::$app->db
            ->createCommand("select id from {{%goods}} where brand_id = {$brandId}")
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
     * @param string $level
     * @param string $title
     * @param int $city
     * @return mixed
     */
    public static function priceDetail($level = '', $title = '',$city = 510100)
    {
        if (empty($level) && empty($title)) {
            echo '请正确输入值';
            exit;
        } else {
            $db = Yii::$app->db;
            $sql = "SELECT goods.id,goods.platform_price,goods.supplier_price,goods_attr. name,goods_attr.value,goods_brand. name,goods_category.title,logistics_district.district_name FROM goods LEFT JOIN goods_attr ON goods_attr.goods_id = goods.id LEFT JOIN goods_brand ON goods.brand_id = goods_brand.id LEFT JOIN goods_category ON goods.category_id = goods_category.id LEFT JOIN logistics_district ON goods.id = logistics_district.goods_id WHERE logistics_district.district_code = ".$city."  AND goods_category.`level` = ".$level." AND goods_category.title LIKE '".$title."'";
            $a = $db->createCommand($sql)->queryAll();
        }
        if(!empty($a)){
            foreach ($a as $v => $k) {
                $c [] = ($k['platform_price'] - $k['supplier_price']) / $k['supplier_price'];
                $max = array_search(max($c), $c);
            }
            return $a[$max];
        }
    }

    public static function findByIdAll($level = '', $title = '', $series = '1', $style = '2')
    {
        if (empty($level) && empty($title)) {
            echo '请正确输入值';
            exit;
        } else {
            $db = \Yii::$app->db;
            $sql = "SELECT goods.id,goods.platform_price,goods.supplier_price,goods_brand. name,goods_category.title FROM goods,goods_brand,goods_category WHERE goods.brand_id = goods_brand.id AND goods.category_id = goods_category.id AND goods_category.`level` = " . $level . " AND goods_category.title LIKE " . "'%$title%' AND goods.series_id =" . $series . " AND goods.style_id =" . $style;
            $all = $db->createCommand($sql)->queryAll();
        }
        return $all;
    }

    /**
     * @param array $id
     */
    public static function findQueryAll($all = [],$city =510100)
    {
        if ($all) {
            $goods_id = [];
            foreach ($all as $single) {
                $goods_id [] = $single['goods_id'];
            }
            $id = implode(',',$goods_id);
            $db = \Yii::$app->db;
            $sql = "SELECT goods.id,goods.platform_price,goods.supplier_price,goods_attr. name,goods_attr.value,goods_brand. name,goods_category.title,logistics_district.district_name FROM goods LEFT JOIN goods_attr ON goods_attr.goods_id = goods.id LEFT JOIN goods_brand ON goods.brand_id = goods_brand.id LEFT JOIN goods_category ON goods.category_id = goods_category.id LEFT JOIN logistics_district ON goods.id = logistics_district.goods_id  WHERE logistics_district.district_code = ".$city."
AND goods.id IN (".$id .")";
            $all_goods = $db->createCommand($sql)->queryAll();
        }
        return $all_goods;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['title', 'subtitle', 'category_id', 'brand_id', 'image1', 'supplier_price', 'platform_price', 'market_price', 'purchase_price_decoration_company', 'purchase_price_manager', 'purchase_price_designer', 'left_number', 'logistics_template_id', 'after_sale_services'], 'required'],
            [['title', 'subtitle'], 'string', 'length' => [1, 16]],
            [['category_id', 'brand_id', 'supplier_price', 'platform_price', 'market_price', 'purchase_price_decoration_company', 'purchase_price_manager', 'purchase_price_designer', 'left_number', 'logistics_template_id'], 'number', 'integerOnly' => true, 'min' => 0],
            ['supplier_price', 'validateSupplierPrice'],
            ['after_sale_services', 'validateAfterSaleServices'],
            ['description', 'safe']
        ];
    }

    /**
     * Validates after_sale_services
     *
     * @param string $attribute after_sale_services to validate
     * @return bool
     */
    public function validateAfterSaleServices($attribute)
    {
        $afterSaleServices = explode(',', $this->$attribute);
        $serviceIds = array_keys(self::AFTER_SALE_SERVICES);

        if (array_diff($afterSaleServices, $serviceIds)) {
            $this->addError($attribute);
            return false;
        }

        return true;
    }

    /**
     * Validates prices
     *
     * @param string $attribute supplier_price, platform_price, market_price to validate
     * @return bool
     */
    public function validateSupplierPrice($attribute)
    {
        if ($this->$attribute <= $this->purchase_price_decoration_company
            && $this->purchase_price_decoration_company <= $this->purchase_price_manager
            && $this->purchase_price_manager <= $this->platform_price
            && $this->purchase_price_decoration_company <= $this->purchase_price_designer
            && $this->purchase_price_designer <= $this->platform_price
            && $this->platform_price <= $this->market_price
        ) {
            return true;
        }

        $this->addError($attribute);
        return false;
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

    /**
     * Do some ops before insertion
     *
     * @param bool $insert if is a new record
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $now = time();

            if ($insert) {
                $this->create_time = $now;
                $this->status = self::STATUS_WAIT_ONLINE;

                $user = Yii::$app->user->identity;
                if (!$user) {
                    return false;
                }

                if ($user->login_role_id == Yii::$app->params['supplierRoleId']) {
                    $supplier = Supplier::find()->where(['uid' => $user->id])->one();
                    if (!$supplier) {
                        return false;
                    }

                    $this->supplier_id = $supplier->id;
                }

                $this->description && $this->description = HtmlPurifier::process($this->description);
            }

            return true;
        } else {
            return false;
        }
    }

    public function getOrders()
    {
        return $this->hasOne(GoodsBrand::className(), ['id' => 'brand_id']);
    }
}