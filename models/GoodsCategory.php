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
    const APP_FIELDS = ['id', 'title', 'icon'];
    const PAGE_SIZE_DEFAULT = 12;
    const REVIEW_STATUS_APPROVE = 2;
    const REVIEW_STATUS_REJECT = 1;
    const REVIEW_STATUS_NOT_REVIEWED = 0;
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_REVIEW = 'review';
    const SCENARIO_TOGGLE_STATUS = 'toggle';
    const SCENARIO_RESET_OFFLINE_REASON = 'reset_offline_reason';
    const SEPARATOR_TITLES = ' - ';

    /**
     * @var array admin fields
     */
    public static $adminFields = ['id', 'title', 'icon', 'pid', 'parent_title', 'level', 'create_time', 'online_time', 'offline_time', 'approve_time', 'reject_time', 'review_status', 'reason', 'offline_reason', 'description', 'supplier_name', 'online_person', 'offline_person', 'deleted', 'path'];

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
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_category';
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
     * @param  array $orderBy order by fields default sold_number desc
     * @return array
     */
    public static function pagination($where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = ['id' => SORT_ASC])
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

            if (isset($category['level'])) {
                $category['titles'] = '';
                if ($category['level'] == self::LEVEL3) {
                    $path = trim($category['path'], ',');
                    list($rootId, $parentId, $id) = explode(',', $path);
                    $rootCategory = self::findOne($rootId);
                    $category['titles'] = $rootCategory->title
                        . self::SEPARATOR_TITLES
                        . $category['parent_title']
                        . self::SEPARATOR_TITLES
                        . $category['title']
                    ;
                } elseif ($category['level'] == self::LEVEL2) {
                    $category['titles'] = $category['parent_title']
                        . self::SEPARATOR_TITLES
                        . $category['title']
                    ;
                } elseif ($category['level'] == self::LEVEL1) {
                    $category['titles'] = $category['title']
                    ;
                }

                $category['level'] = self::$levels[$category['level']];

                if (isset($category['path'])) {
                    unset($category['path']);
                }
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

            if (!empty($category['supplier_name'])) {
                $category['applicant'] = $category['supplier_name'];
            } else {
                if ($category['deleted'] == self::STATUS_ONLINE) {
                    $category['applicant'] = $category['offline_person'];
                } else {
                    $category['applicant'] = $category['online_person'];
                }
            }

            if (isset($category['deleted'])) {
                $category['status'] = self::$statuses[1 - $category['deleted']];
                unset($category['deleted']);
            }

            if (isset($category['offline_person'])) {
                unset($category['offline_person']);
            }
            if (isset($category['online_person'])) {
                unset($category['online_person']);
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

        if (self::find()->where('deleted = ' . self::STATUS_ONLINE . ' and ' . $where)->count()) {
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
     * Get all level 2 and 3 category ids by pids
     *
     * @param  array $pids pids
     * @return array
     */
    public static function level23IdsByPids(array $pids)
    {
        $ids = [];
        foreach ($pids as $pid) {
            $ids = array_merge($ids, self::level23Ids($pid));
        }
        return array_unique($ids);
    }

    /**
     * Get all level 2 and 3 category ids by pid
     *
     * @param  int $pid parent category id
     * @return array
     */
    public static function level23Ids($pid)
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
        if ($category->level == self::LEVEL2) {
            return $db->createCommand("select id from {{%goods_category}} where pid = {$pid}")->queryColumn();
        } elseif ($category->level == self::LEVEL1) {
            $pids = $db->createCommand("select id from {{%goods_category}} where pid = {$pid}")->queryColumn();
            $ret = [];
            foreach ($pids as $pid) {
                $ret = array_merge($ret, $db->createCommand("select id from {{%goods_category}} where pid = {$pid}")->queryColumn());
            }

            return array_unique(array_merge($ret, $pids));
        }

        return [];
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
            [['title'], 'unique', 'on' => self::SCENARIO_ADD],
            [['title'], 'validateTitle', 'on' => self::SCENARIO_EDIT],
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
     * Validates title when edit category
     *
     * @param string $attribute title to validate
     * @return bool
     */
    public function validateTitle($attribute)
    {
        if (!$this->isNewRecord && $this->isAttributeChanged($attribute)) {
            if (self::find()->where([$attribute => $this->$attribute])->exists()) {
                $this->addError($attribute);
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
                } elseif ($this->scenario == self::SCENARIO_EDIT) {
                    if ($this->isAttributeChanged('pid')) {
                        $pid = $this->pid + 1;
                        $this->setLevelPath($pid);
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
     * @param $pid
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
}