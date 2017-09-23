<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "worker_works_review".
 *
 * @property integer $id
 * @property integer $works_id
 * @property integer $star
 * @property integer $uid
 * @property integer $role_id
 * @property integer $create_time
 * @property string $review
 */
class WorkerWorksReview extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_works_review';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['works_id', 'star', 'uid', 'role_id', 'create_time'], 'integer'],
            [['review'], 'string', 'max' => 350],
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
            'star' => '作品评分',
            'uid' => '用户id',
            'role_id' => '用户角色id',
            'review' => '评论内容',
        ];
    }

    public function beforeSave($insert)
    {
        $insert && $this->create_time = time();
        return parent::beforeSave($insert);
    }
}
