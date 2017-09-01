<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "worker_order_item".
 *
 * @property integer $id
 * @property integer $worker_order_id
 * @property integer $worker_item_id
 * @property integer $worker_craft_id
 * @property string $area
 */
class WorkerOrderItem extends \yii\db\ActiveRecord
{
    const STATUS=[
        0=>'否',
        1=>'是'
    ];
    const UNITS = [
        '无',
        'L',
        'M',
        'M^2',
        'Kg',
        'MM'
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_order_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['worker_order_id', 'worker_item_id', 'worker_craft_id', 'area'], 'integer'],
        ];
    }
    /**
     * @param $item_id
     * @return array|null
     */
    public static function getpidbyitem($item_id){
        $worker_item=WorkerItem::find()
            ->select('pid,unit')
            ->asArray()
            ->where(['id'=>$item_id])
            ->one();

        return $worker_item;
    }

    /**
     * @param $array
     * @return array
     */
    public static function addMudorderitem($array){

        $data=[];
        $data['worker_type']=WorkerType::find()
            ->where(['id'=>$array['worker_type_id']])
            ->one()
            ->worker_type;

        //客厅
      if(isset($array['hall_area'])){
          $hall_item=(new Query())
              ->from('worker_craft')
              ->where(['id'=>$array['hall_craft_id']])
              ->select('item_id,univalent')
              ->one();
          $worker_item=self::getpidbyitem($hall_item['item_id']);
          $data['hall_id']=$worker_item['pid'];
          $data['hall_area']=$array['hall_area'];
          $data['hall_carft_univalent']=$hall_item['univalent'];


      }
        //厨房
      if(isset($array['kitchen_area'])){
          $kitchen_item=(new Query())
              ->from('worker_craft')
              ->where(['id'=>$array['kitchen_craft_id']])
              ->select('item_id,univalent')
              ->one();

          $worker_item=self::getpidbyitem($kitchen_item['item_id']);
          $data['kitchen_id']=$worker_item['pid'];
          $data['kitchen_carft_univalent']=$kitchen_item['univalent'];

          $data['kitchen_area']=$array['kitchen_area'];
      }
        //卫生间
        if(isset($array['toilet_area'])){
            $toilet_item=(new Query())
                ->from('worker_craft,univalent')
                ->where(['id'=>$array['toilet_craft_id']])
                ->select('item_id')
                ->one();
            $worker_item=self::getpidbyitem($toilet_item['item_id']);
            $data['toilet_carft_univalent']=$toilet_item['univalent'];
            $data['toilet_id']=$worker_item['pid'];
            $data['toilet_area']=$array['toilet_area'];
        }
        //阳台
        if(isset($array['balcony_area'])){
            $balcony_item=(new Query())
                ->from('worker_craft,univalent')
                ->where(['id'=>$array['balcony_craft_id']])
                ->select('item_id')
                ->one();
            $worker_item=self::getpidbyitem($balcony_item['item_id']);
            $data['balcony_carft_univalent']=$balcony_item['univalent'];
            $data['balcony_id']=$worker_item['pid'];
            $data['balcony_area']=$array['balcony_area'];
        }
        //补烂
        if(isset($array['fill_area'])){
            $fill_item=(new Query())
                ->from('worker_craft')
                ->where(['id'=>$array['fill_craft_id']])
                ->select('item_id,univalent')
                ->one();
            $worker_item=self::getpidbyitem($fill_item['item_id']);
            $data['fill_carft_univalent']=$fill_item['univalent'];
            $data['fill_id']=$worker_item['pid'];
            $data['fill_area']=$array['fill_area'];
        }
        //包管
        if(isset($array['guarantee'])){
            $data['guarantee']=self::STATUS[$array['guarantee']];
        }
        if(isset($array['chip'])){
            $data['chip']=self::STATUS[$array['chip']];
        }

        $keys=array_keys($array);
        $sum=0;
       foreach ($keys as $k=>&$key){
       if(preg_match('/[_]+[area]/',$key,$m)){
           $sum+=$array[$key];
         }

       }
       if(isset($array['demand'])){
            $data['demand']=$array['demand'];
        }
        if(isset($array['remark'])){
            $data['remark']=$array['remark'];
        }
        $need_time=ceil($sum/12+1);

       $data['need_time']=$need_time;
      if(isset($array['start_time']) && isset($array['end_time'])){
          $data['start_time']=$array['start_time'];
          $data['end_time']=$array['end_time'];
      }


        return $data;
    }

}
