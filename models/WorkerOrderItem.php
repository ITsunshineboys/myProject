<?php

namespace app\models;

use app\services\FileService;
use app\services\StringService;
use Yii;
use yii\db\Query;
use yii\web\UploadedFile;

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
    const UNITXG='/';
    const PEOPL_LEN=15;
    const ADDRESS_LEN=45;
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
     * 泥作类添加
     * @param $array
     * @return array
     */
    public static function addMudorderitem(array $array,$need_time){

        $data=[];
        $data['worker_type_id']=$array['worker_type_id'];
        //客厅
      if(isset($array['hall_area'])){
          $hall_item=(new Query())
              ->from('worker_craft')
              ->where(['id'=>$array['hall_craft_id']])
              ->select('*')
              ->one();
          $worker_item=self::getpidbyitem($hall_item['item_id']);

          $data['hall_item']['hall_item_id']=WorkerItem::parenttitle($worker_item['pid'])['id'];
          $data['hall_item']['hall_craft_id']=$array['hall_craft_id'];
          $data['hall_item']['hall_area']=$array['hall_area'];
      }
        //厨房
      if(isset($array['kitchen_area'])){
          $kitchen_item=(new Query())
              ->from('worker_craft')
              ->where(['id'=>$array['kitchen_craft_id']])
              ->select('*')
              ->one();

          $worker_item=self::getpidbyitem($kitchen_item['item_id']);
          $data['kitchen_item']['kitchen_item_id']=WorkerItem::parenttitle($worker_item['pid'])['id'];
          $data['kitchen_item']['kitchen_craft_id']=$array['kitchen_craft_id'];
          $data['kitchen_item']['kitchen_area']=$array['kitchen_area'];
      }
        //卫生间
        if(isset($array['toilet_area'])){
            $toilet_item=(new Query())
                ->from('worker_craft')
                ->where(['id'=>$array['toilet_craft_id']])
                ->select('item_id')
                ->one();
            $worker_item=self::getpidbyitem($toilet_item['item_id']);

            $data['toilet_item']['toilet_item_id']=WorkerItem::parenttitle($worker_item['pid'])['id'];

            $data['toilet_item']['toilet_craft_id']=$array['toilet_craft_id'];
            $data['toilet_item']['toilet_area']=$array['kitchen_area'];
        }
        //阳台
        if(isset($array[' ']['balcony_area'])){
            $balcony_item=(new Query())
                ->from('worker_craft')
                ->where(['id'=>$array['balcony_craft_id']])
                ->select('item_id')
                ->one();
            $worker_item=self::getpidbyitem($balcony_item['item_id']);
            $data['balcony_item']['balcony_item_id']=WorkerItem::parenttitle($worker_item['pid'])['id'];
            $data['balcony_item']['balcony_craft_id']=$array['balcony_craft_id'];
            $data['balcony_item']['balcony_area']=$array['balcony_area'];
        }
        //补烂
        if(isset($array['fill_area'])){
            $data['fill_item']['fill_item_id']=WorkerItem::parenttitle($worker_item['pid'])['id'];
            $data['fill_item']['fill_craft_id']=0;
            $data['fill_item']['fill_area']=$array['fill_area'];
        }
        //包管
        if(isset($array['guarantee'])){
            $data['guarantee_item']['guarantee']=self::STATUS[$array['guarantee']];
        }
        if(isset($array['chip'])){
            $data['chip_item']['chip']=self::STATUS[$array['chip']];
        }

        $data['need_time']=$need_time;
       if(isset($array['demand'])){
            $data['demand']=$array['demand'];
        }
        if(isset($array['remark'])){
            $data['remark']=$array['remark'];
        }

      if(isset($array['start_time']) && isset($array['end_time'])){
          $data['start_time']=strtotime($array['start_time']);
          $data['end_time']=strtotime($array['end_time']);
      }
        return $data;
    }
    /**
     * @param $con_people
     * @param $con_tel
     * @param $address
     * @param $map_location
     * @return int
     */
    public static function addownerinfo($con_people,$con_tel,$address,$map_location){
      $code=1000;
      if(!$con_people || mb_strlen($con_people)>self::PEOPL_LEN){
          return $code;
      }
        if(!$con_tel || !StringService::isMobile($con_tel)){
        return $code;
       }
       if(mb_strlen($map_location)>self::ADDRESS_LEN){
           return $code;
       }

        if(!$address){
           return $code;
        }

        return 200;
    }

//    public static function indexinfos(array $infos){
//        $data=[];
//
//        $data['worker_type']=WorkerType::find()
//            ->select('worker_type')
//            ->where(['id'=>$infos['worker_type_id']])
//            ->one()['worker_type'];
//
//        $keys=array_keys($infos);
//
//        foreach ($keys as $k=>&$key){
//            if(preg_match('/[_]+[craft]+[_]+[id]/',$key,$m)){
//                $data['craft']=WorkerCraft::find()
//                    ->where(['id'=>$infos[$key]])
//                    ->select('craft')
//                    ->one()['craft'];
//
//
//            }
//            if(preg_match('/[_]+[area]/',$key,$m)){
//                $data['area']= $infos[$key].self::UNITS[3];
//            }
//
//         }
//         $data['start_time']=date('Y-m-d',$infos['start_time']);
//         $data['end_time']=date('Y-m-d',$infos['end_time']);
//
//        var_dump($data);
//    }

}
