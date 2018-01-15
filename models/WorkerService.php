<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "worker_service".
 *
 * @property string $id
 * @property string $service_name
 * @property integer $pid
 * @property string $service_image
 * @property integer $create_time
 * @property integer $status
 */
class WorkerService extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_service';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_name', 'pid', 'service_image', 'create_time'], 'required'],
            [['pid', 'create_time', 'status'], 'integer'],
            [['service_name'], 'string', 'max' => 100],
            [['service_image'], 'string', 'max' => 255],
        ];
    }


    /**
     * @param $worker_type_id
     * @return mixed
     */
    public static function getparenttype($worker_type_id){
        return self::find()
            ->select('service_name')
            ->asArray()
            ->where(['id'=>$worker_type_id,'status'=>1])
            ->andWhere(['pid'=>0])
            ->one()['service_name'];
    }

    public static function parent(){
        return self::find()->where(['pid'=>0,'status'=>1])->asArray()->all();
    }

    /**
     * 根据父级工种找子级
     *@return string
     */
    public static function getworkertype($parents){

        foreach ($parents as $k=>$chlid){
            $data[$k]=self::find()->select('service_name')->where(['pid'=>$chlid['id']])->asArray()->all();

        }
        return $data;
    }
}
