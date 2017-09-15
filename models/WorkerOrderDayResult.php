<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "worker_order_day_result".
 *
 * @property integer $id
 * @property string $order_no
 * @property string $work_desc
 */
class WorkerOrderDayResult extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_order_day_result';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_no'], 'string', 'max' => 50],
            [['work_desc'], 'string', 'max' => 350],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_no' => '订单号',
            'work_desc' => '工作描述',
        ];
    }
}
