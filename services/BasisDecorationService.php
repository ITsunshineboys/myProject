<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/6 0006
 * Time: 下午 14:42
 */
namespace app\services;

use app\models\Effect;
use app\models\EngineeringStandardCarpentryCoefficient;
use app\models\EngineeringStandardCraft;
use app\models\GoodsAttr;
use app\models\ProjectView;
use yii\helpers\Json;

class BasisDecorationService
{
    const WALL_SPACE = 4;
    const BRICK_UNITS = 1000;
    const GOODS_PRICE_UNITS = 100;

    const DEFAULT_VALUE = [
      'value1' => 0,
      'value2' => 1,
    ];

    /**
     * goods name
     */
    const GOODS_NAME =[
        'reticle'=>'网线',
        'wire'=>'电线',
        'spool'=>'线管',
        'bottom_case'=>'底盒',
        'pvc'=>'PVC管',
        'ppr'=>'PPR水管',
        'waterproof_coating'=>'防水涂料',
        'plasterboard'=>'石膏板',
        'keel'=>'龙骨',
        'lead_screw'=>'丝杆',
        'concave_line'=>'阴角线',
        'lamp'=>'灯具',
        'curtains'=>'窗帘',
        'river_sand'=>'河沙',
        'cement'=>'水泥',
        'self_leveling'=>'自流平',
        'putty'=>'腻子',
        'emulsion_varnish_primer'=>'乳胶漆底漆',
        'emulsion_varnish_surface'=>'乳胶漆面漆',
        'land_plaster'=>'石膏粉',
        'closet'=>'衣柜',
        'wood_floor'=>'木地板',
        'aluminium_alloy'=>'铝合金门窗',
        'bath_heater'=>'浴霸',
        'ventilator'=>'换气扇',
        'ceiling_light'=>'吸顶灯',
        'tap'=>'水龙头',
        'marble'=>'人造大理石',
        'sofa'=>'沙发',
        'bed'=>'床',
        'night_table'=>'床头柜',
        'kitchen_ventilator'=>'抽油烟机',
        'stove'=>'灶具',
        'upright_air_conditioner'=>'立柜式空调',
        'hang_air_conditioner'=>'挂壁式空调',
        'central_air_conditioner'=>'中央空调',
        'mattress'=>'床垫',
        'shower_partition'=>'淋浴隔断',
        'sprinkler'=>'花洒套装',
        'bath_cabinet'=>'浴柜',
        'closestool'=>'马桶',
        'squatting_pan'=>'蹲便器',
        'elbow'=>'弯头',
        'tiling'=>'贴砖',
        'wall_brick'=>'墙砖',
        'floor_tile'=>'地砖',
        'air_brick'=>'空心砖',
        'stairs'=>'楼梯',
        'timber_door' => '木门',
    ];

    /**
     * house message
     */
    const HOUSE_MESSAGE = [
        'kitchen_area'=>'厨房面积',
        'toilet_area' =>'卫生间面积',
        'bedroom_area' =>'卧室面积',
        'hall_area' =>'客厅面积',
        'kitchen_waterproof' =>'厨房防水',
        'toilet_waterproof' =>'卫生间防水',
        'modelling_length' =>'造型长度石膏板',
        'flat_area' =>'平顶面积石膏板',
        'bedroom' =>'卧室',
        'kitchen' =>'厨房',
        'toilet' =>'卫生间',
        'household' =>'入户',
        'drawing_room_balcony' =>'客厅阳台',
        'passage' =>'客厅卧室过道',
        'live_balcony' =>'生活阳台',
        'guest_restaurant' =>'客餐厅',
        'hall' =>'客厅',

    ];

    /**
     * units
     */
    const UNITS =[
        'length' =>'长度',
        'breadth' =>'宽度',
        'texture' =>'材质',
        'long' =>'长',
        'wide' =>'宽',
        'high' =>'高',
        'area' =>'面积',
    ];

    /**
     * backman details
     */
    const BACKMAN_DETAILS =[
        'dismantle_12_area'=>'拆除12墙面积',
        'dismantle_24_area'=>'拆除24墙面积',
        'new_12_area'=>'新建12墙面积',
        'new_24_area'=>'新建24墙面积',
        'repair_length'=>'补烂长度',
        'clear_12'=>'清运12墙',
        'clear_24'=>'清运24墙',
        'vehicle_12_area'=>'运渣车12墙面积',
        'vehicle_24_area'=>'运渣车24墙面积',
        'vehicle_cost'=>'运渣车费用',
        '12_cement_dosage'=>'12墙水泥用量',
        '24_cement_dosage'=>'24墙水泥用量',
        'repair_cement_dosage'=>'补烂水泥用量',
        '12_river_sand_dosage'=>'12墙河沙用量',
        '24_river_sand_dosage'=>'24墙河沙用量',
        'repair_river_sand_dosage'=>'补烂河沙用量',
    ];

    const CARPENTRY_DETAILS =[
      'keel_sculpt'=>'龙骨做造型长度',
      'screw_rod_sculpt'=>'丝杆做造型长度',
      'plasterboard_sculpt'=>'石膏板造型长度',
      'plasterboard_area'=>'石膏板平顶面积',
      'tv_day'=>'电视墙需要天数',
      'tv_plasterboard'=>'电视墙所需石膏板',
      'keel_area'=>'龙骨做平顶面积',
      'screw_rod_area'=>'丝杆做平顶面积',
    ];

    /**
     *   防水  水路  强电  弱电 人工费
     * @param string $points
     * @param array $labor
     * @return float
     *
     */
    public static function laborFormula($points,$day_points)
    {
        $p  = !empty($points)    ? $points    : self::DEFAULT_VALUE['value1'];
//        $l  = !empty($labor)     ? $labor     : self::DEFAULT_VALUE['value2'];
        $d  = !empty($day_points)? $day_points: self::DEFAULT_VALUE['value1'];
        //人工费：（电路总点位÷【每天做工点位】）×【工人每天费用】
        return ($p / $d);
    }


    public static function P($points,$day_points,$labor)
    {
        $p  = !empty($points)    ? $points    : self::DEFAULT_VALUE['value1'];
        $l  = !empty($labor)     ? $labor     : self::DEFAULT_VALUE['value2'];
        $d  = !empty($day_points)? $day_points: self::DEFAULT_VALUE['value1'];
        //人工费：（电路总点位÷【每天做工点位】）×【工人每天费用】
        return ceil(($p / $d)) * $l;
    }

    /**
     * 电线计算公式
     * @param string $points
     * @param array $goods
     * @param string $crafts
     * @return mixed
     */
    public static function quantity($points,$goods,$crafts)
    {
        foreach ($crafts as $craft) {
            switch ($craft) {
                case $craft['project_details'] == self::GOODS_NAME['reticle'] || $craft['project_details'] == self::GOODS_NAME['wire']:
                    $material = $craft['material'];
                    break;
                case $craft['project_details'] == self::GOODS_NAME['spool']:
                    $spool = $craft['material'];
                    break;
            }
        }



        $goods_id = [];
        foreach ($goods as $one) {
            switch ($one) {
                case $one['title'] == self::GOODS_NAME['reticle'] || $one['title'] == self::GOODS_NAME['wire']:
                    $goods_price = $one['platform_price'];
                    $goods_procurement = $one['purchase_price_decoration_company'];
                    $goods_id [] = $one['id'];
                    break;
                case $one['title'] == self::GOODS_NAME['spool']:
                    $spool_price = $one['platform_price'];
                    $spool_procurement = $one['purchase_price_decoration_company'];
                    $goods_id [] = $one['id'];
                    break;
                case $one['title'] == self::GOODS_NAME['bottom_case']:
                    $bottom_case = $one['platform_price'];
                    $bottom_procurement = $one['purchase_price_decoration_company'];
                    $goods_id [] = $one['id'];
                    break;
            }
        }
        $ids = GoodsAttr::findByGoodsIdUnit($goods_id);
        if ($ids == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }
        foreach ($ids as $one_unit) {
            switch ($one_unit) {
                case $one_unit['title'] == self::GOODS_NAME['reticle'] || $one_unit['title'] == self::GOODS_NAME['wire']:
                    $goods_value = $one_unit['value'];
                    break;
                case $one_unit['title'] == self::GOODS_NAME['spool']:
                    $spool_value = $one_unit['value'];
                    break;
            }
        }

        //线路个数计算 ,线路费用计算
        $electricity['wire_quantity'] = ceil($points * $material / $goods_value);
        $electricity['wire_cost'] = round($electricity['wire_quantity'] * $goods_price,2);
        $electricity['wire_procurement'] = round($electricity['wire_quantity'] * $goods_procurement,2);

        //线管个数计算,线管费用计算
        $electricity['spool_quantity'] = ceil($points * $spool / $spool_value);
        $electricity['spool_cost'] =  round($electricity['spool_quantity'] * $spool_price,2);
        $electricity['spool_procurement'] =  round($electricity['spool_quantity'] * $spool_procurement,2);

        // 底盒个数计算.底盒费用计算
        $electricity['bottom_quantity'] = $points;
        $electricity['bottom_cost'] = round($points * $bottom_case,2);
        $electricity['bottom_procurement'] = round($points * $bottom_procurement,2);

        //总费用
        $electricity['total_cost'] = $electricity['wire_cost'] + $electricity['spool_cost'] + $electricity['bottom_cost'];
     return  $electricity;
    }

