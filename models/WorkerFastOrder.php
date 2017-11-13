<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "worker_fast_order".
 *
 * @property integer $id
 * @property integer $worker_order_id
 * @property string $worker_item_ids
 * @property integer $worker_type_id
 */
class WorkerFastOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_fast_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['worker_order_id', 'worker_type_id'], 'integer'],
            [['worker_item_ids'], 'required'],
            [['worker_item_ids'], 'string', 'max' => 50],
        ];
    }

}
