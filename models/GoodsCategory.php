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
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;
    const LEVEL1 = 1;
    const LEVEL2 = 2;
    const LEVEL3 = 3;
    const APP_FIELDS = ['id', 'title', 'icon'];
    const PAGE_SIZE_DEFAULT = 12;
    const REVIEW_STATUS_APPROVE = 2;
    const REVIEW_STATUS_REJECT = 1;
    const SCENARIO_REVIEW = 'review';
    const SCENARIO_TOGGLE_STATUS = 'toggle';

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
                $cache->set($key, $categories);
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
        $recommendList = self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();
        foreach ($recommendList as &$recommend) {
            if (isset($recommend['create_time'])) {
                if (!empty($recommend['create_time'])) {
                    $recommend['create_time'] = date('Y-m-d', $recommend['create_time']);
                }
            }
        }

        return $recommendList;
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
            [['title'], 'unique'],
            [['pid', 'approve_time', 'review_status', 'supplier_id'], 'number', 'integerOnly' => true, 'min' => 0],
            ['pid', 'validatePid'],
            [['reason', 'description', 'icon'], 'string'],
            ['description', 'safe'],
            ['description', 'default', 'value' => ''],
            ['review_status', 'in', 'range' => array_keys(Yii::$app->params['reviewStatuses'])],
            ['review_status', 'validateReviewStatus', 'on' => self::SCENARIO_REVIEW],
            ['supplier_id', 'validateSupplierId', 'on' => self::SCENARIO_REVIEW],
            ['approve_time', 'validateApproveTime', 'on' => self::SCENARIO_REVIEW],
        ];
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
     * Check if can disable category records
     *
     * @param string $ids category record ids separated by commas
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

        if (self::find()->where('deleted = ' . self::STATUS_ONLINE . ' and ' . $where)->count()) {
            return false;
        }

        return true;
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
                }
            } else {
                if ($this->scenario == self::SCENARIO_REVIEW) {
                    if ($this->review_status == self::REVIEW_STATUS_REJECT) {
                        $this->reject_time = $now;
                        $this->approve_time = 0;
                    } elseif ($this->review_status == self::REVIEW_STATUS_APPROVE) {
                        $this->approve_time = $now;
                        $this->reject_time = 0;
                    }
                }
            }

            return true;
        } else {
            return false;
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

        if ($insert && $this->pid) {
            $parentCategory = self::findOne($this->pid);
            $this->level = $parentCategory->level + 1;
            $this->path = $parentCategory->path . $this->id . ',';
            if (!$this->save()) {
                $this->delete();
            }
        }
    }
}