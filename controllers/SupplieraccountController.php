<?php
namespace app\controllers;



use app\models\BrandCategory;
use app\models\GoodsBrand;
use app\models\GoodsCategory;
use app\models\OwnerCashManager;
use app\models\Supplier;
use app\models\SupplierCashregister;
use app\models\User;
use app\models\UserAccessdetail;
use app\models\UserCashregister;
use app\models\UserFreezelist;
use app\services\AuthService;
use app\services\ExceptionHandleService;
use app\services\ModelService;
use app\services\StringService;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use Yii;
use yii\web\Request;


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
        'owner-account-list',
        'owner-account-detail',
        'owner-freeze-money',
        'owner-apply-freeze',
        'owner-freeze-list',
        'owner-freeze-taw',
        'Owner-access-detail-list'
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
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $pid=(int)trim(\Yii::$app->request->get('pid',''),'');
        $cate_title=(new Query())->from('goods_category')->select('title,pid,id')->where(['pid'=>$pid])->all();
        if($cate_title){
            return Json::encode([
                'code' => 200,
                'msg' => 'OK',
                'data' =>$cate_title
            ]);
        }else{
            return Json::encode([
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
            return Json::encode([
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
            return Json::encode([
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

        return Json::encode([
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
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
                $code=1000;
            $request=new Request();
            $supplier_id = trim($request->get('id', ''), '');
            $model=Supplier::find()->where(['id'=>$supplier_id])->one();
            if(!$supplier_id || !$model){
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],

                ]);
            }
            $data= Supplier::getsupplierdata($supplier_id,$model->uid);
            return Json::encode([
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
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $code=1000;
            $request=new Request();
            $supplier_id = trim($request->get('id', ''), '');
            if(!$supplier_id){
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $supplier=Supplier::find()->where(['id'=>$supplier_id])->one();
            $freezed_money=sprintf('%.2f',(float)$supplier->availableamount*0.01);
            if($supplier){
                return Json::encode([
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
     * 商家冻结金额
     * @return string
     */
    public function actionApplyFreeze(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=new Request();
        $supplier_id=trim(Yii::$app->request->post('supplier_id'));

        $freeze_money =  trim($request->post('freeze_money', ''), '');
        $freeze_reason = trim($request->post('freeze_reason', ''), '');
        $supplier=Supplier::find()->where(['id'=>$supplier_id])->one();
        $role_id=6;
        if(!$supplier_id || !$supplier){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=OwnerCashManager::applyfreeze($supplier->uid,$freeze_money,$freeze_reason,$role_id);
        return Json::encode([
            'code' => $code,
            'msg' =>$code==200?'ok': Yii::$app->params['errorCodes'][$code]
        ]);

    }
    /**
     * 商家账户冻结列表
     * @return array
     */
    public function actionFreezeList(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
                $code = 1000;
                $supplier_id=trim(Yii::$app->request->get('supplier_id'));
                 $uid=Supplier::find()->asArray()->where(['id'=>$supplier_id])->one()['uid'];
                if(!$supplier_id || !$uid){
                    return Json::encode([
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
                        return Json::encode([
                            'code' => $code,
                            'msg' => Yii::$app->params['errorCodes'][$code],
                        ]);
                    }
                    if($startTime==$endTime){
                        list($startTime, $endTime) =ModelService::timeDeal($startTime);
                    }else{
                        $endTime && $endTime .= ' 23:59:59';
                    }
                } else {
                    list($startTime, $endTime) = StringService::startEndDate($timeType);


                }
                if ($startTime) {
                    $startTime = (int)strtotime($startTime);
                    $startTime && $where .= " and create_time >= {$startTime}";
                }
                if ($endTime) {
                    $endTime = (int)(strtotime($endTime));
                    $endTime && $where .= " and create_time <= {$endTime}";
                }
                $page = (int)Yii::$app->request->get('page', 1);
                $size = (int)Yii::$app->request->get('size', UserFreezelist::PAGE_SIZE_DEFAULT);

                $paginationData = UserFreezelist::pagination($uid,$where, UserFreezelist::FIELDS_ADMIN, $page, $size);
                return Json::encode([
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
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=1000;
        $request=new Request();
        $id = trim($request->get('id', ''), '');
        if (!$id) {
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }else{
            $model=UserFreezelist::find()->where(['id'=>$id])->one();
            return Json::encode([
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
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=new Request();
            $code=1000;
            $freeze_id=(int)trim($request->get('freeze_id',''),'');
            $role_id=6;
            if(!$freeze_id){
                return Json::encode([
                    'code'=>$code,
                    'msg'=>Yii::$app->params['errorCodes'][$code]
                ]);
            }
            $code=OwnerCashManager::freezeTaw($freeze_id,$role_id);
            return Json::encode([
                'code' => $code,
                'msg' =>$code==200?'ok': Yii::$app->params['errorCodes'][$code]
            ]);
    }

    /**
     * 商家账户已提现列表
     * @return array
     */
    public function actionCashedList(){

        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code = 1000;
        $supplier_id=trim(Yii::$app->request->get('supplier_id'));
        $uid=Supplier::find()->asArray()->where(['id'=>$supplier_id])->one()['uid'];
        if(!$supplier_id || !$uid){
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $timeType = trim(Yii::$app->request->get('time_type', ''));
        $where=" status=2 and role_id=".Supplier::ROLE_SUPPLIER ;
        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));
            if (($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime))
            ) {
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
            if($startTime==$endTime){
                list($startTime, $endTime) =ModelService::timeDeal($startTime);
            }else{
                $endTime && $endTime .= ' 23:59:59';
            }
        } else {
            list($startTime, $endTime) = StringService::startEndDate($timeType);

        }
        if ($startTime) {
            $startTime = strtotime($startTime);
            $startTime && $where .= " and apply_time >= {$startTime}";
        }
        if ($endTime) {
                $endTime = (int)(strtotime($endTime));
            $endTime && $where .= " and apply_time <= {$endTime}";
        }
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', SupplierCashregister::PAGE_SIZE_DEFAULT);
        $paginationData = UserCashregister::pagination($uid,$where, UserCashregister::FIELDS_ADMIN, $page, $size);
        return Json::encode([
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
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=1000;
        $request=new Request();
        $cash_id = trim($request->get('id', ''), '');
        if(!$cash_id){
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],

            ]);
        }
          $data=UserCashregister::getcashviewdata($cash_id);

        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data'=>$data

        ]);
    }


    /**大后台用户账户管理
     * @return string
     */
    public function actionOwnerAccountList(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $vaue_all=Yii::$app->params['value_all'];
        $status=(int)Yii::$app->request->get('status',$vaue_all);
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', Supplier::PAGE_SIZE_DEFAULT);
        $keyword=trim(Yii::$app->request->get('keyword',''));
        $where="last_role_id_app=7";
        if($keyword){
            $where.=" and CONCAT(nickname,aite_cube_no) like '%{$keyword}%'";
        }
        if($status!=-1){
            $where.= $status == self::STATUS_ONLINE ? ' and deadtime >0' : ' and  deadtime = 0';
        }
        $paginationData = OwnerCashManager::pagination($where, OwnerCashManager::FIELDS_USER_MANAGER, $page, $size);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' =>
                $paginationData

        ]);

    }

    /**
     * 大后台业主的收支明细
     * @return string
     */
    public function actionOwnerAccessDetailList(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }

        $request = Yii::$app->request;
        $user_id=(int)$request->get('user_id','');
        $page=trim($request->get('page',1));
        $size=trim($request->get('size',ModelService::PAGE_SIZE_DEFAULT));
        $keyword = trim($request->get('keyword', ''));
        $timeType = trim($request->get('time_type', ''));
        $type=trim($request->get('type',''));
        if(!$user_id){
            $code=1000;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        if (array_key_exists($type,UserCashregister::ACCESS_TYPE_LIST))
        {
            $where="access_type={$type} and role_id=7 and uid=$user_id";
        }else{
            $where="role_id=7 and uid=$user_id";
        }
        if($keyword){
            $where .=" and   CONCAT(order_no,transaction_no) like '%{$keyword}%'";
        }
        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));
            if (($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime))
            ){
                $code=1000;
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            if($startTime==$endTime){
                list($startTime, $endTime) =ModelService::timeDeal($startTime);
            }else{
                $endTime && $endTime .= ' 23:59:59';
            }
        }else{
            list($startTime, $endTime) = StringService::startEndDate($timeType);
        }
        if ($startTime) {
            $startTime = (int)strtotime($startTime);
            $startTime && $where .= " and create_time >= {$startTime}";
        }
        if ($endTime) {
            $endTime = (int)strtotime($endTime);
            $endTime && $where .= " and create_time <= {$endTime}";
        }
        $sort_time=trim($request->get('sort_time','2'));
        switch ($sort_time)
        {
            case 1:
                $sort='create_time asc';
                break;
            case 2:
                $sort='create_time desc';
                break;
        }
        $data=UserAccessdetail::pagination($where,[],$page,$size,$sort);
        return Json::encode([
            'code'=>200,
            'msg' =>'ok',
            'data' => $data
        ]);

    }

    /**
     *大后台 业主账户详情
     * @return string
     */
    public function actionOwnerAccountDetail(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=1000;
        $user_id=(int)Yii::$app->request->get('user_id','');
        if(!$user_id){
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $data=OwnerCashManager::getOwnerView($user_id);
        return Json::encode([
            'code' => 200,
            'msg' => 'ok',
            'data'=>$data

        ]);
    }

    /**
     * 大后台业主可冻结金额
     * @return string
     */
    public function actionOwnerFreezeMoney()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code = 1000;

        $user_id = (int)(Yii::$app->request->get('id', ''));
        if (!$user_id) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $user = User::find()->where(['id' => $user_id])->one();
        $freezed_money = sprintf('%.2f', (float)$user->availableamount * 0.01);
        if ($user) {
            return Json::encode([
                'code' => 200,
                'msg' => 'ok',
                'data' => [
                    'freeze_money' => $freezed_money,
                    'id' => $user->id
                ]
            ]);
        }
    }


    /**
     * 大后台业主冻结操作
     * @return string
     */
    public function actionOwnerApplyFreeze(){
        $user = Yii::$app->user->identity;
        if (!$user) {
            $code = 1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=1000;
        $user_id=(int)(Yii::$app->request->post('user_id',''));
        $freeze_money=trim(Yii::$app->request->post('freeze_money',''));
        $freeze_reason=trim(Yii::$app->request->post('freeze_reason',''));
        $role_id=7;
        if(!$user_id || !$freeze_money ){
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=OwnerCashManager::applyfreeze($user_id,$freeze_money,$freeze_reason,$role_id);
        return Json::encode([
            'code' => $code,
            'msg' =>$code==200?'ok': Yii::$app->params['errorCodes'][$code]
        ]);


    }

    /**
     * 大后台业主冻结列表
     * @return string
     */
    public function actionOwnerFreezeList(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code = 1000;
        $user_id=trim(Yii::$app->request->get('user_id'));

        if(!$user_id){
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $timeType = trim(Yii::$app->request->get('time_type', ''));
        $where=" role_id=".OwnerCashManager::OWNER_ROLE ." AND status=".self::STATUS_JD;
        if ($timeType == 'custom') {
            $startTime = trim(Yii::$app->request->get('start_time', ''));
            $endTime = trim(Yii::$app->request->get('end_time', ''));
            if (($startTime && !StringService::checkDate($startTime))
                || ($endTime && !StringService::checkDate($endTime))
            ) {
                return Json::encode([
                    'code' => $code,
                    'msg' => Yii::$app->params['errorCodes'][$code],
                ]);
            }
            if($startTime==$endTime){
                list($startTime, $endTime) =ModelService::timeDeal($startTime);
            }else{
                $endTime && $endTime .= ' 23:59:59';
            }
        } else {
            list($startTime, $endTime) = StringService::startEndDate($timeType);


        }
        if ($startTime) {
            $startTime = (int)strtotime($startTime);
            $startTime && $where .= " and create_time >= {$startTime}";
        }
        if ($endTime) {
            $endTime = (int)(strtotime($endTime));
            $endTime && $where .= " and create_time <= {$endTime}";
        }
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', UserFreezelist::PAGE_SIZE_DEFAULT);

        $paginationData = UserFreezelist::pagination($user_id,$where, UserFreezelist::FIELDS_ADMIN, $page, $size);
        return Json::encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$paginationData
        ]);
    }

    /**
     * 大后台业主解冻操作
     * @return string
     */
    public function actionOwnerFreezeTaw(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $request=new Request();
        $code=1000;
        $freeze_id=(int)trim($request->get('freeze_id',''),'');
        $role_id=7;
        if(!$freeze_id){
            return Json::encode([
                'code'=>$code,
                'msg'=>Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $code=OwnerCashManager::freezeTaw($freeze_id,$role_id);
        return Json::encode([
            'code' => $code,
            'msg' =>$code==200?'ok': Yii::$app->params['errorCodes'][$code]
        ]);
    }

    /**
     * 商家后台品牌列表
     * @return string
     */
    public function actionSupplierBrandList(){
        $user = Yii::$app->user->identity;
        if (!$user){
            $code=1052;
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code]
            ]);
        }
        $supplier_id=Supplier::find()->select('id')->where(['uid'=>$user->getId()])->one();
        $where="supplier_id={$supplier_id->id}";
        $sort_time=(int)Yii::$app->request->get('sort_time',2);
        switch ($sort_time)
        {
            case 1:
                $sort='create_time asc';
                break;
            case 2:
                $sort='create_time desc';
                break;
        }
        $page = (int)Yii::$app->request->get('page', 1);
        $size = (int)Yii::$app->request->get('size', UserFreezelist::PAGE_SIZE_DEFAULT);
        $total=(int)GoodsBrand::find()->where($where)->asArray()->count();
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'details' => GoodsBrand::pagination($where, GoodsBrand::$adminFields, $page, $size, $sort),
                'total' =>$total ,
                'total_page'=>ceil($total/$size)

            ],
        ]);

    }
}