<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "worker_type_item".
 *
 * @property integer $id
 * @property integer $worker_type_id
 * @property integer $worker_item_id
 */
class WorkerTypeItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_type_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['worker_type_id', 'worker_item_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'worker_type_id' => '工种id(只能选pid为0的)',
            'worker_item_id' => '工人条目id(只能选pid为0的)',
        ];
    }
}
