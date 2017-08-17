<?php

namespace app\models;

/**
 * This is the model class for table "user_chat".
 *
 * @property integer $id
 * @property integer $u_id
 * @property integer $role_id
 * @property string $chat_username
 * @property integer $create_time
 * @property integer $login_time
 * @property integer $status
 */
class UserChat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_chat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['u_id', 'role_id'], 'required'],
            [['u_id', 'role_id', 'status'], 'integer'],
            [['chat_username'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'u_id' => '用户id',
            'role_id' => '角色id',
            'chat_username' => '环信用户名',
            'create_time' => '创建时间',
            'login_time' => '最后登录时间',
            'status' => '状态  0:封禁 1:正常 ',
        ];
    }

    public function beforeSave($insert)
    {
        $time = time();
        if ($insert) {
            $this->create_time = $time;
            $username = sprintf('%6d', $this->u_id . $this->role_id);
            $chat_username = \Yii::$app->security->generatePasswordHash($username);
            $this->chat_username = str_replace(['$' ,'/'], 's', $chat_username);
        }
        $this->login_time = $time;
        return parent::beforeSave($insert);

    }
}
