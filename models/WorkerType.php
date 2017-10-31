<?php

namespace app\models;

use app\controllers\FindworkerController;
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
            [['worker_type'], 'string', 'max' => 20],
            [['image'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => '所属上级工种id',
            'worker_type' => '工种名字',
            'image' => '服务工种图片',
        ];
    }

    /**
     * 根据父级工种找子级
     *@return string
     */
    public static function getworkertype($parents){

         foreach ($parents as $k=>$chlid){
            $data[$k]=self::find()->select('worker_type')->where(['pid'=>$chlid['id']])->asArray()->all();

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
            ->select('worker_type')
            ->asArray()
            ->where(['id'=>$worker_type_id])
            ->one()['worker_type'];
    }
    /**
     * @param $worker_type_id
     * @return mixed
     */
    public static function getparenttype($worker_type_id){
        return self::find()
            ->select('worker_type')
            ->asArray()
            ->where(['id'=>$worker_type_id])
            ->andWhere(['pid'=>self::PARENT])
            ->one()['worker_type'];
    }

    public static function parent(){
       return self::find()->where(['pid'=>self::PARENT])->asArray()->all();
    }

    public static function findByList()
    {
        $sql = 'SELECT worker_type.worker_name,worker_type.establish_time,COUNT(worker_type.rank_name) as rank_name_value,COUNT(worker.id) as worker_value FROM worker_type LEFT JOIN worker ON worker.worker_type_id = worker_type.id WHERE worker_type.pid = 0 GROUP BY worker_type.worker_name';
        return Yii::$app->db
            ->createCommand($sql)
            ->queryAll();
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
}
