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

    const PARENT=0;
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
            ->select('wi.title,wi.id')
            ->from('worker_item as wi')
            ->leftJoin('worker_type_item as wti','wi.id=wti.worker_item_id')
            ->where(['wti.worker_type_id'=>$worker_type_id])
            ->all();
        return $data;
    }


    /**
     * get craft by item_id
     * @param $item_id
     * @return array|null
     */
    public static  function getcraft($item_id){
        $kt=self::find()
            ->where(['id'=>$item_id])
            ->andWhere(['pid'=>self::PARENT])
            ->one();
        if(!$kt){
            return null;
        }
        $array = (new Query())
            ->from('worker_item as wi')
            ->select('wc.craft,wc.id')
            ->leftJoin('worker_craft as wc','wi.id=wc.item_id')
            ->where(['wi.pid' => $kt->id])
            ->andWhere('wi.id=wc.item_id')
            ->all();
        if($array){
            return $array;
        }
   return null;
    }
    /**
     * get chlid item by item_id
     * @param $item_id
     * @return array|null
     */

    public static function getchliditem($item_id)
    {

        $parent=self::find()
            ->where(['id'=>$item_id])
            ->one();
        if(!$parent){
            return null;
        }
            $data = self::find()
                ->select('title,id')
                ->where(['pid' => $item_id])
                ->asArray()
                ->all();

        return $data;

    }
    /**
     * @param $pid
     * @return array|null
     */
    public static function parenttitle($pid){
      return  self::find()
            ->select('id,title')
            ->where(['id'=>$pid])
            ->one();
    }
}
