<?php

namespace app\models;

use app\controllers\WorkerController;
use app\services\ModelService;
use Yii;
use yii\data\Pagination;
use yii\db\Exception;
/**
 * This is the model class for table "worker_order".
 *
 * @property integer $id
 * @property string $uid
 * @property string $worker_id
 * @property string $order_no
 * @property string $create_time
 * @property string $start_time
 * @property string $end_time
 * @property string $need_time
 * @property string $map_location
 * @property string $address
 * @property string $con_people
 * @property string $con_tel
 * @property string $amount
 * @property string $front_money
 * @property integer $status
 * @property integer $worker_type_id
 * @property string $describe
 * @property string $demand
 * @property string $type
 * @property string $days
 */
class WorkerOrder extends \yii\db\ActiveRecord
{
    const TIMESTYPE = '~';
    const STATUS_INSERT = 1;
    const ORDER_OLD = 1;
    const ORDER_NEW = 0;


    const WORKER_ORDER_NOT_BEGIN = 0;
    const WORKER_ORDER_PREPARE = 1;
    const WORKER_ORDER_READY = 2;
    const WORKER_ORDER_APPLY = 3;
    const WORKER_ORDER_ING=4;
    const WORKER_ORDER_DONE = 5;
    const WORKER_ORDER_NO = 6;

//    const WORKER_ORDER_STATUS = [
//        self::WORKER_ORDER_NOT_BEGIN => '未开始',
//        self::WORKER_ORDER_ING => '施工中',
//        self::WORKER_ORDER_DONE => '完工',
//
//    ];

    const USER_WORKER_ORDER_STATUS = [
        self::WORKER_ORDER_NOT_BEGIN => '未开始',
        self::WORKER_ORDER_PREPARE => '接单中',
        self::WORKER_ORDER_READY => '已接单',
        self::WORKER_ORDER_APPLY => '申请开工',
        self::WORKER_ORDER_ING => '施工中',
        self::WORKER_ORDER_DONE => '已完工',
        self::WORKER_ORDER_NO=>'已取消'
    ];

    const IMG_PAGE_SIZE_DEFAULT = 3;
    const IMG_COUNT_DEFAULT = 9;

    const IS_OLD = 1;
    const IS_NEW = 0;

