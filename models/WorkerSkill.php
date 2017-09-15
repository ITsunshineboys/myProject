<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "worker_skill".
 *
 * @property integer $id
 * @property string $skill
 */
class WorkerSkill extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_skill';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['skill'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'skill' => '工人特长',
        ];
    }
}
