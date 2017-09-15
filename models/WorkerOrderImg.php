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

    const IMAGES_COUNT=9;
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
            [['worker_order_no'], 'string', 'max' => 50],
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
            'worker_order_no' => '工人订单号',
            'order_img' => '工单图片地址',
        ];
    }
    /**
     * count
     * @param array $images
     * @return bool
     */

    public static function validateImages(array $images)
    {
        return count($images) <= self::IMAGES_COUNT;
    }
}
