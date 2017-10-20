<?php

namespace app\controllers;


use app\models\User;
use app\models\UserChat;
use app\services\ChatService;
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
        $role_id = User::find()->where(['id' => $u_id])->one()->login_role_id;
        return [$u_id, $role_id];
    }

    public function actionTest()
    {

        $username=trim(\Yii::$app->request->post('username',''));
        $password=trim(\Yii::$app->request->post('password',''));
        $chat=new ChatService();
        $a=$chat->createUser($username,$password);
        var_dump($a);
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
        $password=trim(\Yii::$app->request->post('password',''));
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
    public function actionEditNickname(){

    }
    public function actionAllUser(){
        $chat=new ChatService();
        var_dump($chat->getUsersForPage(0,''));

    }
}