    /**
     * 水路商品
     * @param string $points
     * @param array $goods
     * @param string $crafts
     * @return float
     */
    public static function waterwayGoods($points,$goods,$crafts)
    {
        foreach ($goods as $one) {
            switch ($one) {
                case $one['title'] == self::GOODS_NAME['pvc']:
                    $pvc_price = $one['platform_price'];
                    $pvc_procurement = $one['purchase_price_decoration_company'];
                    $goods_id [] = $one['id'];
                    break;
                case $one['title'] == self::GOODS_NAME['ppr']:
                    $ppr_price = $one['platform_price'];
                    $ppr_procurement = $one['purchase_price_decoration_company'];
                    $goods_id [] = $one['id'];
                    break;
            }
        }

        $ids = GoodsAttr::findByGoodsIdUnit($goods_id);
        if ($goods == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }

        foreach ($ids as $one_unit) {
            switch ($one_unit) {
                case $one_unit['title'] == self::GOODS_NAME['ppr']:
                    $ppr_value = $one_unit['value'];
                    break;
                case $one_unit['title'] == self::GOODS_NAME['pvc']:
                    $pvc_value = $one_unit['value'];
                    break;
            }
        }

        foreach ($crafts as $craft) {
            switch ($craft) {
                case $craft['project_details'] == self::GOODS_NAME['ppr']:
                    $ppr = $craft['material'];
                    break;
                case $craft['project_details'] == self::GOODS_NAME['pvc']:
                    $pvc =  $craft['material'];
                    break;
            }
        }


//           PPR费用：个数×抓取的商品价格
//           个数：（水路总点位×【2m】÷抓取的商品的长度）
//           PVC费用：个数×抓取的商品价格
//           个数：（水路总点位×【2m】÷抓取的商品的长度）
        $waterway['ppr_quantity'] = ceil($points * $ppr / $ppr_value);
        $waterway['pvc_quantity'] = ceil($points * $pvc / $pvc_value);

        $waterway['ppr_cost'] = round($waterway['ppr_quantity'] * $ppr_price,2);
        $waterway['pvc_cost'] = round($waterway['pvc_quantity'] * $pvc_price,2);

        $waterway['ppr_procurement'] = round($waterway['pvc_quantity'] * $ppr_procurement,2);
        $waterway['pvc_procurement'] = round($waterway['pvc_quantity'] * $pvc_procurement,2);

        $waterway['total_cost'] =  $waterway['ppr_cost'] + $waterway['pvc_cost'];
        return $waterway;
    }

    /**
     * 防水面积计算
     * @param array $arr
     * @param string $house_area
     * @param int $quantity
     * @return float
     */
    public static  function waterproofArea($area,$height,$house_area,$quantity = 1)
    {

//            厨房地面面积：【x】%×（房屋面积)
        $ground = $area * $house_area;
//            厨房墙面积：（厨房地面积÷厨房个数）开平方×【0.3m】×4 ×厨房个数
        $sqrt = sqrt($ground / $quantity);
        $wall_space = $sqrt * $height * self::WALL_SPACE * $quantity;
//            厨房防水面积：厨房地面积+厨房墙面积
        $all_area = $ground + $wall_space;
        $total_area = round($all_area,2);

        return $total_area;

    }

    /**
     * 防水商品
     * @param string $points
     * @param array $goods
     * @param string $crafts
     * @return float
     */
    public static function waterproofGoods($points,$goods,$crafts)
    {
        foreach ($crafts as $craft) {
            $material = $craft['material'];
        }

        if (count($goods) == count($goods, 1)) {
            $goods_platform_price = $goods['platform_price'];
            $goods_price = $goods['purchase_price_decoration_company'];
            $goods_id [] = $goods['id'];
        } else {
            foreach ($goods as $one) {
                $goods_platform_price = $one['platform_price'];
                $goods_price = $one['purchase_price_decoration_company'];
                $goods_id [] = $one['id'];
            }
        }

        $ids = GoodsAttr::findByGoodsIdUnit($goods_id);
        foreach ($ids as $one_unit) {
            if ($one_unit['title'] == self::GOODS_NAME['waterproof_coating']) {
                $goods_value = $one_unit['value'];
            }
        }

//            个数：（防水总面积×【1.25】÷抓取的商品的KG）
        $waterproof['quantity'] = ceil($points * $material /$goods_value);
//            防水涂剂费用：个数×抓取的商品价格
        $waterproof['cost'] =  round($waterproof['quantity'] * $goods_platform_price,2);
        $waterproof['procurement'] =  round($waterproof['quantity'] * $goods_price,2);
        return $waterproof;
    }

    /**
     * 地面面积计算公式
     * @param array $arr
     * @return int|mixed
     */
    public static function groundArea($arr)
    {
        $all_area = [];
        //总面积
        $all_area ['hostToilet_area'] =  $arr ['hostToilet_area'];
        $all_area ['kitchen_area'] =  $arr ['kitchen_area'];
        $all_area ['toilet_balcony_area'] =  $arr ['toilet_balcony_area'];
        $all_area ['kitchen_balcony_area'] =  $arr ['kitchen_balcony_area'];
        $area = 0;
        foreach ($all_area as $v=>$k) {
            $area += $k;
        }
        return $area;
    }

    /**
     * 墙面空间计算
     * @param array $arr
     * @return int|mixed
     */
    public static function wallSpace($arr)
    {
        $all_area = [];
        $toilet_wall_space_high = '1.8';
        $kitchen_wall_space_high = '0.3';

        //总周长
        $all_area ['toilet_perimeter'] =  $arr ['toilet_perimeter'];
        $all_area ['kitchen_perimeter'] =  $arr ['kitchen_perimeter'];
        $all_area ['toilet_balcony_perimeter'] =  $arr ['toilet_balcony_perimeter'];
        $all_area ['kitchen_balcony_perimeter'] =  $arr ['kitchen_balcony_perimeter'];
        $area_all = [];
        $area_all[] = $all_area ['toilet_perimeter'] * $toilet_wall_space_high;
        $area_all[] = $all_area ['toilet_balcony_perimeter'] * $toilet_wall_space_high;
        $area_all[] = $all_area ['kitchen_perimeter'] * $kitchen_wall_space_high;
        $area_all[] = $all_area ['kitchen_balcony_perimeter'] * $kitchen_wall_space_high;
        $area = 0;
        foreach ($area_all as $v=>$k) {
            $area += $k;
        }
        return $area;
    }

    /**
     * 木作人工计算公式
     * @param $modelling_day
     * @param $flat_day
     * @param int $video_wall
     * @param $worker_day_cost
     * @return float
     */
    public static function carpentryLabor($modelling_day,$flat_day,$video_wall = 1,$worker_day_cost)
    {
        if(!empty($modelling_day) && !empty($flat_day) ) {
            //人工费：（造型天数+平顶天数+【1】天）×【工人每天费用】
            $artificial_fee = ceil($modelling_day + $flat_day + $video_wall) * $worker_day_cost;
        }
       return $artificial_fee;
    }

