<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "worker_valuedetails".
 *
 * @property string $id
 * @property integer $worker_id
 * @property string $order_no
 * @property integer $create_time
 * @property string $reason
 * @property integer $value
 * @property integer $status
 */
class WorkerValuedetails extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_valuedetails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['worker_id', 'create_time', 'value', 'status'], 'integer'],
            [['create_time', 'reason'], 'required'],
            [['order_no'], 'string', 'max' => 50],
            [['reason'], 'string', 'max' => 100],
        ];
    }

    /**
     *
     * @param $worker_id
     * @return array|string|\yii\db\ActiveRecord[]
     */
    public static function findAllByWorkerid($worker_id){
        $list=self::find()
            ->asArray()
            ->select([])
            ->where(['worker_id'=>$worker_id])
            ->all();
        if(!$list){
            return '';
        }
        return $list;
    }

}
