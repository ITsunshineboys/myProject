<?php

namespace app\models;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "work_result".
 *
 * @property integer $id
 * @property string $order_no
 * @property string $work_des
 * @property integer $create_time
 */
class WorkResult extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'work_result';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time'], 'integer'],
            [['work_des'], 'string', 'max' => 100],
        ];
    }
    public static function Insetworks($works_des,$images,$order_no){
        $worker_result=new self();
        $worker_result->order_no=$order_no;
        $worker_result->work_des=$works_des;
        $worker_result->create_time=time();
        $tran=Yii::$app->db->beginTransaction();
        try{
          if(!$worker_result->validate()){
              $tran->rollBack();
              $code=1000;
              return $code;
          }
          if(!$worker_result->save()){
              $tran->rollBack();
              $code=500;
              return $code;
          }
         $id=Yii::$app->db->lastInsertID;

          foreach ($images as &$v){

              $res=Yii::$app->db->createCommand()->Insert(
                  'work_result_img',
                  [
                      'result_img'=>$v['path'],
                      'work_result_id'=>$id,
                      'result_img_name'=>$v['name']
                  ]
              )->execute();
          }
          if(!$res){
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

}
