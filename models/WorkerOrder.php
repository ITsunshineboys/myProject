<?php

namespace app\models;

use app\controllers\WorkerController;
use app\services\ModelService;
use Yii;
use yii\data\Pagination;

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
 */
class WorkerOrder extends \yii\db\ActiveRecord
{
    const STATUS_INSERT=1;
    const WORKER_ORDER_STATUS = [
        0 => '完工',
        1 => '未开始',
        2 => '施工中',
        3 => '完工'
    ];

    const USER_WORKER_ORDER_STATUS = [
        0 => '已取消',
        1 => '接单中',
        2 => '施工中',
        3 => '已完工'
    ];

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
            [['uid', 'worker_id', 'create_time', 'start_time', 'end_time', 'need_time', 'amount', 'front_money', 'status'], 'integer'],
            [['con_tel'], 'required'],
            [['order_no'], 'string', 'max' => 50],
            [['map_location', 'address'], 'string', 'max' => 100],
            [['con_people'], 'string', 'max' => 25],
            [['con_tel'], 'string', 'max' => 11],
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
            'order_no' => '工单号',
            'create_time' => '创建时间',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'need_time' => '工期(天数)',
            'map_location' => '地图定位',
            'address' => '施工详细地址',
            'con_people' => '联系人',
            'con_tel' => '联系电话',
            'amount' => '订单总金额',
            'front_money' => '订金',
            'status' => '0: 已取消(完成)，1：未开始(接单中)，2：施工中，3：已完工(完成)',
        ];
    }

    /**
     * 工人智管工地列表
     * @param $uid
     * @param $status
     * @param $page
     * @param $page_size
     * @return array
     */
    public static function getWorkerOrderList($uid, $status, $page, $page_size)
    {
        $worker = Worker::getWorkerByUid($uid);
        $worker_id = $worker->id;
        $query = self::find()
            ->select(['create_time', 'amount', 'status'])
            ->where(['uid' => $uid, 'worker_id' => $worker_id]);
        if ($status != WorkerController::STATUS_ALL) {
            if ($status == 0 || $status == 3) {
                $status = [0, 3];
            }
            $query->andWhere(['status' => $status]);
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $page_size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->all();

        $worker_type_id = Worker::find()->where(['id' => $worker_id])->one()->worker_type_id;
        $worker_type = WorkerType::find()->where(['id' => $worker_type_id])->one()->worker_type;

        foreach ($arr as &$v) {
            $v['worker_type'] = $worker_type;
            $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
            $v['amount'] = sprintf('%.2f', (float)$v['amount'] / 100);
            $v['status'] = self::WORKER_ORDER_STATUS[$v['status']];
        }

        $data = ModelService::pageDeal($arr, $count, $page, $page_size);
        return $data;
    }

    /**
     * 用户工程订单列表
     * @param $uid
     * @param $status
     * @param $page
     * @param $page_size
     * @return array
     */
    public static function getUserWorkerOrderList($uid, $status, $page, $page_size)
    {
        $query = self::find()
            ->select(['create_time', 'amount', 'status', 'worker_id'])
            ->where(['uid' => $uid]);
        if ($status != WorkerController::STATUS_ALL) {
            $query->andWhere(['status' => $status]);
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $page_size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->all();

        foreach ($arr as &$v) {
            $worker_type_id = Worker::find()->where(['id' => $v['worker_id']])->one()->worker_type_id;
            $v['worker_type'] = WorkerType::find()->where(['id' => $worker_type_id])->one()->worker_type;
            $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
            $v['amount'] = sprintf('%.2f', (float)$v['amount'] / 100);
            $v['status'] = self::USER_WORKER_ORDER_STATUS[$v['status']];
        }

        $data = ModelService::pageDeal($arr, $count, $page, $page_size);
        return $data;
    }

    /**
     * get User worker_order detail
     * @param $order_id
     * @return array|int
     */
    public static function getUserWorkerOrderDetail($order_id)
    {
        $order = self::find()->where(['id' => $order_id])->one();
        if ($order == null) {
            return 1000;
        }

        $worker_type_id = $order->worker_type_id;

        $worker_type_items = WorkerTypeItem::find()->where(['worker_type_id' => $worker_type_id])->all();
        $worker_items = [];
        foreach ($worker_type_items as $worker_type_item) {
            $worker_item_id = $worker_type_item->worker_item_id;
            $worker_item = WorkerItem::find()
                ->where(['id' => $worker_item_id])
                ->select(['id', 'title'])
                ->asArray()->one();
//            var_dump($worker_item);
            $worker_items[] = $worker_item;
        }

        $order->create_time && $order->create_time = date('Y-m-d H:i', $order->create_time);
        $order->start_time && $order->start_time = date('Y-m-d H:i', $order->start_time);
        $order->end_time && $order->end_time = date('Y-m-d H:i', $order->end_time);
        $order->amount && $order->amount = sprintf('%.2f', (float)$order->amount / 100);
        $order->front_money && $order->front_money = sprintf('%.2f', (float)$order->front_money / 100);

        //TODO 查出工人的labor_cost_id(等级，省，市)，(成交数量，风格)待定， 调整历史单独分出来(对应订单)
        //TODO 如果状态是0  查出取消时间和取消原因   worker_order 表需要加上cancel_time 和 cancel_reason字段

        $worker = [];
        //只要有工人id,便显示工人信息
        if ($order->worker_id) {
            $worker = Worker::find()
                ->where(['id' => $order->worker_id])
                ->select(['id', 'nickname', 'work_year', 'comprehensive_score', 'icon'])
                ->one();
        }

        $order->status = self::USER_WORKER_ORDER_STATUS[$order->status];
        $order_img = WorkerOrderImg::find()->where(['worker_order_id' => $order_id])->all();

        return [
            'order' => $order,
            'worker_items' => $worker_items,
            'order_img' => $order_img,
            'worker' => $worker
        ];
    }
    public function beforeSave($insert)
    {
        if($insert){
            $this->status=self::STATUS_INSERT;
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

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
                        ->where(['id'=> $craft_id])
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
     * 生成订单
     * @param $uid
     * @param $homeinfos
     * @param $ownerinfos
     * @param $front_money
     * @param $amount
     * @return int
     */

    public static function addorderinfo($uid, $homeinfos,$ownerinfos,$front_money,$amount){

        $worker_order = new self();
        $worker_order->uid =1;
        $worker_order->worker_type_id = $homeinfos['worker_type_id'];
        $worker_order->order_no = date('md', time()) . '1' . rand(10000, 99999);
        $worker_order->create_time = time();
        $worker_order->start_time = $homeinfos['start_time'];
        $worker_order->end_time = $homeinfos['end_time'];
        $worker_order->need_time = $homeinfos['need_time'];
        $worker_order->map_location=$ownerinfos['map_location'];
        $worker_order->address=$ownerinfos['address'];
        $worker_order->con_people=$ownerinfos['con_people'];
        $worker_order->con_tel=$ownerinfos['con_tel'];
        $worker_order->amount=$amount;
        $worker_order->front_money=$front_money;
        if(isset($homeinfos['describe'])){
            $worker_order->describe=$homeinfos['describe'];
        }
        $transaction = Yii::$app->db->beginTransaction();
        if(!$worker_order->save(false)){
            $transaction->rollBack();
            $code = 500;
            return $code;
        }
            $data=[];
            $worker_order_item=new WorkerOrderItem();
            $id=$worker_order_item->id=$worker_order->id;
            $keys=array_keys($homeinfos);
            foreach ($keys as $k=>&$key){
                if(preg_match('/(item)/',$key,$m)){
                    $data[$k]=$homeinfos[$key];
                    foreach ($data as $k=>&$dat){
                        $dat['id']=$id;
                    }
                }
            }
            $connection = \Yii::$app->db;
            $connection
                ->createCommand()
                ->batchInsert(
                'worker_order_item',
                ['worker_item_id','worker_craft_id','area','worker_order_id'],
                $data
            )->execute();


        if(!$worker_order->save(false)){
            $transaction->rollBack();

            $code = 500;
            return $code;
        }
        $transaction->commit();
        return 200;


    }

}
