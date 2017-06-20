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
    const AFTER_SALE_SERVICE_NECESSARY = 0;
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_REVIEW = 'review';
    const EXCEPT_FIELDS_WHEN_CHANGE_ONLINE_TO_WAIT = [
        'left_number',
    ];

    const CATEGORY_GOODS_APP = ['id', 'title', 'subtitle', 'platform_price', 'comment_number', 'favourable_comment_rate', 'cover_image'];

    const AFTER_SALE_SERVICES = [
        '提供发票',
        '上门安装',
        '上门维修',
        '上门退货',
        '上门换货',
        '退货',
        '换货',
    ];

    const FIELDS_ADMIN = [
        'id',
        'sku',
        'title',
        'supplier_price',
        'platform_price',
        'market_price',
        'purchase_price_decoration_company',
        'purchase_price_manager',
        'purchase_price_designer',
        'left_number',
        'sold_number',
        'status',
        'create_time',
        'online_time',
        'offline_time',
        'delete_time',
        'description',
        'reason',
        'offline_reason',
        'offline_person',
        'offline_uid',
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
    public static function pagination($where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = ['sold_number' => SORT_DESC], $fromLhzz = false)
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
            || in_array('purchase_price_decoration_company', $select)
            || in_array('purchase_price_manager', $select)
            || in_array('purchase_price_designer', $select)
            || in_array('create_time', $select)
            || in_array('online_time', $select)
            || in_array('offline_time', $select)
            || in_array('delete_time', $select)
            || in_array('status', $select)
        ) {
            foreach ($goodsList as &$goods) {
                isset($goods['platform_price']) && $goods['platform_price'] /= 100;
                isset($goods['supplier_price']) && $goods['supplier_price'] /= 100;
                isset($goods['market_price']) && $goods['market_price'] /= 100;
                isset($goods['purchase_price_decoration_company']) && $goods['purchase_price_decoration_company'] /= 100;
                isset($goods['purchase_price_manager']) && $goods['purchase_price_manager'] /= 100;
                isset($goods['purchase_price_designer']) && $goods['purchase_price_designer'] /= 100;

                if (isset($goods['create_time'])) {
                    $goods['create_time'] = $goods['create_time']
                        ? date('Y-m-d H:i', $goods['create_time'])
                        : '';
                }

                if (isset($goods['online_time'])) {
                    $goods['online_time'] = $goods['online_time']
                        ? date('Y-m-d H:i', $goods['online_time'])
                        : '';
                }

                if (isset($goods['offline_time'])) {
                    $goods['offline_time'] = $goods['offline_time']
                        ? date('Y-m-d H:i', $goods['offline_time'])
                        : '';
                }

                if (isset($goods['delete_time'])) {
                    $goods['delete_time'] = $goods['delete_time']
                        ? date('Y-m-d H:i', $goods['delete_time'])
                        : '';
                }

                if ($fromLhzz) {
                    if (isset($goods['status'])) {
                        if ($goods['status'] == self::STATUS_OFFLINE) {
                            $goods['operator'] = $goods['offline_person'];
                        } elseif ($goods['status'] == self::STATUS_ONLINE) {
                            $goods['operator'] = $goods['online_person'];
                        }
                    }
                } else {
                    if (isset($goods['offline_person'])
                        && isset($goods['status'])
                        && $goods['status'] == self::STATUS_OFFLINE
                    ) {
                        $goods['operator'] = $goods['offline_uid'] > 0 ? '系统下架' : $goods['offline_person'];
                    }
                }

                if (isset($goods['offline_uid'])) {
                    unset($goods['offline_uid']);
                }

                if (isset($goods['offline_person'])) {
                    unset($goods['offline_person']);
                }

                if (isset($goods['online_person'])) {
                    unset($goods['online_person']);
                }

                isset($goods['status']) && $goods['status'] = self::$statuses[$goods['status']];
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
     * @param array $categoryIds category ids
     * @param ActiveRecord $lhzz lhzz model
     */
    public static function disableGoodsByCategoryIds(array $categoryIds, ActiveRecord $lhzz)
    {
        foreach ($categoryIds as $categoryId) {
            self::disableGoodsByCategoryId($categoryId, $lhzz);
        }
    }

    /**
     * Disable goods by category id
     *
     * @param int $categoryId category id
     * @param ActiveRecord $lhzz lhzz model
     */
    public static function disableGoodsByCategoryId($categoryId, ActiveRecord $lhzz)
    {
        $goodsIds = self::findIdsByCategoryId($categoryId);
        if ($goodsIds) {
            $goodsIds = implode(',', $goodsIds);
            $where = 'id in(' . $goodsIds . ')';
            self::updateAll([
                'status' => self::STATUS_OFFLINE,
                'offline_time' => time(),
                'offline_reason' => Yii::$app->params['category']['offline_reason'],
                'offline_uid' => $lhzz->id,
                'offline_person' => $lhzz->nickname,
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
     * @param array $brandIds brand ids
     * @param ActiveRecord $lhzz lhzz model
     */
    public static function disableGoodsByBrandIds(array $brandIds, ActiveRecord $lhzz)
    {
        foreach ($brandIds as $brandId) {
            self::disableGoodsByBrandId($brandId, $lhzz);
        }
    }

    /**
     * Disable goods by brand id
     *
     * @param int $brandId brand id
     * @param ActiveRecord $lhzz lhzz model
     */
    public static function disableGoodsByBrandId($brandId, ActiveRecord $lhzz)
    {
        $goodsIds = self::findIdsByBrandId($brandId);
        if ($goodsIds) {
            $goodsIds = implode(',', $goodsIds);
            $where = 'id in(' . $goodsIds . ')';
            self::updateAll([
                'status' => self::STATUS_OFFLINE,
                'offline_time' => time(),
                'offline_reason' => Yii::$app->params['brand']['offline_reason'],
                'offline_uid' => $lhzz->id,
                'offline_person' => $lhzz->nickname,
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
    public static function priceDetail($level = '', $title = '', $city = 510100)
    {
        if (empty($level) && empty($title)) {
            echo '请正确输入值';
            exit;
        } else {
            $db = Yii::$app->db;
            $sql = "SELECT goods.id,goods.platform_price,goods.supplier_price,goods_attr. name,goods_attr.value,goods_brand. name,goods_category.title,logistics_district.district_name FROM goods LEFT JOIN goods_attr ON goods_attr.goods_id = goods.id LEFT JOIN goods_brand ON goods.brand_id = goods_brand.id LEFT JOIN goods_category ON goods.category_id = goods_category.id LEFT JOIN logistics_district ON goods.id = logistics_district.goods_id WHERE logistics_district.district_code = " . $city . "  AND goods_category.`level` = " . $level . " AND goods_category.title LIKE '" . $title . "'";
            $a = $db->createCommand($sql)->queryAll();
        }
        if (!empty($a)) {
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
    public static function findQueryAll($all = [], $city = 510100)
    {
        if ($all) {
            $goods_id = [];
            foreach ($all as $single) {
                $goods_id [] = $single['goods_id'];
            }
            $id = implode(',', $goods_id);
            $db = \Yii::$app->db;
            $sql = "SELECT goods.id,goods.platform_price,goods.supplier_price,goods_attr. name,goods_attr.value,goods_brand. name,goods_category.title,logistics_district.district_name FROM goods LEFT JOIN goods_attr ON goods_attr.goods_id = goods.id LEFT JOIN goods_brand ON goods.brand_id = goods_brand.id LEFT JOIN goods_category ON goods.category_id = goods_category.id LEFT JOIN logistics_district ON goods.id = logistics_district.goods_id  WHERE logistics_district.district_code = " . $city . "
AND goods.id IN (" . $id . ")";
            $all_goods = $db->createCommand($sql)->queryAll();
        }
        return $all_goods;
    }

    /**
     * Check if can disable goods records
     *
     * @param  string $ids goods record ids separated by commas
     * @return bool
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

        if (self::find()->where('status = ' . self::STATUS_ONLINE . ' and ' . $where)->count()
            != count(explode(',', $ids))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Check if can delete goods records
     *
     * @param  string $ids goods record ids separated by commas
     * @return bool
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

        if (self::find()->where('offline_uid = 0 and status = ' . self::STATUS_OFFLINE . ' and ' . $where)->count()
            != count(explode(',', $ids))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Check if can enable goods records
     *
     * @param  string $ids goods record ids separated by commas
     * @return bool
     */
    public static function canEnable($ids)
    {
        $ids = trim($ids);
        $ids = trim($ids, ',');

        if (!$ids) {
            return false;
        }

        $where = 'id in(' . $ids . ')';
        $idsArr = explode(',', $ids);

        if (self::find()->where($where)->count() != count($idsArr)) {
            return false;
        }

        if ((self::find()->where('status = ' . self::STATUS_OFFLINE . ' and ' . $where)->count()
                == count($idsArr))
            || (self::find()->where('status = ' . self::STATUS_WAIT_ONLINE . ' and ' . $where)->count()
                == count($idsArr))
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['title', 'subtitle', 'category_id', 'brand_id', 'cover_image', 'supplier_price', 'platform_price', 'market_price', 'purchase_price_decoration_company', 'purchase_price_manager', 'purchase_price_designer', 'left_number', 'logistics_template_id', 'after_sale_services'], 'required', 'on' => self::SCENARIO_REVIEW],
            [['title', 'subtitle', 'category_id', 'brand_id', 'cover_image', 'supplier_price', 'platform_price', 'market_price', 'left_number', 'logistics_template_id', 'after_sale_services'], 'required', 'on' => self::SCENARIO_ADD],
            [['title', 'subtitle'], 'string', 'length' => [1, 16]],
            [['cover_image', 'offline_reason', 'reason'], 'string'],
            [['category_id', 'brand_id', 'supplier_price', 'platform_price', 'market_price', 'purchase_price_decoration_company', 'purchase_price_manager', 'purchase_price_designer', 'left_number', 'logistics_template_id'], 'number', 'min' => 0],
            ['supplier_price', 'validateSupplierPrice', 'on' => self::SCENARIO_REVIEW],
            ['platform_price', 'validatePlatformPrice', 'on' => [self::SCENARIO_ADD, self::SCENARIO_EDIT]],
            ['after_sale_services', 'validateAfterSaleServices'],
            [['category_id'], 'validateCategoryId'],
            [['brand_id'], 'validateBrandId'],
            [['style_id'], 'validateStyleId'],
            [['series_id'], 'validateSeriesId'],
            [['logistics_template_id'], 'validateLogisticsTemplateId'],
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

        if (array_diff($afterSaleServices, $serviceIds)
            || !in_array(self::AFTER_SALE_SERVICE_NECESSARY, $serviceIds)
        ) {
            $this->addError($attribute);
            return false;
        }

        return true;
    }

    /**
     * Validates prices
     *
     * @param string $attribute supplier_price, platform_price, market_price and purchase prices to validate
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
     * Validates prices
     *
     * @param string $attribute supplier_price, platform_price, market_price to validate
     * @return bool
     */
    public function validatePlatformPrice($attribute)
    {
        if ($this->supplier_price <= $this->$attribute
            && $this->$attribute <= $this->market_price
        ) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }

    /**
     * Validates logistics_template_id
     *
     * @param string $attribute logistics_template_id to validate
     * @return bool
     */
    public function validateLogisticsTemplateId($attribute)
    {
        $where = [
            'id' => $this->$attribute,
            'status' => LogisticsTemplate::STATUS_ONLINE,
        ];

        if ($this->$attribute > 0
            && LogisticsTemplate::find()->where($where)->exists()
        ) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }

    /**
     * Sanitize post data
     *
     * @param ActiveRecord $user user model
     * @param array $postData post data
     */
    public function sanitize(ActiveRecord $user, array &$postData)
    {
        if (isset($postData['category_id'])) {
            unset($postData['category_id']);
        }

        if ($user->login_role_id == Yii::$app->params['supplierRoleId']) {
            if (isset($postData['purchase_price_decoration_company'])) {
                unset($postData['purchase_price_decoration_company']);
            }
            if (isset($postData['purchase_price_manager'])) {
                unset($postData['purchase_price_manager']);
            }
            if (isset($postData['purchase_price_designer'])) {
                unset($postData['purchase_price_designer']);
            }
            if (isset($postData['offline_reason'])) {
                unset($postData['offline_reason']);
            }
        } elseif ($user->login_role_id == Yii::$app->params['lhzzRoleId']) {
            if (in_array($this->status, [self::STATUS_WAIT_ONLINE, self::STATUS_OFFLINE, self::STATUS_WAIT_ONLINE])) {
                $cleanData = [];

                if (isset($postData['purchase_price_decoration_company'])) {
                    $cleanData['purchase_price_decoration_company'] = $postData['purchase_price_decoration_company'];
                }
                if (isset($postData['purchase_price_manager'])) {
                    $cleanData['purchase_price_manager'] = $postData['purchase_price_manager'];
                }
                if (isset($postData['purchase_price_designer'])) {
                    $cleanData['purchase_price_designer'] = $postData['purchase_price_designer'];
                }
                if ($this->status == self::STATUS_OFFLINE && isset($postData['offline_reason'])) {
                    $cleanData['offline_reason'] = $postData['offline_reason'];
                }
                if ($this->status == self::STATUS_WAIT_ONLINE && isset($postData['reason'])) {
                    $cleanData['reason'] = $postData['reason'];
                }

                $postData = $cleanData;
            }
        }
    }

    /**
     * Check if can edit goods
     *
     * @param ActiveRecord $user user model
     * @return bool
     */
    public function canEdit(ActiveRecord $user)
    {
        $statuses = [
            self::STATUS_WAIT_ONLINE,
            self::STATUS_ONLINE,
            self::STATUS_OFFLINE
        ];

        if (!in_array($this->status, $statuses)) {
            return false;
        }

        if ($this->status == self::STATUS_OFFLINE
            && $user->login_role_id == Yii::$app->params['supplierRoleId']
        ) {
            return false;
        }

        return true;
    }

    /**
     * Check if can enable goods
     *
     * @param ActiveRecord $user user model
     * @return bool
     */
    public function canOnline(ActiveRecord $user)
    {
        if ($user->login_role_id == Yii::$app->params['lhzzRoleId']
            && in_array($this->status, [self::STATUS_WAIT_ONLINE, self::STATUS_OFFLINE])
        ) {
            if ($this->validateCategoryId('category_id')
                && $this->validateBrandId('brand_id')
                && $this->validateSupplierId('supplier_id')
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validates category_id
     *
     * @param string $attribute category_id to validate
     * @return bool
     */
    public function validateCategoryId($attribute)
    {
        $where = [
            'id' => $this->$attribute,
            'deleted' => GoodsCategory::STATUS_OFFLINE,
            'level' => GoodsCategory::LEVEL3
        ];

        if ($this->$attribute > 0
            && GoodsCategory::find()->where($where)->exists()
        ) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }

    /**
     * Validates brand_id
     *
     * @param string $attribute brand_id to validate
     * @return bool
     */
    public function validateBrandId($attribute)
    {
        $where = [
            'id' => $this->$attribute,
            'status' => GoodsBrand::STATUS_ONLINE,
        ];

        if ($this->$attribute > 0
            && GoodsBrand::find()->where($where)->exists()
        ) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }

    /**
     * Validates supplier_id
     *
     * @param string $attribute supplier_id to validate
     * @return bool
     */
    public function validateSupplierId($attribute)
    {
        $where = [
            'id' => $this->$attribute,
            'status' => Supplier::STATUS_ONLINE,
        ];

        if ($this->$attribute > 0
            && Supplier::find()->where($where)->exists()
        ) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }

    /**
     * Validates style_id
     *
     * @param string $attribute style_id to validate
     * @return bool
     */
    public function validateStyleId($attribute)
    {
        $where = [
            'id' => $this->$attribute,
            'status' => Style::STATUS_ONLINE,
        ];

        if ($this->$attribute > 0
            && Style::find()->where($where)->exists()
        ) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }

    /**
     * Validates series_id
     *
     * @param string $attribute series_id to validate
     * @return bool
     */
    public function validateSeriesId($attribute)
    {
        $where = [
            'id' => $this->$attribute,
            'status' => Series::STATUS_ONLINE,
        ];

        if ($this->$attribute > 0
            && Series::find()->where($where)->exists()
        ) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }

    /**
     * Check if need to set status to STATUS_WAIT_ONLINE
     *
     * @return bool
     */
    public function needSetStatusToWait()
    {
        $changedAttrs = $this->getDirtyAttributes();
        if ($this->status == self::STATUS_ONLINE
            && $changedAttrs
            && !StringService::checkArrayIdentity(self::EXCEPT_FIELDS_WHEN_CHANGE_ONLINE_TO_WAIT,
                array_keys($changedAttrs))
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get view data
     *
     * @return array
     */
    public function view()
    {
        return $goodsView = [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'cover_image' => $this->cover_image,
            'platform_price' => $this->platform_price,
            'description' => $this->description,
            'sku' => $this->sku,
            'brand_name' => GoodsBrand::findOne($this->brand_id)->name,
            'style_name' => $this->style_id ? Style::findOne($this->style_id)->style : '',
            'series_name' => $this->series_id ? Series::findOne($this->series_id)->series : '',
            'attrs' => GoodsAttr::frontDetailsByGoodsId($this->id),
            'images' => GoodsImage::imagesByGoodsId($this->id),
        ];
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
            $user = Yii::$app->user->identity;

            $this->description && $this->description = HtmlPurifier::process($this->description);

            if ($insert) {
                $this->create_time = $now;
                $this->status = self::STATUS_WAIT_ONLINE;

                if ($user->login_role_id == Yii::$app->params['supplierRoleId']) {
                    $supplier = Supplier::find()->where(['uid' => $user->id])->one();
                    if (!$supplier) {
                        return false;
                    }

                    $this->supplier_id = $supplier->id;
                }
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