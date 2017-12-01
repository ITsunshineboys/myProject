<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_logistics_template".
 *
 * @property integer $id
 * @property string $order_no
 * @property string $sku
 * @property integer $supplier_id
 * @property string $name
 * @property integer $delivery_method
 * @property string $delivery_cost_default
 * @property integer $delivery_number_default
 * @property string $delivery_cost_delta
 * @property integer $delivery_number_delta
 * @property integer $status
 */
class OrderLogisticsTemplate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_logistics_template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_no', 'sku'], 'required'],
            [['delivery_method', 'delivery_cost_default', 'delivery_number_default', 'delivery_cost_delta', 'delivery_number_delta'], 'integer'],
            [['order_no', 'sku', 'name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_no' => 'Order No',
            'sku' => 'Sku',
            'supplier_id' => 'Supplier ID',
            'name' => 'Name',
            'delivery_method' => 'Delivery Method',
            'delivery_cost_default' => 'Delivery Cost Default',
            'delivery_number_default' => 'Delivery Number Default',
            'delivery_cost_delta' => 'Delivery Cost Delta',
            'delivery_number_delta' => 'Delivery Number Delta',
            'status' => 'Status',
        ];
    }
}
