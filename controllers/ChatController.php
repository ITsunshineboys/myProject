<?php

namespace app\controllers;


use app\models\User;
use app\models\UserChat;
use app\models\UserFreezelist;
use app\models\UserNewsRecord;
use app\services\ChatService;
use app\services\FileService;
use yii\helpers\Json;
use yii\web\Controller;

class ChatController extends Controller
{

    private static function getUser()
    {
        $user = \Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => 1052,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $u_id = $user->getId();
        $role_id = User::find()->where(['id' => $u_id])->one()->last_role_id_app;
        return [$u_id, $role_id];
    }

    /**
     *第一次登陆app时创建环信用户和本地环信关联数据
     * @return array|string
     */
    public function actionNew()
    {
        $user = self::getUser();
        if (!is_array($user)) {
            return $user;
        }
        list($u_id, $role_id) = $user;
        $username=trim(\Yii::$app->request->post('username',''));
        $password=trim(\Yii::$app->request->post(' ',''));
        if (UserChat::find()->where(['u_id' => $u_id, 'role_id' => $role_id])->one()) {
            $code = 1000;
            return Json::encode([
                'code' => $code,
                'msg' => '已有聊天号，无须新建'
            ]);
        }
        $user_chat = UserChat::newChatUser($username,$password,$u_id,$role_id);
        if (!$user_chat) {
            $code = 500;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        list($chat, $hx) = $user_chat;
        if (array_key_exists('error', $hx)) {
            return Json::encode([
                'code' => 1000,
                'msg' => $hx['error']
            ]);
        }
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $chat,
            'data_online' => $hx
        ]);
    }

    //封禁用户
    public function actionUserBan()
    {
        //改变本地状态
        $user = self::getUser();
        if (!is_array($user)) {
            return $user;
        }
        list($u_id, $role_id) = $user;
        $chat = UserChat::find()->where(['u_id' => $u_id, 'role_id' => $role_id])->one();
        $chat->status = 0;
        $chat->update();
        //改变环信里的状态
        $username = $chat->chat_username;
        $chat_online = new ChatService();
        $hx = $chat_online->deactiveUser($username);

        if (array_key_exists('error', $hx)) {
            return Json::encode([
                'code' => 1000,
                'msg' => $hx['error']
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $chat,
            'data_online' => $hx
        ]);

    }

    //解禁用户
    public function actionUserUnBan()
    {
        //改变本地状态
        $user = self::getUser();
        if (!is_array($user)) {
            return $user;
        }
        list($u_id, $role_id) = $user;
        $chat = UserChat::find()->where(['u_id' => $u_id, 'role_id' => $role_id])->one();
        $chat->status = 1;
        $chat->update();
        //改变环信里的状态
        $username = $chat->chat_username;
        $chat_online = new ChatService();
        $hx = $chat_online->activeUser($username);

        if (array_key_exists('error', $hx)) {
            return Json::encode([
                'code' => 1000,
                'msg' => $hx['error']
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $chat,
            'data_online' => $hx
        ]);

    }
    /**
     * 得到登陆用户的环信号
     * @return array|string
     */
    public function actionGetUsername()
    {
        $user = self::getUser();
        if (!is_array($user)) {
            return $user;
        }
        list($u_id, $role_id) = $user;
        if ($user_chat = UserChat::find()->where(['u_id' => $u_id, 'role_id' => $role_id])->one()) {
            $username = $user_chat->chat_username;
            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
                'username' => $username
            ]);
        }
        return Json::encode([
            'code' => 1051,
            'msg' => '没有聊天号'
        ]);
    }

    /**
     * 得到未读消息数
     * @return string
     */
    public function actionOfflineMsgCount()
    {
        $user = $this->actionGetUsername();
        $username = Json::decode($user)['username'];
        $chat_online = new ChatService();
        $msg_count = $chat_online->getOfflineMessages($username);

        if (array_key_exists('error', $msg_count)) {
            return Json::encode([
                'code' => 1000,
                'msg' => $msg_count['error']
            ]);
        }
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'msg_count' => $msg_count['count']
        ]);
    }
    /**
     * 环信用户修改昵称
     * @return array|string
     */
    public function actionEditNickname(){
        $user = self::getUser();
        if (!is_array($user)) {
            return $user;
        }
        list($u_id, $role_id) = $user;
        $nickname=trim(\Yii::$app->request->post('nickname'));

        $code=UserChat::editnickname($nickname,$u_id,$role_id);
        return Json::encode([
            'code'=>$code,
            'msg'=>$code==200?'ok':\Yii::$app->params['errorCodes'][$code]
        ]);

    }
    /**
     * 发送文本消息给指定用户
     * @return array|string
     */
    public function actionSendTextMessage(){
        $user = self::getUser();
        if (!is_array($user)) {
            return $user;
        }
        list($u_id, $role_id) = $user;
        $code=1000;
        $message=trim(\Yii::$app->request->post('message'));
        $to_user=trim(\Yii::$app->request->post('to_user'));
        $user_hx=new ChatService();
        $res=$user_hx->getUser($to_user);
        if(array_key_exists('error',$res)){
            return Json::encode([
                'code'=>1000,
                'msg'=>$res['error']
            ]);
        }
        $chat_bd=UserChat::find()->where(['u_id'=>$u_id,'role_id'=>$role_id])->asArray()->one();
        $nickname=User::find()->where(['id'=>$chat_bd['u_id']])->asArray()->one()['nickname'];
        if(!$chat_bd || !$nickname ){
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=UserChat::sendTextMessage($message,$nickname,$chat_bd['id'],$to_user);
        if(is_numeric($data)){
            $code=$data;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }else{
            return Json::encode([
                'code'=>200,
                'msg'=>'ok',
                'data'=>$data
            ]);
        }

    }
    /**
     * 发送图片信息给指定用户
     * @return array|string
     */
    public function actionSendImgMessage(){
        $user = self::getUser();
        if (!is_array($user)) {
            return $user;
        }
        list($u_id, $role_id) = $user;
        $code=1000;
        $filepath=trim(\Yii::$app->request->post('filepath'));
        $to_user=trim(\Yii::$app->request->post('to_user'));
        $user_hx=new ChatService();
        $res=$user_hx->getUser($to_user);
        if(array_key_exists('error',$res)){
            return Json::encode([
                'code'=>1000,
                'msg'=>$res['error']
            ]);
        }
        $chat_bd=UserChat::find()->where(['u_id'=>$u_id,'role_id'=>$role_id])->asArray()->one();
        $nickname=User::find()->where(['id'=>$chat_bd['u_id']])->asArray()->one()['nickname'];
        if(!$chat_bd || !$nickname ){
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=UserChat::SendImg($nickname,$chat_bd['id'],$to_user,$filepath);
        if(is_numeric($data)){
            $code=$data;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }else{
            return Json::encode([
                'code'=>200,
                'msg'=>'ok',
                'data'=>$data
            ]);
        }

    }
    /**
     * 发送语音消息
     * @return array|string
     */
    public function actionSendAudioMessage(){
        $user = self::getUser();
        if (!is_array($user)) {
            return $user;
        }
        list($u_id, $role_id) = $user;
        $code=1000;
        $filepath=trim(\Yii::$app->request->post('filepath'));
        $to_user=trim(\Yii::$app->request->post('to_user'));
        $length=trim(\Yii::$app->request->post('length'));//语音长度
        $user_hx=new ChatService();
        $res=$user_hx->getUser($to_user);
        if(array_key_exists('error',$res)){
            return Json::encode([
                'code'=>1000,
                'msg'=>$res['error']
            ]);
        }
        $chat_bd=UserChat::find()->where(['u_id'=>$u_id,'role_id'=>$role_id])->asArray()->one();
        $nickname=User::find()->where(['id'=>$chat_bd['u_id']])->asArray()->one()['nickname'];
        if(!$chat_bd || !$nickname ){
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=UserChat::SendAudio($nickname,$chat_bd['id'],$to_user,$filepath,$length);
        if(is_numeric($data)){
            $code=$data;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }else{
            return Json::encode([
                'code'=>200,
                'msg'=>'ok',
                'data'=>$data
            ]);
        }
    }
    public function actionNewsIndex(){
        $user = self::getUser();
        if (!is_array($user)) {
            return $user;
        }
        list($u_id, $role_id) = $user;

    }

    public function actionTest(){
        var_dump(UserNewsRecord::find()->all());

    }
}
