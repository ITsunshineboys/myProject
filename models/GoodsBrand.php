<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use app\services\ModelService;
use Yii;
use yii\db\ActiveRecord;

class GoodsBrand extends ActiveRecord
{
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_NEW_BRAND_EDIT = 'new_brand_edit';
    const SCENARIO_REVIEW = 'review';
    const SCENARIO_TOGGLE_STATUS = 'toggle';
    const SCENARIO_RESET_OFFLINE_REASON = 'reset_offline_reason';
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;
    const REVIEW_STATUS_APPROVE = 2;
    const REVIEW_STATUS_REJECT = 1;
    const REVIEW_STATUS_NOT_REVIEWED = 0;
    const PAGE_SIZE_DEFAULT = 12;
    const ERROR_CODE_SAME_NAME = 1007;
    const FIELDS_REVIEW_LIST = ['id', 'name', 'logo', 'certificate', 'create_time', 'online_time', 'offline_time', 'approve_time', 'reject_time', 'review_status', 'reason', 'offline_reason', 'supplier_name', 'online_person', 'offline_person', 'supplier_id'];
    /**
     * @var array admin fields
     */
    public static $adminFields = ['id', 'name', 'logo', 'certificate', 'create_time', 'online_time', 'offline_time', 'approve_time', 'reject_time', 'review_status', 'reason', 'offline_reason', 'supplier_name', 'online_person', 'offline_person', 'status'];
    /**
     * @var array online status list
     */
    public static $statuses = [
        self::STATUS_OFFLINE => '已下架',
        self::STATUS_ONLINE => '已上架',
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_brand';
    }

    /**
     * Get brands by brand name
     *
     * @param string $brandName brand name
     * @return array
     */
    public static function findByName($brandName, $select = [])
    {
        if (!$brandName) {
            return [];
        }

        $where = "name like '%{$brandName}%'";
        return self::find()->select($select)->where($where)->all();
    }

    /**
     * @param array $brandIds
     * @return array|ActiveRecord[]
     */
    public static function findByIds($brandIds = [])
    {
        if (empty($brandIds)) {
            return [];
        } else {
            foreach ($brandIds as $brandId) {
                $id [] = $brandId['brand_id'];
            }
        }
        return self::find()->where(['in', 'id', $id])->all();
    }

    /**
     * Find ids by mobile
     *
     * @param int $mobile
     * @return array
     */
    public static function findIdsByMobile($mobile)
    {
        if (empty($mobile)) {
            return [];
        }
        $uids = User::find()->select(['id'])->where(['like', 'mobile', $mobile])->column();
        $supplierIds = Supplier::find()->select(['id'])->where(['in', 'uid', $uids])->column();
        return self::find()->select(['id'])->where(['in', 'supplier_id', $supplierIds])->column();
    }

