<?php

namespace app\models;
use app\controllers\SiteController;
use app\services\ChatService;
use app\services\FileService;
use app\services\StringService;
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
    const TYPE_TEXT=1;
    const TYPE_IMG=2;
    const TYPE_AUDIO=3;

    const TYPE=[
        self::TYPE_TEXT => 'text',
        self::TYPE_IMG => 'img',
        self::TYPE_AUDIO => 'audio',
    ];
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
    public static function sendTextMessage($content, $username,$send_uid,$send_role_id,$to_uid,$to_role_id)
    {
        $to_user=User::find()->where(['id'=>$to_uid])->asArray()->one();
        $trans = \Yii::$app->db->beginTransaction();
        try {

            $chat_hx = new ChatService();
            $from = $username;
            $target = [$to_user['username']];
            $re = $chat_hx->sendText($from, $target_type = 'users', $target, $content);
            if($re) {
                $chat_record = new ChatRecord();
                $chat_record->to_uid = $to_uid;
                $chat_record->to_role_id = $to_role_id;
                $chat_record->send_uid = $send_uid;
                $chat_record->send_role_id = $send_role_id;
                $chat_record->content = $content;
                $chat_record->status=0;
                $chat_record->send_time = time();
                $chat_record->type = self::TYPE_TEXT;
                if (!$chat_record->save(false)) {
                    $trans->rollBack();
                    return $code = 500;
                }
            }else{
                $trans->rollBack();
                return $code=500;
            }
            $trans->commit();
            return 200;

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
    public static function SendImg($username,$send_uid,$send_role_id,$to_uid,$filepath,$to_role_id){

        $to_user=User::find()->where(['id'=>$to_uid])->asArray()->one();
        $trans = \Yii::$app->db->beginTransaction();
        try {
            $chat_hx = new ChatService();
            $from = $username;
            $target = [$to_user['username']];
            $re = $chat_hx->sendImage($filepath,$from, $target_type = 'users', $target);
            if($re) {
                $chat_record = new ChatRecord();
                $chat_record->to_uid = $to_uid;
                $chat_record->to_role_id = $to_role_id;
                $chat_record->send_uid = $send_uid;
                $chat_record->send_role_id = $send_role_id;
                $chat_record->content = $filepath;
                $chat_record->status=0;
                $chat_record->send_time = time();
                $chat_record->type =  self::TYPE_IMG;
                if (!$chat_record->save(false)) {
                    $trans->rollBack();
                    return $code = 500;
                }
            }else{
                $trans->rollBack();
                return $code=500;
            }
            $trans->commit();
            return 200;

        } catch (Exception $e) {
            var_dump($e);
            $trans->rollBack();
            return $code = 500;

        }
    }

    public static function SendAudio($username,$send_uid,$send_role_id,$to_uid,$filepath,$length,$to_role_id){

        $to_user=User::find()->where(['id'=>$to_uid])->asArray()->one();
        $trans = \Yii::$app->db->beginTransaction();
        try {
            $chat_hx = new ChatService();
            $from = $username;
            $target = [$to_user['username']];
            $re = $chat_hx->sendAudio($filepath,$from, $target_type = 'users', $target,$length);
            if($re) {
                $chat_record = new ChatRecord();
                $chat_record->to_uid = $to_uid;
                $chat_record->to_role_id = $to_role_id;
                $chat_record->send_uid = $send_uid;
                $chat_record->send_role_id = $send_role_id;
                $chat_record->content = $filepath;
                $chat_record->length=$length;
                $chat_record->status=0;
                $chat_record->send_time = time();
                $chat_record->type = self::TYPE_AUDIO;
                if (!$chat_record->save(false)) {
                    $trans->rollBack();
                    return $code = 500;
                }
            }else{
                $trans->rollBack();
                return $code=500;
            }
            $trans->commit();
            return 200;

        } catch (Exception $e) {
            $trans->rollBack();
            return $code = 500;

        }
    }
    public static function chatlimt($uid){
        list($startTime, $endTime) = StringService::startEndDate('today');
        if ($startTime) {
            $startTime = (int)strtotime($startTime);
        }
        if ($endTime) {
            $endTime = (int)(strtotime($endTime));

        }
        $chat_limt=\Yii::$app->db->createCommand("select * from chat_record where send_uid=to_uid and send_uid = $uid and send_time >= $startTime and send_time <=$endTime ")->queryAll();
       return count($chat_limt);
    }
    /**
     * @param $uid
     * @param $role_id
     * @param $recipient_id
     * @return array|null
     */
    public static function userinfos($uid,$role_id,$recipient_id,$recipient_role_id){
        $data=[];
        if($recipient_role_id==6){
            $data['recipient']= Supplier::find()
                    ->select(['uid as id','icon','shop_name as name'])
                    ->asArray()
                    ->where(['uid'=>$recipient_id])
                    ->one();
            $data['recipient']['role_id']=$recipient_role_id;
            $data['recipient']['hx_name']=User::find()->asArray()->select('username')->where(['id'=>$recipient_id])->one()['username'];
        }elseif($recipient_role_id==7){
            $data['recipient']=User::find()
                ->select(['id','icon','nickname as name','username as hx_name'])
                ->asArray()
                ->where(['id'=>$recipient_id])
                ->one();
            $data['recipient']['role_id']=$recipient_role_id;
            $data['recipient']['hx_name']=User::find()->asArray()->select('username')->where(['id'=>$recipient_id])->one()['username'];

        }
        if($role_id==6){
            $data['user']=Supplier::find()
                ->select(['uid as id','icon','shop_name as name'])
                ->asArray()
                ->where(['uid'=>$uid])
                ->one();
            $hx=User::find()->asArray()->select('hx_pwd_date,username')->where(['id'=>$uid])->one();
            $data['user']['role_id']=$role_id;
            $data['user']['hx_name']=$hx['username'];
        }elseif($role_id==7){
            $data['user']=User::find()
                ->select(['id','icon','nickname as name','username as hx_name'])
                ->asArray()
                ->where(['id'=>$uid])
                ->one();
            $hx=User::find()->asArray()->select('hx_pwd_date,username')->where(['id'=>$uid])->one();
            $data['user']['role_id']=$role_id;
            $data['user']['hx_name']=$hx['username'];
        }
        $data['chat_record']=\Yii::$app->db->createCommand("SELECT * from chat_record where ((send_uid=$uid and to_uid=$recipient_id) or (send_uid=$recipient_id and to_uid=$uid)) and ((send_role_id=$recipient_role_id and to_role_id=$role_id) or (send_role_id=$role_id and to_role_id=$recipient_role_id))")->queryAll();

        foreach ($data['chat_record'] as &$v){
            $v['type']= UserChat::TYPE[$v['type']];
            $v['content']=ChatRecord::userTextDecode($v['content']);
           $chat= ChatRecord::find()->where(['id'=>$v['id']])->one();
           $chat->status=1;
           $chat->save(false);
            $send_time=date('Y-m-d',$v['send_time']);


             if($send_time==date('Y-m-d',time())){
                 $v['send_time']=date('H:i',$v['send_time']);

             }else{
                 $v['send_time']=$send_time;
             }
        }
        if(!$data){
          return 1000;
        }
        return $data;
    }

    public static function upload()
    {
        $model = new UploadForm;
        $model->file = UploadedFile::getInstance($model, 'file');

        $code = 1000;
        if (!$model->file || !$model->file->extension) {
            return $code;
        }

        $ymdDirs = FileService::makeYmdDirs();
        if (!$ymdDirs) {
            $code = 500;
            return $code;
        }

        $directory = \Yii::getAlias('@webroot') . '/' . UploadForm::DIR_PUBLIC . '/' . $ymdDirs;

        $filename = FileService::generateFilename($directory);
        if ($filename === false) {
            $code = 500;
            return $code;
        }

        $file = $filename . '.' . $model->file->extension;
        if (!$model->file->saveAs($directory . '/' . $file)) {
            $code = 500;
            return $code;
        }

        return UploadForm::DIR_PUBLIC . '/' . $ymdDirs . '/' . $file;
    }
}

