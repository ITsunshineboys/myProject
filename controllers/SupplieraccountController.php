<?php
namespace app\controllers;


use app\models\GoodsCategory;
use app\models\Supplier;
use app\models\SupplierCashregister;
use app\models\SupplierFreezelist;
use app\models\User;
use app\services\ExceptionHandleService;
use app\services\StringService;
use yii\db\Exception;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\validators\ValidationAsset;
use yii\web\Controller;
use Yii;

use yii\web\Request;
use yii\web\ViewAction;

class SupplieraccountController extends  Controller{

    const STATUS_CG=3;
    const ACCESS_LOGGED_IN_USER = [
        'logout',
        'roles',
        'reset-password',
        'roles-status',
        'time-types',
        'upload',
        'upload-delete',
        'review-statuses',
    ];
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;

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
                'only' => self::ACCESS_LOGGED_IN_USER,
                'rules' => [
                    [
                        'actions' => self::ACCESS_LOGGED_IN_USER,
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post',],
                    'reset-password' => ['post',],
                    'upload' => ['post',],
                    'upload-delete' => ['post',]
                ],
            ],
        ];
    }



    /**
     * 获取分类列表
     * @return string
     */

    public function actionCategory(){


        $pid=(int)trim(\Yii::$app->request->get('pid',0),'');

        if($pid){


            $cate_title=(new Query())->from('goods_category')->select('title,pid,id')->where(['pid'=>$pid])->all();
            return json_encode([
                'code' => 200,
                'msg' => 'OK',
                'data' =>$cate_title
                ]);
        }else{
            $code=1000;
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],

            ]);
        }
    }

    /**
     * 商家账户列表
     * @return string
     */
    public function actionAccountList(){

        $code=1000;

        $type_shop=(int)trim(\Yii::$app->request->get('type_shop',''),'');
        $status=(int)trim(\Yii::$app->request->get('status',''),'');
        $keyword=trim(\Yii::$app->request->get('keyword',''),'');


        $pid=(int)trim(\Yii::$app->request->get('pid',0),'');

        $where = '1';
        if(!$keyword) {

            if ($pid) {

                $cate_ids=Supplier::getcategory($pid);
                    if(is_array($cate_ids)){
                        foreach ($cate_ids as $cate_id) {

                            $cd=(new Query())->from('supplier')->select('category_id')->where(['category_id'=>$cate_id])->one();

                            if(!$cd==null){
                                $where.=" and category_id ={$cd['category_id']}";

                            }

                        }
                    }else{
                        $where.=" and category_id ={$pid}";
                    }
            }

            if($type_shop ){

                 $where.= " and type_shop = {$type_shop}";

            }

            if($status==self::STATUS_ONLINE){

                $where.= " and status =".self::STATUS_ONLINE;
            }
            if($status==self::STATUS_OFFLINE){

                $where.= " and status =".self::STATUS_OFFLINE;
            }


        }else{
            $where=" shop_no like '%{$keyword}%' or shop_name like '%{$keyword}%'";
        }



        $page = (int)Yii::$app->request->get('page', 1);

        $size = (int)Yii::$app->request->get('size', Supplier::PAGE_SIZE_DEFAULT);
        $paginationData = Supplier::pagination($where, Supplier::FIELDS_ADMIN, $page, $size);

        return json_encode([
            'code' => 200,
            'msg' => 'OK',
            'data' =>
                $paginationData['details']

        ]);

}
    /**
     * 商家账户详情
     * @return array
     */
    public function actionAccountView(){

                $code=1000;
            $request=new Request();
            $supplier_id = trim($request->get('id', ''), '');
            if(!$supplier_id){
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],

                ]);
            }

            $model=new Supplier();
            $data= $model::getsupplierdata($supplier_id);
            return json_encode([
                'code' => 200,
                'msg' => 'ok',
                'data'=>$data

            ]);
        }
    /**
     * 商家账户冻结金额
     * @return bool
     */
        public function actionFreezeMoney(){

            $code=1000;
            $request=new Request();
            $supplier_id = trim($request->get('id', ''), '');
            if(!$supplier_id){
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],

                ]);
            }
            $user=User::find()->where(['id'=>$supplier_id])->one();

            if(!$user){
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],

                ]);
            }
            if($request->isPost){
                $model=new SupplierFreezelist();
                $model->supplier_id=$supplier_id;
              $freeze_money=  $model->freeze_money=trim($request->post('freeze_money',''),'');
                $model->freeze_reason=trim($request->post('freeze_reason',''),'');
                $model->create_time=time();


               $transaction=Yii::$app->db->beginTransaction();
               try{
                   $code=1052;

                   if($user->balance<$freeze_money){
                       return json_encode([
                           'code' => $code,
                           'msg' => \Yii::$app->params['errorCodes'][$code],


                       ]);
                   }
                  $user->balance=$user->balance-$freeze_money;
                   $model->save();
                   $user->update();
                $transaction->commit();

                   return json_encode([
                       'code'=>200,
                       'msg'=>'ok'
                   ]);
               }catch (Exception $e){

                   $transaction->rollBack();

                   return json_encode([

                       'code'=>$code,
                       'msg' => \Yii::$app->params['errorCodes'][$code],
                   ]);
               }




            }else{
                $code=1050;
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],

                ]);
            }


        }

    /**
     * 商家账户冻结列表
     * @return array
     */
    public function actionFreezeList(){

                $code = 1000;

                $timeType = trim(Yii::$app->request->get('time_type', ''));


                $where = '1';

                if (!$timeType || !in_array($timeType, array_keys(Yii::$app->params['timeTypes']))) {

                    return json_encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code],
                    ]);
                }


                if ($timeType == 'custom') {
                    $startTime = trim(Yii::$app->request->get('start_time', ''));
                    $endTime = trim(Yii::$app->request->get('end_time', ''));



                    if (($startTime && !StringService::checkDate($startTime))
                        || ($endTime && !StringService::checkDate($endTime))
                    ) {
                        return json_encode([
                            'code' => $code,
                            'msg' => Yii::$app->params['errorCodes'][$code],
                        ]);
                    }
                } else {
                    list($startTime, $endTime) = StringService::startEndDate($timeType);
                    $startTime = explode(' ', $startTime)[0];
                    $endTime = explode(' ', $endTime)[0];
                }
            //
                if ($startTime) {
                    $startTime = strtotime($startTime);
                    $startTime && $where .= " and create_time >= {$startTime}";
                }
                if ($endTime) {
                    $endTime = strtotime($endTime);
                    $endTime && $where .= " and create_time <= {$endTime}";
                }

                $page = (int)Yii::$app->request->get('page', 1);

                $size = (int)Yii::$app->request->get('size', SupplierFreezelist::PAGE_SIZE_DEFAULT);
            $paginationData = SupplierFreezelist::pagination($where, SupplierFreezelist::FIELDS_ADMIN, $page, $size);
                return json_encode([
                    'code'=>200,
                    'msg'=>'ok',
                    'data'=>$paginationData['details']
                ]);
            }



    /**
     * 商家账户冻结理由详情
     * @return string
     */

    public function actionFreezeReason(){
        $code=1000;
        $request=new Request();

        $id = trim($request->get('id', ''), '');
        if (!$id) {
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],


            ]);
        }else{

            $model=SupplierFreezelist::find()->where(['id'=>$id])->one();

            return json_encode([
                'code' => 200,
                'msg' => 'ok',
                'data'=>$model->freeze_reason

            ]);
        }

    }

    /**
     * 商家账户解冻
     * @return bool
     */

    public function actionAccountThaw(){
        $request=new Request();
            $code=1000;
            $freeze_id=(int)trim($request->get('freeze_id',''),'');

            if(!$freeze_id){
                return json_encode([
                    'code'=>$code,
                    'msg'=>Yii::$app->params['errorCodes']['code']
                ]);
            }
            $freeze=SupplierFreezelist::findOne($freeze_id);

            $supplier=Supplier::findOne($freeze->supplier_id);

            if($supplier){

                $user=User::findOne($supplier->uid);
                $user->balance+=$freeze->freeze_money;

                 $user->update();
                 return json_encode([
                     'code'=>200,
                     'msg'=>'ok',

                 ]);
            }else{

                return json_encode([
                    'code'=>$code,
                    'msg'=>Yii::$app->params['errorCodes']['code']
                ]);
            }


    }

    /**
     * 商家账户已提现列表
     * @return array
     */
    public function actionCashedList(){

        $code = 1000;

        $timeType = trim(Yii::$app->request->get('time_type', ''));


        $where = '1';



        $where=" status=".self::STATUS_CG;
        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));



            if (($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime))
            ) {
                return json_encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
        } else {
            list($startTime, $endTime) = StringService::startEndDate($timeType);
            $startTime = explode(' ', $startTime)[0];
            $endTime = explode(' ', $endTime)[0];
        }
        //
        if ($startTime) {
            $startTime = strtotime($startTime);
            $startTime && $where .= " and apply_time >= {$startTime}";
        }
        if ($endTime) {
            $endTime = strtotime($endTime);
            $endTime && $where .= " and apply_time <= {$endTime}";
        }

        $page = (int)Yii::$app->request->get('page', 1);

        $size = (int)Yii::$app->request->get('size', SupplierCashregister::PAGE_SIZE_DEFAULT);
        $paginationData = SupplierCashregister::pagination($where, SupplierCashregister::FIELDS_ADMIN, $page, $size);
        return json_encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$paginationData['details']
        ]);


    }


    /**
     * 商家账户已提现详情
     * @return array
     */

    public function actionCashedView(){

        $code=1000;
        $request=new Request();
        $cash_id = trim($request->get('id', ''), '');
        if(!$cash_id){
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],

            ]);
        }

       $model=new SupplierCashregister();
          $data=$model::getcashviewdata($cash_id);

        return json_encode([
            'code' => 200,
            'msg' => 'ok',
            'data'=>$data

        ]);
    }


}