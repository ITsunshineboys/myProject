<?php

namespace app\models;

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


}
