<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use app\services\ModelService;
use app\services\StringService;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;

class GoodsRecommendSupplier extends ActiveRecord
{
    const RECOMMEND_GOODS_TYPE_CAROUSEL = 0;
    const RECOMMEND_GOODS_TYPE_FIRST = 1;
    const RECOMMEND_GOODS_TYPE_SECOND = 2;
    const CACHE_KEY_FIRST = 'recommend_goods_first_supplier_';
    const CACHE_KEY_SECOND = 'recommend_goods_second_supplier_';
    const CACHE_KEY_CAROUSEL = 'recommend_goods_carousel_supplier_';
    const PAGE_SIZE_DEFAULT_ADMIN_INDEX = 1000;
    const PAGE_SIZE_DEFAULT = 12;
    const FROM_TYPE_MALL = 1;
    const FROM_TYPE_LINK = 2;
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;
    const CACHE_KEY_PREFIX_VIEWED_NUMBER = 'recommend_goods_viewed_number_supplier_';
    const CACHE_KEY_PREFIX_SOLD_NUMBER = 'recommend_goods_sold_number_supplier_';
    const SCENARIO_ADD = 'add';

    /**
     * @var array admin fields
     */
    public static $adminFields = ['id', 'sku', 'title', 'description', 'from_type', 'viewed_number', 'sold_number', 'status', 'create_time', 'image', 'url', 'platform_price'];

