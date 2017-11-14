<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "backman_worker_order".
 *
 * @property string $id
 * @property integer $order_id
 * @property integer $worker_item_id
 * @property integer $worker_craft_id
 * @property integer $length
 * @property integer $area
 */
class BackmanWorkerOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'backman_worker_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'worker_item_id', 'worker_craft_id', 'length', 'area'], 'required'],
            [['order_id', 'worker_item_id', 'worker_craft_id', 'length', 'area'], 'integer'],
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
            'length' => 'Length',
            'area' => 'Area',
        ];
    }
}
