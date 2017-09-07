<?php
namespace app\controllers;


use app\models\WorkerItem;
use app\models\WorkerOrder;
use app\models\WorkerOrderItem;
use app\models\WorkerType;
use app\services\ExceptionHandleService;
use app\services\FileService;
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
     *根据厅室获取工艺信息
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
     * 获取当前选择工艺的价格
     * @return string
     */
    public function actionGetCraftprice(){
        $code=1000;
        $craft_id=trim(\Yii::$app->request->get('craft_id',''),'');
        if (!$craft_id) {
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data = WorkerOrderItem::craftprice($craft_id);
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
         $post=\Yii::$app->request->post();
         $front_money=trim(\Yii::$app->request->post('front_money',''),'');
         $amount=trim(\Yii::$app->request->post('amount',''),'');
         $need_time = self::getOrderNeedTime($post['homeinfos']);
         $homeinfos=WorkerOrderItem::addMudorderitem($post['homeinfos']);
         if($homeinfos!=1000){
            if(is_numeric($homeinfos)){
                $code=$homeinfos;
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]
                ]);
            }
         }
         $ownerinfos=WorkerOrderItem::addownerinfo($post['ownerinfos']);
            if($ownerinfos!=1000){
                if(is_numeric($ownerinfos)){
                    $code=$ownerinfos;
                    return json_encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code]
                    ]);
                }
            }
         $homeinfos['need_time'] = $need_time;
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

        $files=FileService::uploadMore();
            if (is_numeric($files)){
                $code=$files;
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]
                ]);
            }else{
                return json_encode([
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
                    return json_encode([
                        'code' => $code,
                        'msg' => \Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $sum += $home_info[$key];
            }
        }
        return  ceil($sum / 12 + 1);
    }

}