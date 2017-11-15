<?php
namespace app\controllers;


use app\models\BankinfoLog;
use app\models\GoodsCategory;
use app\models\Supplier;
use app\models\SupplierCashregister;
use app\models\SupplierFreezelist;
use app\models\User;
use app\models\UserBankInfo;
use app\models\UserCashregister;
use app\models\UserFreezelist;
use app\services\AuthService;
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

    const STATUS_JD=0;
    const STATUS_WJD=1;
    const STATUS_CG=3;
    const ACCESS_LOGGED_IN_USER = [
        'account-list',
        'account-view',
        'apply-freeze',
        'freeze-money',
        'freeze-list',
        'account-thaw',
        'cashed-list',
        'cashed-view',
        'category',
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
                'class' => AuthService::className(),
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
                ],
            ],
        ];
    }

    /**
     * 获取分类列表
     * @return string
     */
    public function actionCategory(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return json_encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $pid=(int)trim(\Yii::$app->request->get('pid',''),'');
        $cate_title=(new Query())->from('goods_category')->select('title,pid,id')->where(['pid'=>$pid])->all();
        if($cate_title){
            return json_encode([
                'code' => 200,
                'msg' => 'OK',
                'data' =>$cate_title
            ]);
        }else{
            return json_encode([
                'code' => 200,
                'msg' => 'OK',
                'data' =>null
            ]);
        }


    }
    /**
     * 商家账户列表
     * @return string
     */
    public function actionAccountList(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return json_encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=1000;
        $vaue_all=Yii::$app->params['value_all'];
        $type_shop=(int)(\Yii::$app->request->get('type_shop',$vaue_all));
        $status=(int)(\Yii::$app->request->get('status',$vaue_all));
        $keyword=trim(\Yii::$app->request->get('keyword',''),'');
        $category_id=(int)trim(\Yii::$app->request->get('category_id',''),'');
        if (!Supplier::checkShopType($type_shop) || !Supplier::checkStatus($status)) {
            return json_encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        $where='1';
        if(!$keyword) {
            if ($type_shop != $vaue_all){
                $where.= " and type_shop = {$type_shop}";
            }else{
                $keys=implode(',',array_keys(Supplier::TYPE_SHOP));
                $where.= " and type_shop in ({$keys}) ";

            }

            if ($status != $vaue_all){
                $where.= " and status ={$status} ";
            }else{
                $keys=implode(',',array_keys(Supplier::STATUSES_ONLINE_OFFLINE));
                $where.= " and  status in ({$keys}) ";

            }
            if ($category_id) {

                $cate_ids=Supplier::getcategory($category_id);
                    if(is_array($cate_ids)){
                        $ids=  implode(',',$cate_ids);
                        $where.=" and category_id in ({$ids})";

                    }else{
                        $where.=" and category_id ={$category_id}";
                    }
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
                $paginationData

        ]);

}
    /**
     * 商家账户详情
     * @return array
     */
    public function actionAccountView(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return json_encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
                $code=1000;
            $request=new Request();
            $supplier_id = trim($request->get('id', ''), '');
            $model=Supplier::find()->where(['id'=>$supplier_id])->one();
            if(!$supplier_id || !$model){
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],

                ]);
            }
            $data= Supplier::getsupplierdata($supplier_id,$model->uid);
            return json_encode([
                'code' => 200,
                'msg' => 'ok',
                'data'=>$data

            ]);
        }


    /**
     * 商家账户可冻结金额
     * @return bool
     */
        public function actionFreezeMoney(){
            $user = Yii::$app->user->identity;
            if (!$user){
                $code=1052;
                return json_encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $code=1000;
            $request=new Request();
            $supplier_id = trim($request->get('id', ''), '');
            if(!$supplier_id){
                return json_encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $supplier=Supplier::find()->where(['id'=>$supplier_id])->one();
            $freezed_money=sprintf('%.2f',(float)$supplier->availableamount*0.01);
            if($supplier){
                return json_encode([
                    'code' => 200,
                    'msg' => 'ok',
                    'data'=>[
                        'freeze_money'=>$freezed_money,
                        'id'=>$supplier->id
                    ]
                ]);
            }
    }
    /**
     * 冻结金额
     * @return string
     */
    public function actionApplyFreeze(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return json_encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=new Request();
        $supplier_id=trim(Yii::$app->request->post('supplier_id'));
        $supplier=Supplier::find()->where(['id'=>$supplier_id])->one();

        if(!$supplier_id || !$supplier){
            $code=1000;
            return json_encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
            $transaction=Yii::$app->db->beginTransaction();
        try {
            $model = new UserFreezelist();
            $model->uid = $supplier->uid;
            $model->role_id = Supplier::ROLE_SUPPLIER;
            $freeze_money = $model->freeze_money = trim($request->post('freeze_money', ''), '');
            $model->freeze_reason = trim($request->post('freeze_reason', ''), '');
            $model->create_time = time();
            if (!$freeze_money) {
                $transaction->rollBack();
                $code = 1000;
                return json_encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],

                ]);
            }
            $supplier=Supplier::find()->where(['id'=>$supplier_id])->one();
            $supplier->availableamount-=$freeze_money*100;
            $model->freeze_money=$freeze_money*100;
            if(!$model->save(false) || !$supplier->update(false)){
                $transaction->rollBack();
                $code=500;
                return json_encode([
                    'code'=>$code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);

            }
            $transaction->commit();
            return json_encode([
                'code'=>200,
                'msg'=>'ok',
            ]);
         }catch (Exception $e){
            $transaction->rollBack();
            $code=500;
            return json_encode([
                'code'=>$code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
         }
    }
    /**
     * 商家账户冻结列表
     * @return array
     */
    public function actionFreezeList(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return json_encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
                $code = 1000;
                $supplier_id=trim(Yii::$app->request->get('supplier_id'));
                 $uid=Supplier::find()->asArray()->where(['id'=>$supplier_id])->one()['uid'];
                if(!$supplier_id || !$uid){
                    return json_encode([
                        'code' => $code,
                        'msg' => Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $timeType = trim(Yii::$app->request->get('time_type', ''));
                $where=" role_id=".Supplier::ROLE_SUPPLIER ." AND status=".self::STATUS_JD;
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
                if ($startTime) {
                    $startTime = (int)strtotime($startTime);
                    $startTime && $where .= " and create_time >= {$startTime}";
                }
                if ($endTime) {
                    if ($timeType=='today')
                    {
                        $endTime = (int)(strtotime($endTime)+24*60*60);

                    }else{
                        $endTime = (int)strtotime($endTime);
                    }
                    $endTime && $where .= " and create_time <= {$endTime}";
                }
                $page = (int)Yii::$app->request->get('page', 1);
                $size = (int)Yii::$app->request->get('size', UserFreezelist::PAGE_SIZE_DEFAULT);

                $paginationData = UserFreezelist::pagination($uid,$where, UserFreezelist::FIELDS_ADMIN, $page, $size);
                return json_encode([
                    'code'=>200,
                    'msg'=>'ok',
                    'data'=>$paginationData
                ]);
            }



    /**
     * 商家账户冻结理由详情
     * @return string
     */

    public function actionFreezeReason(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return json_encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=1000;
        $request=new Request();
        $id = trim($request->get('id', ''), '');
        if (!$id) {
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }else{
            $model=UserFreezelist::find()->where(['id'=>$id])->one();
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

        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return json_encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=new Request();
            $code=1000;
            $freeze_id=(int)trim($request->get('freeze_id',''),'');

            if(!$freeze_id){
                return json_encode([
                    'code'=>$code,
                    'msg'=>Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $freeze=UserFreezelist::find()->where(['id'=>$freeze_id])->one();
            $supplier=Supplier::find()->where(['uid'=>$freeze->uid])->one();
            $transaction = Yii::$app->db->beginTransaction();
        try{
            if($supplier){
                $supplier->availableamount+=$freeze->freeze_money;

                $freeze->status=self::STATUS_WJD;
                if(!$supplier->update(false) || !$freeze->update(false)){
                    $transaction->rollBack();
                    $code=500;
                    return json_encode([
                        'code'=>$code,
                        'msg'=>Yii::$app->params['errorCodes'][$code]
                    ]);
                }
                $transaction->commit();
                return json_encode([
                    'code'=>200,
                    'msg'=>'ok',

                ]);
            }else{
                $transaction->rollBack();
                $code=500;
                return json_encode([
                    'code'=>$code,
                    'msg'=>Yii::$app->params['errorCodes'][$code]
                ]);
            }

        }catch (Exception $e){
            $transaction->rollBack();
            $code=500;
            return json_encode([
                'code'=>$code,
                'msg'=>Yii::$app->params['errorCodes'][$code]
            ]);
        }


    }

    /**
     * 商家账户已提现列表
     * @return array
     */
    public function actionCashedList(){

        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return json_encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code = 1000;
        $supplier_id=trim(Yii::$app->request->get('supplier_id'));
        $uid=Supplier::find()->asArray()->where(['id'=>$supplier_id])->one()['uid'];
        if(!$supplier_id || !$uid){
            return json_encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $timeType = trim(Yii::$app->request->get('time_type', ''));
        $where=" status=".self::STATUS_CG ." and role_id=".Supplier::ROLE_SUPPLIER ;
        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));
            if (($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime) || $startTime>$endTime)
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
        if ($startTime) {
            $startTime = strtotime($startTime);
            $startTime && $where .= " and apply_time >= {$startTime}";
        }
        if ($endTime) {
            if ($timeType=='today')
            {
                $endTime = (int)(strtotime($endTime)+24*60*60);

            }else{
                $endTime = (int)strtotime($endTime);
            }
            $endTime && $where .= " and apply_time <= {$endTime}";
        }
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', SupplierCashregister::PAGE_SIZE_DEFAULT);

        $paginationData = UserCashregister::pagination($uid,$where, UserCashregister::FIELDS_ADMIN, $page, $size);
        return json_encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$paginationData
        ]);


    }


    /**
     * 商家账户已提现详情
     * @return array
     */

    public function actionCashedView(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return json_encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=1000;
        $request=new Request();
        $cash_id = trim($request->get('id', ''), '');
        if(!$cash_id){
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],

            ]);
        }
        $model=new UserCashregister();
          $data=$model::getcashviewdata($cash_id);

        return json_encode([
            'code' => 200,
            'msg' => 'ok',
            'data'=>$data

        ]);
    }

}