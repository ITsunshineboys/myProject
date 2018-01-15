<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "worker_service".
 *
 * @property string $id
 * @property string $service_name
 * @property integer $pid
 * @property string $service_image
 * @property integer $create_time
 * @property integer $status
 */
class WorkerService extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_service';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_name', 'pid', 'service_image', 'create_time'], 'required'],
            [['pid', 'create_time', 'status'], 'integer'],
            [['service_name'], 'string', 'max' => 100],
            [['service_image'], 'string', 'max' => 255],
        ];
    }

}
