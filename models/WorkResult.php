<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "work_result".
 *
 * @property integer $id
 * @property string $work_des
 * @property integer $create_time
 */
class WorkResult extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'work_result';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time'], 'integer'],
            [['work_des'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'work_des' => '工作描述',
            'create_time' => '提交时间',
        ];
    }
}
