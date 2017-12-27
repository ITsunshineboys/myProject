<?php

namespace app\models;

use app\controllers\FindworkerController;
use app\controllers\QuoteController;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "worker_type".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $worker_type
 */
class WorkerType extends \yii\db\ActiveRecord
{
    const WORKER_TYPE='worker_type';
    const PARENT=0;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid'], 'integer'],
            [['worker_name'], 'string','max'=>20],
        ];
    }

    public static function findPidbyid($id){
        $data=self::find()
            ->select('id,worker_name,unit')
            ->where(['pid'=>$id])
            ->asArray()
            ->all();
        foreach ($data as &$v){
            $v['labor_cost_id']=$id;
            $v['quantity']='';
            $v['unit']=WorkerCraftNorm::UNIT[$v['unit']];
        }



        return $data;
    }
    /**
     * 根据父级工种找子级
     *@return string
     */
    public static function getworkertype($parents){

         foreach ($parents as $k=>$chlid){
            $data[$k]=self::find()->select('worker_name')->where(['pid'=>$chlid['id']])->asArray()->all();

        }
        return $data;
    }
    /**
     * 获取工种名称
     * @param $worker_type_id
     * @return mixed
     */
    public static function gettype($worker_type_id){
        return self::find()
            ->select('worker_name')
            ->asArray()
            ->where(['id'=>$worker_type_id])
            ->one()['worker_name'];
    }
    /**
     * @param $worker_type_id
     * @return mixed
     */
    public static function getparenttype($worker_type_id){
        return self::find()
            ->select('worker_name')
            ->asArray()
            ->where(['id'=>$worker_type_id,'status'=>1])
            ->andWhere(['pid'=>self::PARENT])
            ->one()['worker_name'];
    }

    public static function parent(){
       return self::find()->where(['pid'=>self::PARENT,'status'=>1])->asArray()->all();
    }

    public static function findByList()
    {
        $sql = 'SELECT `worker_type`.`worker_name`,`worker_type`.`establish_time`,`worker_type`.`status`,COUNT(worker_rank.rank_name) AS number ,COUNT(worker.id) AS quantity FROM `worker_type` LEFT JOIN `worker_rank` ON worker_type.id = worker_rank.worker_type_id LEFT JOIN `worker` ON worker.worker_type_id = worker_type.id GROUP BY worker_type.worker_name';
        $rows = Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($rows as &$one_row){
            $one_row['establish_time'] = date('Y-m-d H:i:s',$one_row['establish_time']);
        }

        return $rows;
    }

    public static function findByListOne($where)
    {
        return self::find()
            ->asArray()
            ->select(['count(worker.id)'])
            ->where($where)
            ->leftJoin('worker','worker.worker_type_id = worker_type.id')
            ->one();
    }

    public static function ByInsert($worker)
    {
        return Yii::$app->db
            ->createCommand()
            ->insert(self::tableName(),[
                'worker_name'=> $worker['worker_name'],
                'establish_time'=>time(),
                'status'     => self::PARENT,
            ])->execute();
    }

    public static function ByUpdate($worker)
    {
        return Yii::$app->db
            ->createCommand()
            ->update(self::tableName(),[
                'worker_name'=> $worker['worker_name'],
            ],['id' => $worker['id']])->execute();
    }
}
