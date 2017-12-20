<?php

namespace app\controllers;


use app\models\ChatRecord;
use app\models\OrderGoods;
use app\models\Supplier;
use app\models\UploadForm;
use app\models\User;
use app\models\UserChat;
use app\models\UserFreezelist;
use app\models\UserNewsRecord;
use app\models\Worker;
use app\services\ChatService;
use app\services\FileService;
use app\services\ModelService;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\UploadedFile;

class ChatController extends Controller
{
    const DEAF_SIZE=20;
    const SUPPLIER_ROLE=6;
    const OWNER_ROLE=7;
    const WORKER_ROLE=2;

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
        $username=trim(\Yii::$app->request->post('moblie',''));
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
        $to_uid=trim(\Yii::$app->request->post('to_uid'));
        $message=ChatRecord::userTextEncode($message);
        $to_user=User::find()->where(['id'=>$to_uid])->asArray()->one();
        $user_hx=new ChatService();
        $res=$user_hx->getUser($to_user['username']);
        if(array_key_exists('error',$res)){
            return Json::encode([
                'code'=>1000,
                'msg'=>$res['error']
            ]);
        }
        $send_user=User::find()->where(['id'=>$u_id,'last_role_id_app'=>$role_id])->asArray()->one();
        if(!$send_user ){
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=UserChat::sendTextMessage($message,$send_user['username'],$send_user['id'],$send_user['last_role_id_app'],$to_user['id']);
        return Json::encode([
            'code'=>$code,
            'msg'=>$code==200?'ok':\Yii::$app->params['errorCodes'][$code]
        ]);
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
        $to_uid=trim(\Yii::$app->request->get('to_uid'));
        $to_user=User::find()->where(['id'=>$to_uid])->asArray()->one();
        $user_hx=new ChatService();
        $res=$user_hx->getUser($to_user['username']);
        if(array_key_exists('error',$res)){
            return Json::encode([
                'code'=>1000,
                'msg'=>$res['error']
            ]);
        }
        $send_user=User::find()->where(['id'=>$u_id,'last_role_id_app'=>$role_id])->asArray()->one();
        if(!$send_user ){
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $filepath=UserChat::upload();
        if(is_numeric($filepath)){
            $code=$filepath;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=UserChat::SendImg($send_user['username'],$send_user['id'],$send_user['last_role_id_app'],$to_user['id'],$filepath);
        return Json::encode([
            'code'=>$code,
            'msg'=>$code==200?'ok':\Yii::$app->params['errorCodes'][$code]
        ]);
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
        $to_uid=trim(\Yii::$app->request->get('to_uid'));
        $length=trim(\Yii::$app->request->get('length'));//语音长度
        $to_user=User::find()->where(['id'=>$to_uid])->asArray()->one();
        $user_hx=new ChatService();
        $res=$user_hx->getUser($to_user['username']);
        if(array_key_exists('error',$res)){
            return Json::encode([
                'code'=>1000,
                'msg'=>$res['error']
            ]);
        }
        $send_user=User::find()->where(['id'=>$u_id,'last_role_id_app'=>$role_id])->asArray()->one();
        if(!$send_user ){
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $filepath=UserChat::upload();
        if(is_numeric($filepath)){
            $code=$filepath;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=UserChat::SendAudio($send_user['username'],$send_user['id'],$send_user['last_role_id_app'],$to_user['id'],$filepath,$length);

            return Json::encode([
                'code'=>$code,
                'msg'=>$code==200?'ok':\Yii::$app->params['errorCodes'][$code]
        ]);
    }
    /**
     * 用户消息中心
     * @return array|string
     */
    public function actionUserNewsIndex(){
        $user = self::getUser();
        if (!is_array($user)) {
            return $user;
        }
        list($u_id, $role_id) = $user;

        $res['news']=UserNewsRecord::find()
            ->where(['uid'=>$u_id,'role_id'=>$role_id])
            ->asArray()
            ->orderBy('send_time Desc')
            ->one();
        if( $res['news']){
            $res['news']['send_time']=date('Y-m-d',$res['news']['send_time']);
        }else{
            $res['news']=[
                'content'=>'没有更多消息',
                'send_time'=>date('Y-m-d',time()),
                'uid'=>$u_id,
                'status'=>'1'
            ];
        }
        var_dump($u_id);
        var_dump($role_id);die;
        $data=ChatRecord::userlog($u_id,$role_id);


        if(!$data){
            $data=[];
            $res['chat_news']=[];
        }

       foreach ($data as $k=>&$v){
           $v['content']=ChatRecord::userTextDecode($v['content']);
            $all=ChatRecord::find()->asArray()->where(['send_uid'=>$v['lxr'],'to_uid'=>$u_id])->andWhere(['status'=>0])->orderBy('send_time Desc')->all();

          $user_info=User::find()->select('id,last_role_id_app')->asArray()->where(['id'=>$v['lxr']])->one();

          switch ($user_info['last_role_id_app']){
              case self::SUPPLIER_ROLE:
                  $v['nickname']=Supplier::find()->select('shop_name')->asArray()->where(['uid'=>$v['lxr']])->one()['shop_name'];
                  $v['Hx_name']=User::find()->select('username')->asArray()->where(['id'=>$v['lxr']])->one()['username'];
                  $v['icon']=Supplier::find()->select('icon')->asArray()->where(['uid'=>$v['lxr']])->one()['icon'];
                  $v['count']=count($all);
                  if($v['count']==0){
                      $v['status']=1;
                  }
                  break;
              case self::OWNER_ROLE:
                  $v['nickname']=User::find()->select('nickname')->asArray()->where(['id'=>$v['lxr']])->one()['nickname'];
                  $v['Hx_name']=User::find()->select('username')->asArray()->where(['id'=>$v['lxr']])->one()['username'];
                  $v['icon']=User::find()->select('icon')->asArray()->where(['id'=>$v['lxr']])->one()['icon'];
                  $v['count']=count($all);
                  if($v['count']==0){
                      $v['status']=1;
                  }
                  break;
              case self::WORKER_ROLE:
                  $v['nickname']='工人-'.User::find()->select('nickname')->asArray()->where(['id'=>$v['lxr']])->one()['nickname'];
                  $v['Hx_name']=User::find()->select('username')->asArray()->where(['id'=>$v['lxr']])->one()['username'];
                  $v['icon']=User::find()->select('icon')->asArray()->where(['id'=>$v['lxr']])->one()['icon'];
                  $v['count']=count($all);
                  if($v['count']==0){
                      $v['status']=1;
                  }


          }
          $v['send_time']=date('Y-m-d',$v['send_time']);
          unset($v['send_role_id']);
          unset($v['to_role_id']);
          unset($v['send_uid']);
          unset($v['to_uid']);

         $res['chat_news'][]= array_merge($user_info,$v);

       }
       $res['service']=[
            'phone'=>'18349132391',
            'time'=>'8:00-21:00'
       ];
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$res
        ]);


    }
    /**
     * 消息列表
     * @return array|string
     */
    public function actionUserNewsList(){
        $user = self::getUser();
        if (!is_array($user)) {
            return $user;
        }
        list($u_id, $role_id) = $user;
        $page=(int)\Yii::$app->request->get('page',1);
        $size=(int)\Yii::$app->request->get('size',self::DEAF_SIZE);
        $query=(new Query())
            ->from('user_news_record')
            ->where(['role_id'=>$role_id,'uid'=>$u_id])
            ->orderBy('send_time Desc');
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $size, 'pageSizeParam' => false]);
        $new_infos=$query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
         foreach ($new_infos as $k=>&$info){
             $info['send_time']=date('Y-m-d',$info['send_time']);
             $info['image']=OrderGoods::find()
                 ->select('cover_image')
                 ->asArray()
                 ->where(['order_no'=>$info['order_no'],'sku'=>$info['sku']])
                 ->one()['cover_image'];
                 if(!$info['image']){
                     $info['image']='';
                 }
         }


        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=> ModelService::pageDeal($new_infos, $count, $page, $size)
        ]);

    }
    /**
     * 聊天中心
     * @return array|string
     */
    public function actionChatView(){
        $user = self::getUser();
        if (!is_array($user)) {
            return $user;
        }
        list($u_id, $role_id) = $user;
        $code=1000;
        $recipient_id=(int)\Yii::$app->request->get('recipient_id');
        $recipient_role_id=(int)\Yii::$app->request->get('recipient_role_id');
        if(!$recipient_id || !$recipient_role_id){
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user_data=UserChat::userinfos($u_id,$role_id,$recipient_id,$recipient_role_id);
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$user_data
        ]);
    }
    public function actionTest(){


    }
}