    /**
     * 木作造型长度计算
     * @param array $arr
     * @param array $coefficient_all
     * @param string $series
     * @return mixed
     */
    public static function  carpentryModellingLength($arr,$series=1)
    {

        $length = $arr['modelling_length'];
        $engineering = EngineeringStandardCarpentryCoefficient::find()
            ->where(['and',['series_or_style'=>0],['coefficient'=>2]])
            ->asArray()
            ->all();

        if ($engineering){
            foreach ($engineering as $engineering_one) {
                if( $engineering_one['project'] == $series) {
                    $series_one = $engineering_one['value'];
                }
            }
        }

        //造型长度=基本造型长度×系列系数2
        return $length* $series_one;
    }

    /**
     * 造型天数计算公式
     * @param string $modelling
     * @param string $day_modelling
     * @param string $series_all
     * @param string $style_all
     * @param int $series
     * @param int $style
     * @return float|int
     */
    public static function carpentryModellingDay($modelling,$day_modelling,$series =1,$style =1)
    {

        $series_ = EngineeringStandardCarpentryCoefficient::find()
            ->where(['and',['series_or_style'=>0],['coefficient'=>1]])
            ->asArray()
            ->all();
        foreach ($series_ as $one_series){
            if ($one_series['project'] == $series){
                $series_one = $one_series['value'];
            }
        }


        $style_ = EngineeringStandardCarpentryCoefficient::find()
            ->where(['and',['series_or_style'=>1],['coefficient'=>1]])
            ->asArray()
            ->all();
        foreach ($style_ as $one_style){
            if ($one_style['project'] == $style){
                $style_one = $one_style['value'];
            }
        }

//            造型天数=造型长度÷【每天做造型长度】×系列系数1×风格系数1
        $modelling_day = $modelling / $day_modelling * $series_one * $style_one;
        return $modelling_day;
    }

    /**
     * 平顶天数计算公式
     * @param array $area
     * @param string $day_area
     * @param string $series_all
     * @param string $style_all
     * @param int $series
     * @param int $style
     * @return float|int
     */
    public static function flatDay($area,$day_area,$series = 1,$style = 1)
    {
        //平顶面积
        $flat_area = $area['flat_area'];

        $series_ = EngineeringStandardCarpentryCoefficient::find()
            ->where(['and',['series_or_style'=>0],['coefficient'=>3]])
            ->asArray()
            ->all();
        foreach ($series_ as $one_series){
            if ($one_series['project'] == $series){
                $series_one = $one_series['value'];
            }
        }


        $style_ = EngineeringStandardCarpentryCoefficient::find()
            ->where(['and',['series_or_style'=>1],['coefficient'=>2]])
            ->asArray()
            ->all();
        foreach ($style_ as $one_style){
            if ($one_style['project'] == $style){
                $style_one = $one_style['value'];
            }
        }


        //平顶天数=平顶面积÷【每天做平顶面积】×系列系数3×风格系数2
        $flat_day = $flat_area / $day_area * $series_one * $style_one;
        return $flat_day;
    }

    /**
     * 木作石膏板计算公式
     * @param string $modelling_length
     * @param string $flat_area
     * @param float $meter
     * @param array $goods
     * @param int $video_wall
     * @return float
     */
    public static function carpentryPlasterboardCost($modelling_length,$flat_area,$goods,$crafts)
    {
        $plasterboard = [];
        foreach ($goods as $goods_price ) {
            if($goods_price['title'] == self::GOODS_NAME['plasterboard']) {
                $plasterboard = $goods_price;
            }
        }

        foreach ($crafts as $craft) {

           if ($craft['project_details'] == self::CARPENTRY_DETAILS['plasterboard_sculpt']){
               $plasterboard_sculpt = $craft['material'];
           }

           if ($craft['project_details'] == self::CARPENTRY_DETAILS['plasterboard_area']){
               $plasterboard_area = $craft['material'];
           }

           if ($craft['project_details'] == self::CARPENTRY_DETAILS['tv_plasterboard']){
               $tv_plasterboard  = $craft['material'];
           }
        }

//            个数：（造型长度÷【2.5】m+平顶面积÷【2.5】m²+【1】张）
        $plasterboard_cost['quantity'] = ceil($modelling_length / $plasterboard_sculpt + $flat_area / $plasterboard_area + $tv_plasterboard);

//            石膏板费用：个数×商品价格
        $plasterboard_cost['cost'] = round($plasterboard_cost['quantity'] * $plasterboard['platform_price'],2);
        $plasterboard_cost['procurement'] = round($plasterboard_cost['quantity'] * $plasterboard['purchase_price_decoration_company'],2);
        return $plasterboard_cost;
    }

    /**
     * 木作龙骨计算公式
     * @param string $modelling_length
     * @param string $flat_area
     * @param float $modelling_meter
     * @param float $flat_meter
     * @param array $goods
     * @return float
     */
    public static function carpentryKeelCost($modelling_length,$flat_area,$goods,$crafts)
    {
        if(!empty($modelling_length) &&!empty($flat_area) && !empty($goods)) {
            foreach ($goods as $price) {
                if($price['title'] == self::GOODS_NAME['keel']) {
                    $goods_price = $price;
                }
            }


            foreach ($crafts as $craft) {
                if($craft['project_details'] == self::CARPENTRY_DETAILS['keel_sculpt']) {
                    $keel_sculpt = $craft['material'];
                }
                if($craft['project_details'] == self::CARPENTRY_DETAILS['keel_area']) {
                    $keel_area = $craft['material'];
                }
            }
//          个数=个数1+个数2
//          个数1：（造型长度÷【1.5m】）
//          个数2：（平顶面积÷【1.5m²】）
            $keel_cost['quantity'] = ceil($modelling_length / $keel_sculpt + $flat_area /$keel_area);
//          主龙骨费用：个数×商品价格
            $keel_cost['cost'] = round($keel_cost['quantity'] * $goods_price['platform_price'],2);
            $keel_cost['procurement'] = round($keel_cost['quantity'] * $goods_price['purchase_price_decoration_company'],2);
        }
        return $keel_cost;
    }

    /**
     * 木作丝杆计算公式
     * @param string $modelling_length
     * @param string $flat_area
     * @param int $modelling_meter
     * @param int $flat_meter
     * @param array $goods
     * @return float
     */
    public static function carpentryPoleCost($modelling_length,$flat_area,$goods,$crafts)
    {
        if(!empty($modelling_length) && !empty($flat_area) && !empty($goods)) {
            foreach ($goods as $price) {
                if($price['title'] == self::GOODS_NAME['lead_screw']) {
                    $goods_price = $price;
                }
            }
            foreach ($crafts as $craft) {
                if($craft['project_details'] == self::CARPENTRY_DETAILS['screw_rod_sculpt']) {
                    $screw_rod_sculpt = $craft['material'];
                }
                if($craft['project_details'] == self::CARPENTRY_DETAILS['screw_rod_area']) {
                    $screw_rod_area = $craft['material'];
                }
            }
//            个数=个数1+个数2
//            个数1：（造型长度÷【2m】）
//            个数2：（平顶面积÷【2m²】
            $pole_cost['quantity'] = ceil($modelling_length / $screw_rod_sculpt + $flat_area / $screw_rod_area);
//            丝杆费用：个数×抓取的商品价格
            $pole_cost['cost'] = round($pole_cost['quantity'] * $goods_price['platform_price'],2);
            $pole_cost['procurement'] = round($pole_cost['quantity'] * $goods_price['purchase_price_decoration_company'],2);
        }
        return $pole_cost;
    }

    public static function carpentryBlockboard($goods,$post)
    {

        foreach ($goods as $one_goods){
            if ($one_goods['title'] == '细木工板'){
                $blockboard = $one_goods;
            }
        }

        $a = EngineeringStandardCraft::find()
            ->asArray()
            ->where(['project'=>'木作'])
            ->andWhere(['project_details'=>'电视墙用细木工板'])
            ->andWhere(['district_code'=>$post['city']])
            ->one();
        if ($a){
            $tv = $a['material'];
        }else{
            $tv = 1;
        }
//      个数
        $pole_cost['quantity'] = (int)$tv;
//      费用
        $pole_cost['cost'] = round($pole_cost['quantity'] * $blockboard['platform_price'],2);
        $pole_cost['procurement'] = round($pole_cost['quantity'] * $blockboard['purchase_price_decoration_company'],2);

        return $pole_cost;
    }

