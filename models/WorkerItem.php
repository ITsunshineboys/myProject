<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "worker_item".
 *
 * @property integer $id
 * @property string $title
 * @property integer $unit
 * @property integer $pid
 */
class WorkerItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['unit', 'pid'], 'integer'],
            [['title'], 'string', 'max' => 25],
        ];
    }
    /**
     * 获取该工种所负责的地方
     * @param $worker_type_id
     * @return array
     */
    public static function getparent($worker_type_id){
        $data=(new Query())
            ->select('title')
            ->from('worker_item as wi')
            ->leftJoin('worker_type_item as wti','wi.id=wti.worker_item_id')
            ->where(['wti.worker_type_id'=>$worker_type_id])
            ->all();
        return $data;
    }



//    public static  function getktcraft($title){
//        $kt=self::find()->where(['title'=>$title])->one();
//
//        $array = (new Query())
//            ->from('worker_item as wi')
//            ->select('wc.craft')
//            ->leftJoin('worker_craft as wc','wi.id=wc.item_id')
//            ->where(['wi.pid' => $kt->id])
//            ->all();
//        if($array){
//            return $array;
//        }
//   return null;
//    }

}
