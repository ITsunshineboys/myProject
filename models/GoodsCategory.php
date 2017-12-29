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
use yii\db\Query;
use yii\helpers\HtmlPurifier;

class GoodsCategory extends ActiveRecord
{
    const CACHE_PREFIX = 'goods_categories_';
    const CACHE_SUB_CATE_PREFIX = 'goods_category_';
    const CACHE_PREFIX_KEY_LIST = 'goods_category_cache_key_list';
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;
    const LEVEL1 = 1;
    const LEVEL2 = 2;
    const LEVEL3 = 3;
    const APP_FIELDS = ['id', 'title', 'icon', 'pid'];
    const APP_FIELDS_QUOTE = ['id', 'title', 'icon', 'path'];
    const APP_FIELDS_CATEGORY = ['id', 'title', 'pid', 'path'];
    const PAGE_SIZE_DEFAULT = 12;
    const REVIEW_STATUS_APPROVE = 2;
    const REVIEW_STATUS_REJECT = 1;
    const REVIEW_STATUS_NOT_REVIEWED = 0;
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_CATE_EDIT_SUPPLIER='cate_edit_supplier';
    const SCENARIO_REVIEW = 'review';
    const SCENARIO_TOGGLE_STATUS = 'toggle';
    const SCENARIO_RESET_OFFLINE_REASON = 'reset_offline_reason';
    const SEPARATOR_TITLES = ' - ';
    const ERROR_CODE_SAME_NAME = 1006;
    const CATEGORY_BRANDS_STYLES_SERIES = [
        'brands' => [],
        'styles' => [],
        'series' => []
    ];
    const FIELDS_HAVE_STYLE_SERIES_CATEGORIES = ['id', 'title', 'pid'];
    const NAME_STYLE = 'style';
    const NAME_SERIES = 'series';
    const FIELDS_EDIT_BRAND_SELECTED_CATEGORIES = [
        'id',
        'pid',
        'title',
    ];

    /**
     * @var array admin fields
     */
    public static $adminFields = ['id', 'title', 'icon', 'pid', 'parent_title', 'level', 'create_time', 'online_time', 'offline_time', 'approve_time', 'reject_time', 'review_status', 'reason', 'offline_reason', 'description', 'supplier_name', 'online_person', 'offline_person', 'deleted', 'path', 'attr_op_time', 'attr_op_username', 'attr_number', 'path'];

    /**
     * @var array admin fields
     */
    public static $attrAdminFields = ['id', 'title', 'parent_title', 'attr_op_time', 'attr_op_username', 'attr_number', 'level', 'path'];

    /**
     * @var array online status list
     */
    public static $statuses = [
        self::STATUS_OFFLINE => '已下架',
        self::STATUS_ONLINE => '已上架',
    ];

    /**
     * @var array level list
     */
    public static $levels = [
        self::LEVEL1 => '一级',
        self::LEVEL2 => '二级',
        self::LEVEL3 => '三级',
    ];

    /**
     * Get "current category"
     *
     * @return array
     */
    public static function forCurrent()
    {
        return [
            'id' => 0,
            'title' => Yii::$app->params['category']['admin']['currentName'],
            'icon' => ''
        ];
    }

    /**
     * Get "all category" for lhzz admin
     *
     * @return array
     */
    public static function forAll2()
    {
        return [
            'id' => 0,
            'title' => Yii::$app->params['category']['admin']['all'],
            'icon' => ''
        ];
    }

    /**
     * Get "all category"
     *
     * @return array
     */
    public static function forAll()
    {
        return [
            'id' => 0,
            'title' => Yii::$app->params['category']['admin']['allName'],
            'icon' => ''
        ];
    }

    /**
     * Get level 3 categories by level 1 category id
     *
     * @param int $leve1Pid parent id for level 1
     * @return array
     */
    public static function level3CategoriesByLevel1Pid($leve1Pid)
    {
        $level2Ids = self::categoriesByPid(['id'], $leve1Pid);
        return self::categoriesByPids($level2Ids, self::APP_FIELDS_QUOTE);
    }

