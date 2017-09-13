<?php

namespace app\models;


use app\services\StringService;
use yii\db\Query;


/**
 * This is the model class for table "worker_order_item".
 *
 * @property integer $id
 * @property integer $worker_order_id
 * @property integer $worker_item_id
 * @property integer $worker_craft_id
 * @property string $area
 * @property string $status
 * @property string $length
 * @property string $count
 * @property string $electricity
 *
 */
class WorkerOrderItem extends \yii\db\ActiveRecord
{

    const UNITS = [
        '无',
        'L',
        'M',
        'M^2',
        'Kg',
        'MM'
    ];
    const STATUSTNULL=0;
    const STATUSNOTNULL=1;
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
     * 对应工艺的价格
     * @param $craft_id
     * @return array|bool
     */
    public static function craftprice($craft_id){
        $price=(new Query())->from('craft_cost')
            ->select('price')
            ->where(['worker_craft_id'=>$craft_id])
            ->one();
        if($price){
            return $price;
        }
         return null;
    }
    /**
     * 泥作类添加
     * @param $array
     * @return array
     */
    public static function addMudorderitem(array $array){

        $data=[];
        $data['worker_type_id']=$array['worker_type_id'];
        //客厅
      if(isset($array['hall_id'])){
          if(!isset($array['hall_area']) ||!isset($array['hall_craft_id']) ){
              $code=1000;
              return $code;
          }else{
              $data['hall_item']['hall_item_id']=$array['hall_id'];
              $data['hall_item']['hall_craft_id']=$array['hall_craft_id'];
              $data['hall_item']['hall_area']=$array['hall_area'];
          }


      }
        //厨房
      if(isset($array['kitchen_id'])){
          if(!isset($array['kitchen_area']) ||!isset($array['kitchen_craft_id']) ){
              $code=1000;
              return $code;
          }else{
              $data['kitchen_item']['kitchen_item_id']=$array['kitchen_id'];
              $data['kitchen_item']['kitchen_craft_id']=$array['kitchen_craft_id'];
              $data['kitchen_item']['kitchen_area']=$array['kitchen_area'];
          }


      }
        //卫生间
        if(isset($array['toilet_id'])){
            if(!isset($array['toilet_area']) ||!isset($array['toilet_craft_id']) ){
                $code=1000;
                return $code;
            }else {
                $data['toilet_item']['toilet_item_id'] = $array['toilet_id'];
                $data['toilet_item']['toilet_craft_id'] = $array['toilet_craft_id'];
                $data['toilet_item']['toilet_area'] = $array['kitchen_area'];
            }
        }
        //阳台
        if(isset($array['balcony_id'])) {
            if (!isset($array['balcony_area']) || !isset($array['balcony_craft_id'])) {
                $code = 1000;
                return $code;
            } else {
                $data['balcony_item']['balcony_item_id'] = $array['balcony_id'];
                $data['balcony_item']['balcony_craft_id'] = $array['balcony_craft_id'];
                $data['balcony_item']['balcony_area'] = $array['balcony_area'];
            }
        }  //补烂
        if(isset($array['fill_id'])){
            if (!isset($array['fill_area'])) {
                $code = 1000;
                return $code;
            }else{
                $data['fill_item']['fill_item_id']=$array['fill_id'];
                $data['fill_item']['fill_area']=$array['fill_area'];
            }
        }
        //包管
        if(isset($array['guarantee_id'])){
            $data['guarantee_item']['guarantee_item_id']=$array['guarantee_id'];
            $data['guarantee_item']['guarantee_status']=$array['guarantee_status'];
        }
        if(isset($array['chip_id'])){
            $data['chip_item']['chip_item_id']=$array['chip_id'];
            $data['chip_item']['chip_status']=$array['chip_status'];
        }

        if(isset($array['demand'])){
            $data['demand']=$array['demand'];
        }
        if(isset($array['remark'])){
            $data['remark']=$array['remark'];
        }
        $data['images']=$array['images'];
        if(isset($array['start_time']) && isset($array['end_time'])){
            $data['start_time']=strtotime($array['start_time']);
            $data['end_time']=strtotime($array['end_time']);
        }
        return $data;
    }

    /**
     * 水电工 添加
     * @param array $array
     * @return array|int
     */
    public static function addhydropowerdata(array $array){

        $data=[];
        $data['worker_type_id']=$array['worker_type_id'];
        if(isset($array['demand'])){
            $data['demand']=$array['demand'];
        }
        if(isset($array['remark'])){
            $data['remark']=$array['remark'];
        }
        $data['images']=$array['images'];
        if(!isset($array['start_time'])){
            $code = 1000;
            return $code;
        }
        $data['start_time']=strtotime($array['start_time']);
        $data['end_time']=strtotime($array['end_time']);
        if(isset($array['slotting_id'])) {
            if (!isset($array['slotting_craft_id']) || !isset($array['slotting_length']) ||!isset($array['slotting_electricity'])) {
                $code = 1000;
                return $code;
            } else {
                $data['slotting_item']['slotting_item_id'] = $array['slotting_id'];
                $data['slotting_item']['slotting_craft_id'] = $array['slotting_craft_id'];
                $data['slotting_item']['slotting_length'] = $array['slotting_length'];
                $data['slotting_item']['slotting_electricity']=$array['slotting_electricity'];
            }
        }
        if(isset($array['point_id'])) {
            if (!isset($array['point_count']) || !isset($array['point_electricity'])) {
                $code = 1000;
                return $code;
            } else {
                $data['point_item']['point_item_id'] = $array['point_id'];
                $data['point_item']['point_electricity']=$array['point_electricity'];
                $data['point_item']['point_count'] = $array['point_count'];
            }
        }
        return $data;
    }
    /**
     * 业主联系信息
     * @param array $ownerinfos
     * @return int
     */
    public static function addownerinfo(array $ownerinfos){

      $code=1000;
      if(!$ownerinfos['con_people'] || mb_strlen($ownerinfos['con_people'])>self::PEOPL_LEN){
          return $code;
      }
        if(!$ownerinfos['con_tel'] || !StringService::isMobile($ownerinfos['con_tel'])){
        return $code;
       }
       if(mb_strlen($ownerinfos['map_location'])>self::ADDRESS_LEN){
           return $code;
       }

        if(!$ownerinfos['address']){
           return $code;
        }

        return $ownerinfos;
    }
    /**
     *
     * @param $id
     * @param $post
     * @return array|int|null
     */
    public static function getWorkeitem($id,$post){
        $type=WorkerType::getparenttype($id);
        if(!$type){
            return null;
        }
        switch ($type){
            case '泥工';
                $homeinfos=WorkerOrderItem::addMudorderitem($post['homeinfos']);
                break;
            case '水电工';
                $homeinfos=WorkerOrderItem::addhydropowerdata($post['homeinfos']);
                break;
            case '木工';
                $homeinfos=WorkerOrderItem::addcarpentrydata($post['homeinfos']);
                break;
            case '防水工';
                $homeinfos=WorkerOrderItem::addcarpentrydata($post['homeinfos']);
                break;
            case '油漆工';
                $homeinfos=WorkerOrderItem::addcarpentrydata($post['homeinfos']);
                break;
            case '杂工';
                $homeinfos=WorkerOrderItem::addcarpentrydata($post['homeinfos']);
                break;
        }
        if($homeinfos==1000){
            $code=$homeinfos;
            return $code;
        }
        return $homeinfos;
    }
}
