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


    public static function userlog($u_id,$role_id){

        $sql="SELECT * FROM( SELECT a.to_uid as lxr,a.*  FROM chat_record as a  WHERE  (a.send_uid = $u_id)  AND  (a.to_uid <> $u_id)  UNION
    SELECT a.send_uid as lxr ,a.* FROM chat_record as a  WHERE (a.send_uid <> $u_id)  AND (a.to_uid = $u_id) ORDER BY send_time DESC 
  ) as b  WHERE (send_role_id =$role_id or  to_role_id =$role_id) GROUP BY lxr";
        $user_log=Yii::$app->db->createCommand($sql)->queryAll();
        if(!$user_log){
            return null;
        }
        return $user_log;
    }

   public static  function userTextEncode($str){
        if(!is_string($str))return $str;
        if(!$str || $str=='undefined')return '';

        $text = json_encode($str); //暴露出unicode
        $text = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i",function($str){
            return addslashes($str[0]);
        },$text); //将emoji的unicode留下，其他不动，这里的正则比原答案增加了d，因为我发现我很多emoji实际上是\ud开头的，反而暂时没发现有\ue开头。
        return json_decode($text);
    }


    public static function userTextDecode($str){
        $text = json_encode($str); //暴露出unicode
        $text = preg_replace_callback('/\\\\\\\\/i',function($str){
            return '\\';
        },$text); //将两条斜杠变成一条，其他不动
        return json_decode($text);
    }
}