    /**
     * Get direct goods categories by pid
     *
     * @param array $select category fields default empty
     * @param int $parentCategoryId parent category id default 0
     * @return array
     */
    public static function categoriesByPid($select = [], $parentCategoryId = 0)
    {
        $where = "pid = {$parentCategoryId}";
        $reviewApproveStatus = self::REVIEW_STATUS_APPROVE;
        $where .= " and deleted = 0 and (supplier_id = 0 or review_status = {$reviewApproveStatus})";
        return self::find()->select($select)->where($where)->asArray()->all();
    }

    /**
     * Get direct goods categories by pid
     *
     * @param array $pids parent id list
     * @param array $select category fields default empty
     * @return array
     */
    public static function categoriesByPids(array $pids, array $select = [])
    {
        $categories = [];
        foreach ($pids as $pid) {
            is_array($pid) && $pid = $pid['id'];
            $categories = array_merge($categories, self::categoriesByPid($select, $pid));
        }
        return $categories;
    }

    /**
     * Get direct goods categories by pid
     *
     * @param array $select category fields default empty
     * @param int $parentCategoryId parent category id default 0
     * @return array
     */
    public static function categoriesByPidWithCache($select = [], $parentCategoryId = 0)
    {
        $key = self::CACHE_PREFIX . $parentCategoryId;
        $cache = Yii::$app->cache;
        $categories = $cache->get($key);
        if (!$categories) {
            $where = "pid = {$parentCategoryId}";
            $reviewApproveStatus = self::REVIEW_STATUS_APPROVE;
            $where .= " and deleted = 0 and (supplier_id = 0 or review_status = {$reviewApproveStatus})";
            $categories = self::find()->select($select)->where($where)->asArray()->all();
            if ($categories) {
                if ($cache->set($key, $categories)) {
                    $keys = $cache->get(self::CACHE_PREFIX_KEY_LIST);
                    if (!$keys) {
                        $keys = [];
                    }

                    if ($key && !in_array($key, $keys)) {
                        $keys[] = $key;
                        $cache->set(self::CACHE_PREFIX_KEY_LIST, $keys);
                    }
                }
            }
        }

        return $categories;
    }

