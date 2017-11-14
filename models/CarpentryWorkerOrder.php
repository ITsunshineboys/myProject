<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "carpentry_worker_order".
 *
 * @property string $id
 * @property integer $order_id
 * @property integer $worker_item_id
 * @property integer $worker_craft_id
 * @property integer $count
 * @property integer $length
 * @property integer $area
 */
class CarpentryWorkerOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'carpentry_worker_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'worker_item_id', 'worker_craft_id', 'count', 'length', 'area'], 'integer'],
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
            'count' => 'Count',
            'length' => 'Length',
            'area' => 'Area',
        ];
    }
}
