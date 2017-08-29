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
 */
class WorkerOrder extends \yii\db\ActiveRecord
{

    const WORKER_ORDER_STATUS = [
        0 => '完工',
        1 => '接单中',
        2 => '施工中',
        3 => '完工'
    ];

    const USER_WORKER_ORDER_STATUS = [
        0 => '已取消',
        1 => '未开始',
        2 => '施工中',
        3 => '完工'
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
    public function getWorkerOrderList($uid, $status, $page, $page_size)
    {
        $worker = (new Worker())->getWorkerByUid($uid);
        $worker_id = $worker->id;
        $query = self::find()
            ->select(['create_time', 'amount', 'status'])
            ->where(['uid' => $uid, 'worker_id' => $worker_id]);
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
            $v['create_time'] = date('Y-m-d H:i', $v['create_time']);
            $v['amount'] = sprintf('%.2f', (float)$v['amount'] / 100);
            $v['status'] = self::WORKER_ORDER_STATUS[$v['status']];
        }
        $arr['worker_type'] = (new Worker())->getWorkerTypeByWorkerId($worker_id);

        $data = ModelService::pageDeal($arr, $count, $page, $page_size);
        return $data;
    }
}
