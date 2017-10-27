<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "waterproof_worker_order".
 *
 * @property string $id
 * @property integer $order_id
 * @property integer $worker_item_id
 * @property integer $worker_craft_id
 * @property integer $area
 * @property string $brand
 */
class WaterproofWorkerOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'waterproof_worker_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'worker_item_id', 'worker_craft_id', 'area', 'brand'], 'required'],
            [['order_id', 'worker_item_id', 'worker_craft_id', 'area'], 'integer'],
            [['brand'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'worker_item_id' => 'Worker Item ID',
            'worker_craft_id' => 'Worker Craft ID',
            'area' => 'Area',
            'brand' => 'Brand',
        ];
    }
}
