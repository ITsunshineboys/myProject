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
    public static function current()
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
    public static function all()
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
     * @param array $select           category fields default empty
     * @param int   $parentCategoryId parent category id default 0
     * @return array
     */
    public static function categoriesByPid($select = [], $parentCategoryId = 0)
    {
        $key = self::CACHE_PREFIX . $parentCategoryId;
        $cache = Yii::$app->cache;
        $categories = $cache->get($key);
        if (!$categories) {
            $where = "pid = {$parentCategoryId}";
            $where .= " and deleted = 0 and (supplier_id = 0 or approve_time > 0)";
            $categories = self::find()->select($select)->where($where)->asArray()->all();
            if ($categories) {
                $cache->set($key, $categories);
            }
        }

        return $categories;
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
            [['title'], 'required'],
            [['title'], 'unique'],
            [['pid', 'approve_time'], 'number', 'integerOnly' => true, 'min' => 0],
            ['pid', 'validatePid'],
            [['reason'], 'string'],
            ['description', 'safe'],
            ['description', 'default', 'value' => ''],
        ];
    }

    /**
     * Validates pid
     *
     * @param string $attribute pid to validate
     * @return bool
     */
    public function validatePid($attribute)
    {
        if ($this->$attribute == 0) {
            return true;
        }

        if (self::findOne($this->$attribute)) {
            return true;
        }

        $this->addError($attribute);
        return false;
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
            if ($insert) {
                $this->create_time = time();
                $this->deleted = self::STATUS_ONLINE;

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
            if (!($this->validate() && $this->save())) {
                $this->delete();
            }
        }
    }
}