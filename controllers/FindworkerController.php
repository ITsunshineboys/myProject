<?php
namespace app\controllers;

use app\models\WorkerItem;
use app\models\WorkerOrder;
use app\models\WorkerOrderImg;
use app\models\WorkerOrderItem;
use app\models\WorkerType;
use app\services\ExceptionHandleService;
use app\services\FileService;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Request;

class FindworkerController extends Controller{

    const PARENT=0;
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
        return json_encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$parent

        ]);

    }

    /**
     *get home info by worker type
     *@return string
     */

    public function actionGetHomeItem(){

        $code=1000;
        $request=new Request();
        $worker_type_id=trim($request->get('worker_type_id',''),'');
        if(!$worker_type_id){
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
         $data=WorkerItem::getparent($worker_type_id);
               return json_encode([
                   'code'=>200,
                   'msg'=>'ok',
                   'data'=>[
                       'worker_type_id'=>$worker_type_id,
                       'item'=>$data
                   ]
               ]);

         }

    /**
     *get carft by home info
     *@return string
     */
    public function actionGetcarftinfo()
    {
        $code = 1000;
        $item_id = trim(\Yii::$app->request->get('item_id', ''), '');
        if (!$item_id) {
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data = WorkerItem::getcraft($item_id);
        return json_encode([
            'code' => 200,
            'msg' => 'ok',
            'data' => $data
        ]);

    }


    /**
     *get chlid item
     *@return string
     */
    public function actionGetChliditem(){
        $code=1000;
        $item_id = trim(\Yii::$app->request->get('item_id', ''), '');
        if (!$item_id) {
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=WorkerItem::getchliditem($item_id);

            return json_encode([
                'code' => 200,
                'msg' => 'ok',
                'data' => $data
            ]);
        }
    /**
     * add mud info
     * @return string
     */
    public function actionAddMudHomeinfo()
    {
        $user_id = \Yii::$app->user->identity;
        if (!$user_id){
        $code=1052;
        return json_encode([
            'code' => $code,
            'msg' =>\ Yii::$app->params['errorCodes'][$code]
        ]);
    }
        $code = 1000;
        $array = \Yii::$app->request->post();

        if (!$array || !$array['start_time']) {
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $sum=0;
        $keys=array_keys($array);
        foreach ($keys as $k=>&$key){
            if(preg_match('/(area)/',$key,$m)){
                if($array[$key]>200){
                    return json_encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $sum+=$array[$key];
                $need_time=ceil($sum/12+1);
            }
        }
        $data= WorkerOrderItem::addMudorderitem($array,$need_time);//房屋信息


        \Yii::$app->cache->set('homeinfos',$data);

        return json_encode([
            'code' => 200,
            'msg' => 'ok',

        ]);
    }
    /**
     * get owner infos
     * @return string
     */
    public function actionOwnerinfos(){
        $user_id = \Yii::$app->user->identity;
        $code=1052;
        if (!$user_id){
            return json_encode([
            'code' => $code,
            'msg' =>\ Yii::$app->params['errorCodes'][$code]
        ]);
    }
        $code=1000;
        $request=new Request();
        $con_people=trim($request->post('con_people',''),'');
        $con_tel=trim($request->post('con_tel',''),'');
        $address=trim($request->post('address',''),'');
        $map_location=trim($request->post('map_location',''),'');
        $infos=[];
        $code=WorkerOrderItem::addownerinfo($con_people,$con_tel,$address,$map_location);
        if($code!=1000){
            $infos['con_people']=$con_people;
            $infos['con_tel']=$con_tel;
            $infos['address']=$address;
            $infos['map_location']=$map_location;
        }

        \Yii::$app->cache->set('ownerinfos',$infos);
        return json_encode([
            'code' => $code,
            'msg' => $code==200?'ok':\Yii::$app->params['errorCodes'][$code]

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
             return json_encode([
                 'code' => $code,
                 'msg' =>\ Yii::$app->params['errorCodes'][$code]
             ]);
         }
         $code=1000;
         $front_money=trim(\Yii::$app->request->post('front_money',''),'');
         $amount=trim(\Yii::$app->request->post('amount',''),'');
         if(!$front_money || !$amount ){
             return json_encode([
                 'code' => $code,
                 'msg' =>\ Yii::$app->params['errorCodes'][$code]
             ]);
         }
         $homeinfos=\Yii::$app->cache->get('homeinfos');
        $ownerinfos=\Yii::$app->cache->get('ownerinfos');

         $code=WorkerOrder::addorderinfo($user_id,$homeinfos,$ownerinfos,$front_money,$amount);
         return json_encode([
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
        $code = 1000;
        $images = \Yii::$app->request->post('images', []);
        if(!WorkerOrderImg::validateImages($images) || ! \Yii::$app->params['uploadPublic']['maxSize'] || !\Yii::$app->params['uploadPublic']['extensions'] ){
                return json_encode([
                    'code'=>$code,
                    'msg'=>\Yii::$app->params['errorCodes'][$code]
                ]);
        }

    }


}