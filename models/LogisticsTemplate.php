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

class LogisticsTemplate extends ActiveRecord
{
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;
    const DELIVERY_METHOD_LOGISTICS = 0;
    const DELIVERY_METHOD_HOME = 1;
    const DELIVERY_METHOD = [
        self::DELIVERY_METHOD_LOGISTICS => '快递物流',
        self::DELIVERY_METHOD_HOME => '送货上门'
    ];
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';
    const ERROR_CODE_SAME_NAME = 1008;
    const FIELDS_LIST_ADMIN = [
        'id',
        'name',
    ];

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function findOnline()
    {
        return parent::find()->where(['status' => self::STATUS_ONLINE]);
    }

    /**
     * Get template list by supplier id
     *
     * @param  int $supplierId supplier id
     * @param array $select select fields default all
     * @param int $status status default only online template
     * @return array
     */
    public static function findBySupplierId($supplierId, $select = [], $status = self::STATUS_ONLINE)
    {
        $supplierId = (int)$supplierId;
        if ($supplierId <= 0) {
            return [];
        }

        $select = $select ? implode(',', $select) : '*';

        $where = "supplier_id = {$supplierId}";
        $status && $where .= " and status = {$status}";
        return Yii::$app->db
            ->createCommand("select {$select} from {{%" . self::tableName() . "}} where {$where}")
            ->queryAll();
    }

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'logistics_template';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'delivery_method'], 'required'],
            ['name', 'string', 'length' => [1, 10]],
            [['name'], 'validateName'],
            [['delivery_method', 'delivery_cost_default', 'delivery_number_default', 'delivery_cost_delta', 'delivery_number_delta'], 'number', 'integerOnly' => true, 'min' => 0],
            ['delivery_method', 'in', 'range' => array_keys(self::DELIVERY_METHOD)],
            ['delivery_method', 'validateDeliveryMethod'],
            [['delivery_cost_default', 'delivery_number_default', 'delivery_cost_delta', 'delivery_number_delta'], 'default', 'value' => 0]
        ];
    }

    /**
     * Validates deliver_method
     *
     * @param string $attribute deliver_method to validate
     * @return bool
     */
    public function validateDeliveryMethod($attribute)
    {
        if ($this->$attribute == self::DELIVERY_METHOD_HOME) {
            return true;
        }

        if (!$this->delivery_cost_default
            && !$this->delivery_cost_delta
            && !$this->delivery_number_default
            && !$this->delivery_number_delta
        ) {
            return true;
        }

        if ($this->delivery_cost_default > 0
            && $this->delivery_cost_delta > 0
            && $this->delivery_number_default > 0
            && $this->delivery_number_delta > 0
        ) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }

    /**
     * Validates name
     *
     * @param string $attribute name to validate
     * @return bool
     */
    public function validateName($attribute)
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $this->addError($attribute);
            return false;
        }

        $supplier = Supplier::find()->where(['uid' => $user->id])->one();
        if (!$supplier) {
            $this->addError($attribute);
            return false;
        }

        if ($this->isNewRecord) {
            if (self::find()->where(['supplier_id' => $supplier->id, $attribute => $this->$attribute])->exists()) {
                $this->addError($attribute, self::ERROR_CODE_SAME_NAME . ModelService::SEPARATOR_ERRCODE_ERRMSG . Yii::$app->params['errorCodes'][self::ERROR_CODE_SAME_NAME]);
                return false;
            }
        } else {
            if ($this->isAttributeChanged($attribute)) {
                if (self::find()->where(['supplier_id' => $supplier->id, $attribute => $this->$attribute])->exists()) {
                    $this->addError($attribute, self::ERROR_CODE_SAME_NAME . ModelService::SEPARATOR_ERRCODE_ERRMSG . Yii::$app->params['errorCodes'][self::ERROR_CODE_SAME_NAME]);
                    return false;
                }
            }
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
            if ($insert) {
                $this->status = self::STATUS_ONLINE;

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

                return true;
            }

            return true;
        }
    }

    /**
     * Convert price
     */
    public function afterFind()
    {
        parent::afterFind();

        isset($this->delivery_cost_default) && $this->delivery_cost_default /= 100;
        isset($this->delivery_cost_delta) && $this->delivery_cost_delta /= 100;
    }
}