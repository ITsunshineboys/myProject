<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "worker_works".
 *
 * @property integer $id
 * @property integer $worker_id
 * @property integer $order_no
 * @property string $title
 * @property string $desc
 */
class WorkerWorks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_works';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['worker_id', 'order_no'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['desc'], 'string', 'max' => 350],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'worker_id' => '工人id',
            'order_no' => '工人订单号',
            'title' => '标题',
            'desc' => '作品描述',
        ];
    }
}
