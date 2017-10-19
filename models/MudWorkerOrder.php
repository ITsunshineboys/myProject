<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mud_worker_order".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $worker_item_id
 * @property integer $worker_craft_id
 * @property integer $area
 * @property integer $guarantee
 * @property integer $chip
 */
class MudWorkerOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mud_worker_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id','worker_item_id'], 'required'],
            [['order_id', 'worker_item_id', 'worker_craft_id', 'area', 'guarantee', 'chip'], 'integer'],
        ];
    }


}