    /**
     * 乳胶漆面积计算公式
     * @param array $area
     * @param string $house_area
     * @param string $bedroom
     * @param float $tall
     * @param int $wall
     * @return array|string
     */
    public static function paintedArea($area,$house_area ,$bedroom ,$tall= 2.8,$wall = 4)
    {
//        卧室地面积：【z】%×（房屋面积）
            $ground_area = $area * ($house_area / 100);
//        卧室墙面积：（卧室地面积÷卧室个数）开平方×【1.8m】×4 ×卧室个数
            $wall_space_area =  sqrt($ground_area / $bedroom) * $tall * $wall * $bedroom;
//        卧室底漆面积=卧室地面积+卧室墙面积
            $total_area =    $ground_area + $wall_space_area;
        return $total_area;
    }

    /**
     * 乳胶漆周长计算公式
     * @param array $area
     * @param string $house_area
     * @param string $bedroom
     * @param int $wall
     * @return float|string
     */
    public  static function paintedPerimeter($area,$house_area,$bedroom ,$wall = 4)
    {

//        卧室地面积：【z】%×（房屋面积）
            $ground_area =  $area * ($house_area / 100);
//        卧室周长：（卧室地面积÷卧室个数）开平方×4×卧室个数
            $wall_space_perimeter = sqrt($ground_area / $bedroom) * $wall * $bedroom;

        return $wall_space_perimeter;
    }

    /**
     * 乳胶漆 费用计算公式
     * @param array $goods
     * @param array $crafts
     * @param array $area
     * @return int
     */
    public static function paintedCost($goods,$craft,$area)
    {
        $goods_value = GoodsAttr::findByGoodsIdUnit($goods['id']);
        if ($goods_value == null){
            $code = 1061;
            return Json::encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code],
            ]);
        }
        $goods_value_one = '';
        foreach ($goods_value as $value) {
            if ($goods['title'] == self::GOODS_NAME['concave_line']) {
                if ($value['name'] == self::UNITS['length'] && $value['title'] == self::GOODS_NAME['concave_line']) {
                    $goods_value_one = $value['value'];
                } elseif($value['name'] !== self::UNITS['texture']) {
                    $goods_value_one = $value['value'];
                }
            } else {
                $goods_value_one = $value['value'];
            }
        }

//        个数：（腻子面积×【0.33kg】÷抓取的商品的规格重量）
        $putty_cost ['quantity'] = ceil($area * $craft['material'] / $goods_value_one);

