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
            [['name'], 'validateName', 'on' => self::SCENARIO_ADD],
            [['delivery_method', 'delivery_cost_default', 'delivery_number_default', 'delivery_cost_delta', 'delivery_number_delta'], 'number', 'integerOnly' => true, 'min' => 0],
            ['delivery_method', 'in', 'range' => array_keys(self::DELIVERY_METHOD), 'on' => self::SCENARIO_ADD],
            ['delivery_method', 'validateDeliveryMethod', 'on' => self::SCENARIO_ADD],
            [['delivery_cost_default', 'delivery_number_default', 'delivery_cost_delta', 'delivery_number_delta'], 'default', 'value' => 0, 'on' => self::SCENARIO_ADD]
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

        if (self::find()->where(['supplier_id' => $supplier->id, $attribute => $this->$attribute])->exists()) {
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
        }
    }
}