<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "paint_worker_order".
 *
 * @property string $id
 * @property string $order_no
 * @property integer $worker_item_id
 * @property integer $worker_craft_id
 * @property integer $area
 * @property string $brand
 */
class PaintWorkerOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'paint_worker_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['worker_item_id', 'brand'], 'required'],
            [['worker_item_id', 'worker_craft_id', 'area'], 'integer'],
            [['order_no', 'brand'], 'string', 'max' => 50],
        ];
    }

}
