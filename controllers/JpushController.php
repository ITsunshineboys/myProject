<?php
namespace app\controllers;






use app\models\Jpush;

use yii\web\Controller;

class JpushController extends Controller
{
    public function actionPush()
    {

        $push = new Jpush();
        $extras = ['url'=>['www.baidu.com']];//推送附加字段的类型
        $m_time = '86400';//离线保留时间
        $receive = ['registration_id'=>['18071adc0335250241e']];//设备的id标识
        $title='订单发货了!';
        $content = '你的订单已经发货,点击详情查看!';
        $result = $push->push($receive,$title,$content,$extras, $m_time);
        if($result){
            return json_encode([
                'code'=>200,
                'msg'=>'ok'
            ]);
        }else{
            return json_encode([
               'code'=>500,
                'msg'=>'失败'
            ]);
        }
    }


}