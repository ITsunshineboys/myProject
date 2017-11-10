<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hydropower_worker_order".
 *
 * @property string $id
 * @property string $order_no
 * @property integer $worker_item_id
 * @property integer $worker_craft_id
 * @property integer $electricity
 * @property string $length
 */
class HydropowerWorkerOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hydropower_worker_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['worker_item_id', 'worker_craft_id', 'length'], 'integer'],
            [['order_no'], 'string', 'max' => 50],
        ];
    }


}
