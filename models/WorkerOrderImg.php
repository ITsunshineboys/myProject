<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "worker_order_img".
 *
 * @property integer $id
 * @property integer $worker_order_no
 * @property string $order_img_name
 * @property string $order_img
 */
class WorkerOrderImg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_order_img';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['worker_order_no'], 'integer'],
            [['order_img_name'], 'string', 'max' => 50],
            [['order_img'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'worker_order_no' => '工单号',
            'order_img_name' => '工单图片名称',
            'order_img' => '工单图片地址',
        ];
    }
}