    /**
     * Check if can disable brand records
     *
     * @param  string $ids brand record ids separated by commas
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

        if (self::find()->where('review_status <> ' . self::REVIEW_STATUS_APPROVE . ' and ' . $where)->count()) {
            return false;
        }

        return true;
    }

    /**
     * Check if can enable brand records
     *
     * @param  string $ids brand record ids separated by commas
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

        if (self::find()->where('status = ' . self::STATUS_ONLINE . ' and ' . $where)->count()) {
            return false;
        }

        if (self::find()->where('review_status <> ' . self::REVIEW_STATUS_APPROVE . ' and ' . $where)->count()) {
            return false;
        }

        return true;
    }

    /**
     * Get brand list
     *
     * @param  array $where search condition
     * @param  array $select select fields default all fields
     * @param  int $page page number default 1
     * @param  int $size page size default 12
     * @param  array $orderBy order by fields default id desc
     * @return array
     */
    public static function pagination($where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = ['id' => SORT_ASC])
    {
        $offset = ($page - 1) * $size;
        $brandList = self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();
        foreach ($brandList as &$brand) {
            if (isset($brand['create_time'])) {
                $brand['create_time'] = date('Y-m-d H:i', $brand['create_time']);
            }

            if (isset($brand['online_time'])) {
                $brand['online_time'] = date('Y-m-d H:i', $brand['online_time']);
            }

            if (isset($brand['offline_time'])) {
                $brand['offline_time'] = date('Y-m-d H:i', $brand['offline_time']);
            }

            if (isset($brand['review_status'])) {
                $brand['review_status'] = Yii::$app->params['reviewStatuses'][$brand['review_status']];
            }

            if (isset($brand['approve_time']) || isset($brand['reject_time'])) {
                $brand['review_time'] = date('Y-m-d H:i', $brand['approve_time'] > 0 ? $brand['approve_time'] : $brand['reject_time']);
                if (isset($brand['approve_time'])) {
                    unset($brand['approve_time']);
                }
                if (isset($brand['reject_time'])) {
                    unset($brand['reject_time']);
                }
            }

            if (!empty($brand['supplier_name'])) {
                $brand['applicant'] = $brand['supplier_name'];
            } elseif (isset($brand['status'])) {
                if ($brand['status'] == self::STATUS_ONLINE) {
                    $brand['applicant'] = $brand['online_person'];
                } else {
                    $brand['applicant'] = $brand['offline_person'];
                }
            }

            if (isset($brand['status'])) {
                $brand['status'] = self::$statuses[$brand['status']];
            }

            if (isset($brand['offline_person'])) {
                unset($brand['offline_person']);
            }
            if (isset($brand['online_person'])) {
                unset($brand['online_person']);
            }

            if (isset($brand['id'])) {
                $brand['category_titles'] = BrandCategory::categoryNamesByBrandId($brand['id']);
                $brand['categories'] = BrandCategory::categoriesByBrandId($brand['id'], GoodsCategory::FIELDS_EDIT_BRAND_SELECTED_CATEGORIES);
            }

            if (!empty($brand['supplier_id'])) {
                $supplier = Supplier::findOne($brand['supplier_id']);
                $brand['mobile'] = User::findOne($supplier->uid)->mobile;
            }
        }

        return $brandList;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'certificate', 'logo'], 'required'],
            ['name', 'string', 'length' => [1, 16]],
            [['name'], 'unique', 'on' => self::SCENARIO_ADD, 'message' => self::ERROR_CODE_SAME_NAME . ModelService::SEPARATOR_ERRCODE_ERRMSG . Yii::$app->params['errorCodes'][self::ERROR_CODE_SAME_NAME]],
            [['name'], 'validateName', 'on' => self::SCENARIO_EDIT],
            ['review_status', 'in', 'range' => array_keys(Yii::$app->params['reviewStatuses']), 'on' => self::SCENARIO_REVIEW],
            ['review_status', 'validateReviewStatus', 'on' => self::SCENARIO_REVIEW],
            ['approve_time', 'validateApproveTime', 'on' => self::SCENARIO_REVIEW],
            ['review_status', 'validateReviewStatusEdit', 'on' => [self::SCENARIO_EDIT, self::SCENARIO_RESET_OFFLINE_REASON, self::SCENARIO_TOGGLE_STATUS]],
            ['review_status', 'validateStatusEdit', 'on' => [self::SCENARIO_NEW_BRAND_EDIT]],
            [['name'], 'validateName', 'on' => self::SCENARIO_NEW_BRAND_EDIT],
        ];
    }

    /**
     * Validates name when edit
     *
     * @param string $attribute name to validate
     * @return bool
     */
    public function validateName($attribute)
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
    public function validateStatusEdit($attribute)
    {

        if ($this->$attribute == self::REVIEW_STATUS_REJECT) {
            return true;
        }

        $this->addError($attribute);
        return false;
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
                $this->status = self::STATUS_OFFLINE;
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
                    $this->status = self::STATUS_OFFLINE;
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
            } else {
                if ($this->scenario == self::SCENARIO_REVIEW) {
                    if ($this->review_status == self::REVIEW_STATUS_REJECT) {
                        $this->reject_time = $now;
                        $this->approve_time = 0;
                    } elseif ($this->review_status == self::REVIEW_STATUS_APPROVE) {
                        $this->approve_time = $now;
                        $this->reject_time = 0;
                        $this->status = self::STATUS_ONLINE;
                        $this->online_time = $now;
                    }
                } elseif ($this->scenario == self::SCENARIO_EDIT) {
                }
            }

            return true;
        } else {
            return false;
        }
    }
}