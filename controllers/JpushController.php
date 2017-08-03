<?php
namespace app\controllers;





use JPush\Client;
use JPush\Exceptions\APIConnectionException;
use JPush\Exceptions\APIRequestException;
use JPush\PushPayload;
use yii\web\Response;
use yii;
class JpushController extends \yii\web\Controller
{



//    public function actionIndex(){
//        if(Yii::$app->request->isAjax){
//            $client = new \JPush\Client('af59e43d6324a2a9d995f72f','44c54839bd576b9a2a476275');
//            $data=array(
//                'act'=>'',
//                'data'=>array(
//                    'id'=>2,
//                    'orderid'=>'101728225126629786',
//                    'poi_name'=>'湘菜龙虾湘菜龙虾',
//                    'poi_lat'=>'4530397.424610',
//                    'poi_lng'=>'12535784.223957',
//                )
//            );
//
////            var_dump($re);exit;
//            $response = $client->push()
//                ->setPlatform('all')
//                ->addAllAudience()
//                ->setNotificationAlert('Hi, JPush');
//
//
//
//            if(true){
//                Yii::$app->response->format = Response::FORMAT_JSON;
//                return ['code'=>1,'message'=>'发生成功'];
//            }else{
//                Yii::$app->response->format = Response::FORMAT_JSON;
//                return ['code'=>2,'message'=>'发生失败'];
//            }
//        }
//        return $this->render('index');
//    }

public function actionPu(){
    $client = new \JPush\Client(' af59e43d6324a2a9d995f72f','44c54839bd576b9a2a476275');


//   $a= $push->message(['1'=>'2'],['a'=>2]);

//    $a= $push->setNotificationAlert('alert');

//    $a= $push->iosNotification();
// OR
//    $b= $push->iosNotification('hello');
//// OR
//    $a=$push->iosNotification('hello', [
//        'sound' => 'sound',
//        'badge' => '+1',
//        'extras' => [
//            'key' => 'value'
//        ]
//    ]);
//    var_dump($a);
//$res=new PushPayload($client);
//   $a= $res
//        ->setPlatform('ios')
//        ->setAudience('all')
//        ->iosNotification('ater')
//        ->send();
//    $a=$push->getCid('1','push');
//    $a= $push->addAllAudience();

//    $push->addTag('tag1');
//// OR
//    $push->addTag(['tag1', 'tag2']);
    //获取cid
//    $cid=$push->getCid('1','push');
////    return json_encode($cid['body']['cidlist']);
//
//   var_dump($cid['body']['cidlist']);
//    $a=$push->getCid();
//    var_dump($a);

//    $audience=$push->setAudience('all');
//   $pusher = $client->push();
//    $pusher->setPlatform('all');
//    $pusher->addAllAudience();
//    $pusher->setNotificationAlert('yangru, ddddd');
//    try {
//        $pusher->send();
//    } catch (\JPush\Exceptions\JPushException $e) {
//        // try something else here
//        print $e;
//    }
//    $app_key = "7b528331738ec719165798fd";
//    $master_secret = "32da4e2c06ac7b55da2c9228";
//    $client = new Client($app_key, $master_secret);

    //简单的推送样例
    $auth=base64_decode("7973f8b4ad32cb15c10ea2b0:7cf2502eba69046f73bebb39");



    $headers=[
        "Content-Type: application/json",
        "Authorization: Basic $auth"
    ];

    $url="https://api.jpush.cn/v3/push";
    //初始化
    $curl = curl_init();
    //设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);

    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);

    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    //执行命令
    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);
    //显示获得的数据

    var_dump($data);
//    $push_payload = $client->push()
//        ->setPlatform('all')
//        ->addAllAudience()
//        ->setNotificationAlert('Hi, JPush');
//    try {
//        $response = $push_payload->send();
//        var_dump($response);
//    } catch (\JPush\Exceptions\APIConnectionException $e) {
//        // try something here
//        print $e;
//    } catch (\JPush\Exceptions\APIRequestException $e) {
//        // try something here
//        print $e;
//    }

 $push_payload=$client->push()->setPlatform('all')->addAllAudience()->setNotificationAlert('alert');
    try{
        $response=$push_payload->send();
        var_dump($response);


    }catch (APIConnectionException $e){

        echo $e;

    }catch (APIRequestException $e){

        echo $e;
    }

}



}