<?php

namespace app\controllers;


use app\models\ChatRecord;
use app\models\OrderGoods;
use app\models\Supplier;
use app\models\SupplierCashManager;
use app\models\User;
use app\models\UserChat;
use app\models\UserNewsRecord;
use app\services\ChatService;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\Controller;


class ChatController extends Controller
{
    const SUPPLIER_ROLE=6;
    const OWNER_ROLE=7;
    const WORKER_ROLE=2;

    /**
     *
     * @return array|string
     */
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
     * 发送文本消息给指定用户
     * @return array|string
     */
    public function actionSendTextMessage()
    {
        $user = self::getUser();
        if (!is_array($user)) {
            return $user;
        }
        list($u_id, $role_id) = $user;
        $code=1000;
        $message=trim(\Yii::$app->request->post('message'));
        $to_uid=trim(\Yii::$app->request->post('to_uid'));
        $to_role_id=trim(\Yii::$app->request->post('role_id'));

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
        $chat_limt = UserChat::chatlimt($u_id);
        if($chat_limt>=100){
            $code =1082;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=UserChat::sendTextMessage($message,$send_user['username'],$send_user['id'],$send_user['last_role_id_app'],$to_uid,$to_role_id);
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
        $path_data=\Yii::$app->request->get('path_data');
        $to_uid=trim(\Yii::$app->request->get('to_uid'));
        $to_role_id=trim(\Yii::$app->request->get('role_id'));
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
        if($path_data){
            $filepath=$path_data;
        }else{
            $filepath=UserChat::upload();
        }


        if(is_numeric($filepath)){
            $code=$filepath;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $chat_limt = UserChat::chatlimt($u_id);
        if($chat_limt>=100){
            $code =1082;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=UserChat::SendImg($send_user['username'],$send_user['id'],$send_user['last_role_id_app'],$to_uid,$filepath,$to_role_id);
        return Json::encode([
            'code'=>$code,
            'msg'=>$code==200?'ok':\Yii::$app->params['errorCodes'][$code]
        ]);
    }
    /**
     * 发送语音消息给指定用户
     * @return array|string
     */
    public function actionSendAudioMessage(){
        $user = self::getUser();
        if (!is_array($user)) {
            return $user;
        }
        list($u_id, $role_id) = $user;
        $code=1000;
        $path_data=trim(\Yii::$app->request->get('path_data'));//IOS
        $to_uid=trim(\Yii::$app->request->get('to_uid'));
        $to_role_id=trim(\Yii::$app->request->get('role_id'));
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
        if($path_data){
            $filepath=$path_data;
        }else{
            $filepath=UserChat::upload();
        }

        if(is_numeric($filepath)){
            $code=$filepath;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $chat_limt = UserChat::chatlimt($u_id);
        if($chat_limt>=100){
            $code =1082;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=UserChat::SendAudio($send_user['username'],$send_user['id'],$send_user['last_role_id_app'],$to_uid,$filepath,$length,$to_role_id);

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
        $weak_t2=strtotime("-2 week");
        $res['news']=UserNewsRecord::find()
            ->where(['uid'=>$u_id,'role_id'=>$role_id])
            ->andWhere("send_time>=$weak_t2")
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

        $data=ChatRecord::userlog($u_id,$role_id);


        if(!$data){
            $data=[];
            $res['chat_news']=[];
        }

       foreach ($data as $k=>&$v){
           $v['content']=ChatRecord::userTextDecode($v['content']);
            $all=ChatRecord::find()->asArray()->where(['send_uid'=>$v['uid'],'to_uid'=>$u_id])->andWhere(['status'=>0])->orderBy('send_time Desc')->all();

          switch ($v['role_id']){
              case self::SUPPLIER_ROLE:
                  $v['nickname']=Supplier::find()->select('shop_name')->asArray()->where(['uid'=>$v['uid']])->one()['shop_name'];
                  $v['Hx_name']=User::find()->select('username')->asArray()->where(['id'=>$v['uid']])->one()['username'];
                  $v['icon']=Supplier::find()->select('icon')->asArray()->where(['uid'=>$v['uid']])->one()['icon'];
                  $count=count($all);
                  if ($count>99){
                      $v['count'] ='99+';
                  }else{
                      $v['count']=$count;
                  }
                  if($count==0){
                      $v['status']=1;
                  }
                  break;
              case self::OWNER_ROLE:
                  $v['nickname']=User::find()->select('nickname')->asArray()->where(['id'=>$v['uid']])->one()['nickname'];
                  $v['Hx_name']=User::find()->select('username')->asArray()->where(['id'=>$v['uid']])->one()['username'];
                  $v['icon']=User::find()->select('icon')->asArray()->where(['id'=>$v['uid']])->one()['icon'];
                  $v['count']=count($all);
                  $count=count($all);
                  if ($count>99){
                      $v['count'] ='99+';
                  }else{
                      $v['count']=$count;
                  }
                  if($count==0){
                      $v['status']=1;
                  }
                  break;
              case self::WORKER_ROLE:
                  $v['nickname']='工人-'.User::find()->select('nickname')->asArray()->where(['id'=>$v['uid']])->one()['nickname'];
                  $v['Hx_name']=User::find()->select('username')->asArray()->where(['id'=>$v['uid']])->one()['username'];
                  $v['icon']=User::find()->select('icon')->asArray()->where(['id'=>$v['uid']])->one()['icon'];
                  $v['count']=count($all);

                  $count=count($all);
                  if ($count>99){
                      $v['count'] ='99+';
                  }else{
                      $v['count']=$count;
                  }
                  if($count==0){
                      $v['status']=1;
                  }
                  break;


          }
          $v['type']=UserChat::TYPE[$v['type']];
           $v['nickname']==null?[]:$v['nickname'];
           $v['Hx_name']==null?[]:$v['Hx_name'];
           $v['icon']==null?[]:$v['icon'];
          $v['lxr']=$v['uid'];
          $v['last_role_id_app']=$v['role_id'];
//          $v['send_time']=date('Y-m-d',$v['send_time']);
//          var_dump($v['send_time']);
//          var_dump(SupplierCashManager::getToday()[0]);
//          var_dump(SupplierCashManager::getToday()[1]);die;
          if(SupplierCashManager::getToday()[0]>=$v['send_time'] && SupplierCashManager::getToday()[1]<=$v['send_time']){
              $v['send_time']=date('H:i',$v['send_time']);
          }
          unset($v['role_id']);
          unset($v['uid']);

         $res['chat_news']= $data;

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
     * 推送消息列表
     * @return array|string
     */
    public function actionUserNewsList(){
        $user = self::getUser();
        if (!is_array($user)) {
            return $user;
        }
        list($u_id, $role_id) = $user;
        $weak_t2=strtotime("-2 week");
        $new_infos=(new Query())
            ->from('user_news_record')
            ->where(['role_id'=>$role_id,'uid'=>$u_id])
            ->andWhere("send_time>=$weak_t2")
            ->orderBy('send_time Desc')
            ->limit(50)
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
            'data'=>$new_infos
        ]);

    }
    /**
     * 聊天界面中心
     * @return array|string
     */
    public function actionChatView(){
        $user = self::getUser();
        if (!is_array($user)) {
            return $user;
        }
        list($u_id, $role_id) = $user;

        $recipient_id=(int)\Yii::$app->request->get('recipient_id');
        $recipient_role_id=(int)\Yii::$app->request->get('recipient_role_id');
        if(!$recipient_id || !$recipient_role_id){
            $code=1000;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user_data=UserChat::userinfos($u_id,$role_id,$recipient_id,$recipient_role_id);
        if(is_numeric($user_data)){
           $code=$user_data;
        }else{
            $code=200;
        }
        return Json::encode([
            'code'=>$code,
            'msg'=>$code==200?'ok':\Yii::$app->params['errorCodes'][$code],
            'data'=>$code==200?$user_data:[]
        ]);
    }

    /**
     * 删除聊天
     * @return array|string
     */
    public function actionDelChatNews(){
        $user = self::getUser();
        if (!is_array($user)) {
            return $user;
        }
        list($uid, $role_id) = $user;

        $to_uid=(int)\Yii::$app->request->post('to_uid');
        $to_role_id=(int)\Yii::$app->request->post('to_role_id');
        if(!$to_uid || !$to_role_id){
            $code=1000;
            return Json::encode([
                'code'=>$code,
                'msg'=>\Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=\Yii::$app->db->createCommand("SELECT * from chat_record where ((send_uid=$uid and to_uid=$to_uid) or (send_uid=$to_uid and to_uid=$uid)) and ((send_role_id=$to_role_id and to_role_id=$role_id) or (send_role_id=$role_id and to_role_id=$to_role_id))")->queryAll();
        foreach ($data as &$v) {
            $chat = ChatRecord::find()->where(['id' => $v['id']])->one();
            $chat->del_status = 1;
            $res = $chat->save(false);
            if(!$res){
                $code=500;
                return Json::encode([
                    'code'=>\Yii::$app->params['errorCodes'][$code],
                    'msg'=>'ok',
                ]);
            }
        }
        return Json::encode([
            'code'=>200,
            'msg'=>'ok'
        ]);
    }

    public function actionUpload()
    {
        $uploadRet = UserChat::upload();
        if (is_int($uploadRet)) {
            return Json::encode([
                'code' => $uploadRet,
                'msg' => \Yii::$app->params['errorCodes'][$uploadRet],
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'file_path' => $uploadRet,
            ],
        ]);
    }

}
