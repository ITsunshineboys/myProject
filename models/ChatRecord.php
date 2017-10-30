<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "chat_record".
 *
 * @property string $id
 * @property integer $send_uid
 * @property integer $to_uid
 * @property integer $send_role_id
 * @property integer $to_role_id
 * @property integer $type
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
            [['send_uid', 'content','to_uid'], 'required'],
            [['send_uid','to_uid'], 'integer'],
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

    public static function userlog($u_id,$role_id){
//
        $sql="SELECT * FROM( SELECT a.to_uid as lxr,a.*  FROM chat_record as a  WHERE  (a.send_uid = $u_id)  AND  (a.to_uid <> $u_id)  UNION
    SELECT a.send_uid as lxr ,a.* FROM chat_record as a  WHERE (a.send_uid <> '1')  AND (a.to_uid = $u_id) ORDER BY send_time Desc
  ) as b  WHERE send_role_id =$role_id or  to_role_id =$role_id GROUP BY lxr; ";
        $user_log=Yii::$app->db->createCommand($sql)->queryAll();
        if(!$user_log){
            return null;
        }
        return $user_log;
    }
}