    /**
     * Get category list
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
        $offset = ($page - 1) * $size;
        $categoryList = self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();
        foreach ($categoryList as &$category) {
            if (isset($category['create_time'])) {
                $category['create_time'] = date('Y-m-d H:i', $category['create_time']);
            }

            if (isset($category['online_time'])) {
                $category['online_time'] = date('Y-m-d H:i', $category['online_time']);
            }

            if (isset($category['offline_time'])) {
                $category['offline_time'] = date('Y-m-d H:i', $category['offline_time']);
            }

            if (isset($category['level']) && isset($category['path'])) {
                $category['titles'] = '';
                if ($category['level'] == self::LEVEL3) {
                    $path = trim($category['path'], ',');
                    list($rootId, $parentId, $id) = explode(',', $path);
                    $rootCategory = self::findOne($rootId);
                    $category['titles'] = $rootCategory->title
                        . self::SEPARATOR_TITLES
                        . $category['parent_title']
                        . self::SEPARATOR_TITLES
                        . $category['title'];
                } elseif ($category['level'] == self::LEVEL2) {
                    $category['titles'] = $category['parent_title']
                        . self::SEPARATOR_TITLES
                        . $category['title'];
                } elseif ($category['level'] == self::LEVEL1) {
                    $category['titles'] = $category['title'];
                }

                $category['level'] = self::$levels[$category['level']];
            }

            if (isset($category['review_status'])) {
                $category['review_status'] = Yii::$app->params['reviewStatuses'][$category['review_status']];
            }

            if (isset($category['approve_time']) || isset($category['reject_time'])) {
                $category['review_time'] = date('Y-m-d H:i', $category['approve_time'] > 0 ? $category['approve_time'] : $category['reject_time']);
                if (isset($category['approve_time'])) {
                    unset($category['approve_time']);
                }
                if (isset($category['reject_time'])) {
                    unset($category['reject_time']);
                }
            }

//            if (isset($category['supplier_name'])) {
//                if (!empty($category['supplier_name'])) {
//                    $category['applicant'] = $category['supplier_name'];
//                } else {
//                    if ($category['deleted'] == self::STATUS_ONLINE) {
//                        $category['applicant'] = $category['offline_person'];
//                    } else {
//                        $category['applicant'] = $category['online_person'];
//                    }
//                }
//            }

            if (isset($category['deleted'])) {
                $category['status'] = self::$statuses[1 - $category['deleted']];
                unset($category['deleted']);
            }

//            if (isset($category['offline_person'])) {
//                unset($category['offline_person']);
//            }
//            if (isset($category['online_person'])) {
//                unset($category['online_person']);
//            }

            if (isset($category['attr_op_time'])) {
                $category['attr_op_time'] = $category['attr_op_time'] > 0
                    ? date('Y-m-d H:i', $category['attr_op_time'])
                    : '';
            }
        }

        return $categoryList;
    }

    /**
     * Check if can disable category records
     *
     * @param  string $ids category record ids separated by commas
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

        if (self::find()->where('deleted = 0 and ' . $where)->count()
            != count(explode(',', $ids))
        ) {
            return false;
        }

        if (self::find()->where('review_status <> ' . self::REVIEW_STATUS_APPROVE . ' and ' . $where)->count()) {
            return false;
        }

        return true;
    }

    /**
     * Check if can enable category records
     *
     * @param  string $ids category record ids separated by commas
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

        if (self::find()->where($where)->count() != count(explode(',', $ids))) {
            return false;
        }

        if (self::find()->where('deleted = ' . self::STATUS_OFFLINE . ' and ' . $where)->count()) {
            return false;
        }

        if (self::find()->where('review_status <> ' . self::REVIEW_STATUS_APPROVE . ' and ' . $where)->count()) {
            return false;
        }

        return true;
    }

    /**
     * Get all level3 category ids
     *
     * @return array
     */
    public static function allLevel3CategoryIds($onlyOnline = true)
    {
        $db = Yii::$app->db;
        $sql = "select id from {{%" . self::tableName() . "}} where pid = 0";
        $sql .= $onlyOnline ? ' and deleted = 0' : ' and review_status = ' . self::REVIEW_STATUS_APPROVE;
        $rootIds = $db->createCommand($sql)->queryColumn();
        return self::level23IdsByPids($rootIds, true, $onlyOnline);
    }

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_category';
    }

    /**
     * Get all level 2 and 3 category ids by pids
     *
     * @param  array $pids pids
     * @param int $onlyLevel3 if only get level3 categories
     * @return array
     */
    public static function level23IdsByPids(array $pids, $onlyLevel3 = false, $onlyOnline = true)
    {
        $ids = [];
        foreach ($pids as $pid) {
            $ids = array_merge($ids, self::level23Ids($pid, $onlyLevel3, $onlyOnline));
        }
        return array_unique($ids);
    }

    /**
     * Get all level 3 and/or 2 category ids by pid
     *
     * @param int $pid parent category id
     * @param bool $onlyLevel3 if only get level3 categories
     * @param bool $onlyOnline if only get online categories
     * @return array
     */
    public static function level23Ids($pid, $onlyLevel3 = false, $onlyOnline = true)
    {
        $pid = (int)$pid;
        if ($pid <= 0) {
            return [];
        }

        $category = self::findOne($pid);
        if (!$category) {
            return [];
        }

        $db = Yii::$app->db;

        $sql = "select id from {{%" . self::tableName() . "}} where pid = {$pid}";
        $sql .= $onlyOnline ? ' and deleted = 0' : ' and review_status = ' . self::REVIEW_STATUS_APPROVE;
        if ($category->isLevel2()) {
            return $db->createCommand($sql)->queryColumn();
        } elseif ($category->isLevel1()) {
            $pids = $db->createCommand($sql)->queryColumn();
            $ret = [];
            foreach ($pids as $pid) {
                $sql = "select id from {{%" . self::tableName() . "}} where pid = {$pid}";
                $sql .= $onlyOnline ? ' and deleted = 0' : ' and review_status = ' . self::REVIEW_STATUS_APPROVE;
                $ret = array_merge($ret, $db->createCommand($sql)->queryColumn());
            }

            return array_unique($onlyLevel3 ? $ret : array_merge($ret, $pids));
        } elseif ($category->isLevel3()) {
            if ($onlyOnline) {
                if ($category->deleted == 0) {
                    return [$pid];
                }
            }
            return [$pid];
        }

        return [];
    }

    /**
     * Check if level 2
     *
     * @return bool
     */
    public function isLevel2()
    {
        return $this->level == self::LEVEL2;
    }

    /**
     * Check if level 1
     *
     * @return bool
     */
    public function isLevel1()
    {
        return $this->level == self::LEVEL1;
    }

    /**
     * Check if level 3
     *
     * @return bool
     */
    public function isLevel3()
    {
        return $this->level == self::LEVEL3;
    }

    /**
     * Disable categories by ids
     *
     * @param int $ids category ids
     */
    public static function disableByIds(array $ids)
    {
        if ($ids) {
            $ids = implode(',', $ids);
            $where = 'id in(' . $ids . ')';
            self::updateAll([
                'deleted' => self::STATUS_ONLINE,
                'offline_time' => time()
            ], $where);
        }
    }

    /**
     * Get brands, styles or series by category id
     *
     * @param $categoryId category id
     * @param array $fields data fields
     * @param int $fromAddGoodsPage if from "add goods" page default 0
     * @return array|int
     */
    public static function brandsStylesSeriesByCategoryId($categoryId, array $fields, $fromAddGoodsPage = 0)
    {
        $brandsStylesSeries = [];

        $categoryId = (int)$categoryId;
        if ($categoryId <= 0) {
            return $brandsStylesSeries;
        }

        if ($fields && array_diff($fields, array_keys(self::CATEGORY_BRANDS_STYLES_SERIES))) {
            $code = 1000;
            return $code;
        }

        if ($fields) {
            in_array('styles', $fields) && $styles = Style::stylesByCategoryId($categoryId);
            in_array('series', $fields) && $series = Series::seriesByCategoryId($categoryId);

            foreach ($fields as $field) {
                if ($field == 'brands') {
                    $brandsStylesSeries[$field] = BrandCategory::brandsByCategoryId($categoryId, $fromAddGoodsPage);
                } elseif ($field == 'styles') {
                    $brandsStylesSeries[$field] = $styles;
                } else {
                    $brandsStylesSeries[$field] = $series;
                }
            }
        } else {
            $fields = array_keys(self::CATEGORY_BRANDS_STYLES_SERIES);
            in_array('styles', $fields) && $styles = Style::stylesByCategoryId($categoryId);
            in_array('series', $fields) && $series = Series::seriesByCategoryId($categoryId);

            foreach (self::CATEGORY_BRANDS_STYLES_SERIES as $field => $v) {
                if ($field == 'brands') {
                    $brandsStylesSeries[$field] = BrandCategory::brandsByCategoryId($categoryId, $fromAddGoodsPage);
                } elseif ($field == 'styles') {
                    $brandsStylesSeries[$field] = $styles;
                } else {
                    $brandsStylesSeries[$field] = $series;
                }
            }
        }

        return $brandsStylesSeries;
    }

    /**
     * Goods category id find pid
     * @param $goods
     * @return array|ActiveRecord[]
     */
    public static function findLevel($level)
    {
        $select = "	goods_category.title,goods_category.id";
        $all = self::find()
            ->asArray()
            ->select($select)
            ->where(['and',['deleted'=>0],['in', 'level', $level]])
            ->all();
        return $all;
    }

    /**
     * Get categories by id list
     *
     * @param array $ids id list
     * @param array $select select fields default id, title and icon
     * @return array
     */
    public static function findByIds(array $ids, array $select = self::APP_FIELDS)
    {
        return self::find()->select($select)->where(['in', 'id', $ids])->asArray()->all();
    }

    /**
     * Get categories by title
     *
     * @param string $title title
     * @param array $select select fields default id, title and icon
     * @return array
     */
    public static function findByTitle($title, array $select = self::APP_FIELDS)
    {
        return self::find()->select($select)->where(['like', 'title', $title])->asArray()->all();
    }

    /**
     * Get online categories by title
     *
     * @param string $title title
     * @param array $select select fields default id, title and icon
     * @return array
     */
    public static function findValidLvl3ByTitle($title, array $select = self::APP_FIELDS)
    {
        return self::find()
            ->select($select)
            ->where(['deleted' => 0, 'level' => self::LEVEL3])
            ->andWhere(['like', 'title', $title])
            ->asArray()
            ->all();
    }

    /**
     * Reset category attribute has_style and/or has_series
     *
     * @param ActiveRecord $operator operator
     * @param array $newCatIds category id list to be reset
     * @param string $type $type type(style, seires or both) default both
     * @return int
     */
    public static function resetStyleSeries(ActiveRecord $operator, array $newCatIds = [], $type = '')
    {
        switch ($type) {
            case self::NAME_STYLE:
                $updateAttrs = ['has_style' => 0];
                break;
            case self::NAME_SERIES:
                $updateAttrs = ['has_series' => 0];
                break;
            default:
                $updateAttrs = ['has_style' => 0, 'has_series' => 0];
                break;
        }

        $fieldName = 'id';
        $fields = [$fieldName];
        $rows = self::haveStyleSeriesCategoriesByPid(0, $type, $fields);
        $catIds = StringService::valuesByKey($rows, $fieldName);

        $tran = Yii::$app->db->beginTransaction();
        $code = 500;
        try {
            if (!StringService::checkArrayIdentity($catIds, $newCatIds)) {
                $reducedCatIds = array_diff($catIds, $newCatIds);
                $reducedAttrs = array_map(function ($row) {
                    return 0;
                }, $updateAttrs);

                $increasedAttrs = array_map(function ($row) {
                    return 1;
                }, $updateAttrs);
                $increasedCatIds = array_diff($newCatIds, $catIds);

                if ($reducedCatIds) {
                    self::updateAll($reducedAttrs, ['in', 'id', $reducedCatIds]);
                    Goods::disableGoodsByCategoryIds($reducedCatIds, $operator, Yii::$app->params['style_series']['offline_reason']);
                }
                $increasedCatIds && self::updateAll($increasedAttrs, ['in', 'id', $increasedCatIds]);
            }

            $tran->commit();
            $code = 200;
        } catch (\Exception $e) {
            $tran->rollBack();
        }
        return $code;
    }

    /**
     * Get categories which have style or/and series
     *
     * @param int $pid parent category id default 0
     * @param string $type type(style, seires or both) default both
     * @param array $select select fields default id and title
     * @return array
     */
    public static function haveStyleSeriesCategoriesByPid($pid = 0, $type = '', array $select = self::FIELDS_HAVE_STYLE_SERIES_CATEGORIES)
    {
        $query = new Query;
        $query
            ->select($select)
            ->from(self::tableName())
            ->where(['level' => self::LEVEL3]);
        $pid > 0 && $query->andWhere(['pid' => $pid]);

        switch ($type) {
            case self::NAME_STYLE:
                $query->andWhere(['has_style' => 1]);
                break;
            case self::NAME_SERIES:
                $query->andWhere(['has_series' => 1]);
                break;
            default:
                $query->andWhere([
                    'or',
                    ['has_style' => 1],
                    ['has_series' => 1],
                ]);
                break;
        }

        return $query->all();
    }

    /**
     * Get full title
     *
     * @return string
     */
    public function fullTitle()
    {
        $fullTitle = '';

        if ($this->level == self::LEVEL3) {
            $path = trim($this->path, ',');
            list($rootId, $parentId, $id) = explode(',', $path);
            $rootCategory = self::findOne($rootId);
            $fullTitle = $rootCategory->title
                . self::SEPARATOR_TITLES
                . $this->parent_title
                . self::SEPARATOR_TITLES
                . $this->title;
        } elseif ($this->level == self::LEVEL2) {
            $fullTitle = $this->parent_title
                . self::SEPARATOR_TITLES
                . $this->title;
        } elseif ($this->level == self::LEVEL1) {
            $fullTitle = $this->title;
        }

        return $fullTitle;
    }

    /**
     * Get goods categories(including subcategories) by pid
     *
     * @param int $pid parent id
     * @return array goods categories
     */
    public function categories($pid = 0)
    {
        $cache = Yii::$app->cache;
        $key = self::CACHE_SUB_CATE_PREFIX . $pid;
        $categories = $cache->get($key);
        if (!$categories) {
            $categories = $this->_categories($pid);
            $categories && $cache->set($key, $categories);
        }
        return $categories;
    }

    /**
     * Get goods categories by pid
     *
     * @access private
     * @param int $pid parent id
     * @return array goods categories
     */
    private function _categories($pid = 0)
    {
        $db = Yii::$app->db;
        $sql = "select id, title from {{%goods_category}} where pid= :pid";
        $categories = $db->createCommand($sql)->bindParam(':pid', $pid)->queryAll();
        $arr = [];
        foreach ($categories as $category) {
            $category['children'] = $this->_categories($category['id']); // 调用函数，传入参数，继续查询下级
            $arr[] = $category; // 组合数组
        }
        return $arr;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['title', 'icon', 'pid'], 'required'],
            ['title', 'string', 'length' => [1, 10]],
            [['title'], 'unique', 'on' => self::SCENARIO_ADD, 'message' => self::ERROR_CODE_SAME_NAME . ModelService::SEPARATOR_ERRCODE_ERRMSG . Yii::$app->params['errorCodes'][self::ERROR_CODE_SAME_NAME]],
            [['title'], 'validateTitle', 'on' => [self::SCENARIO_EDIT, self::SCENARIO_CATE_EDIT_SUPPLIER]],
            [['pid', 'approve_time', 'review_status', 'supplier_id'], 'number', 'integerOnly' => true, 'min' => 0],
            ['pid', 'validatePid'],
            [['reason', 'description', 'icon'], 'string'],
            ['description', 'safe'],
            ['description', 'default', 'value' => ''],
            ['review_status', 'in', 'range' => array_keys(Yii::$app->params['reviewStatuses'])],
            ['review_status', 'validateReviewStatus', 'on' => self::SCENARIO_REVIEW],
            ['supplier_id', 'validateSupplierId', 'on' => self::SCENARIO_REVIEW],
            ['approve_time', 'validateApproveTime', 'on' => self::SCENARIO_REVIEW],
            ['review_status', 'validateReviewStatusEdit', 'on' => [self::SCENARIO_EDIT, self::SCENARIO_RESET_OFFLINE_REASON, self::SCENARIO_TOGGLE_STATUS]],
            ['review_status', 'validateStatusEditSupplier', 'on' => [self::SCENARIO_CATE_EDIT_SUPPLIER,]],
