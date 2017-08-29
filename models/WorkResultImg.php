<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "work_result_img".
 *
 * @property integer $id
 * @property integer $work_result_id
 * @property string $result_img_name
 * @property string $result_img
 */
class WorkResultImg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'work_result_img';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['work_result_id'], 'integer'],
            [['result_img_name'], 'string', 'max' => 50],
            [['result_img'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'work_result_id' => '工作成果id',
            'result_img_name' => '工作成果图片名称',
            'result_img' => '工作单成果图片地址',
        ];
    }
}
