<?php
namespace app\controllers;


use app\models\Worker;
use app\models\WorkerItem;
use app\models\WorkerOrder;
use app\models\WorkerOrderItem;
use app\models\WorkerType;
use app\services\ExceptionHandleService;
use app\services\FileService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;


class FindworkerController extends Controller{

    const PARENT=0;
    const STATUS_SINGLE=1;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    $code = 403;
                    new ExceptionHandleService($code);
                    exit;
                },
                'only' => ['logout', 'about'],
                'rules' => [
                    [
                        'actions' => ['logout', 'about'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post',],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    /**
     *get Services type list
     *@return string
     */
    public function actionServiceList()
    {
        $parents = WorkerType::find()->where(['pid'=>self::PARENT])->asArray()->all();
        $data=WorkerType::getworkertype($parents);
            $parent=[];
            for ($i=0;$i<count($data);$i++){
                $parent[]=[
                    $parents[$i]['worker_type']=>$data[$i],
                ];
            }
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$parent

        ]);

    }

    /**
     *
     *get home info by worker type
     *@return string
     */

    public function actionGetHomeItem(){

        $code=1000;
        $request=new Request();
        $worker_type_id=trim($request->get('worker_type_id',''),'');
        if(!$worker_type_id){
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
         $data=WorkerItem::getparent($worker_type_id);
               return Json::encode([
                   'code'=>200,
                   'msg'=>'ok',
                   'data'=>[
                       'worker_type_id'=>WorkerType::gettype($worker_type_id),
                       'item'=>$data
                   ]
               ]);

         }

    /**
     *根据厅室获取所有工艺
     *@return string
     */
    public function actionGetcraftinfo()
    {
        $code = 1000;
        $item_id = trim(\Yii::$app->request->get('item_id', ''), '');
        if (!$item_id) {
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data = WorkerItem::getcraft($item_id);
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);

    }
    /**
     *
     * get craft price by craft_id
     * @return string
     */
    public function actionGetCraftprice(){
        $code=1000;
        $craft_id=trim(\Yii::$app->request->get('craft_id',''),'');
        if (!$craft_id) {
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data = WorkerOrderItem::craftprice($craft_id);
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);
    }

    /**
     *get chlid item by parent item_id
     *@return string
     */
    public function actionGetChliditem(){
        $code=1000;
        $item_id = trim(\Yii::$app->request->get('item_id', ''), '');
        if (!$item_id) {
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=WorkerItem::getchliditem($item_id);

            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
                'data' => $data
            ]);
        }
    /**
     * Generate Order
     * @return string
     */
     public function actionGenerateOrder(){
         $user_id = \Yii::$app->user->identity;
         $code=1052;
         if(!$user_id){
             return Json::encode([
                 'code' => $code,
                 'msg' =>\ Yii::$app->params['errorCodes'][$code]
             ]);
         }
         $post=\Yii::$app->request->post();
         $front_money=trim(\Yii::$app->request->post('front_money',''),'');
         $amount=trim(\Yii::$app->request->post('amount',''),'');
         $demand=trim(\Yii::$app->request->post('demand',''),'');
         $describe=trim(\Yii::$app->request->post('describe',''),'');
         $need_time = self::getOrderNeedTime($post['homeinfos']);
         $homeinfos=WorkerOrderItem::addMudorderitem($post['homeinfos']);
         if($homeinfos!=1000){
            if(is_numeric($homeinfos)){
                $code=$homeinfos;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]
                ]);
            }
         }
         $ownerinfos=WorkerOrderItem::addownerinfo($post['ownerinfos']);
            if($ownerinfos!=1000){
                if(is_numeric($ownerinfos)){
                    $code=$ownerinfos;
                    return Json::encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code]
                    ]);
                }
            }
         $homeinfos['need_time'] = $need_time;
         $code=WorkerOrder::addorderinfo($user_id,$homeinfos,$ownerinfos,$front_money,$amount,$demand,$describe);
         return Json::encode([
             'code' => $code,
             'msg' => $code==200?'ok':\Yii::$app->params['errorCodes'][$code]
         ]);
    }
    /**
     *add home images
     *@return string
     */
    public function actionAddHomeimages()
    {

        $files=FileService::uploadMore();
            if (is_numeric($files)){
                $code=$files;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]
                ]);
            }else{
                return Json::encode([
                    'code' => 200,
                    'msg' => 'ok',
                    'data'=>$files
                ]);
            }
    }

    /**
     * 计算出需要的天数
     * @param $home_info
     * @return string
     */
    public static function getOrderNeedTime($home_info)
    {
        $sum = 0;
        $code = 1000;
        $keys = array_keys($home_info);
        foreach ($keys as $k => &$key) {

            if (preg_match('/(area)/', $key, $m)) {
                if ($home_info[$key] > 200) {
                    return Json::encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $sum += $home_info[$key];
            }
        }
        return  ceil($sum / 12 + 1);
    }
    /**
     *抢单信息
     * @return string
     */
    public function actionLaborGrabsheet(){

        $order_info=WorkerOrder::getorderinfo();
        if($order_info==null){
            return Json::encode([
                'code'=>200,
                'msg'=>'ok',
                'data'=>null
            ]);
        }
        $woker_type=WorkerType::gettype($order_info['worker_type_id']);
        $servicstyle=WorkerItem::getparent($order_info['worker_type_id']);
        $time=WorkerOrder::timedata($order_info['id']);

       return Json::encode([
            'code'=>200,
           'msg'=>'ok',
           'data'=>[
               'order_id'=>$order_info['id'],
               'worker_type'=>$woker_type,
               'servicstyle'=>$servicstyle,
               'time'=>$time,
               'demand'=>$order_info['demand'],
               'money'=>sprintf('%.2f',(float)$order_info['amount']*0.01)
           ]

       ]);
    }
    /**
     * 工人抢单
     * @return string
     */
    public function actionGrabsingle(){
        $user_id = \Yii::$app->user->identity;
        $code=1052;
        if(!$user_id){
            return Json::encode([
                'code' => $code,
                'msg' =>\ Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=1000;
        $order_id=trim(\Yii::$app->request->get('order_id',''),'');
        if(!$order_id){
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $worker_id=Worker::find()
            ->select('id')
            ->where(['uid'=>$user_id])
            ->asArray()
            ->one();
        $orderdata=WorkerOrder::find()
            ->where(['id'=>$order_id])
            ->andWhere(['status'=>self::STATUS_SINGLE])
            ->one();
        $orderdata->is_old=self::STATUS_SINGLE;
        $orderdata->worker_id=$worker_id['id'];
        if(!$orderdata->save(false)){
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data'=>[
                'tel'=>$orderdata->con_tel,
                'name'=>$orderdata->con_people
            ]
        ]);
    }
}