    const WORKER_WORKS_BEFORE = 1;
    const WORKER_WORKS_ING = 2;
    const WORKER_WORKS_AFTER = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'worker_id', 'create_time', 'modify_time', 'start_time', 'end_time', 'need_time', 'amount', 'front_money', 'status', 'worker_type_id', 'is_old'], 'integer'],
            [['uid', 'worker_id', 'worker_type_id', 'create_time', 'modify_time', 'start_time', 'end_time', 'need_time', 'amount', 'front_money', 'status', 'is_old'], 'integer'],
            [['con_tel'], 'required'],
            [['order_no'], 'string', 'max' => 50],
            [['describe'], 'string', 'max' => 350],
            [['reason'], 'string', 'max' => 350],
            [['demand'], 'string', 'max' => 300],
            [['days'], 'string', 'max' => 1000],
            [['map_location', 'address'], 'string', 'max' => 100],
            [['con_people'], 'string', 'max' => 25],
            [['con_tel'], 'string', 'max' => 11],
            [['describe', 'reason'], 'string', 'max' => 350],
            [['demand'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '用户id',
            'worker_id' => '工人id',
            'worker_type_id' => '工人类型id',
            'order_no' => '工单号',
            'create_time' => '创建时间',
            'modify_time' => '修改时间',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'need_time' => '工期(天数)',
            'days' => '工作的具体日期',
            'map_location' => '地图定位',
            'address' => '施工详细地址',
            'con_people' => '联系人',
            'con_tel' => '联系电话',
            'amount' => '订单总金额',
            'front_money' => '订金',
            'status' => '0: 已取消(完成)，1：未开始(接单中)，2：未开始(已接单)，3: 未开始(申请开工)，4：施工中，5：已完工(完成)',
            'describe' => '订单描述',
            'is_old' => '是否旧数据，0：不是，  1：是',
            'demand' => '个性需求',
            'reason' => '修改原因',
        ];
    }

    /**
     * 工人智管工地列表-工人
     * @param $uid
     * @param $status
     * @param $page
     * @param $page_size
     * @return array
     */
    public static function getWorkerOrderList($uid, $status, $page,$page_size)
    {

        $worker = Worker::getWorkerByUid($uid);
        $worker_id = $worker->id;
        $query = self::find()
            ->select(['id','worker_id','create_time', 'amount', 'status'])
            ->where(['worker_id' => $worker_id]);
        if ($status == WorkerController::STATUS_ALL) {

                $status = [
                    self::WORKER_ORDER_NOT_BEGIN,
                    self::WORKER_ORDER_ING,
                    self::WORKER_ORDER_DONE
                ];
                $worker_status=implode(',',array_values($status));
                $query->andWhere("status in ($worker_status)");
        }else{
            $query->andWhere(['status' => $status]);
        }


        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $page_size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->all();

        $worker_type_id = Worker::find()->where(['id' => $worker_id])->one()->worker_type_id;
        $worker_type = WorkerType::find()->where(['id' => $worker_type_id])->one()->worker_name;

        foreach ($arr as &$v) {
            $v['worker_name'] = $worker_type;
            $v['create_time'] = date('Y-m-d ', $v['create_time']);
            $v['amount'] = sprintf('%.2f', (float)$v['amount'] / 100);
            $v['status'] = self::USER_WORKER_ORDER_STATUS[$v['status']];
        }

        $data = ModelService::pageDeal($arr, $count, $page, $page_size);
        return $data;
    }
    /**
     * 智管工地列表-用户
     * @param $uid
     * @param $status
     * @param $page
     * @param $page_size
     * @return array
     */
    public static function getUserOrderList($uid,$status,$page,$page_size){

        $query = self::find()
            ->select(['id','worker_id','create_time', 'amount', 'status','worker_type_id'])
            ->where(['uid' => $uid])
            ->andWhere('worker_id!=0')
        ->orderBy('create_time Desc');
        if ($status == WorkerController::STATUS_ALL) {

            $status = [
                self::WORKER_ORDER_NOT_BEGIN,
                self::WORKER_ORDER_ING,
                self::WORKER_ORDER_DONE
            ];
            $worker_status=implode(',',array_values($status));
            $query->andWhere("status in ($worker_status)");
        }else{
            $query->andWhere(['status' => $status]);
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $page_size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->all();

//        $worker_type_id = Worker::find()->where(['id' => $worker_id])->one()->worker_type_id;
//        $worker_type = WorkerType::find()->where(['id' => $worker_type_id])->one()->worker_type;

        foreach ($arr as &$v) {


                $v['worker_name'] = WorkerType::find()->where(['id' => $v['worker_type_id']])->one()->worker_name;
                $v['create_time'] = date('Y-m-d ', $v['create_time']);
                $v['amount'] = sprintf('%.2f', (float)$v['amount'] / 100);
                $v['status'] = self::USER_WORKER_ORDER_STATUS[$v['status']];
                unset($v['worker_type_id']);


        }

        $data = ModelService::pageDeal($arr, $count, $page, $page_size);
        return $data;
    }

    /**
     * 订单详情除了工人信息
     *
     * @param $order_id
     * @return array|int
     */
    private static function getOrderDetail($order_id)
    {
        if (is_array($order_id)) {
            $return = [];
            $orders = self::find()->where(['id' => $order_id])->all();
            if ($orders) {
                return 1000;
            }
            foreach ($orders as $order) {
                $return[] = self::dealOrder($order);
            }
        } else {
            $order = self::find()
                ->select('id,uid,order_no,worker_type_id,worker_id,create_time,modify_time,end_time,need_time,amount,front_money,status,con_people,con_tel,address,map_location')
                ->where(['id' => $order_id])
                ->one();
            if (!$order) {
                return 1000;
            }
            $return = self::dealOrder($order);
        }
        return $return;
    }
    /**
     * 泥作条目详情
     * @param $order_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function MudorderView($order_id){
        $mud_item_data=MudWorkerOrder::find()->asArray()->where(['order_id'=>$order_id])->all();

        foreach ($mud_item_data as &$mud_item){
            $mud_item['worker_item']=WorkerItem::find()
                ->where(['id'=>$mud_item['worker_item_id']])
                ->asArray()
                ->one()['title'];

            $mud_item['worker_item_craft']=WorkerCraft::getcraftitle($mud_item['worker_craft_id'])['craft'];
            if($mud_item['worker_item_craft']==null){
                unset($mud_item['worker_item_craft']);
            }
            if($mud_item['guarantee']==null){
                unset($mud_item['guarantee']);
            }else{
                $mud_item['guarantee']='是';
            }
            if($mud_item['chip']==null){
                unset($mud_item['chip']);
            }
            unset($mud_item['order_id']);
            unset($mud_item['id']);
            unset($mud_item['worker_item_id']);
            unset($mud_item['worker_craft_id']);
        }
       return $mud_item_data;

    }
    /**
     * 防水详情
     * @param $order_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function WaterprooforderView($order_id){
        $water_item_data=WaterproofWorkerOrder::find()->asArray()->where(['order_'=>$order_id])->all();
        foreach ($water_item_data as &$water_item){
            $water_item['worker_item']=WorkerItem::find()
                ->where(['id'=>$water_item['worker_item_id']])
                ->asArray()
                ->one()['title'];

            $water_item['worker_item_craft']=WorkerCraft::getcraftitle($water_item['worker_craft_id'])['craft'];
            if($water_item['worker_item_craft']==null){
                unset($water_item['worker_item_craft']);
            }
            unset($water_item['order_id']);
            unset($water_item['id']);
            unset($water_item['worker_item_id']);
            unset($water_item['worker_craft_id']);
        }
        return $water_item_data;
    }

    //TODO  油漆工的详情
    public static function painterorderView(){

    }
    private static function dealOrder($order)
    {
        $worker_type_id = $order->worker_type_id;
        $type=WorkerType::getparenttype($worker_type_id);
        switch ($type){
            case '泥工':
                $data=self::MudorderView($order->id);
                break;
            case '防水工':
                $data=self::WaterprooforderView($order->id);
                break;
            case '油漆工':
                $data=self::painterorderView($order->id);
        }

//        $worker_type_items = WorkerTypeItem::find()->where(['worker_type_id' => $worker_type_id])->all();
//        $worker_items = [];
//        foreach ($worker_type_items as $worker_type_item) {
//            $worker_item_id = $worker_type_item->worker_item_id;
//            $worker_item = WorkerItem::find()
//                ->where(['id' => $worker_item_id])
//                ->select(['id', 'title'])
//                ->asArray()->one();
//            $worker_items[] = $worker_item;
//        }
        $order->worker_type_id=WorkerType::getparenttype($order->worker_type_id);
        $order->create_time && $order->create_time = date('Y-m-d H:i', $order->create_time);
        $order->modify_time && $order->modify_time = date('Y-m-d H:i', $order->modify_time);
        $order->start_time && $order->start_time = date('Y-m-d H:i', $order->start_time);
        $order->end_time && $order->end_time = date('Y-m-d H:i', $order->end_time);
//        状态是0的时候有取消时间和取消原因
//        $order->cancel_time && $order->cancel_time = date('Y-m-d H:i', $order->cancel_time);
        $order->amount && $order->amount = sprintf('%.2f', (float)$order->amount / 100);
        $order->front_money && $order->front_money = sprintf('%.2f', (float)$order->front_money / 100);
        return [$order, $data];
    }

    /**
     * 用户-工程订单列表
     * @param $uid
     * @param $status
     * @param $page
     * @param $page_size
     * @return array
     */
    public static function getUserWorkerOrderList($uid, $status, $page, $page_size)
    {

        $query = self::find()
        ->select(['id','uid','create_time', 'amount', 'status', 'worker_id','worker_type_id'])
        ->where(['uid' => $uid])
        ->orderBy('create_time Desc');
        if ($status != WorkerController::STATUS_ALL) {
            if($status==self::WORKER_ORDER_READY){
            $status=[
                self::WORKER_ORDER_READY,
                self::WORKER_WORKS_AFTER,
            ];
            $worker_status=implode(',',array_values($status));
            $query->andWhere("status in ($worker_status)");
        }else{
            $query->andWhere(['status' => $status]);
        }

        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $page_size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->all();
        foreach ($arr as &$v) {
            $worker_type = WorkerType::find()->where(['id' => $v['worker_type_id']])->one();
            $worker_type && $v['worker_name'] = $worker_type->worker_name;
            $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
            $v['amount'] = sprintf('%.2f', (float)$v['amount'] / 100);
            $v['status'] = self::USER_WORKER_ORDER_STATUS[$v['status']];
            unset($v['worker_type_id']);
        }

        $data = ModelService::pageDeal($arr, $count, $page, $page_size);
        return $data;
    }
    /**
     * 工人-工程订单列表
     * @param $uid
     * @param $status
     * @param $page
     * @param $page_size
     * @return array
     */
    public static function  getWorkerWorkerOrderList($uid, $status, $page, $page_size){
        $worker_id=Worker::find()->select('id')
            ->asArray()
            ->where(['uid'=>$uid])
            ->one()['id'];

        $query = self::find()
            ->select(['id','create_time', 'amount', 'status', 'worker_id'])
            ->Where(['worker_id'=>$worker_id]);
        if ($status != WorkerController::STATUS_ALL) {
            if($status==self::WORKER_ORDER_READY){
                $status=[
                    self::WORKER_ORDER_READY,
                    self::WORKER_WORKS_AFTER,
                ];
                $worker_status=implode(',',array_values($status));
                $query->andWhere("status in ($worker_status)");
            }else{
                $query->andWhere(['status' => $status]);
            }
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $page_size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->all();

        foreach ($arr as &$v) {
            $worker = Worker::find()->where(['id' => $v['worker_id']])->one();
            $worker && $worker_type_id = $worker->worker_type_id;
            $worker_type_id && $worker_type = WorkerType::find()->where(['id' => $worker_type_id])->one();
            $worker_type && $v[''] = $worker_type->worker_name;
            $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
            $v['amount'] = sprintf('%.2f', (float)$v['amount'] / 100);
            $v['status'] = self::USER_WORKER_ORDER_STATUS[$v['status']];
        }

        $data = ModelService::pageDeal($arr, $count, $page, $page_size);
        return $data;
    }
    /**
     * 获取用户工程订单详情
     * @param $order_id
     * @return array|int
     */
    public static function getUserWorkerOrderDetail($order_id)
    {
        $order_detail = self::getOrderDetail($order_id);
        if (is_int($order_detail)) {
            return 1000;
        }
        list($order, $worker_items) = $order_detail;

        //TODO  调整历史单独分出来(对应订单)  好评率
        $worker = [];
        //只要有工人id,便显示工人信息

        if ($order->worker_id) {
            $worker = Worker::find()
                ->select('id,uid,nickname,icon,order_done,level,worker_type_id,skill_ids')
                ->where(['id' => $order->worker_id])
                ->asArray()
                ->one();
            $worker['mobile'] = User::find()
                ->select('mobile')
                ->where(['id' => $worker['uid']])
                ->one()['mobile'];
            $worker['worker_type_id']=WorkerType::getparenttype($worker['worker_type_id']);
            $rank=WorkerRank::find()
                ->asArray()
                ->where(['id'=>$worker['level']])
                ->select('rank_name')
                ->one();
            switch ($order->status){
                case 2:
                    $worker['info']='工人已接单';
                    break;
                case 3:
                    $worker['info']='工人申请开工';
                    break;
                case 4:
                    $worker['info']='工人施工中';
                    break;
                case 5:
                    $worker['info']='工人已完成';
                    break;
            }
            $worker=array_merge($worker,$rank);


        }elseif($order->status==6){
            $worker['info']='你已取消订单';
        }elseif($order->status==1){
            $worker['info']='等待工人接单中';
        }

        $order_no = self::getOrderNoById($order_id);
//        $works_id = 0;
//        if ($order->status == self::WORKER_ORDER_DONE) {
//            //得到worker_works_id
//            $works_id = self::getWorksIdByOrderNo($order_no);
//        }
        $order_img = self::getOrderImg($order_no);
        $order->status = self::USER_WORKER_ORDER_STATUS[$order->status];

        return [
            'order' => $order,
            'worker_items' => $worker_items,
            'order_img' => $order_img,
            'worker' => $worker,
        ];
    }
    /**
     * 工程订单详情-工人
     * @param $order_id
     * @return array|int
     */
    public static function getWorkerWorkerOrderDetail($order_id){
        $order_detail = self::getOrderDetail($order_id);
        if (is_int($order_detail)) {
            return 1000;
        }
        list($order, $worker_items) = $order_detail;
        $order_no = self::getOrderNoById($order_id);
        $order_img = self::getOrderImg($order_no);
        $order->status = self::USER_WORKER_ORDER_STATUS[$order->status];

        return [
            'order' => $order,
            'worker_items' => $worker_items,
            'order_img' => $order_img,
        ];

    }
    /**
     * 智管工地详情-工人
     * @param $order_no
     * @param $uid
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function workersiteview($order_no,$uid){
        //获取今天的时间
        $today_time=date('Ymd',time());
        $worker_order=WorkerOrder::find()
            ->select('status,days')
            ->asArray()
            ->where(['order_no'=>$order_no])
            ->one();

        $renovation_infos=[];
        if($worker_order['status']==self::WORKER_ORDER_PREPARE || $worker_order['status']==self::WORKER_WORKS_AFTER || $worker_order['status']==self::WORKER_ORDER_TOYI ){
            $data['time']=date('Y-m-d',time());
            $data['status']=self::WORKER_ORDER_STATUS[$worker_order['status']];
            $data['renovation_infos']=$renovation_infos;
            return $data;
        }elseif($worker_order['status']==self::WORKER_ORDER_ING ){
            $days=explode(',',$worker_order['days']);
            if(in_array($today_time,$days)){
                $worker_info=Worker::find()
                    ->select('icon,nickname,id')
                    ->asArray()
                    ->where(['uid'=>$uid])
                    ->one();
                $data['view']['time']=date('Y-m-d',time());
                $data['view']['status']=self::WORKER_ORDER_STATUS[$worker_order['status']];
                $work_result['result']=WorkResult::find()
                    ->asArray()
                    ->where(['order_no'=>$order_no])
                    ->orderBy('create_time Desc')
                    ->all();
                foreach ($work_result['result'] as $k=>&$value){
                    $value['create_time']=date('Y-m-d',$value['create_time']);
                    $img=WorkResultImg::find()
                        ->select('result_img')
                        ->asArray()
                        ->where(['work_result_id'=>$value['id']])
                        ->all();

                    $value['img']=$img;
                    $value['worker_info']=$worker_info;
                }
            }
        }elseif($worker_order['status']==self::WORKER_ORDER_DONE) {
            $days = explode(',', $worker_order['days']);
            if (in_array($today_time, $days)) {
                $worker_info = Worker::find()
                    ->select('icon,nickname')
                    ->asArray()
                    ->where(['uid' => $uid])
                    ->one();
                $data['view']['status'] = self::WORKER_ORDER_STATUS[$worker_order['status']];
                $work_result['result'] = WorkResult::find()
                    ->asArray()
                    ->where(['order_no' => $order_no])
                    ->orderBy('create_time Desc')
                    ->all();
                foreach ($work_result['result'] as $k => &$value) {
                    $value['create_time'] = date('Y-m-d', $value['create_time']);
                    $img = WorkResultImg::find()
                        ->select('result_img')
                        ->asArray()
                        ->where(['work_result_id' => $value['id']])
                        ->all();

                    $value['img'] = $img;
                    $value['worker_info'] = $worker_info;

                }
            }
        }
                return array_merge($data, $work_result);
    }
    /**
     * 智管工地详情-用户
     * @param $order_no
     * @param $uid
     * @return array
     */
    public static function usersiteview($order_no,$uid){
        $today_time=date('Ymd',time());
        $worker_order=WorkerOrder::find()
            ->select('status,days')
            ->asArray()
            ->where(['order_no'=>$order_no])
            ->one();

        $renovation_infos=[];
        $data=[];
        $work_result=[];
        if($worker_order['status']==self::WORKER_ORDER_NOT_BEGIN ){
            $data['time']=date('Y-m-d',time());
            $data['status']=self::USER_WORKER_ORDER_STATUS[$worker_order['status']];
            $data['renovation_infos']=$renovation_infos;
        }elseif($worker_order['status']==self::WORKER_ORDER_ING ){
            $days=explode(',',$worker_order['days']);
            if(in_array($today_time,$days)){
                $worker_info=Worker::find()
                    ->select('worker.id,worker.icon,worker.nickname,worker_rank.rank_name,worker_type.worker_type')
                    ->leftJoin('worker_type','worker.worker_type_id=worker_type.id')
                    ->leftJoin('worker_rank','worker_rank.id=worker.level')
                    ->asArray()
                    ->where(['uid'=>$uid])
                    ->one();
                $data['view']['time']=date('Y-m-d',time());
                $data['view']['status']=self::USER_WORKER_ORDER_STATUS[$worker_order['status']];
                $data['view']['worker_info']=$worker_info;
                $work_result['result']=self::worksresult($order_no,$worker_info);
            }else{
                $worker_info=Worker::find()
                    ->select('icon,nickname')
                    ->asArray()
                    ->where(['uid'=>$uid])
                    ->one();
                $data['view']['time']=date('Y-m-d',time());
                $data['view']['status']='休息';
                $work_result['result']=self::worksresult($order_no,$worker_info);
            }

        }elseif($worker_order['status']==self::WORKER_ORDER_DONE) {
            $days = explode(',', $worker_order['days']);
            if (in_array($today_time, $days)) {
                $worker_info = Worker::find()
                    ->select('icon,nickname')
                    ->asArray()
                    ->where(['uid' => $uid])
                    ->one();
                $data['view']['status'] = self::USER_WORKER_ORDER_STATUS[$worker_order['status']];
                $work_result['result'] = self::worksresult($order_no, $worker_info);

            }
        }
        return array_merge($data,$work_result);
    }


    public static function worksresult($order_no,$worker_info){
        $data= WorkResult::find()
            ->asArray()
            ->where(['order_no' => $order_no])
            ->orderBy('create_time Desc')
            ->all();
        foreach ($data as $k => &$value) {
            $value['create_time'] = date('Y-m-d', $value['create_time']);
            $img = WorkResultImg::find()
                ->select('result_img')
                ->asArray()
                ->where(['work_result_id' => $value['id']])
                ->all();

            $value['img'] = $img;
            $value['worker_info'] = $worker_info;

        }
        return $data;
    }
    public static function getOrderImg($order_no, $page_size = self::IMG_PAGE_SIZE_DEFAULT, $page = 1)
    {
        $query = WorkerOrderImg::find()
            ->where(['worker_order_no' => $order_no]);

        $count = $query->count();

        $pagination = new Pagination([
            'totalCount' => $count,
            'pageSize' => $page_size,
            'pageSizeParam' => false
        ]);

        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->all();

        $data = ModelService::pageDeal($arr, $count, $page, $page_size);
        return $data;
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->status = self::STATUS_INSERT;
        }
        return parent::beforeSave($insert);
    }


//    public function afterSave($insert, $changedAttributes)
//    {
//        //每完成一个订单
//        if (!$insert && $changedAttributes['status'] == self::WORKER_ORDER_DONE) {
//            //TODO 工人完成订单数加1
//        }
//
//        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
//    }

    /**
     * 得到订单条目详情
     * @param $order_id
     * @param $worker_item_id
     * @return array|int
     */
    public static function getOrderItemDetail($order_id, $item_id)
    {
        $worker_items = WorkerItem::find()->where(['pid' => $item_id])->asArray()->all();

        foreach ($worker_items as &$worker_item) {
            $worker_order_item = WorkerOrderItem::find()
                ->where(['worker_order_id' => $order_id, 'worker_item_id' => $worker_item['id']])
                ->one();
            if ($worker_order_item) {
                if ($worker_order_item->worker_craft_id) {
                    $craft_id = (int)$worker_order_item->worker_craft_id;
                    $worker_item['craft'] = WorkerCraft::find()
                        ->where(['id' => $craft_id])
                        ->select('craft')
                        ->one()['craft'];
                } elseif ($worker_order_item->area) {
                    $worker_item['area'] = $worker_order_item->area;
                }
            }
        }
        return $worker_items;
    }

    /**
     * save images
     * @param array $images
     * @param $order_id
     * @return bool
     */
    public static function saveorderimgs(array $images, $order_no)
    {
        $worker_order_img = new WorkerOrderImg();
        foreach ($images as $attributes) {
            $_model = clone $worker_order_img;
            $_model->order_img = $attributes;
            $_model->worker_order_no = $order_no;
            $res = $_model->save();
        }
        if (!$res) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 获取时间段精确到具体每一天
     * @param $start_time
     * @param $end_time
     * @return string
     */
    public static function dataeveryday($start_time, $end_time)
    {
        $days = ($end_time - $start_time) / 86400 + 1;
        $date = [];
        for ($i = 0; $i < $days; $i++) {
            $date[] = date('Ymd', $start_time + (86400 * $i));
        }
        return implode(',', $date);
    }

//    /**
//     * add guarantee info into worker_order_item
//     * @param $id
//     * @param $item_id
//     * @param $guarantee
//     * @return bool
//     */
//    public static function inserstatus($id, $item_id, $status)
//    {
//        $connection = \Yii::$app->db;
//        $res = $connection->createCommand()
//            ->insert('worker_order_item',
//                [
//                    'worker_order_id' => $id,
//                    'worker_item_id' => $item_id,
//                    'status' => $status
//                ])
//            ->execute();
//        if ($res) {
//            return true;
//        } else {
//            return false;
//        }
//    }
    /**
     * 快捷下单
     * @param $uid
     * @param array $array
     * @return int
     */
    public static function addorderFastinfo($uid,array $array){

        $worker_order = new self();
        $worker_order->uid = $uid;
        $worker_order->worker_type_id = $array['worker_type_id'];
        $worker_order->order_no = GoodsOrder::SetOrderNo();
        $worker_order->create_time = time();
        $worker_order->map_location = $array['map_location'];
        $worker_order->address = $array['address'];
        $worker_order->con_people = $array['con_people'];
        $worker_order->con_tel = $array['con_tel'];
        $worker_order->amount = $array['amount'] * 100;
        $worker_order->front_money = $array['front_money'] * 100;
        $worker_order->status=self::WORKER_ORDER_NOT_BEGIN;
        if(isset($array['demand'])){
            $worker_order->demand = $array['demand'];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$worker_order->save(false)) {
                $transaction->rollBack();
                $code = 500;
                return $code;
            }
            $worker_order_img = new WorkerOrderImg();
            $order_no = $worker_order_img->worker_order_no = $worker_order->order_no;
            $rest = self::saveorderimgs($array['images'], $order_no);
            if ($rest == false) {
                $transaction->rollBack();
                $code = 500;
                return $code;
            }
            $worker_order_item=new WorkerOrderItem();
            $worker_item_ids=implode(',',$array['worker_item_id']);
            $worker_order_item->worker_item_ids=$worker_item_ids;
            $worker_order_item->worker_type_id=$array['worker_type_id'];
            $worker_order_item->worker_order_no=$order_no;
            if(!$worker_order_item->save(false)){
                $transaction->rollBack();
                $code=500;
                return $code;
            }
            $code=200;
            $transaction->commit();
            return $code;
        }catch (Exception $e){
            $transaction->rollBack();
            $code = 500;
            return $code;
        }
    }
    /**
     * 生成订单
     * @param $uid
     * @param $homeinfos
     * @param $ownerinfos
     * @param $front_money
     * @param $amount
     * @return int
     */

    public static function addorderinfo($uid,array $array)

    {
        $worker_order = new self();
        $worker_order->uid = $uid;
        $worker_order->worker_type_id = $array['worker_type_id'];
        $worker_order->order_no = GoodsOrder::SetOrderNo();
        $worker_order->create_time = time();
        $start_time = $worker_order->start_time = strtotime($array['start_time']);
        $end_time = $worker_order->end_time = strtotime($array['end_time']);
        $worker_order->need_time = $array['need_time'];
        $worker_order->map_location = $array['map_location'];
        $worker_order->address = $array['address'];
        $worker_order->con_people = $array['con_people'];
        $worker_order->con_tel = $array['con_tel'];
        $worker_order->amount = $array['amount'] * 100;
        $worker_order->front_money = $array['front_money'] * 100;
        $worker_order->status=self::WORKER_ORDER_PREPARE;
        $worker_order->type=self::WORKER_ORDER_PREPARE;
        $days = self::dataeveryday($start_time, $end_time);
        $worker_order->days = $days;
        if (isset($describe)) {
            $worker_order->describe = $describe;
        }
        if (isset($demand)) {
            $worker_order->demand = $demand;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$worker_order->save(false)) {
                $transaction->rollBack();
                $code = 500;
                return $code;
            }
            $worker_order_img = new WorkerOrderImg();
            $order_no = $worker_order_img->worker_order_no = $worker_order->order_no;
            $rest = self::saveorderimgs($array['images'], $order_no);
            if ($rest == false) {
                $transaction->rollBack();
                $code = 500;
                return $code;
            }
            $type=WorkerType::getparenttype($array['worker_type_id']);
            if(!$type){
                return 1000;
            }
            $ks=array_keys($array);
            foreach ($ks as $k=>$key){
                if(preg_match('/(items)/',$key,$v)){
                        $data=$array[$key];
                }
            }
            switch ($type){
                case '泥工';
                    $res=self::saveMuditem($data,$worker_order->id);
                    break;
                case '水电工';
                    $res=self::savehydropoweritem($data,$worker_order->id);
                    break;
                case '木工';
                    $res=self::savecarpentryitem($data,$worker_order->id);
                    break;
                case '防水工';
                    $res=self::savewaterproofitme($data,$worker_order->id);
                    break;
                case '油漆工';
                    $res=self::savepainteritem($data,$worker_order->id);
                    break;
                case '杂工';
                    $res=self::savebackmanitem($data,$worker_order->id);
                    break;
            }
            if(!$res){
                $code=500;
                $transaction->rollBack();
                return $code;
            }
            $transaction->commit();
            return 200;
        } catch (Exception $e) {
            $transaction->rollBack();
            return 500;
        }

    }
    /**
     * 保存泥作条目信息
     * @param array $array
     * @param $order_id
     * @return bool
     */
    public static function saveMuditem(array $array,$order_id)
    {
        foreach ($array as &$v) {
            if(!isset($v['guarantee'])){
                $v['guarantee']=0;

            }
            if(!isset($v['chip'])){
                $v['chip']=0;
            }
                $res= \Yii::$app->db->createCommand()->insert('mud_worker_order',[
                    'order_id'=>$order_id,
                    'worker_item_id'=>$v['item_id'],
                    'worker_craft_id'=>$v['craft_id'],
                    'area'=>$v['area'],
                    'guarantee'=>$v['guarantee'],
                    'chip'=>$v['chip']
                ])->execute();

        }
        if (!$res) {
            return false;
        } else {
            return true;
        }
    }
    /**
     *保存水电工条目信息
     * @param array $array
     * @param $order_no
     * @return bool
     */
    public static function savehydropoweritem(array $array,$order_id){
        foreach ($array as &$v) {
                foreach ($v as &$data){
                    if(!$data['craft_id']){
                        $data['craft_id']=0;
                    }
                    if(!$data['length']){
                        $data['length']=0;
                    }
                    if(!$data['electricity']){
                        $data['electricity']=null;
                    }
                    if(!$data['count']){
                        $data['count']=0;
                    }
                    $res= \Yii::$app->db->createCommand()->insert('hydropower_worker_order',[
                        'order_id'=>$order_id,
                        'worker_item_id'=>$data['item_id'],
                        'worker_craft_id'=>$data['craft_id'],
                        'length'=>$data['length'],
                        'electricity'=>$data['electricity'],
                        'count'=>$data['count']
                    ])->execute();
                }
        }
        if (!$res) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 保存防水工条目信息
     * @param array $array
     * @param $order_no
     * @return bool
     */
    public static function savewaterproofitme(array $array,$order_id){
        foreach ($array as &$v) {
            $res= \Yii::$app->db->createCommand()->insert('waterproof_worker_order',[
                'order_id'=>$order_id,
                'worker_item_id'=>$v['item_id'],
                'worker_craft_id'=>$v['craft_id'],
                'area'=>$v['area'],
                'brand'=>$v['brand'],
            ])->execute();

        }
        if (!$res) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 保存油漆工条目信息
     * @param array $array
     * @param $order_no
     * @return bool
     */
    public static function savepainteritem(array $array,$order_id){
        foreach ($array as &$v) {
            foreach ($v as &$data) {
                if(!$data['craft_id']){
                    $data['craft_id']=0;
                }
                if(!$data['area']){
                    $data['area']=0;
                }
                if(!$data['brand']){
                    $data['brand']=0;
                }

                $res = \Yii::$app->db->createCommand()->insert('painter_worker_order', [
                    'order_id' => $order_id,
                    'worker_item_id' => $data['item_id'],
                    'worker_craft_id' => $data['craft_id'],
                    'area' => $data['area'],
                    'brand' => $data['brand'],
                ])->execute();
            }
        }
        if (!$res) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 保存木工条目信息
     * @param array $array
     * @param $order_no
     * @return bool
     */
    public static function savecarpentryitem(array $array,$order_id){
            foreach ($array as $v){
                foreach ($v as &$data) {
                    if(!$data['craft_id']){
                        $data['craft_id']=0;
                    }
                    if(!$data['count']){
                        $data['count']=0;
                    }
                    if(!$data['length']){
                        $data['length']=0;
                    }
                    $res = \Yii::$app->db->createCommand()->insert('carpentry_worker_order', [
                        'order_id' => $order_id,
                        'worker_item_id' => $data['item_id'],
                        'worker_craft_id' => $data['craft_id'],
                        'count' => $data['count'],
                        'length' => $data['length'],
                    ])->execute();
                }
            }
        if (!$res) {
            return false;
        } else {
            return true;
        }

    }
    /**
     * 保存杂工条目信息
     * @param array $array
     * @param $order_no
     * @return bool
     */
    public static function savebackmanitem(array $array,$order_id){
        foreach ($array as $v){
            foreach ($v as &$data) {
                if(!$data['craft_id']){
                    $data['craft_id']=0;
                }
                if(!$data['area']){
                    $data['area']=0;
                }
                if(!$data['stack']){
                    $data['stack']=0;
                }
                if(!$data['length']){
                    $data['length']=0;
                }
                $res = \Yii::$app->db->createCommand()->insert('backman_worker_order', [
                    'order_id' => $order_id,
                    'worker_item_id' => $data['item_id'],
                    'worker_craft_id' => $data['craft_id'],
                    'area' => $data['area'],
                    'length' => $data['length'],
                    'stack' => $data['stack'],
                ])->execute();
            }
        }
        if (!$res) {
            return false;
        } else {
            return true;
        }

    }
    /**
     * 刷新订单随机
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getorderinfo($worker_type_id)
    {

        $data = self::find()
            ->asArray()
            ->where(['is_old' => self::ORDER_NEW])
            ->andWhere(['status' => self::STATUS_INSERT])
            ->andWhere(['worker_type_id'=>$worker_type_id])
            ->all();

        if(!$data){
            return null;
        }
        foreach ($data as $key => &$v) {

            $ids[] = $v['id'];
        }
        $rand_keys = array_rand($ids, 1);
        $id = $ids[$rand_keys];
        $info = self::find()
            ->asArray()
            ->where(['is_old' => self::ORDER_NEW])
            ->andWhere(['worker_id'=>self::IS_NEW])
            ->andWhere(['id' => $id])
            ->one();
        if (empty($info)) {
            return null;
        } else {
            return $info;
        }
    }

    /**
     * 时间格式
     * @param $order_id
     * @return null|string
     */
    public static function timedata($order_id)
    {
        $time = [];
        $order_info = self::find()
            ->select('start_time,end_time,need_time')
            ->where(['id' => $order_id])
            ->asArray()
            ->one();
        if (!$order_info) {
            return null;
        }
        $start_time = date('Y.n.j', $order_info['start_time']);
        $end_time = date('Y.n.j', $order_info['end_time']);
        $time['need_time'] = $order_info['need_time'];
        $time['time_length'] = $start_time . self::TIMESTYPE . $end_time;
        return $time;
    }

    /**
     * @param int $order_id
     */
    public static function getOrderNoById($order_id)
    {
        $order = self::find()->where(['id' => $order_id])->one();
        if ($order) {
            return $order->order_no;
        }
        return null;
    }

    public static function getOrderHistory($uid, $order_no)
    {
        $data = WorkerOrder::find()
            ->where(['uid' => $uid, 'order_no' => $order_no, 'is_old' => self::IS_OLD])
            ->asArray()
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if ($data) {
            $data['status'] = self::USER_WORKER_ORDER_STATUS[$data['status']];
            return $data;
        }

        return false;
    }

    /**
     * 新工人作品
     *
     * @param $worker_id
     * @param $order_no
     * @param string $title
     * @param string $desc
     * @return int|array
     */
    public static function newWorkerWorks($worker_id, $order_no, $title = '', $desc = '')
    {
        $worker_works = WorkerWorks::find()->where(['worker_id' => $worker_id, 'order_no' => $order_no])->exists();
        if ($worker_works) {
            return 1000;
        }

        if (!isset($title) || trim($title) == '') {

            $worker_order = self::find()->where(['order_no' => $order_no, 'is_old' => self::IS_NEW])->one();

            if ($worker_order == null) {
                return 1000;
            }

            $worker_type_id = $worker_order->worker_type_id;

            $title = WorkerType::find()
                ->where(['id' => $worker_type_id])
                ->one();
            $title && $title = $title->worker_type;
        }

        $worker_works = new WorkerWorks();
        $worker_works->setAttributes([
            'worker_id' => $worker_id,
            'order_no' => $order_no,
            'title' => $title,
            'desc' => $desc
        ]);

        $trans = Yii::$app->db->beginTransaction();
        if (!$worker_works->save(false)) {
            $trans->rollBack();
            return 1000;
        }
        $trans->commit();
        return [200, $worker_works->id];
    }


    /**
     * 新的工人作品详情
     *
     * @param $works_id
     * @return int
     */
    public static function newWorkerWorksDetail($works_id)
    {
        $works = WorkerWorks::find()->where(['id' => $works_id])->exists();

        if (!$works) {
            return 1000;
        }

        $detail = WorkerWorksDetail::find()->where(['works_id' => $works_id])->exists();

        if ($detail) {
            return 1000;
        }

        $details = [];

        $detail = new WorkerWorksDetail();

        $details[0]['works_id'] = $works_id;
        $details[0]['status'] = self::WORKER_WORKS_BEFORE;
        $details[0]['img_ids'] = self::getWorksImg($works_id, self::WORKER_WORKS_BEFORE);

        $details[1]['works_id'] = $works_id;
        $details[1]['status'] = self::WORKER_WORKS_ING;
        $details[1]['img_ids'] = self::getWorksImg($works_id, self::WORKER_WORKS_ING);

        $details[2]['works_id'] = $works_id;
        $details[2]['status'] = self::WORKER_WORKS_AFTER;
        $details[2]['img_ids'] = self::getWorksImg($works_id, self::WORKER_WORKS_AFTER);

        $trans = Yii::$app->db->beginTransaction();

        foreach ($details as $d) {
            $_detail = clone $detail;
            $_detail->setAttributes($d, false);
            if (!$_detail->save(false)) {
                $trans->rollBack();
                return 1000;
            }
        }

        $trans->commit();
        return 200;
    }


    /**
     * 自动生成工人的作品图片
     *
     * @param $works_id
     * @param $status
     * @return string
     */
    public static function getWorksImg($works_id, $status)
    {

        $works = WorkerWorks::find()->where(['id' => $works_id])->one();

        if (!$works) {
            return '';
        }

        $img = [];

        $order_no = $works->order_no;
        switch ($status) {

            case self::WORKER_WORKS_BEFORE:
                //先找用户下订单的图片
                $order_img = WorkerOrderImg::find()
                    ->where(['worker_order_no' => $order_no])
                    ->limit(self::IMG_COUNT_DEFAULT)
                    ->all();
                if ($order_img) {
                    foreach ($order_img as $value) {
                        $img[] = '0' . $value->id;
                    }
                } else {
                    $worker_order_day = WorkerOrderDayResult::find()
                        ->where(['order_no' => $order_no])
                        ->limit(1)
                        ->orderBy(['id' => SORT_ASC])
                        ->one();
                    if (!$worker_order_day) {
                        return '';
                    }

                    $order_day_result_id = $worker_order_day->id;

                    $result_img = WorkResultImg::find()
                        ->where(['order_day_result_id' => $order_day_result_id])
                        ->limit(self::IMG_COUNT_DEFAULT)
                        ->orderBy(['id' => SORT_ASC])
                        ->all();

                    if (!$result_img) {
                        return '';
                    }
                    foreach ($result_img as $value) {
                        $img[] = $value->id;
                    }
                }
                break;

            case self::WORKER_WORKS_ING:
                //装修的天数/2取整   这天的图片前3张
                $query = WorkerOrderDayResult::find()
                    ->where(['order_no' => $order_no]);
                $count = $query->count();

                $day = ceil($count / 2) - 1;

                $worker_order_day = $query
                    ->offset($day)
                    ->limit(1)
                    ->orderBy(['id' => SORT_ASC])
                    ->one();

                if (!$worker_order_day) {
                    return '';
                }

                $order_day_result_id = $worker_order_day->id;

                $result_img = WorkResultImg::find()
                    ->where(['order_day_result_id' => $order_day_result_id])
                    ->limit(self::IMG_COUNT_DEFAULT)
                    ->orderBy(['id' => SORT_ASC])
                    ->all();
                if (!$result_img) {
                    return '';
                }
                foreach ($result_img as $value) {
                    $img[] = $value->id;
                }
                break;

            case self::WORKER_WORKS_AFTER:
                $worker_order_day = WorkerOrderDayResult::find()
                    ->where(['order_no' => $order_no])
                    ->limit(1)
                    ->orderBy(['id' => SORT_DESC])
                    ->one();
                if (!$worker_order_day) {
                    return '';
                }

                $order_day_result_id = $worker_order_day->id;

                $result_img = WorkResultImg::find()
                    ->where(['order_day_result_id' => $order_day_result_id])
                    ->limit(self::IMG_COUNT_DEFAULT)
                    ->orderBy(['id' => SORT_ASC])
                    ->all();

                if (!$result_img) {
                    return '';
                }
                foreach ($result_img as $value) {
                    $img[] = $value->id;
                }
                break;

            default:
                return '';
        }

        if ($img == []) {
            $img = '';
        } else {
            $img = implode(',', $img);
        }

        return $img;
    }


    public static function getWorksIdByOrderNo($order_no)
    {
        $id = 0;
        $works = WorkerWorks::find()->where(['order_no' => $order_no])->one();
        if ($works) {
            $id = $works->id;
        }
        return $id;
    }

    public static function getWorksByWorkerId($worker_id)
    {
        return WorkerWorks::find()->where(['work_id' => $worker_id])->all();
    }

    /**
     * get works by worker_id
     *
     * @param  int $worker_id
     * @param int $page
     * @param int $page_size
     * @return array
     */
    public static function getWorksByWorkerIdAll($worker_id, $page = 1, $page_size = self::IMG_PAGE_SIZE_DEFAULT)
    {
        $query = WorkerWorks::find()->where(['worker_id' => $worker_id]);

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $page_size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->all();

        //开始时间和结束时间,图片选择完工的图片随机
        foreach ($arr as &$v) {

            $works_detail = WorkerWorksDetail::find()
                ->where(['id' => $v['id'], 'status' => self::WORKER_WORKS_AFTER])
                ->one();
            if ($works_detail) {

                $img_ids = $works_detail->img_ids;

                if ($img_ids) {
                    $ids = explode(',', $img_ids);
                    $id = array_rand($ids);
                    if($id!=0){
                        $v['img'] = WorkResultImg::find()
                            ->where(['id' => $id])
                            ->one()
                            ->result_img;
                    }
                }
            } else {
                $v['img'] = '';
            }

            $worker_order = WorkerOrder::find()
                ->where(['order_no' => $v['order_no'], 'is_old' => self::IS_OLD])
                ->one();

            if(!$worker_order){
                return null;
            }
            $v['start_time'] = date('Y-n-j', $worker_order->start_time);
            $v['end_time'] = date('Y-n-j', $worker_order->end_time);
        }

        $data = ModelService::pageDeal($arr, $count, $page, $page_size);
        return $data;
    }

    /**
     * 得到工人某个月的排班
     *
     * @param $worker_id
     * @param $time_area
     * @return array
     */
    public static function getWorkDaysByTimeArea($worker_id, $time_area)
    {
        list($start_time, $end_time) = $time_area;

        //!备注： eg: 8月的排班   开始时间 <= 8月31  结束时间 >= 8月1

        //得到工人在此区间内的天数
        $all_days = WorkerOrder::find()
            ->where([
                'worker_id' => $worker_id,
                'is_old' => self::IS_NEW,
                'status' => [2, 3, 4]
            ])
            ->andWhere(['<=', 'start_time', $end_time])
            ->andWhere(['>=', 'end_time', $start_time])
            ->select('days')
            ->all();

        if (!$all_days) {
            return [];
        }
        //查出来的days里面筛选出符合返回的day
        $days_arr = [];
        foreach ($all_days as $days) {
            if ($days) {
                $days_arr_tmp = explode(',', $days->days);
                foreach ($days_arr_tmp as $day_tmp) {
                    if (strtotime($day_tmp) >= $start_time
                        && strtotime($day_tmp) <= $end_time
                    ) {
                        $days_arr[] = $day_tmp;
                    }
                }
            }
        }

        $return = array_unique($days_arr);
        sort($return, SORT_NUMERIC);

        return $return;
    }

    /**
     * 得到工人的全部排班日期
     *
     * @param $worker_id
     * @return array
     */
    public static function getWorkDaysAll($worker_id)
    {
        $all_days = WorkerOrder::find()
            ->where([
                'worker_id' => $worker_id,
                'is_old' => self::IS_NEW,
                'status' => [2, 3, 4]
            ])
            ->select('days')
            ->all();

        if (!$all_days) {
            return [];
        }

        $days_arr = [];
        foreach ($all_days as $days) {
            if ($days) {
                $days_arr = array_merge($days_arr, explode(',', $days->days));
            }
        }

        $return = array_unique($days_arr);
        sort($return, SORT_NUMERIC);

        return $return;
    }

    public static function countMoney()
    {
//        return self::
    }

    public static function orderList($where = [],$size,$page)
    {
        $offset = ($page - 1) * $size;

        $select = 'worker_order.con_people,worker_order.con_tel,worker_order.order_no,worker_order.create_time,user.aite_cube_no,worker_type.worker_name,worker_order.amount,worker_order.status';
        $details = self::find()
            ->select($select)
            ->where($where)
            ->leftJoin('worker','worker.id = worker_order.worker_id')
            ->leftJoin('user','user.id = worker.uid')
            ->leftJoin('worker_type','worker_type.id = worker_order.id')
            ->offset($offset)
            ->limit($size)
            ->groupBy('worker_order.order_no')
            ->asArray()
            ->all();


        foreach ($details as &$one_details){
            $one_details['create_time'] = date('Y-m-d H:i',$one_details['create_time']);
            $one_details['amount'] = sprintf('%.2f',$one_details['amount'] * 0.01);
        }

        return [
            'total' => (int)self::find()->where($where)->asArray()->count(),
            'page'  => $page,
            'size'  => $size,
            'details' => $details
        ];
    }

    /**工人修改订单
     * @param $array
     * @return int
     */
    public static function UpdateOrder($array){
        $order=WorkerOrder::find()->asArray()->where(['id'=>$array['order_id'],'is_old'=>0])->one();
        //修改泥作订单表的内容
        //修改建议工期


        $tran=Yii::$app->db->beginTransaction();
        try{
            //修改旧数据
            $res_edit_old=WorkerOrder::find()->where(['id'=>$array['order_id'],'is_old'=>0])->one();
            $res_edit_old->is_old=1;
            if(!$res_edit_old->update(false)){
                $tran->rollBack();
                $code=500;
                return $code;
            };
            $last_id=self::editorderinset($order,$array);
            if(!$last_id){
                $tran->rollBack();
                $code=500;
                return $code;
            }
            //修改items数据
            switch (WorkerType::gettype($array['worker_type_id'])){
                case '泥工':
                    $items_res=self::saveMuditem($array['items'],$last_id);
                    break;
                case '防水工':
                    $items_res=self::savewaterproofitme($array['items'],$last_id);
                    break;
                case '油漆工':
                    $items_res=self::savepainteritem($array['items'],$last_id);
                    break;
                case '水电工':
                    $items_res=self::savehydropoweritem($array['items'],$last_id);
                    break;
                case '木工':
                    $items_res=self::savecarpentryitem($array['items'],$last_id);
                    break;
                case '杂工':
                    $items_res=self::savebackmanitem($array['items'],$last_id);
                    break;
            }
            if(!$items_res){
                $tran->rollBack();
                $code=500;
                return $code;
            }
            $tran->commit();
            return 200;

        }catch (Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }



    }

    public static function editorderinset($order_info,$edit_info){

        if(isset($edit_info['need_time'])){
            $need_time=$edit_info['need_time'];
        }else{
            $need_time=$order_info['need_time'];
        }
        //修改订单金额
        if(isset($edit_info['amount'])){
            $amount=$edit_info['amount'];
            $reason=$edit_info['reason'];
        }else{
            $amount=$order_info['amount'];
            $reason='';
        }
        $worker_order=new WorkerOrder();
        $worker_order->uid= $order_info['uid'];
        $worker_order->worker_type_id = $order_info['worker_type_id'];
        $worker_order->worker_id = $order_info['worker_id'];
        $worker_order->order_no =$order_info['order_no'];
        $worker_order->create_time = $order_info['create_time'];
        $worker_order->modify_time=time();
        $worker_order->need_time=$need_time;
        $worker_order->start_time=$order_info['start_time'];
        $worker_order->end_time=$order_info['end_time'];
        $worker_order->days=$order_info['days'];
        $worker_order->map_location = $order_info['map_location'];
        $worker_order->address = $order_info['address'];
        $worker_order->con_people = $order_info['con_people'];
        $worker_order->con_tel = $order_info['con_tel'];
        $worker_order->amount = $amount;
        $worker_order->reason=$reason;
        $worker_order->front_money = $order_info['front_money'];
        $worker_order->type = $order_info['type'];
        $worker_order->status = $order_info['status'];
        $worker_order->is_old = self::WORKER_ORDER_NOT_BEGIN;

        if(!$worker_order->save(false)){
            return false;
        }
        return Yii::$app->db->lastInsertID;
    }
}
