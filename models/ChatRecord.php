<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "chat_record".
 *
 * @property string $id
 * @property integer $chat_id
 * @property string $content
 */
class ChatRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chat_id', 'content'], 'required'],
            [['chat_id'], 'integer'],
            [['content'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_id' => 'Chat ID',
            'content' => 'Content',
        ];
    }
}