//            [['title'], 'validateTitle', 'on' => self::SCENARIO_NEW_CATE_EDIT],
        ];
    }

    /**
     * Validates review_status when edit
     *
     * @param string $attribute review_status to validate
     * @return bool
     */
    public function validateReviewStatusEdit($attribute)
    {

        if ($this->$attribute == self::REVIEW_STATUS_APPROVE) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }

    /**
     * @param $attribute
     * @return bool
     */
    public function validateStatusEditSupplier($attribute)
    {

        if ($this->$attribute == self::REVIEW_STATUS_REJECT) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }

    /**
     * Validates title when edit category
     *
     * @param string $attribute title to validate
     * @return bool
     */
    public function validateTitle($attribute)
    {
        if (!$this->isNewRecord && $this->isAttributeChanged($attribute)) {
            if (self::find()->where([$attribute => $this->$attribute])->exists()) {
                $this->addError($attribute, self::ERROR_CODE_SAME_NAME . ModelService::SEPARATOR_ERRCODE_ERRMSG . Yii::$app->params['errorCodes'][self::ERROR_CODE_SAME_NAME]);
                return false;
            }
        }

        return true;
    }

    /**
     * Could review only once
     *
     * @param string $attribute approve_time and reject_time to validate
     * @return bool
     */
    public function validateApproveTime($attribute)
    {
        if ($this->$attribute > 0 || $this->reject_time > 0) {
            $this->addError($attribute);
            return false;
        }

        return true;
    }

    /**
     * Validates review_status
     *
     * @param string $attribute review_status to validate
     * @return bool
     */
    public function validateReviewStatus($attribute)
    {
        if (in_array($this->$attribute, [
            self::REVIEW_STATUS_REJECT,
            self::REVIEW_STATUS_APPROVE
        ])) {
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
        if ($this->$attribute > 0) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }

    /**
     * Validates pid
     *
     * @param string $attribute pid to validate
     * @return bool
     */
    public function validatePid($attribute)
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $this->addError($attribute);
            return false;
        }

        if ($user->login_role_id == Yii::$app->params['supplierRoleId']) {
            if ($this->$attribute == 0) {
                $this->addError($attribute);
                return false;
            }
        }

        if ($this->$attribute == 0 || self::findOne($this->$attribute)) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }

    /**
     * Check if of different level
     *
     * @param  int $newPid new parent category id
     * @return int
     */
    public function checkSameLevelByPid($newPid)
    {
        if ($this->supplier_id !=0 && $this->review_status == self::REVIEW_STATUS_REJECT) {
            return 200;
        }

        $newPid = (int)$newPid;
        if ($newPid == 0) {
            if ($this->level != self::LEVEL1) {
                return 1005;
            }
        } elseif ($newPid > 0) {
            $newParentCategory = self::findOne($newPid);
            if (!$newParentCategory) {
                return 1000;
            }

            if ($newParentCategory->level + 1 != $this->level) {
                return 1005;
            }
        } else {
            return 1000;
        }

        return 200;
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
                $this->deleted = self::STATUS_ONLINE;
                $this->offline_time = $now;

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
                    $this->supplier_name = $supplier->nickname;
                } elseif ($user->login_role_id == Yii::$app->params['lhzzRoleId']) {
                    $this->deleted = self::STATUS_ONLINE;
                    $this->offline_time = $now;

                    $lhzz = Lhzz::find()->where(['uid' => $user->id])->one();
                    if (!$lhzz) {
                        return false;
                    }

                    $this->user_id = $lhzz->id;
                    $this->user_name = $lhzz->nickname;
                    $this->review_status = self::REVIEW_STATUS_APPROVE;
                    $this->approve_time = $now;
                    $this->offline_person = $lhzz->nickname;
                }

                $pid = $this->pid + 1;
                $this->setLevelPath($pid);

                $this->description && $this->description = HtmlPurifier::process($this->description);
            } else {
                if ($this->scenario == self::SCENARIO_REVIEW) {
                    if ($this->review_status == self::REVIEW_STATUS_REJECT) {
                        $this->reject_time = $now;
                        $this->approve_time = 0;
                    } elseif ($this->review_status == self::REVIEW_STATUS_APPROVE) {
                        $this->approve_time = $now;
                        $this->reject_time = 0;
                        $this->deleted = self::STATUS_OFFLINE;
                        $this->online_time = $now;
                    }
                } elseif (in_array($this->scenario, [self::SCENARIO_EDIT, self::SCENARIO_CATE_EDIT_SUPPLIER])) {
                    if ($this->isAttributeChanged('pid')) {
                        $pid = $this->pid + 1;
                        $this->setLevelPath($pid);
                    } elseif ($this->isAttributeChanged('description')) {
                        $this->description = HtmlPurifier::process($this->description);
                    }
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Set level and path by pid
     *
     * @param int $pid parent category id
     */
    public function setLevelPath($pid)
    {
        if ($pid != $this->pid) {
            if ($this->pid) {
                $parentCategory = self::findOne($this->pid);
                $this->level = $parentCategory->level + 1;
                $this->path = $parentCategory->path . $this->id . ',';
                $this->parent_title = $parentCategory->title;
            } else {
                $this->level = self::LEVEL1;
                $this->path = $this->id . ',';
                $this->parent_title = '';
            }
        }
    }

    /**
     * Update sub-categories' paths
     *
     * @param $pid parent category id
     */
    public function updateSubCategoryPath($pid)
    {
        $children = self::find()->where(['pid' => $pid])->all();

        if (!$children) {
            return;
        }

        foreach ($children as $child) {
            $child->path = $this->path . $child->id . ModelService::SEPARATOR_GENERAL;
            if (!$child->save(false)) {
                throw new \RuntimeException('category not saved');
            }

            $child->updateSubCategoryPath($child->id);
        }
    }

    /**
     * Do some ops after insertion
     *
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $pid = $this->pid + 1;
            $this->setLevelPath($pid);

            if (!$this->save()) {
                $this->delete();
            }
        }

        $key = self::CACHE_PREFIX . $this->pid;
        Yii::$app->cache->delete($key);
    }

    public function getChildren()
    {
    return $this->hasMany(self::className(),['pid'=>'id']);
    }


    public static function GoodsAttrValue($material)
    {
        $select = "goods.id,goods_category.title,goods_attr.name,goods_attr.value";
        return self::find()
            ->asArray()
            ->select($select)
            ->leftJoin('goods_attr', 'goods_attr.category_id = goods_category.id')
            ->leftJoin('goods', 'goods.category_id = goods_category.id')
            ->where(['in', 'goods_category.id', $material])
            ->groupBy('goods_category.title')
            ->all();
    }

    public static function attrValue($material)
    {
        $select = "goods_attr.id,goods_category.title,goods_attr.name,goods_attr.value";
        return self::find()
            ->asArray()
            ->select($select)
            ->leftJoin('goods_attr', 'goods_attr.category_id = goods_category.id')
            ->leftJoin('goods', 'goods.category_id = goods_category.id')
            ->where(['in', 'goods_attr.goods_id', $material])
//            ->groupBy('goods_category.title')
            ->all();
    }

    public static function findByHeadTitle()
    {
        return self::find()
            ->asArray()
            ->select('id,title')
            ->where(['and',['level'=>1],['deleted'=>0]])
            ->all();
    }

    public static function findById($id,$select = [])
    {
        return self::find()
            ->asArray()
            ->select($select)
            ->where(['id'=>$id])
            ->one();
    }


    public  static  function  GetCateGoryById($category_id)
    {
        $category=GoodsCategory::findOne($category_id);
        if ($category)
        {
            $category_arr=explode(',',$category->path);
            $first_category=GoodsCategory::find()
                ->select('path,title,parent_title')
                ->where(['id'=>$category_arr[0]])
                ->one();
            return $first_category->title.'-'.$category->parent_title.'-'.$category->title;
        }else{
            return '';
        }

    }
    


    public  static  function  GetCategory($category_id)
    {
        $category=GoodsCategory::findOne($category_id);
        if ($category)
        {
            $category_arr=explode(',',$category->path);
            $first_category=GoodsCategory::find()
                ->select('path,title,parent_title')
                ->where(['id'=>$category_arr[0]])
                ->one();
            return [
                'one_category'=>$first_category->title,
                'two_category'=>$category->parent_title,
                'three_category'=>$category->title
            ];
        }else{
            return [];
        }
    }
}