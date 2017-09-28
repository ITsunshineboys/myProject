<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "worker_works".
 *
 * @property integer $id
 * @property integer $worker_id
 * @property integer $order_no
 * @property string $title
 * @property string $desc
 */
class WorkerWorks extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_works';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['worker_id', 'order_no'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['desc'], 'string', 'max' => 350],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'worker_id' => '工人id',
            'order_no' => '工人订单号',
            'title' => '标题',
            'desc' => '作品描述',
        ];
    }
    /**
     * 工人最近作品
     * @param $worker_id
     * @return array|bool|null
     */
    public static function getLatelyWorks($worker_id){
        $query=(new Query())
            ->from('worker_works as ww')
            ->select('ww.*,wrj.result_img,wo.start_time,wo.end_time')
            ->leftJoin('work_result_img as wrj','ww.id=wrj.work_result_id')
            ->leftJoin('worker_order as wo','ww.worker_id=wo.worker_id')
            ->where(['ww.id'=>$worker_id])
            ->andWhere(['is_old'=>1])
            ->orderBy('wo.end_time Desc')
            ->one();
        $query['start_time']=date('Y-m-d',$query['start_time']);
        $query['end_time']=date('Y-m-d',$query['end_time']);
       if(!$query){
           return null;
       }
       return $query;
    }
}
