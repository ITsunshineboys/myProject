<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "worker_works_detail".
 *
 * @property integer $id
 * @property integer $works_id
 * @property integer $status
 * @property string $desc
 * @property string $img_ids
 */
class WorkerWorksDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_works_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['works_id', 'status'], 'integer'],
            [['desc'], 'string', 'max' => 350],
            [['img_ids'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'works_id' => '工人作品id',
            'status' => '状态: 0:无效, 1:前, 2:中, 3:后',
            'desc' => '描述',
            'img_ids' => '图片,work_result_img的id,逗号分隔',
        ];
    }
}
