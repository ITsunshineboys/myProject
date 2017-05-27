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

class GoodsBrand extends ActiveRecord
{
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_REVIEW = 'review';
    const SCENARIO_TOGGLE_STATUS = 'toggle';
    const SCENARIO_RESET_OFFLINE_REASON = 'reset_offline_reason';
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;
    const REVIEW_STATUS_APPROVE = 2;
    const REVIEW_STATUS_REJECT = 1;
    const REVIEW_STATUS_NOT_REVIEWED = 0;

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

        if (self::find()->where('status = ' . self::STATUS_OFFLINE . ' and ' . $where)->count()) {
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
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'certificate', 'logo'], 'required'],
            [['name'], 'unique', 'on' => self::SCENARIO_ADD],
            [['name'], 'validateName', 'on' => self::SCENARIO_EDIT],
            ['review_status', 'in', 'range' => array_keys(Yii::$app->params['reviewStatuses']), 'on' => self::SCENARIO_REVIEW],
            ['review_status', 'validateReviewStatus', 'on' => self::SCENARIO_REVIEW],
            ['approve_time', 'validateApproveTime', 'on' => self::SCENARIO_REVIEW],
            ['review_status', 'validateReviewStatusEdit', 'on' => [self::SCENARIO_EDIT, self::SCENARIO_RESET_OFFLINE_REASON, self::SCENARIO_TOGGLE_STATUS]],
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
                $this->addError($attribute);
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