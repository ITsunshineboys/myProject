<?php

namespace app\models;
use app\controllers\SiteController;
use app\services\ChatService;
use app\services\FileService;
use yii\db\Exception;
use yii\web\UploadedFile;

/**
 * This is the model class for table "user_chat".
 *
 * @property integer $id
 * @property integer $u_id
 * @property integer $role_id
 * @property integer $nickname
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
            [['chat_username', 'nickname'], 'string', 'max' => 100],
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
//            $username = sprintf('%6d', $this->u_id . $this->role_id);
//            $chat_username = \Yii::$app->security->generatePasswordHash($username);
//            $this->chat_username = str_replace(['$' ,'/'], 's', $chat_username);
        }
        $this->login_time = $time;
        return parent::beforeSave($insert);
    }

    /**
     * @param $u_id
     * @param $role_id
     * @return array|bool
     */
    public static function newChatUser($username, $password, $u_id, $role_id)
    {

        $trans = \Yii::$app->db->beginTransaction();
        try {
            $chat = new self();
            $chat->u_id = $u_id;
            $chat->role_id = $role_id;
            $chat->chat_username = $username;
            $chat->save();
            $chat_online = new ChatService();
            $username = $chat->chat_username;

            $password = \Yii::$app->security->generatePasswordHash($password);
            $hx = $chat_online->createUser($username, $password);
            if(!$hx){
                $trans->rollBack();
                return false;
            }
            $trans->commit();

        } catch (Exception $e) {
            $trans->rollBack();
            return false;
        }
        //创建环信号

        return [$chat, $hx];
    }

    /**
     * 修改Nickname
     * @param $nickename
     * @param $u_id
     * @param $role_id
     * @return bool
     */
    public static function editnickname($nickname, $u_id, $role_id)
    {
        $trans = \Yii::$app->db->beginTransaction();
        try {
            $user = User::find()
                ->where(['id' => $u_id])
                ->one();
            if (!$user) {
                $code = 1000;
                $trans->rollBack();
                return $code;
            }
            $user->nickname = $nickname;

            if (!$user->save()) {
                $trans->rollBack();
                $code = 500;
                return $code;
            }
            $chat_hx = new ChatService();
            $re = $chat_hx->editNickname($user->chat_username, $nickname);
            if (!$re) {
                $trans->rollBack();
                $code = 500;
                return $code;
            }
            $trans->commit();
            return 200;
        } catch (Exception $e) {
            $trans->rollBack();
            return 500;
        }


    }
    /**
     * 发送文本消息
     * @param $content
     * @param string $nickname
     * @param $chat_id
     * @param $to_user
     * @return int|mixed
     */
    public static function sendTextMessage($content, $username,$send_uid,$send_role_id,$to_uid)
    {

        $to_user=User::find()->where(['id'=>$to_uid])->asArray()->one();
        $trans = \Yii::$app->db->beginTransaction();
        try {
            $chat_hx = new ChatService();
            $from = $username;
            $target = [$to_user['mobile']];
            $re = $chat_hx->sendText($from, $target_type = 'users', $target, $content);
            if($re) {
                $chat_record = new ChatRecord();
                $chat_record->to_uid = $to_uid;
                $chat_record->to_role_id = $to_user['last_role_id_app'];
                $chat_record->send_uid = $send_uid;
                $chat_record->send_role_id = $send_role_id;
                $chat_record->content = $content;
                $chat_record->status=0;
                $chat_record->send_time = time();
                $chat_record->type = 'text';
                if (!$chat_record->save()) {
                    $trans->rollBack();
                    return $code = 500;
                }
            }else{
                $trans->rollBack();
                return $code=500;
            }
            $trans->commit();
            return $re;

        } catch (Exception $e) {
            $trans->rollBack();
            return $code = 500;

        }
    }
    /**
     * 发送图片消息
     * @param string $nickname
     * @param $chat_id
     * @param $to_user
     * @param $filepath
     * @return int|mixed
     */
    public static function SendImg($username,$send_uid,$send_role_id,$to_uid,$filepath){

        $to_user=User::find()->where(['id'=>$to_uid])->asArray()->one();
        $trans = \Yii::$app->db->beginTransaction();
        try {
            $chat_hx = new ChatService();
            $from = $username;
            $target = [$to_user['mobile']];
            $re = $chat_hx->sendImage($filepath,$from, $target_type = 'users', $target);
            if($re) {
                $chat_record = new ChatRecord();
                $chat_record->to_uid = $to_uid;
                $chat_record->to_role_id = $to_user['last_role_id_app'];
                $chat_record->send_uid = $send_uid;
                $chat_record->send_role_id = $send_role_id;
                $chat_record->content = $filepath;
                $chat_record->status=0;
                $chat_record->send_time = time();
                $chat_record->type = 'img';
                if (!$chat_record->save()) {
                    $trans->rollBack();
                    return $code = 500;
                }
            }else{
                $trans->rollBack();
                return $code=500;
            }
            $trans->commit();
            return $re;

        } catch (Exception $e) {
            $trans->rollBack();
            return $code = 500;

        }
    }

    public static function SendAudio($username,$send_uid,$send_role_id,$to_uid,$filepath,$length){

        $to_user=User::find()->where(['id'=>$to_uid])->asArray()->one();
        $trans = \Yii::$app->db->beginTransaction();
        try {
            $chat_hx = new ChatService();
            $from = $username;
            $target = [$to_user['mobile']];
            $re = $chat_hx->sendAudio($filepath,$from, $target_type = 'users', $target,$length);
            if($re) {
                $chat_record = new ChatRecord();
                $chat_record->to_uid = $to_uid;
                $chat_record->to_role_id = $to_user['last_role_id_app'];
                $chat_record->send_uid = $send_uid;
                $chat_record->send_role_id = $send_role_id;
                $chat_record->content = $filepath;
                $chat_record->status=0;
                $chat_record->send_time = time();
                $chat_record->type = 'audio';
                if (!$chat_record->save()) {
                    $trans->rollBack();
                    return $code = 500;
                }
            }else{
                $trans->rollBack();
                return $code=500;
            }
            $trans->commit();
            return $re;

        } catch (Exception $e) {
            $trans->rollBack();
            return $code = 500;

        }
    }

    public static function chatinfos($chat_uid,$u_id){
        $chat_user_infos=User::find()
            ->select('nickname,icon')
            ->where(['id'=>$chat_uid])
            ->asArray()
            ->one();
        $user_infos=User::find()
            ->select('nickname,icon')
            ->where(['id'=>$u_id])
            ->asArray()
            ->one();
        var_dump($chat_uid,$user_infos);
    }
}

