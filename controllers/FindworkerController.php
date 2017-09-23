<?php
namespace app\controllers;


use app\models\District;
use app\models\Worker;
use app\models\WorkerItem;
use app\models\WorkerOrder;
use app\models\WorkerOrderItem;
use app\models\WorkerSkill;
use app\models\WorkerType;
use app\models\WorkerTypeItem;
use app\models\WorkerWorksReview;
use app\services\ExceptionHandleService;
use app\services\FileService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use app\models\User;


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
        $parents = WorkerType::parent();
        $data=WorkerType::getworkertype($parents);
            $parent=[];
            for ($i=0;$i<count($data);$i++){
                $parent[]=[
                      'image'=>$parents[$i]['image'],
                      'id'=>$parents[$i]['id'],
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
                       'worker_type_id'=>$worker_type_id,
                       'worker_type'=>WorkerType::gettype($worker_type_id),
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
     * 生成订单
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
         $homeinfos=WorkerOrderItem::getWorkeitem($post['homeinfos']['worker_type_id'],$post);
         if(is_numeric($homeinfos)){
            $code=$homeinfos;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
         }
         $ownerinfos=WorkerOrderItem::addownerinfo($post['ownerinfos']);
            if(is_numeric($ownerinfos)){
                $code=$ownerinfos;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code]
                ]);
            }

         $homeinfos['need_time'] = $need_time;
         $code=WorkerOrder::addorderinfo($user_id->getId(),$homeinfos,$ownerinfos,$front_money,$amount,$demand,$describe);
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
     * 工人个人中心
     * @return string
     */
    public function actionWorkerIndex(){
        $user_id = \Yii::$app->user->identity->getId();
        $code=1052;
        if(!$user_id){
            return Json::encode([
                'code' => $code,
                'msg' =>\ Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $worker_info=User::find()->where(['id'=>$user_id])->one();
        $worker['aite_cube_no']=$worker_info->aite_cube_no;
        $worker['name']=Worker::getWorkerByUid($user_id)->nickname;
        $worker['uid']=Worker::getWorkerByUid($user_id)->id;
        $worker['worker_no']=$worker_info->aite_cube_no;
        $worker['balance']=sprintf('%.2f',(float)$worker_info->balance*0.01);
        $order=Worker::getordertypebystatus($user_id);
        if(is_int($order)){
            $code=$order;
            return Json::encode([
                'code' => $code,
                'msg' =>\ Yii::$app->params['errorCodes'][$code]
            ]);
        }
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>[
               'worker_infos'=> $worker,
                'worker_orders'=>$order
            ]
        ]);

    }
    /**
     * 工人账户管理
     * @return string
     */
    public function actionWorkerAccount(){
        $user_id = \Yii::$app->user->identity->getId();
        $code=1052;
        if(!$user_id){
            return Json::encode([
                'code' => $code,
                'msg' =>\ Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=Worker::getWorkerAccount($user_id);
        if(!$data){
            return Json::encode([
                'code' => 200,
                'msg' =>'ok',
                'data'=>null
            ]);
        }
        return Json::encode([
            'code' => 200,
            'msg' =>'ok',
            'data'=>$data
        ]);
    }
    /**
     * 个人资料-工人
     * @return string
     */
    public function actionOwenInfos(){
        $user_id = \Yii::$app->user->identity->getId();
        $code=1052;
        if(!$user_id){
            return Json::encode([
                'code' => $code,
                'msg' =>\ Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $data=Worker::find()
            ->select('icon,nickname,province_code,city_code')
            ->where(['uid'=>$user_id])
            ->asArray()
            ->one();

        $data['province_code'] && $data['city_code']?$data['origin']='已设置':$data['origin']='未设置';
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$data
        ]);

    }
    /**
     * 设置籍贯
     * @return string
     */
    public function actionSetOrigin(){
        $user_id = \Yii::$app->user->identity->getId();
        $code=1052;
        if(!$user_id){
            return Json::encode([
                'code' => $code,
                'msg' =>\ Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $worker=Worker::find()->where(['uid'=>$user_id])->one();
        $province_code=(int)trim(\Yii::$app->request->post('province_code',''),'');
        $city_code=(int)trim(\Yii::$app->request->post('city_code',''),'');
        $district_code=(int)trim(\Yii::$app->request->post('district_code',''),'');
        if(!$province_code || !$city_code || !$district_code){
            return Json::encode([
                'code' => $code,
                'msg' =>\ Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $province=District::findByCode($province_code)->name;
        $city=District::findByCode($city_code)->name;
        $district=District::findByCode($district_code)->name;
        $worker->native_place=$province.$city.$district;
        if(!$worker->save(false)){
            $code=500;
            return Json::encode([
                'code' => $code,
                'msg' =>\ Yii::$app->params['errorCodes'][$code]
            ]);
        }
        return Json::encode([
            'code' => 200,
            'msg' =>'ok'
        ]);
    }
    /**
     * 获取工种
     * @return string
     */
    public function actionWorkerParentype(){
        return Json::encode([
           'code'=>200,
           'msg'=>'ok',
            'data'=>WorkerType::parent()
        ]);
    }
    /**
     * 实名认证
     */
    public function  actionCertification(){
        $user_id = \Yii::$app->user->identity->getId();
        $code=1052;
        if(!$user_id){
            return Json::encode([
                'code' => $code,
                'msg' =>\ Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $post=\Yii::$app->request->post();
        $code=Worker::Certification($post,$user_id);
        return Json::encode([
            'code' => $code,
            'msg' =>$code==200?'ok':\ Yii::$app->params['errorCodes'][$code]
        ]);


    }
    /**
     * 获取所有特长与工人自己的特长
     * @return string
     */
    public function actionSkillsList(){
        $user_id = \Yii::$app->user->identity->getId();
        $code=1052;
        if(!$user_id){
            return Json::encode([
                'code' => $code,
                'msg' =>\ Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $skills['worker_skill']=WorkerSkill::getWorkerSkillname($user_id);

        $skills['other_skill']=WorkerSkill::getOtherSkillname($user_id);

       return Json::encode([
           'code'=>200,
            'msg'=>'ok',
           'data'=>$skills
       ]);
    }
    /**
     * 工人设置特长
     * @return string
     */
    public function actionSetSkills(){
        $user_id = \Yii::$app->user->identity->getId();
        $code=1052;
        if(!$user_id){
            return Json::encode([
                'code' => $code,
                'msg' =>\ Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=1000;
        $skill_id=trim(\Yii::$app->request->get('skill_id',''),'');
        if(!$skill_id){
            return Json::encode([
                'code' => $code,
                'msg' =>\ Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=WorkerSkill::getSetSkillids($user_id,$skill_id);

        return Json::encode([
            'code' => $code,
            'msg' =>$code==200?'ok':\ Yii::$app->params['errorCodes'][$code]
        ]);


    }
    /**
     * 删除工人特长
     * @return string
     */
    public function actionDelWorkerSkill(){
        $user_id = \Yii::$app->user->identity->getId();
        $code=1052;
        if(!$user_id){
            return Json::encode([
                'code' => $code,
                'msg' =>\ Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=1000;
        $skill_id=trim(\Yii::$app->request->get('skill_id',''),'');
        if(!$skill_id){
            return Json::encode([
                'code' => $code,
                'msg' =>\ Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=WorkerSkill::DelWorkerSkill($user_id,$skill_id);
        return Json::encode([
            'code' => $code,
            'msg' =>$code==200?'ok':\ Yii::$app->params['errorCodes'][$code]
        ]);
    }
    /**
     * 工人详情
     * @return string
     */
    public function actionWorkerView(){
        $code=1000;
        $worker_id=(int)trim(\Yii::$app->request->get('worker_id'));
        if(!$worker_id){
            return Json::encode([
                'code' => $code,
                'msg' =>\ Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $restview=WorkerWorksReview::getOwenerPLone($worker_id);
        $data=Worker::workerinfos($worker_id);
        if($data && $restview){
            return Json::encode([
                'code' => 200,
                'msg' =>'ok',
                'data'=>[
                    'worker_view'=>$data,
                    'owener_resview'=>$restview
                ]
            ]);
        }
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
        $worker_id=Worker::getWorkerByUid($user_id)->id;
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