//        腻子费用：个数×商品价格
        $putty_cost ['cost']  =  round($putty_cost['quantity'] * $goods['platform_price'],2);
        $putty_cost ['procurement']  = round($putty_cost['quantity'] * $goods['purchase_price_decoration_company'],2);
        return $putty_cost;
    }

    /**
     * 泥作面积
     * @param array $ground_area
     * @param array $craft
     * @param int $quantity
     * @param int $wall
     * @return float
     */
    public static function mudMakeArea($ground_area,$craft,$quantity = 1,$wall = 4)
    {

        //        （卫生间地面积÷卫生间个数）开平方×【2.4m】×4 ×卫生间个数
        $sqrt= sqrt($ground_area / $quantity);
        $wall_area = $sqrt * $craft * $wall * $quantity;

        return $wall_area;
    }

    /**
     * 泥作费用
     * @param int $area
     * @param array $goods
     * @param int $craft
     * @param string $project
     * @return mixed
     */
    public static function mudMakeCost($area,$goods,$craft,$goods_attr,$project)
    {

        foreach ($goods as $one) {
            if ($one['title'] == $project) {
                $goods_price = $one['platform_price'];
                $goods_procurement = $one['purchase_price_decoration_company'];
            }
        }
        $goods_unit = 0;
        foreach ($goods_attr as $one_goods_attr) {
            if ($one_goods_attr['title'] == $project) {
                $goods_unit = $one_goods_attr['value'];
            }
        }
        if ($goods_unit == 0){
            $goods_unit = 1;
        }

        //        个数：（水泥面积×【15kg】÷抓取的商品的KG）
        $mud_make['quantity'] = ceil($area * $craft / $goods_unit);
        //        水泥费用:个数×抓取的商品价格
        $mud_make['cost'] = round($mud_make['quantity'] * $goods_price,2);
        $mud_make['procurement'] = round($mud_make['quantity'] * $goods_procurement,2);
        return $mud_make;
    }

    /**
     * 杂工拆除
     * @param array $get_area
     * @param $day_area
     * @return mixed
     */
    public static function wallArea($get_area,$day_area,$_area)
    {
        foreach ($day_area as $skill) {
            switch ($skill) {
                case $skill['worker_kind_details'] == self::BACKMAN_DETAILS['dismantle_12_area']:
                    $dismantle_12 = $skill;
                    break;
                case $skill['worker_kind_details'] == self::BACKMAN_DETAILS['dismantle_24_area']:
                    $dismantle_24 = $skill;
                    break;
                case $skill['worker_kind_details'] == self::BACKMAN_DETAILS['new_12_area']:
                    $new_construction_12 = $skill;
                    break;
                case $skill['worker_kind_details'] == self::BACKMAN_DETAILS['new_24_area']:
                    $new_construction_24 = $skill;
                    break;
                case $skill['worker_kind_details'] == self::BACKMAN_DETAILS['repair_length']:
                    $repair = $skill;
                    break;
            }
        }
//        12墙拆除天数=12墙拆除面积÷【每天拆除12墙面积】
        $day['dismantle_12'] = $get_area['12_dismantle'] / $dismantle_12['quantity'];
//        24墙拆除天数=24墙拆除面积÷【每天拆除24墙面积】
        $day['dismantle_24'] = $get_area['24_dismantle'] / $dismantle_24['quantity'];
//        ①拆除天数=12墙拆除天数+24墙拆除天数
        $day['dismantle_day'] = $day['dismantle_12'] + $day['dismantle_24'];

//        12墙新建天数=12墙新建面积÷【每天新建12墙面积】
        $day['new_construction_12'] = $get_area['12_new_construction'] / $new_construction_12['quantity'];
//        24墙新建天数=24墙新建面积÷【每天新建24墙面积】
        $day['new_construction_24'] = $get_area['24_new_construction'] / $new_construction_24['quantity'];
//        ②新建天数=12墙新建天数+24墙新建天数
        $day['new_construction_day'] =  $day['new_construction_12'] + $day['new_construction_24'];

//        ③补烂天数=补烂长度÷【每天补烂长度】
        $day['repair_day'] = $get_area['repair'] / $repair['quantity'];

//        总天数=拆除天数+新建天数+补烂天数
        $day['total_day'] = ceil($day['dismantle_day'] + $day['new_construction_day'] + $day['repair_day'] + $_area);

        return $day;
    }

    /**
     * 杂工清运有建渣点
     * @param array $get_area
     * @param array $craft
     * @return mixed
     */
    public static function haveBuildingScrap($get_area,$craft)
    {
        $clear_12 = 0;
        $clear_24 = 0;
        foreach ($craft as $one_craft) {
            switch ($one_craft) {
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['clear_12']:
                    $clear_12 = $one_craft['material'];
                    break;
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['clear_24']:
                    $clear_24 = $one_craft['material'];
                    break;
            }
        }
//            运到小区楼下费用=（12墙拆除面积）×【40】
        $transportation_cost['12_wall'] = $get_area['12_dismantle'] * $clear_12;
//            运到小区楼下费用=（12墙拆除面积）×【20】
        $transportation_cost['24_wall'] = $get_area['24_dismantle'] * $clear_24;
//            清运建渣费用=清运24墙费用+清运12墙费用
        $transportation_cost['cost'] = $transportation_cost['12_wall'] + $transportation_cost['24_wall'];
        return $transportation_cost;
    }

    /**
     * 杂工清运无建渣点
     * @param array $get_area
     * @param array $craft
     * @return mixed
     */
    public static function nothingBuildingScrap($get_area,$craft)
    {
        $clear_12 = 0;
        $vehicle_12_area = 0;
        $clear_24 = 0;
        $vehicle_24_area = 0;
        $vehicle_cost = 0;
        foreach ($craft as $one_craft) {
            switch ($one_craft) {
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['clear_12']:
                    $clear_12 = $one_craft['material'];
                    break;
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['vehicle_12_area']:
                    $vehicle_12_area = $one_craft['material'];
                    break;
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['clear_24']:
                    $clear_24 = $one_craft['material'];
                    break;
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['vehicle_24_area']:
                    $vehicle_24_area = $one_craft['material'];
                    break;
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['vehicle_cost']:
                    $vehicle_cost = $one_craft['material'];
                    break;
            }
        }
//            运到小区楼下费用=（12墙拆除面积）×【40】
        $transportation_cost['12_wall'] = $get_area['12_dismantle'] * $clear_12;
//            单独外运费用=（12墙拆除面积÷【20】）×【300】
        $transportation_cost['12_wall_transportation'] = ceil($get_area['12_dismantle'] / $vehicle_12_area) * $vehicle_cost;
//            清运12墙费用=运到小区楼下费用+单独外运费用
        $transportation_cost['12_wall_cost'] = $transportation_cost['12_wall'] + $transportation_cost['12_wall_transportation'];


//            运到小区楼下费用=（24墙拆除面积）×【20】
        $transportation_cost['24_wall'] = $get_area['24_dismantle'] * $clear_24;
//            单独外运费用=（24墙拆除面积÷【10】）×【300】
        $transportation_cost['24_wall_transportation'] = ceil($get_area['24_dismantle'] / $vehicle_24_area) * $vehicle_cost;
//            清运24墙费用=运到小区楼下费用+单独外运费用
        $transportation_cost['24_wall_cost'] = $transportation_cost['24_wall'] + $transportation_cost['24_wall_transportation'];


//            清运建渣费用=清运24墙费用+清运12墙费用
        $transportation_cost['cost'] = $transportation_cost['12_wall_cost'] +  $transportation_cost['24_wall_cost'];
        return $transportation_cost;
    }

    /**
     * 杂工水泥计算公式
     * @param array $get_area
     * @param array $craft
     * @param array $goods
     * @return mixed
     */
    public static function cementCost($get_area,$craft,$goods,$goods_attr)
    {
        $cement_12 = 0;
        $cement_24 = 0;
        $repair = 0;
        foreach ($craft as $one_craft) {
            switch ($one_craft) {
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['12_cement_dosage']:
                    $cement_12 = $one_craft['material'];
                    break;
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['24_cement_dosage']:
                    $cement_24 = $one_craft['material'];
                    break;
                case $one_craft['project_details'] == self::BACKMAN_DETAILS['repair_cement_dosage']:
                    $repair = $one_craft['material'];
                    break;
            }
        }
        $value = 0;
        foreach ($goods_attr as $one_goods) {
            $value = $one_goods['value'];
        }

//            水泥用量=新建用量+补烂用量
//        新建用量=12墙新建面积×【10kg】+24墙新建面积×【15kg】+补烂长度×【2kg】
        $new_12 = $get_area['12_new_construction'] * $cement_12;
        $new_24 = $get_area['24_new_construction'] * $cement_24;
        $new_repair = $get_area['repair'] * $repair;
        $new_dosage = $new_12 + $new_24 + $new_repair;
        if ($new_dosage == 0){
            $cement['quantity'] = 0;
            $cement['cost'] = 0;
            return  $cement;
        }
//        个数：（水泥用量÷抓取的商品的KG）
        $cement['quantity'] = ceil($new_dosage/$value);
//        水泥费用：个数×抓取的商品价格
        $cement['cost'] = round($cement['quantity'] * $goods['platform_price'],2);
        $cement['procurement'] = round($cement['quantity'] * $goods['purchase_price_decoration_company'],2);
        return $cement;
    }

    /**
     * 杂工空心砖计算公式
     * @param array $get_area
     * @param array $goods
     * @param array $goods_standard
     * @return mixed
     */
    public static function brickCost($get_area,$goods,$goods_standard )
    {
        if ($get_area && $goods && $goods_standard) {
            $length = 0;
            $wide = 0;
            $high = 0;
            foreach ($goods_standard as $standard) {
                if ($standard['name'] == self::UNITS['long']) {
                    $length = $standard['value'] / self::BRICK_UNITS;
                }
                if ($standard['name'] == self::UNITS['wide']) {
                    $wide = $standard['value'] / self::BRICK_UNITS;
                }
                if ($standard['name'] == self::UNITS['high']) {
                    $high = $standard['value'] / self::BRICK_UNITS;
                }
            }
//        空心砖费用：个数×抓取的商品价格
//        个数：（空心砖用量）
//        空心砖用量=12墙新建面积÷长÷高+24墙新建面积÷宽÷高
            $dosage_12 = $get_area['12_new_construction'] / $length / $wide;
            $dosage_24 = $get_area['24_new_construction'] / $wide / $high;
            $brick['quantity'] = ceil($dosage_12 + $dosage_24);
            $brick['cost'] = round($brick['quantity'] * $goods['platform_price'],2);
            $brick['procurement'] = round($brick['quantity'] * $goods['purchase_price_decoration_company'],2);
        } else {
            $brick['quantity'] = 0;
            $brick['cost'] = $brick['quantity'] * $goods['platform_price'];
            $brick['procurement'] = $brick['quantity'] * $goods['purchase_price_decoration_company'];
        }
        return $brick;
    }

    /**
     * @param array $get_area
     * @param array $goods
     * @param array $craft
     * @return mixed
     */
    public static function riverSandCost($get_area,$goods,$craft,$goods_attr)
    {
        if ($get_area && $goods && $craft) {
            foreach ($craft as $one_craft) {
                if ($one_craft['project_details'] == self::BACKMAN_DETAILS['12_river_sand_dosage']) {
                    $river_sand_12 = $one_craft['material'];
                }
                if ($one_craft['project_details'] == self::BACKMAN_DETAILS['24_river_sand_dosage']) {
                    $river_sand_24 = $one_craft['material'];
                }
                if ($one_craft['project_details'] == self::BACKMAN_DETAILS['repair_river_sand_dosage']) {
                    $river_sand_repair = $one_craft['material'];
                }
            }

            $value = '';
            foreach ($goods_attr as $one_goods_attr) {
                $value = $one_goods_attr['value'];
            }
//              河沙用量=新建用量+补烂用量
//              新建用量=12墙新建面积×【3kg】+24墙新建面积×【3kg】+补烂长度×【2kg】
            $dosage_12 = $get_area['12_new_construction'] * $river_sand_12;
            $dosage_24 = $get_area['24_new_construction'] * $river_sand_24;
            $dosage_repair = $get_area['repair'] * $river_sand_repair;
            $river_sand_dosage = $dosage_12 + $dosage_24 + $dosage_repair;
//              个数：（河沙用量÷抓取的商品的KG）
            $river_sand['quantity'] =  ceil($river_sand_dosage / $value);
//              河沙费用：个数×抓取的商品价格
            $river_sand['cost'] =   round($river_sand['quantity'] * $goods['platform_price'],2);
            $river_sand['procurement'] =   round($river_sand['quantity'] * $goods['platform_price'],2);
        } else {
            $river_sand['quantity'] =  0;
            $river_sand['cost'] =  0;
        }
        return $river_sand;
    }

    /**
     * 木地板计算公式
     * @param array $bedroom_area
     * @param string $area
     * @param array $goods
     * @return mixed
     */
    public static function woodFloorCost($bedroom_area,$area,$goods,$nature)
    {
        if ($bedroom_area && $area && $goods) {
            foreach ($bedroom_area as $one_bedroom) {
                //        卧室地面积=【z】%×（房屋面积）
                $bedroom = $one_bedroom['project_value'] * $area;
            }
//        木地板面积=卧室地面积
            $wood_floor_area =  $bedroom;

            foreach ($nature as $one_nature) {
                if ($one_nature['name'] == self::UNITS['length'] ) {
                    $length = $one_nature['value'] / self::BRICK_UNITS;
                }
                if ($one_nature['name'] == self::UNITS['length'] ) {
                    $wide = $one_nature['value'] / self::BRICK_UNITS;
                }
            }
            $wood_floor_area_1 = $length * $wide;
            //        个数：（木地板面积÷抓取木地板面积）
            $wood_floor['quantity'] = ceil($wood_floor_area / $wood_floor_area_1);
            //        木地板费用：个数×抓取的商品价格
            $wood_floor['cost'] = $wood_floor['quantity'] * $goods['platform_price'];
        }
        return $wood_floor;
    }

    /**
     * 大理石计算公式
     * @param string $post
     * @param array $goods
     * @return mixed
     */
    public static function marbleCost($post,$goods)
    {
        //        个数=飘窗米数
        $marble['quantity'] = $post;
        //        大理石费用：个数×抓取的商品价格
        $marble['cost'] = $marble['quantity'] * $goods['platform_price'];
        return $marble;
    }

    /**
     * 价格转化
     * @param $goods
     * @return array
     */
    public static function priceConversion($goods)
    {
        foreach ($goods as &$one_goods) {
            $one_goods['platform_price'] =  $one_goods['platform_price'] / self::GOODS_PRICE_UNITS;
            $one_goods['supplier_price'] =  $one_goods['supplier_price'] / self::GOODS_PRICE_UNITS;
            $one_goods['purchase_price_decoration_company'] =  $one_goods['purchase_price_decoration_company'] / self::GOODS_PRICE_UNITS;
        }

        return $goods;
    }

    /**
     * 条件判断
     * @param $goods
     * @param $post
     * @return array|bool
     */
    public static function judge($goods,$post)
    {
        foreach ($goods as $one_goods) {
            switch ($one_goods) {
                case $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style']:
                     $goods_judge[] = $one_goods;
                    break;
                default:
                    $goods_judge[] = $one_goods;
                    break;
            }
        }
        return $goods_judge;
    }

    /**
     * 泥作规格
     * @param $goods
     * @return mixed
     */
    public static function mudMakeMaterial($goods)
    {
        if ($goods) {
            $property = [];
            $id = [];
            foreach ($goods as $one_goods) {
                $id[] = $one_goods['id'];
            }
            $goods_property = GoodsAttr::findByGoodsIdUnit($id);
            if ($goods_property == null){
                $code = 1061;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                    'data' => $goods,
                ]);
            }
            foreach ($goods_property as $one_goods_property) {
                switch ($one_goods_property) {
                    case $one_goods_property['title'] == self::GOODS_NAME['river_sand']:
                        $property['river_sand']['title'] = self::GOODS_NAME['river_sand'];
                        $property['river_sand']['value'] = $one_goods_property['value'];
                        break;
                    case $one_goods_property['title'] == self::GOODS_NAME['cement']:
                        $property['concrete']['title'] = self::GOODS_NAME['cement'];
                        $property['concrete']['value'] = $one_goods_property['value'];
                        break;
                    case $one_goods_property['title'] == self::GOODS_NAME['self_leveling']:
                        $property['self_leveling']['title'] = self::GOODS_NAME['self_leveling'];
                        $property['self_leveling']['value'] = $one_goods_property['value'];
                        break;
                }
            }

            return $property;
        }
    }

    /**
     * 利润率最大
     * @param $goods
     * @return mixed
     */
    public static function profitMargin($goods)
    {
        if (count($goods) == count($goods, 1)) {
            return $goods;
        } elseif ($goods == null){
            return new \stdClass;
        }else {
            echo 11;
            var_dump($goods);exit;
            foreach($goods as $v) {
//                $r[$v['title']][$v['profit_rate']] = $v;
//                $max = max($r[$v['title']][$v['profit_rate']],$v['profit_rate']);

                $r = max($v,$v['profit_rate']);
            }
            return $r;
        }
    }

    /**
     * 乳胶漆风格和系列
     * @param $goods_price
     * @param $crafts
     * @param $post
     * @return mixed
     */
    public static function coatingSeriesAndStyle($goods_price,$post)
    {
        foreach ($goods_price as $goods) {
            switch ($goods) {
                case $goods['title'] == self::GOODS_NAME['putty'] && $goods['series_id'] == $post['series']:
                    $all_goods[] = $goods;
                    $goods_max = self::profitMargin($goods);
                    $goods_all ['putty'] = $goods_max;
                    break;
                case $goods['title'] == self::GOODS_NAME['emulsion_varnish_primer'] && $goods['series_id'] == $post['series']:
                    $all_goods[] = $goods;
                    $goods_max = self::profitMargin($goods);
                    $goods_all ['primer'] = $goods_max;
                    break;
                case $goods['title'] == self::GOODS_NAME['emulsion_varnish_surface'] && $goods['series_id'] == $post['series']:
                    $all_goods[] = $goods;
                    $goods_max = self::profitMargin($goods);
                    $goods_all ['finishing_coat'] = $goods_max;
                    break;
                case $goods['title'] == self::GOODS_NAME['concave_line'] && $goods['style_id'] == $post['style']:
                    $all_goods[] = $goods;
                    $goods_max = self::profitMargin($goods);
                    $goods_all ['concave_line'] = $goods_max;
                    break;
                case $goods['title'] == self::GOODS_NAME['land_plaster'] && $goods['style_id'] == 0 && $goods['series_id'] == 0:
                    $all_goods[] = $goods;
                    $goods_max = self::profitMargin($goods);
                    $goods_all ['gypsum_powder'] = $goods_max;
                    break;
            }
        }
        return $goods_all;
    }


    /**
     * 强电点位
     * @param $points
     * @param $post
     * @return mixed
     */
    public static function strongCurrentPoints($points,$post)
    {
        foreach ($points as $one_points) {
            switch ($one_points) {
                case $one_points['place'] == self::HOUSE_MESSAGE['bedroom']:
                    $bedroom = $one_points['points_total'] * $post['bedroom'];
                    break;
                case $one_points['place'] == self::HOUSE_MESSAGE['kitchen']:
                    $kitchen = $one_points['points_total'] * $post['kitchen'];
                    break;
                case $one_points['place'] == self::HOUSE_MESSAGE['toilet']:
                    $toilet = $one_points['points_total'] * $post['toilet'];
                    break;
                case $one_points['place'] == self::HOUSE_MESSAGE['household']:
                    $register = $one_points['points_total'];
                    break;
                case $one_points['place'] == self::HOUSE_MESSAGE['drawing_room_balcony']:
                    $balcony = $one_points['points_total'];
                    break;
                case $one_points['place'] == self::HOUSE_MESSAGE['passage']:
                    $passage = $one_points['points_total'];
                    break;
                case $one_points['place'] == self::HOUSE_MESSAGE['live_balcony']:
                    $live_balcony= $one_points['points_total'];
                    break;
            }
            if ($one_points['place'] == self::HOUSE_MESSAGE['guest_restaurant']) {
                if ($post['hall'] != 2) {
                    $hall = $one_points['points_total'] * $post['hall'];
                } else {
                    $hall = $one_points['points_total'] + 2;
                }
            }
        }
        $strong_current_points = $hall+$bedroom+$kitchen+$toilet+$register+$balcony+$passage+$live_balcony;
        return $strong_current_points;
    }

    /**
     * 墙砖面积计算
     * @param $id
     * @return bool|float|int
     */
    public static function wallBrickAttr($id)
    {
        if ($id) {
            $goods_attr = GoodsAttr::findByGoodsIdUnit($id);
            if ($goods_attr == null){
                $code = 1061;
                return Json::encode([
                    'code' => $code,
                    'msg' => \Yii::$app->params['errorCodes'][$code],
                ]);
            }
            foreach ($goods_attr as $one_goods_attr) {
                if ($one_goods_attr['name'] == self::UNITS['length']) {
                    $length = $one_goods_attr['value'] / self::BRICK_UNITS;
                }
                if ($one_goods_attr['name'] == self::UNITS['breadth']) {
                    $wide = $one_goods_attr['value'] / self::BRICK_UNITS;
                }
            }
            $goods_area = $length * $wide;
            return $goods_area;
        }else
        {
            return false;
        }
    }

    /**
     * 泥作地砖
     * @param $goods
     * @return array|bool
     */
    public static function floorTile($goods)
    {
        $id = [];
        $goods_attr_details = [];
        foreach ($goods as $one_goods) {
            $id [] = $one_goods['id'];
        }
        $goods_attr = GoodsAttr::findByGoodsIdUnit($id);

        foreach ($goods_attr as $one_goods_attr) {
            switch ($one_goods_attr) {
                case $one_goods_attr['value'] == self::HOUSE_MESSAGE['kitchen']:
                    $kitchen_id = $one_goods_attr['goods_id'];
                    $goods_attr_details['kitchen']['id'] = $one_goods_attr['goods_id'];
                    $goods_attr_details['kitchen']['name'] = $one_goods_attr['value'];
                    break;
                case $one_goods_attr['value'] == self::HOUSE_MESSAGE['toilet']:
                    $toilet_id = $one_goods_attr['goods_id'];
                    $goods_attr_details['toilet']['id'] = $one_goods_attr['goods_id'];
                    $goods_attr_details['toilet']['name'] = $one_goods_attr['value'];
                    break;
                case $one_goods_attr['value'] == self::HOUSE_MESSAGE['hall']:
                    $hall_id = $one_goods_attr['goods_id'];
                    $goods_attr_details['hall']['id'] = $one_goods_attr['goods_id'];
                    $goods_attr_details['hall']['name'] = $one_goods_attr['value'];
                    break;
            }
        }
        foreach ($goods_attr as  $goods_area) {
            switch ($goods_area) {
                case $goods_area['goods_id'] == $kitchen_id:
                    if ($goods_area['name'] == self::UNITS['length']) {
                        $kitchen_length = $goods_area['value'] / self::BRICK_UNITS;
                    }
                    if ($goods_area['name'] == self::UNITS['breadth']) {
                        $kitchen_wide = $goods_area['value'] / self::BRICK_UNITS;
                    }
                    break;
                case $goods_area['goods_id'] == $toilet_id:
                    if ($goods_area['name'] == self::UNITS['length']) {
                        $toilet_length = $goods_area['value'] / self::BRICK_UNITS;
                    }
                    if ($goods_area['name'] == self::UNITS['breadth']) {
                        $toilet_wide = $goods_area['value'] / self::BRICK_UNITS;
                    }
                    break;
                case $goods_area['goods_id'] == $hall_id:
                    if ($goods_area['name'] == self::UNITS['length']) {
                        $hall_length = $goods_area['value'] / self::BRICK_UNITS;
                    }
                    if ($goods_area['name'] == self::UNITS['breadth']) {
                        $hall_wide = $goods_area['value'] / self::BRICK_UNITS;
                    }
                    break;
            }
        }
        foreach ($goods as $goods_price)
        {
            switch ($goods_price)
            {
                case $goods_price['id'] == $kitchen_id:
                    $goods_attr_details['kitchen']['price'] =  $goods_price['platform_price'];
                    $goods_attr_details['kitchen']['purchase_price_decoration_company'] =  $goods_price['purchase_price_decoration_company'];
                    break;
                case $goods_price['id'] == $toilet_id:
                    $goods_attr_details['toilet']['price'] =  $goods_price['platform_price'];
                    $goods_attr_details['toilet']['purchase_price_decoration_company'] =  $goods_price['purchase_price_decoration_company'];
                    break;
                case $goods_price['id'] == $hall_id:
                    $goods_attr_details['hall']['price'] =  $goods_price['platform_price'];
                    $goods_attr_details['hall']['purchase_price_decoration_company'] =  $goods_price['purchase_price_decoration_company'];
                    break;
            }
        }
        $goods_attr_details['kitchen']['area'] = $kitchen_length * $kitchen_wide;
        $goods_attr_details['toilet']['area'] = $toilet_length * $toilet_wide;
        $goods_attr_details['hall']['area'] = $hall_length * $hall_wide;
        return $goods_attr_details;
    }


    public static function formula($goods,$post)
    {
        foreach ($goods as $one_goods){
            switch ($one_goods){
                case $one_goods['title'] == self::GOODS_NAME['wood_floor'] && $one_goods['series_id'] == $post['series']: // 木地板
                //木地板面积=卧室地面积    卧室地面积=【z】%×（房屋面积） 木地板费用：个数×抓取的商品价格 个数：（木地板面积÷抓取木地板面积）
                    $goods_area = GoodsAttr::findByGoodsIdUnit($one_goods['id']);
                    foreach ($goods_area as $one_goods_area) {
                        if ($one_goods_area['name'] == self::UNITS['length']) {
                            $length = $one_goods_area['value'] / self::BRICK_UNITS;
                        }
                        if ($one_goods_area['name'] == self::UNITS['breadth']) {
                            $breadth = $one_goods_area['value'] / self::BRICK_UNITS;
                        }
                    }
                    $area = $length * $breadth;
                    $one_goods['quantity'] = ceil($post['bedroom_area'] / $area);
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $wood_floor [] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['marble']: // 大理石
                    if ($post['window'] > 1) {
                        $one_goods['quantity'] = $post['window'];
                        $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                        $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                        $marble [] = $one_goods;
                    }else {
                        $marble = null;
                    }
                    break;
                case $one_goods['title'] == self::GOODS_NAME['elbow']: // 弯头
                    $one_goods['quantity'] = $post['toilet'] * 4;
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $elbow[] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['timber_door'] && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style']: //木门
                    $one_goods['quantity'] = $post['bedroom'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $timber_door[] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['bath_heater'] && $one_goods['series_id'] == $post['series'] : //浴霸
                    $one_goods['quantity'] = $post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $bath_heater[] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['ventilator'] && $one_goods['series_id'] == $post['series'] : //换气扇
                    $one_goods['quantity'] = $post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $ventilator[] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['ceiling_light'] && $one_goods['series_id'] == $post['series'] : //吸顶灯
                    $one_goods['quantity'] = $post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $ceiling_light[] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['tap'] && $one_goods['series_id'] == $post['series'] : //水龙头
                    $one_goods['quantity'] = $post['toilet'] + $post['kitchen'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $tap[] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['bed'] && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style']: //床
                    $one_goods['quantity'] = $post['bedroom'] ;
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $bed[] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['night_table'] && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style']: //床头柜
                    $one_goods['quantity'] = $post['bedroom'] * 2;
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $night_table[] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['kitchen_ventilator'] && $one_goods['series_id'] == $post['series'] : //抽油烟机
                    $one_goods['quantity'] = $post['kitchen'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $kitchen_ventilator[] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['stove'] && $one_goods['series_id'] == $post['series'] : //灶具
                    $one_goods['quantity'] = $post['kitchen'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $stove[] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['upright_air_conditioner'] && $one_goods['series_id'] == $post['series'] : //立柜空调
                    $one_goods['quantity'] = $post['hall'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $upright_air_conditioner[] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['hang_air_conditioner'] && $one_goods['series_id'] == $post['series'] : //壁挂空调
                    $one_goods['quantity'] = $post['bedroom'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $hang_air_conditioner[] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['lamp'] && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style'] : //灯具
                    $one_goods['quantity'] = $post['bedroom'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $lamp[] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['mattress'] && $one_goods['series_id'] == $post['series'] : //床垫
                    $one_goods['quantity'] = $post['bedroom'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $mattress[] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['closestool'] && $one_goods['series_id'] == $post['series'] : //马桶
                    $one_goods['quantity'] = $post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $closestool[] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['bath_cabinet'] && $one_goods['series_id'] == $post['series'] : //浴柜
                    $one_goods['quantity'] = $post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $bath_cabinet[] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['sprinkler'] && $one_goods['series_id'] == $post['series'] : //花洒套装
                    $one_goods['quantity'] = $post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $sprinkler[] = $one_goods;
                    break;
                case $one_goods['title'] == self::GOODS_NAME['shower_partition'] && $one_goods['series_id'] == $post['series'] : //淋浴隔断
                    $one_goods['quantity'] = $post['toilet'];
                    $one_goods['cost'] = round($one_goods['platform_price'] * $one_goods['quantity'],2);
                    $one_goods['procurement'] = round($one_goods['purchase_price_decoration_company'] * $one_goods['quantity'],2);
                    $shower_partition[] = $one_goods;
                    break;
            }
        }

        $wf = isset($wood_floor) ? $wood_floor :[];
        $ma = isset($marble) ? $marble :[];
        $el = isset($elbow) ? $elbow :[];
        $td = isset($timber_door) ? $timber_door :[];
        $bh = isset($bath_heater) ? $bath_heater :[];
        $ve = isset($ventilator) ? $ventilator :[];
        $cl= isset($ceiling_light) ? $ceiling_light :[];
        $ta = isset($tap) ? $tap :[];
        $be = isset($bed) ? $bed :[];
        $nt = isset($night_table) ? $night_table :[];
        $kv = isset($kitchen_ventilator) ? $kitchen_ventilator :[];
        $st = isset($stove) ? $stove :[];
        $uac = isset($upright_air_conditioner) ? $upright_air_conditioner :[];
        $hac = isset($hang_air_conditioner) ? $hang_air_conditioner :[];
        $l = isset($lamp) ? $lamp :[];
        $m = isset($mattress) ? $mattress :[];
        $cs = isset($closestool) ? $closestool :[];
        $bc = isset($bath_cabinet) ? $bath_cabinet :[];
        $spr = isset($sprinkler) ? $sprinkler :[];
        $sp = isset($shower_partition) ? $shower_partition :[];

        $material []  = self::profitMargin($wf);
        $material []  = self::profitMargin($ma);
        $material []  = self::profitMargin($el);
        $material []  = self::profitMargin($td);
        $material []  = self::profitMargin($bh);
        $material []  = self::profitMargin($ve);
        $material []  = self::profitMargin($cl);
        $material []  = self::profitMargin($ta);
        $material []  = self::profitMargin($be);
        $material []  = self::profitMargin($nt);
        $material []  = self::profitMargin($kv);
        $material []  = self::profitMargin($st);
        $material []  = self::profitMargin($uac);
        $material []  = self::profitMargin($hac);
        $material []  = self::profitMargin($l);
        $material []  = self::profitMargin($m);
        $material []  = self::profitMargin($bc);
        $material []  = self::profitMargin($spr);
        $material []  = self::profitMargin($sp);
        $material []  = self::profitMargin($cs);

        $goods_material = [];
        foreach ($material as $one){
            if($one != null){
                $goods_material[] =   $one;
            }
        }

        return $goods_material;
    }

    /**
     * 电工材料利润最大
     * @param $goods
     * @param $material_price
     * @return array
     */
    public static function electricianMaterial($goods,$material_price)
    {
        foreach ($goods as $one_weak_current) {
            switch ($one_weak_current) {
                case $one_weak_current['title'] == self::GOODS_NAME['reticle'] || $one_weak_current['title'] == self::GOODS_NAME['wire']:
                    $one_weak_current['quantity'] = $material_price['wire_quantity'];
                    $one_weak_current['cost'] = $material_price['wire_cost'];
                    $one_weak_current['procurement'] = $material_price['wire_procurement'];
                    $wire [] =  $one_weak_current;
                    break;
                case $one_weak_current['title'] == self::GOODS_NAME['spool']:
                    $one_weak_current['quantity'] = $material_price['spool_quantity'];
                    $one_weak_current['cost'] = $material_price['spool_cost'];
                    $one_weak_current['procurement'] = $material_price['spool_procurement'];
                    $spool [] =  $one_weak_current;
                    break;
                case $one_weak_current['title'] == self::GOODS_NAME['bottom_case']:
                    $one_weak_current['quantity'] = $material_price['bottom_quantity'];
                    $one_weak_current['cost'] = $material_price['bottom_cost'];
                    $one_weak_current['procurement'] = $material_price['bottom_procurement'];
                    $bottom [] =  $one_weak_current;
                    break;
            }
        }

        if (!$wire && !$spool && !$bottom){
            return false;
        }


        $material ['total_cost'] = round($material_price['total_cost'],2);
        $material ['material'] [] = BasisDecorationService::profitMargin($wire);
        $material ['material'] []= BasisDecorationService::profitMargin($spool);
        $material ['material'] []= BasisDecorationService::profitMargin($bottom);
        return $material;
    }

    /**
     * 水路材料
     * @param $goods
     * @param $material_price
     * @return array
     */
    public static function waterwayMaterial($goods,$material_price)
    {
        foreach ($goods as $one_waterway_current) {
            switch ($one_waterway_current) {
                case $one_waterway_current['title'] == self::GOODS_NAME['ppr'];
                    $one_waterway_current['quantity'] = $material_price['ppr_quantity'];
                    $one_waterway_current['cost'] = $material_price['ppr_cost'];
                    $one_waterway_current['procurement'] = $material_price['ppr_procurement'];
                    $ppr[] = $one_waterway_current;
                    break;
                case $one_waterway_current['title'] == self::GOODS_NAME['pvc'];
                    $one_waterway_current['quantity'] = $material_price['pvc_quantity'];
                    $one_waterway_current['cost'] = $material_price['pvc_cost'];
                    $one_waterway_current['procurement'] = $material_price['pvc_procurement'];
                    $pvc[] = $one_waterway_current;
                    break;
            }
        }
        if (!$ppr && !$pvc){
            return false;
        }
        $material ['total_cost'] = round($material_price['total_cost'],2);
        $material ['material'][] = BasisDecorationService::profitMargin($ppr);
        $material ['material'][] = BasisDecorationService::profitMargin($pvc);
        return $material;
    }


    /**
     * 无分类的商品
     * @param $without_assort_goods_price
     * @param $assort_material
     * @param $post
     * @return array
     */
    public static function withoutAssortGoods($goods,$assort_material,$post)
    {
        foreach ($goods as &$one){
            foreach ($assort_material as $value){
                if ($one['title'] == $value['title']){
                    $one['quantity'] =$value['quantity'];
                    $one['cost'] = round($one['quantity'] * $one['platform_price'],2);
                    $one['procurement'] = round($one['quantity'] * $one['purchase_price_decoration_company'],2);
                }
            }
        }
        foreach ($goods as $goods_value){
            if ($goods_value['series_id'] == $post['series'] && $goods_value['style_id'] == $post['style']){
                $series_style_goods[] =  $goods_value;
            }elseif ($goods_value['series_id'] == $post['series'] && $goods_value['style_id'] == 0){
                $series_style_goods[] =  $goods_value;
            }elseif ($goods_value['style_id'] == $post['style'] && $goods_value['series_id'] == 0){
                $series_style_goods[] =  $goods_value;
            }elseif ($goods_value['series_id'] == 0 && $goods_value['style_id'] == 0){
                $series_style_goods[] =  $goods_value;
            }
        }


        $effect = Effect::array_group_by($series_style_goods,'title');
//        foreach ($effect as $c){
//            $material[] = self::profitMargin($c);
//        }
        var_dump($effect['茶几']);exit;
        $material[] = self::profitMargin($effect['茶几']);
        var_dump($material);
        exit;
//        return $material;
    }

    public static function carpentryGoods($goods_price,$keel_cost,$pole_cost,$plasterboard_cost,$material_cost)
    {
        $material_total = [];
        foreach ($goods_price as &$one_goods_price) {
            switch ($one_goods_price) {
                case $one_goods_price['title'] == BasisDecorationService::GOODS_NAME['plasterboard']:
                    $one_goods_price['quantity'] = $plasterboard_cost['quantity'];
                    $one_goods_price['cost'] = $plasterboard_cost['cost'];
                    $one_goods_price['procurement'] = $plasterboard_cost['procurement'];
                    $plasterboard [] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == BasisDecorationService::GOODS_NAME['keel']:
                    $one_goods_price['quantity'] = $keel_cost['quantity'];
                    $one_goods_price['cost'] = $keel_cost['cost'];
                    $one_goods_price['procurement'] = $keel_cost['procurement'];
                    $keel [] = $one_goods_price;
                    break;
                case $one_goods_price['title'] == BasisDecorationService::GOODS_NAME['lead_screw']:
                    $one_goods_price['quantity'] = $pole_cost['quantity'];
                    $one_goods_price['cost'] = $pole_cost['cost'];
                    $one_goods_price['procurement'] = $pole_cost['procurement'];
                    $pole [] = $one_goods_price;
                    break;
            }
        }

        $material_total['material'][] = BasisDecorationService::profitMargin($plasterboard);
        $material_total['material'][] = BasisDecorationService::profitMargin($keel);
        $material_total['material'][] = BasisDecorationService::profitMargin($pole);
        $material_total['total_cost'][] =  round($material_cost,2);
        return $material_total;
    }

    public static function handymanGoods()
    {

    }
}