    /**
     * @var array from types
     */
    public static $fromTypes = [
        self::FROM_TYPE_MALL => '商家',
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
     * @var array app fields
     */
    public static $appFields = ['id', 'title', 'image', 'description', 'platform_price', 'url'];

    /**
     * @var array cache keys
     */
    private static $cacheKeys = [
        self::CACHE_KEY_CAROUSEL,
        self::CACHE_KEY_SECOND
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_recommend_supplier';
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

        $goodsRecommend = self::find()->where(['type' => self::RECOMMEND_GOODS_TYPE_FIRST, 'status' => self::STATUS_ONLINE])->one();
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
     * @param int $districtCode district code
     * @param int $page page default 1
     * @param int $size page size default 12
     * @return array
     */
    public static function second($districtCode, $page = 1, $size = self::PAGE_SIZE_DEFAULT)
    {
        $page <= 0 && $page = 1;
        $size <= 0 && $size = self::PAGE_SIZE_DEFAULT;
        $offset = ($page - 1) * $size;
        return array_slice(self::secondAll($districtCode), $offset, $size);
    }

    /**
     * Get all recommended goods for type second
     *
     * @param int $districtCode district code default null
     * @return array
     */
    public static function secondAll($districtCode = null)
    {
        $key = self::CACHE_KEY_SECOND;
        $cache = Yii::$app->cache;
        $recommendGoods = $cache->get($key);
        if (!$recommendGoods) {
            $recommendGoods = self::_secondAll($districtCode, self::$appFields);
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
     * @param int $districtCode district code default null
     * @param array $select select fields default all fields
     * @return array
     */
    private static function _secondAll($districtCode = null, $select = [])
    {
        $where = [
            'type' => self::RECOMMEND_GOODS_TYPE_SECOND,
            'status' => self::STATUS_ONLINE,
        ];
        $districtCode && $where['district_code'] = $districtCode;

        return self::find()
            ->select($select)
            ->where($where)
            ->all();
    }

    /**
     * Get carousel
     *
     * @param int $supplierId supplier id
     * @param int $districtCode district code default null
     * @return array
     */
    public static function carousel($supplierId, $districtCode = null)
    {
        return self::_carousel($supplierId, $districtCode, self::$appFields);
        /*
        $key = self::CACHE_KEY_CAROUSEL . $supplierId;
        $cache = Yii::$app->cache;
        $recommendGoods = $cache->get($key);
        if (!$recommendGoods) {
            $recommendGoods = self::_carousel($supplierId, $districtCode, self::$appFields);
            if ($recommendGoods) {
                $cache->set($key, $recommendGoods);
            }
        }

        return $recommendGoods;
        */
    }

    /**
     * Get carousel
     *
     * @access private
     * @param int $supplierId supplier id
     * @param int $districtCode district code default null
     * @param array $select select fields default all fields
     * @param array $orderBy order by fields default id desc
     * @return array
     */
    private static function _carousel($supplierId, $districtCode = null, $select = [], $orderBy = ['id' => SORT_DESC])
    {
        $where = [
            'type' => self::RECOMMEND_GOODS_TYPE_CAROUSEL,
            'status' => self::STATUS_ONLINE,
            'supplier_id' => $supplierId,
        ];
        $districtCode && $where['district_code'] = $districtCode;

        return self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->limit(Yii::$app->params['carouselMaxNumber'])
            ->all();
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
    public static function pagination($where = [], $select = [], $page = 1, $size = ModelService::PAGE_SIZE_DEFAULT, $orderBy = ['id' => SORT_ASC])
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
        $recommendList = self::find()
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
            || in_array('platform_price', $select)
            || in_array('sku', $select)
            || isset($hasViewedNumber)
            || isset($hasSoldNumber)
        ) {
            foreach ($recommendList as &$recommend) {
                isset($hasViewedNumber) && $recommend['viewed_number'] = self::viewedNumber($recommend['create_time'], $recommend['delete_time'], $recommend['id']);
//                isset($hasSoldNumber) && $recommend['sold_number'] = self::soldNumber($recommend['create_time'], $recommend['delete_time'], $recommend['id']);

                if (isset($recommend['create_time'])) {
                    if (!empty($recommend['create_time'])) {
                        $recommend['create_time'] = date('Y-m-d H:i', $recommend['create_time']);
                    }
                }

                if (isset($recommend['delete_time'])) {
                    if (!empty($recommend['delete_time'])) {
                        $recommend['delete_time'] = date('Y-m-d H:i', $recommend['delete_time']);
                    }
                }

                isset($recommend['from_type']) && $recommend['from_type'] = self::$fromTypes[$recommend['from_type']];
                isset($recommend['status']) && $recommend['status'] = self::$statuses[$recommend['status']];

                if (isset($recommend['platform_price'])) {
                    $recommend['show_price'] = $recommend['platform_price'];
                    unset($recommend['platform_price']);
                }

                if (!empty($recommend['sku'])) {
                    $goods = Goods::find()->where(['sku' => $recommend['sku']])->one();
                    if ($goods) {
                        $recommend['platform_price'] = StringService::formatPrice($goods->platform_price / 100);
                        $recommend['market_price'] = StringService::formatPrice($goods->market_price / 100);
                        $recommend['supplier_price'] = StringService::formatPrice($goods->supplier_price / 100);
                        $recommend['left_number'] = $goods->left_number;
                        $recommend['purchase_price_decoration_company'] = StringService::formatPrice($goods->purchase_price_decoration_company / 100);
                        $recommend['purchase_price_manager'] = StringService::formatPrice($goods->purchase_price_manager / 100);
                        $recommend['purchase_price_designer'] = StringService::formatPrice($goods->purchase_price_designer / 100);
                        $recommend['goods_status'] = $goods->status;
                    }
                }
            }
        }

        return $recommendList;
    }

    /**
     * Get viewed number
     *
     * @param int $createTime banner create time default 0
     * @param int $deleteTime banner delete time default 0
     * @param int $recommendId recommend id default 0
     * @return int
     */
    public static function viewedNumber($createTime = 0, $deleteTime = 0, $recommendId = 0)
    {
        $createTime = (int)$createTime;
        $deleteTime = (int)$deleteTime;
        if (!$createTime) {
            return 0;
        }

//        $key = self::CACHE_KEY_PREFIX_VIEWED_NUMBER . $createTime . '_' . $deleteTime;
//        $cache = Yii::$app->cache;
//        $viewedNumber = $cache->get($key);
//        if ($viewedNumber === false) {
//            $viewedNumber = self::_viewedNumber($createTime, $deleteTime, $recommendId);
//            $cache->set($key, $viewedNumber);
//        }

        return self::_viewedNumber($createTime, $deleteTime, $recommendId);
    }

    /**
     * Get viewed number
     *
     * @access private
     * @param int $createTime banner create time
     * @param int $deleteTime banner delete time
     * @param int $recommendId recommend id default 0
     * @return int
     */
    public static function _viewedNumber($createTime, $deleteTime, $recommendId = 0)
    {
        $where = "create_time >= {$createTime}";
        $deleteTime && $where .= " and create_time <= {$deleteTime}";
        $recommendId = (int)$recommendId;
        $recommendId && $where .= " and recommend_id = {$recommendId}";
        return (int)GoodsRecommendViewLog::find()->where($where)->asArray()->count();
    }

    /**
     * Get sold number
     *
     * @param int $createTime banner create time default 0
     * @param int $deleteTime banner delete time default 0
     * @return int
     */
    public static function soldNumber($createTime = 0, $deleteTime = 0, $recommendId = 0)
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
            $viewedNumber = self::_soldNumber($createTime, $deleteTime, $recommendId);
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
    public static function _soldNumber($createTime, $deleteTime, $recommendId = 0)
    {
        $where = "create_time >= {$createTime} and create_time <= {$deleteTime}";
        $recommendId = (int)$recommendId;
        $recommendId && $where .= " and recommend_id = {$recommendId}";
        return (int)GoodsRecommendSaleLog::find()
            ->select('sum(number) as soldNumber')
            ->where($where)
            ->asArray()
            ->all()[0]['soldNumber'];
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

//        if (self::find()->where('status = ' . self::STATUS_ONLINE . ' and ' . $where)->count()) {
//            return -1;
//        }

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

        if (self::find()->where('status = ' . self::STATUS_ONLINE . ' and ' . $where)->count()
            != count(explode(',', $ids))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Sort recommend list
     *
     * @param  array $ids recommend id list
     * @return int
     */
    public static function sort($ids)
    {
        $code = 1000;

        if (!$ids) {
            return $code;
        }

        $idArr = $ids;
        $ids = implode(',', $ids);
        $where = 'id in (' . $ids . ')';
        try {
            $recommendList = self::find()->where($where)->all();
        } catch (Exception $dbException) {
            return $code;
        }

        if (!$recommendList || count($recommendList) != count($idArr)) {
            return $code;
        }

        $transaction = Yii::$app->db->beginTransaction();

        foreach ($recommendList as $recommend) {
            $recommend->sorting_number = array_search($recommend->id, $idArr) + 1;
            if (!$recommend->save()) {
                $transaction->rollBack();

                $code = 500;
                return $code;
            }
        }

        $transaction->commit();

        $code = 200;
        return $code;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['url', 'title', 'image', 'from_type'], 'required'],
            ['status', 'in', 'range' => array_keys(self::$statuses)],
            ['type', 'in', 'range' => self::$types],
            ['from_type', 'in', 'range' => array_keys(self::$fromTypes)],
            ['sku', 'number', 'integerOnly' => true],
            ['sku', 'validateSku', 'skipOnEmpty' => false],
            ['description', 'validateDescription', 'skipOnEmpty' => false],
            ['platform_price', 'validatePlatformPrice', 'skipOnEmpty' => false],
//            ['district_code', 'validateDistrictCode', 'skipOnEmpty' => false, 'on' => self::SCENARIO_ADD],
        ];
    }

    /**
     * Validates district_code
     *
     * @param string $attribute district_code to validate
     * @return bool
     */
    public function validateDistrictCode($attribute)
    {
        if (!StringService::checkDistrict($this->$attribute)) {
            $this->addError($attribute);
            return false;
        }

        return true;
    }

    /**
     * Validates description
     *
     * @param string $attribute description to validate
     * @return bool
     */
    public function validateDescription($attribute)
    {
//        if ($this->type == self::RECOMMEND_GOODS_TYPE_CAROUSEL) {
//            return true;
//        }
//
//        if (empty($this->$attribute)) {
//            $this->addError($attribute);
//            return false;
//        }

        return true;
    }

    /**
     * Validates platform price
     *
     * @param string $attribute platform price to validate
     * @return bool
     */
    public function validatePlatformPrice($attribute)
    {
//        if ($this->type == self::RECOMMEND_GOODS_TYPE_CAROUSEL) {
//            if (isset($this->$attribute)) {
//                unset($this->$attribute);
//            }
//            return true;
//        }
//
//        if (empty($this->$attribute)) {
//            $this->addError($attribute);
//            return false;
//        }

        return true;
    }

    /**
     * Validates sku
     *
     * @param string $attribute sku to validate
     * @return bool
     */
    public function validateSku($attribute)
    {
        if ($this->from_type == self::FROM_TYPE_LINK) {
            if (isset($this->$attribute)) {
                unset($this->$attribute);
            }
            return true;
        }

        if (!$this->$attribute) {
            $this->addError($attribute);
            return false;
        }

        $goods = Goods::find()->where([$attribute => $this->$attribute])->one();
        if (!$goods) {
            $this->addError($attribute);
            return false;
        }

        $supplier = Supplier::findOne($goods->supplier_id);
        if (!$supplier) {
            $this->addError($attribute);
            return false;
        }

        return true;
    }

    /**
     * Set cache after updated model
     *
     * @param bool $insert
     * @param array $changedAttributes

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $cache = Yii::$app->cache;
        if ($this->type == self::RECOMMEND_GOODS_TYPE_CAROUSEL) {
            $cache->delete(self::CACHE_KEY_CAROUSEL . $this->supplier_id);
        } elseif ($this->type == self::RECOMMEND_GOODS_TYPE_SECOND) {
            $cache->delete(self::CACHE_KEY_SECOND . $this->supplier_id);
        }
    }*/

    /**
     * Do some ops before insertion
     *
     * @param bool $insert if is a new record
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->create_time = time();
                $this->status = self::STATUS_ONLINE;
            }
            return true;
        } else {
            return false;
        }
    }
}