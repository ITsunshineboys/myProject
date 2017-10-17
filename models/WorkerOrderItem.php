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
            [['worker_order_id', 'worker_item_id', 'worker_craft_id', 'area', 'status', 'electricity', 'count'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'worker_order_id' => '工单id',
            'worker_item_id' => '工人条目id',
            'worker_craft_id' => '工艺id',
            'area' => '面积,单位: dm^2',
            'status' => '0: 否，1：是',
            'electricity' => '（水电：0:弱电,1:强电）',
            'count' => '(水电：个数)',
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
     * 泥作类 展示
     * @param $array
     * @return array
     */
    public static function addMudorderitem(array $array){

        $data=[];
        //客厅
      if(isset($array['hall_id'])){
          if($array['hall_area']>200){
              $code=1000;
              return $code;
          }
              $data['hall_item']['id']=$array['hall_id'];
              $data['hall_item']['hall_item_name']=WorkerItem::findtitlebyId($array['hall_id']);
              $data['hall_item']['hall_craft']=WorkerCraft::getcraftitle($array['hall_craft_id'])['craft'];
              $data['hall_item']['hall_area']=$array['hall_area'];
          }
        //厨房
      if(isset($array['kitchen_id'])){
          if($array['kitchen_area']>200){
              $code=1000;
              return $code;
          }
              $data['kitchen_item']['id']=$array['kitchen_id'];
              $data['kitchen_item']['kitchen_item_name']=WorkerItem::findtitlebyId($array['kitchen_id']);
              $data['kitchen_item']['kitchen_craft']=WorkerCraft::getcraftitle($array['kitchen_craft_id'])['craft'];
              $data['kitchen_item']['kitchen_area']=$array['kitchen_area'];
          }
        //卫生间
        if(isset($array['toilet_id'])){
            if($array['toilet_area']>200){
                $code=1000;
                return $code;
            }
                $data['toilet_item']['id']=$array['toilet_id'];
                $data['toilet_item']['toilet_item_name'] = WorkerItem::findtitlebyId($array['toilet_id']);
                $data['toilet_item']['toilet_craft'] = WorkerCraft::getcraftitle($array['toilet_craft_id'])['craft'];
                $data['toilet_item']['toilet_area'] = $array['kitchen_area'];

        }
        //阳台
        if(isset($array['balcony_id'])) {
            if($array['balcony_area']>200){
                $code=1000;
                return $code;
            }
                $data['balcony_item']['id']=$array['balcony_id'];
                $data['balcony_item']['balcony_item_name'] =  WorkerItem::findtitlebyId($array['balcony_id']);
                $data['balcony_item']['balcony_craft'] =WorkerCraft::getcraftitle($array['balcony_craft_id'])['craft'];
                $data['balcony_item']['balcony_area'] = $array['balcony_area'];

        }  //补烂
        if(isset($array['fill_id'])){
            if($array['fill_area']>200){
                $code=1000;
                return $code;
            }
                $data['fill_item']['id']=$array['fill_id'];
                $data['fill_item']['fill_item_name']=WorkerItem::findtitlebyId($array['fill_id']);
                $data['fill_item']['fill_area']=$array['fill_area'];
            }
        //包管
        if(isset($array['guarantee_id'])){
            $data['guarantee_item']['id']=$array['guarantee_id'];
            $data['guarantee_item']['guarantee_item_name']=WorkerItem::findtitlebyId($array['guarantee_id']);
            $data['guarantee_item']['guarantee_status']=$array['guarantee_status']?'是':'否';
        }
        if(isset($array['chip_id'])){
            $data['chip_item']['id']=$array['chip_id'];
            $data['chip_item']['chip_item_id']=$array['chip_id'];
            $data['chip_item']['chip_status']=$array['chip_status']?'是':'否';
        }

        if(isset($array['demand'])){
            $data['demand']=$array['demand'];
        }
        if(isset($array['remark'])){
            $data['remark']=$array['remark'];
        }
        $data['images']=$array['images'];
        $data['need_time']=$array['need_time'];
        if(isset($array['start_time'])){
            $data['start_time']=$array['start_time'];
            $data['end_time']=$array['end_time'];
        }else{
            $code=1000;
            return $code;
        }
        return $data;
    }

    /**
     * 水电工 用户下单
     * @param array $array
     * @return array|int
     */
    public static function addhydropowerdata(array $array){
        $data=[];
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
        $data['need_time']=$array['need_time'];
        $data['start_time']=$array['start_time'];
        $data['end_time']=$array['end_time'];
    //开槽
        if(isset($array['slotting'])) {
                $data['slotting_item']['slotting_id'] = $array['slotting']['slotting_id'];
                $data['slotting_item']['slotting_item_name'] = WorkerItem::findtitlebyId($array['slotting']['slotting_id']);
                unset($array['slotting']['slotting_id']);
                foreach ($array['slotting'] as $k=>&$slotting){
                    if($slotting['slotting_length'] && $slotting['slotting_length']>200){
                        return 1000;
                    }
                    $data['slotting_item'][$k]['slotting_craft_id'] = WorkerCraft::getcraftitle($slotting['slotting_craft_id'])['craft'];
                $data['slotting_item'][$k]['slotting_length'] =$slotting['slotting_length'];
                $data['slotting_item'][$k]['slotting_electricity'] = $slotting['slotting_electricity']?'强电':'弱电';
                }
        }
        //点位
        if(isset($array['point'])) {
            $data['point_item']['point_id'] = $array['point']['point_id'];
            $data['point_item']['point_item_name'] = WorkerItem::findtitlebyId($array['point']['point_id']);
            unset($array['point']['point_id']);
            foreach ($array['point'] as $k => &$point) {
                if ($point['point_count'] && $point['point_count'] > 200) {
                    return 1000;
                }
                $data['point_item'][$k]['point_count'] = $point['point_count'];
                $data['point_item'][$k]['point_electricity'] = $point['point_electricity'] ? '强电' : '弱电';
            }
        }
        //线路
//        if(isset($array[''])) {
//            $data['point_item']['point_id'] = $array['point']['point_id'];
//            $data['point_item']['point_item_name'] = WorkerItem::findtitlebyId($array['point']['point_id']);
//            unset($array['point']['point_id']);
//            foreach ($array['point'] as $k => &$point) {
//                if ($point['point_count'] && $point['point_count'] > 200) {
//                    return 1000;
//                }
//                $data['point_item'][$k]['point_count'] = $point['point_count'];
//                $data['point_item'][$k]['point_electricity'] = $point['point_electricity'] ? '强电' : '弱电';
//            }
//        }

        return $data;
    }
    /**
     * 防水工 添加
     * @param array $array
     */
    public static function addwaterproofdata(array $array){
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
        if(isset($array['indoor_id'])){
            if (!isset($array['indoor_craft_id']) || !isset($array['indoor_area'])) {
                $code = 1000;
                return $code;
            }else {
                $data['indoor_item']['indoor_item_id'] = $array['indoor_id'];
                $data['indoor_item']['indoor_craft_id'] = $array['indoor_craft_id'];
                $data['indoor_item']['indoor_area'] = $array['indoor_area'];

            }
        }
        if(isset($array['outdoor_id'])){
            if (!isset($array['outdoor_craft_id']) || !isset($array['outdoor_area'])) {
                $code = 1000;
                return $code;
            }else {
                $data['outdoor_item']['outdoor_item_id'] = $array['outdoor_id'];
                $data['outdoor_item']['outdoor_craft_id'] = $array['outdoor_craft_id'];
                $data['outdoor_item']['outdoor_area'] = $array['outdoor_area'];

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
//    /**
//     * 订单具体项目
//     * @param $order_id
//     */
//    public static function OrderItem($order_id){
//        $data=self::find()
//            ->asArray()
//            ->select('worker_item_id')
//            ->where(['worker_order_id'=>$order_id])
//            ->all();
//        var_dump($data);exit;
//    }
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
                $homeinfos=WorkerOrderItem::addMudorderitem($post);
                break;
            case '水电工';
                $homeinfos=WorkerOrderItem::addhydropowerdata($post);
                break;
            case '木工';
                $homeinfos=WorkerOrderItem::addcarpentrydata($post);
                break;
            case '防水工';
                $homeinfos=WorkerOrderItem::addwaterproofdata($post);
                break;
            case '油漆工';
                $homeinfos=WorkerOrderItem::addcarpentrydata($post);
                break;
            case '杂工';
                $homeinfos=WorkerOrderItem::addcarpentrydata($post);
                break;
        }
        if($homeinfos==1000){
            $code=$homeinfos;
            return $code;
        }
        return $homeinfos;
    }
    /**
     * 订单某条目详情
     * @param $order_id
     * @param $item_id
     * @return array|int
     */
    public static function getorderitemview($order_id,$item_id){
        $data=[];
        $items=self::find()
            ->asArray()
            ->where(['worker_order_id'=>$order_id])
            ->andWhere(['worker_item_id'=>$item_id])
            ->one();
        if(!$items){
            $code=1000;
            return $code;
        }
        $carft_info=WorkerCraft::getcraftitle($items['worker_craft_id']);
        if($carft_info){
            $unit=WorkerItem::find()->select('unit')
                ->asArray()
                ->where(['id'=>$carft_info['item_id']])
                ->one();
            $data['craft']=$carft_info['craft'].self::UNITS[$unit['unit']];
            $data['price']=self::craftprice($carft_info['id'])['price'].self::UNITXG.self::UNITS[3];
        }
        if($items['area']){
            $data['area']=$items['area'].self::UNITS['3'];
        }
        if($items['length']){
            $data['length']=$items['length'].self::UNITS['2'];
        }
       return $data;



    }
}
