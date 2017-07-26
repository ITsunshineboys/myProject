<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/6 0006
 * Time: 下午 14:42
 */
namespace app\services;

use app\models\Goods;
use app\models\GoodsAttr;
use yii\web\BadRequestHttpException;

class BasisDecorationService
{

    /**
     * 人工费
     * @param string $points
     * @param array $labor
     * @return float
     *
     */
    public static function  laborFormula($points,$labor)
    {
        if($points && $labor){
            //人工费：（电路总点位÷【每天做工点位】）×【工人每天费用】
            $labor_formula = ceil(($points / $labor['quantity'])) * $labor['univalence'];
        }
        return $labor_formula;
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
        if($goods && $points)
        {
            foreach ($crafts as $craft){
                if($craft['project_details'] == '网线' || $craft['project_details'] == '电线'){
                    $material = $craft['material'];
                }

                if($craft['project_details'] == '线管'){
                    $spool = $craft['material'];
                }
            }
            $goods_id = [];
            foreach ($goods as $one)
            {
                if($one['title'] == '网线' || $one['title'] == '电线' )
                {
                    $goods_price = $one['platform_price'];
                    $goods_id [] = $one['id'];
                }

                if($one['title'] == '线管')
                {
                    $spool_price = $one['platform_price'];
                    $goods_id [] = $one['id'];
                }

                if($one['title'] == '底盒')
                {
                    $bottom_case = $one['platform_price'];
                    $goods_id [] = $one['id'];
                }
            }
            $ids = GoodsAttr::findByGoodsIdUnit($goods_id);
            foreach ($ids as $one_unit)
            {
                if($one_unit['title'] == '网线' || $one_unit['title'] == '电线' )
                {
                    $goods_value = $one_unit['value'];
                }
                if($one_unit['title'] == '线管'){
                    $spool_value = $one_unit['value'];;
                }
            }

            //线路个数计算 ,线路费用计算
            $electricity['wire_quantity'] = ceil($points * $material / $goods_value);
            $electricity['wire_cost'] = $electricity['wire_quantity'] * $goods_price;

            //线管个数计算,线管费用计算
            $electricity['spool_quantity'] = ceil($points * $spool / $spool_value);
            $electricity['spool_cost'] =  $electricity['spool_quantity'] * $spool_price;

            // 底盒个数计算.底盒费用计算
            $electricity['bottom_quantity'] = $points;
            $electricity['bottom_cost'] = $points * $bottom_case;

            //总费用
            $electricity['total_cost'] = $electricity['wire_cost'] + $electricity['spool_cost'] + $electricity['bottom_cost'];
        }
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
        if ($points && $goods)
       {
            foreach ($goods as $one)
            {
                if($one['title'] == 'PVC管')
                {
                    $pvc_price = $one['platform_price'];
                    $goods_id [] = $one['id'];
                }
                if($one['title'] == 'PPR水管')
                {
                    $ppr_price = $one['platform_price'];
                    $goods_id [] = $one['id'];
                }
            }
           $ids = GoodsAttr::findByGoodsIdUnit($goods_id);
           foreach ($ids as $one_unit)
           {
               if ($one_unit['title'] == 'PPR水管')
               {
                   $ppr_value = $one_unit['value'];
               }
               if ($one_unit['title'] == 'PVC管')
               {
                    $pvc_value = $one_unit['value'];
               }
           }

            foreach ($crafts as $craft)
            {
                if($craft['project_details'] == 'PPR水管')
                {
                    $ppr = $craft['material'];
                }
                if($craft['project_details'] == 'PVC管')
                {
                    $pvc =  $craft['material'];
                }
            }
//             PPR费用：个数×抓取的商品价格
//            个数：（水路总点位×【2m】÷抓取的商品的长度）
//            PVC费用：个数×抓取的商品价格
//            个数：（水路总点位×【2m】÷抓取的商品的长度）
            $waterway['ppr_quantity'] = ceil($points * $ppr / $ppr_value);
            $waterway['pvc_quantity'] = ceil($points * $pvc / $pvc_value);

            $waterway['ppr_cost'] = $waterway['ppr_quantity'] * $ppr_price;
            //PPR费用
            $waterway['pvc_cost'] = $waterway['pvc_quantity'] * $pvc_price;
            $waterway['total_cost'] =  $waterway['ppr_cost'] + $waterway['pvc_cost'];

       }
        return $waterway;
    }

    /**
     * 防水面积计算
     * @param array $arr
     * @param string $house_area
     * @param int $quantity
     * @return float
     */
    public static  function waterproofArea($arr,$house_area,$quantity = 1)
    {
        if ($arr)
        {
            $area = [];
            $height = [];
            foreach ($arr as $one)
            {
                if($one['project_particulars'] == '厨房面积' || $one['project_particulars'] == '卫生间面积'){
                       $area = $one;
                }
                if ($one['project_particulars'] == '厨房防水' || $one['project_particulars'] == '卫生间防水'){
                        $height = $one;
                }
            }
//            厨房地面面积：【x】%×（房屋面积)
            $ground = $area['project_value'] * $house_area;
//            厨房墙面积：（厨房地面积÷厨房个数）开平方×【0.3m】×4 ×厨房个数
            $sqrt = sqrt($ground);
            $wall_space = $sqrt * $height['project_value'] * 4 * $quantity;
//            厨房防水面积：厨房地面积+厨房墙面积
            $all_area = $ground + $wall_space;
            $total_area = round($all_area,2);

            return $total_area;
        }

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
        if ($points && $goods)
        {
            foreach ($crafts as $craft)
            {
                $material = $craft['material'];
            }
            if (count($goods) == count($goods, 1)) {
                $goods_platform_price = $goods['platform_price'];
                $goods_id [] = $goods['id'];
            } else {
                foreach ($goods as $one)
                {
                    $goods_platform_price = $one['platform_price'];
                    $goods_id [] = $one['id'];
                }
            }
            $ids = GoodsAttr::findByGoodsIdUnit($goods_id);
            foreach ($ids as $one_unit)
            {
                if ($one_unit['title'] == '防水涂料')
                {
                    $goods_value = $one_unit['value'];

                }
            }

//            个数：（防水总面积×【1.25】÷抓取的商品的KG）
            $waterproof['quantity'] = ceil($points * $material /$goods_value);
//            防水涂剂费用：个数×抓取的商品价格
            $waterproof['cost'] =  $waterproof['quantity'] * $goods_platform_price;
        }
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
        if($arr)
        {
            //总面积
            $all_area ['hostToilet_area'] =  $arr ['hostToilet_area'];
            $all_area ['kitchen_area'] =  $arr ['kitchen_area'];
            $all_area ['toilet_balcony_area'] =  $arr ['toilet_balcony_area'];
            $all_area ['kitchen_balcony_area'] =  $arr ['kitchen_balcony_area'];
            $area = 0;
            foreach ($all_area as $v=>$k)
            {
                $area += $k;
            }
        }
        return $area;
    }

    /**
     * 墙面空间计算
     * @param array $arr
     * @return int|mixed
     */
    public static function wallSpace($arr = [])
    {
        $all_area = [];
        if($arr)
        {
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
            foreach ($area_all as $v=>$k)
            {
                $area += $k;
            }
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
        if(!empty($modelling_day) && !empty($flat_day) )
        {

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
    public static function  carpentryModellingLength($arr,$coefficient_all,$series=1)
    {
        switch ($series)
        {
            case  1:
                $series = '齐家';
                break;
            case  2:
                $series = '享家';
                break;
            case  3:
                $series = '享家+';
                break;
            case  4:
                $series = '智家';
                break;
            case  5:
                $series = '智家+';
                break;
            default:
                echo "请输入正确1-5的值";
        }

        $length = $arr['modelling_length'];
        foreach ($coefficient_all as $coefficient_one)
        {
            if( $coefficient_one['series'] == $series)
            {
                $series_one = $coefficient_one['modelling_length_coefficient'];
            }
        }
        //造型长度 = 木作添加项 *  系数
        $modelling_length = $series_one * $length;
        return $modelling_length;
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
    public static function carpentryModellingDay($modelling,$day_modelling,$series_all,$style_all,$series =1,$style =1)
    {
        switch ($series)
        {
            case  1:
                $series = '齐家';
                break;
            case  2:
                $series = '享家';
                break;
            case  3:
                $series = '享家+';
                break;
            case  4:
                $series = '智家';
                break;
            case  5:
                $series = '智家+';
                break;
            default:
                echo "请输入正确1-5的值";
        }

        switch ($style)
        {
            case  1:
                $style = '美式田园';
                break;
            case  2:
                $style = '欧式';
                break;
            case  3:
                $style = '日式';
                break;
            case  4:
                $style = '现代简约';
                break;
            case  5:
                $style = '中国风';
                break;
            default:
                echo "请输入正确1-5的值";
        }

        if(!empty($modelling) && !empty($day_modelling) && !empty($series_all) && !empty($style_all))
        {
            $series_find = [];
            $enjoy_family = 0;
            $wisdom_family = 0;
            foreach ($series_all as $series_one)
            {
                if($series_one['series'] == '享家+')
                {
                    $enjoy_family = $series_one['modelling_day_coefficient'];
                }elseif ($series_one['series'] == '智家')
                {
                    $wisdom_family = $series_one['modelling_day_coefficient'];
                }

                if($series_one['series'] == $series)
                {
                    $series_find = $series_one;
                }
            }
            $family = $enjoy_family * $wisdom_family;

            $style_find = [];
            foreach ($style_all as $style_one)
            {
                if($style_one['style'] == $style)
                {
                    $style_find = $style_one;
                }
            }
//            齐家，享家：【1】
//            享家+：【1.2】
//            智家：享家+×【1.2】
//            智家+：智家×【1.2】
            $series_coefficient = 0;
            if($series_find['series'] == '齐家' || $series_find['series'] == '享家')
            {
                echo 111;exit;
                $series_coefficient = $series_find['modelling_day_coefficient'];
            }
            elseif ($series_find['series'] == '享家+')
            {
                $series_coefficient = $series_find['modelling_day_coefficient'];

            }
            elseif ($series_find['series'] == '智家')
            {
                $series_coefficient = $enjoy_family * $series_find['modelling_day_coefficient'];
            }
            elseif ($series_find['series'] == '智家+')
            {
                $series_coefficient = $family *  $series_find['modelling_day_coefficient'];
            }
//            造型天数=造型长度÷【每天做造型长度】×系列系数1×风格系数1
            $modelling_day = $modelling / $day_modelling * $series_coefficient * $style_find['modelling_day_coefficient'];
        }
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
    public static function flatDay($area = [],$day_area = '',$series_all = '',$style_all = '',$series = 1,$style = 1)
    {
        switch ($series)
        {
            case  1:
                $series = '齐家';
                break;
            case  2:
                $series = '享家';
                break;
            case  3:
                $series = '享家+';
                break;
            case  4:
                $series = '智家';
                break;
            case  5:
                $series = '智家+';
                break;
            default:
                echo "请输入正确1-5的值";
        }

        switch ($style)
        {
            case  1:
                $style = '现代简约';
                break;
            case  2:
                $style = '中国风';
                break;
            case  3:
                $style = '美式田园';
                break;
            case  4:
                $style = '现代简约';
                break;
            case  5:
                $style = '中国风';
                break;
            default:
                echo "请输入正确1-5的值";
        }
        if($area && $day_area)
        {
            //平顶面积
            $flat_area = $area['flat_area'];

            $series_find = [];
            $neat_family = 0;
            $enjoy_family = 0;
            $enjoy_family_plus = 0;
            $wisdom_family = 0;
            $wisdom_family_plus = 0;

            foreach ($series_all as $series_one)
           {
               if($series_one['series'] == $series){
                   $series_find = $series_one;
               }

               if($series_one['series'] == '齐家')
               {
                   $neat_family = $series_one['flat_day_coefficient'];
               }elseif ($series_one['series'] == '享家')
               {
                   $enjoy_family = $series_one['flat_day_coefficient'];
               }elseif ($series_one['series'] == '享家+')
               {
                   $enjoy_family_plus = $series_one['flat_day_coefficient'];
               }elseif ($series_one['series'] == '智家')
               {
                   $wisdom_family = $series_one['flat_day_coefficient'];
               }elseif ($series_one['series'] == '智家+')
               {
                   $wisdom_family_plus = $series_one['flat_day_coefficient'];
               }
           }
            $style_find = [];
            foreach ($style_all as $style_one)
            {
                if($style_one['style'] == $style)
                {
                     $style_find = $style_one;
                }
            }
//            齐家：【1】
//            享家：齐家×【1.2】
//            享家+：享家×【1.2】
//            智家：享家+×【1.2】
//            智家+：智家×【1.2】
            $series_coefficient = 0;
            if($series_find['series'] == '齐家')
            {
                $series_coefficient = $series_find['flat_day_coefficient'];
            }elseif ($series_find['series'] == '享家')
            {
                $series_coefficient = $neat_family * $series_find['flat_day_coefficient'];
            }elseif ($series_find['series'] == '享家+')
            {
                $series_coefficient = $neat_family * $enjoy_family * $series_find['flat_day_coefficient'];
            }elseif ($series_find['series'] == '智家')
            {
                $series_coefficient = $neat_family * $enjoy_family * $enjoy_family_plus * $series_find['flat_day_coefficient'];
            }elseif ($series_find['series'] == '智家+')
            {
                $series_coefficient = $neat_family * $enjoy_family * $enjoy_family_plus * $wisdom_family * $series_find['flat_day_coefficient'];
            }

            //平顶天数=平顶面积÷【每天做平顶面积】×系列系数3×风格系数2
            $flat_day = $flat_area / $day_area * $series_coefficient * $style_find['flat_day_coefficient'];
        }
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
    public static function carpentryPlasterboardCost($modelling_length,$flat_area,$goods,$crafts ,$video_wall = 1)
    {
        if(!empty($modelling_length) && !empty($flat_area)){
            $plasterboard = [];
            foreach ($goods as $goods_price ){
                if($goods_price['title'] == '石膏板'){
                    $plasterboard = $goods_price;
                }else
                {
                    $plasterboard = null;
                }
            }
            foreach ($crafts as $craft)
            {
                if($craft['project_details'] == '造型长度石膏板')
                {
                    $plasterboard_material = $craft['material'];
                }
                if ($craft['project_details'] == '平顶面积石膏板')
                {
                    $area_material = $craft['material'];
                }
            }
//            个数：（造型长度÷【2.5】m+平顶面积÷【2.5】m²+【1】张）
                $plasterboard_cost['quantity'] = ceil($modelling_length / $plasterboard_material + $flat_area / $area_material +$video_wall);

//            石膏板费用：个数×商品价格
            $plasterboard_cost['cost'] = $plasterboard_cost['quantity'] * $plasterboard['platform_price'];
        }
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
    public static function carpentryKeelCost($modelling_length = '',$flat_area = '',$goods = [],$crafts = '')
    {
        if(!empty($modelling_length) &&!empty($flat_area) && !empty($goods)){
            foreach ($goods as $price)
            {
                if($price['title'] == '龙骨')
                {
                    $goods_price = $price;
                }
            }

            $plasterboard_material = 0;
            foreach ($crafts as $craft){
                if($craft['project_details'] == '龙骨'){
                    $plasterboard_material = $craft['material'];
                }
            }
            if ($modelling_length == 0 || $plasterboard_material == 0 || $flat_area == 0 )
            {
                $keel_cost['quantity'] = 0;
            }else
            {
//          个数=个数1+个数2
//          个数1：（造型长度÷【1.5m】）
//          个数2：（平顶面积÷【1.5m²】）
                $keel_cost['quantity'] = ceil($modelling_length / $plasterboard_material + $flat_area /$plasterboard_material);
            }

//          主龙骨费用：个数×商品价格
            $keel_cost['cost'] = $keel_cost['quantity'] * $goods_price['platform_price'];
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
    public static function carpentryPoleCost($modelling_length = '',$flat_area = '',$goods = [],$crafts = '')
    {
        if(!empty($modelling_length) && !empty($flat_area) && !empty($goods))
        {
            foreach ($goods as $price)
            {
                if($price['title'] == '丝杆'){
                    $goods_price = $price;
                }
            }
            $plasterboard_material = 0;
            foreach ($crafts as $craft){
                if($craft['project_details'] == '丝杆'){
                    $plasterboard_material = $craft['material'];
                }
            }
            if ($modelling_length == 0 || $plasterboard_material == 0 || $flat_area == 0 )
            {
                $pole_cost['quantity'] = 0;
            }else{
//            个数=个数1+个数2
//            个数1：（造型长度÷【2m】）
//            个数2：（平顶面积÷【2m²】
                $pole_cost['quantity'] = ceil($modelling_length / $plasterboard_material + $flat_area / $plasterboard_material);
            }
//            丝杆费用：个数×抓取的商品价格
            $pole_cost['cost'] = $pole_cost['quantity'] * $goods_price['platform_price'];
        }
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
        if ($area && $house_area && $bedroom)
        {
            if ($area <= 1){
//        卧室地面积：【z】%×（房屋面积）
                $ground_area = $area * $house_area;
//        卧室墙面积：（卧室地面积÷卧室个数）开平方×【1.8m】×4 ×卧室个数
                $wall_space_area =  sqrt($ground_area / $bedroom) * $tall * $wall * $bedroom;
//        卧室底漆面积=卧室地面积+卧室墙面积
                $total_area =    $ground_area + $wall_space_area;
            }else{
//        卧室墙面积：（卧室地面积÷卧室个数）开平方×【1.8m】×4 ×卧室个数
                $wall_space_area = sqrt($area / $bedroom)* $tall * $wall * $bedroom;
//        卧室底漆面积=卧室地面积+卧室墙面积
                $total_area =    $area + $wall_space_area;
            }
        }else{
            return '错误信息';
        }
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
        if ($area && $house_area){
            if ($area <= 1){
                //        卧室地面积：【z】%×（房屋面积）
                $ground_area =  $area * $house_area;
                //        卧室周长：（卧室地面积÷卧室个数）开平方×4×卧室个数
                $wall_space_perimeter = sqrt($ground_area / $bedroom) * $wall * $bedroom;
            }else{
                //        卧室周长：（卧室地面积÷卧室个数）开平方×4×卧室个数
                $wall_space_perimeter = sqrt($area / $bedroom) * $wall * $bedroom;
            }
        }else{
            return '错误信息';
        }
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
        if ($goods && $craft && $area)
        {
            $goods_value = GoodsAttr::findByGoodsIdUnit($goods['id']);
            $goods_value_one = '';
            foreach ($goods_value as $value)
            {
                if ($goods['title'] == '阴角线')
                {
                    if ($value['name'] == '长度' && $value['title'] =='阴角线')
                    {
                        $goods_value_one = $value['value'];
                    }elseif($value['name'] !=='材质')
                    {
                        $goods_value_one = $value['value'];
                    }
                }else
                {
                    $goods_value_one = $value['value'];
                }
            }
//        个数：（腻子面积×【0.33kg】÷抓取的商品的规格重量）
            $putty_cost ['quantity'] = ceil($area * $craft['material'] / $goods_value_one);
//        腻子费用：个数×商品价格
            $putty_cost ['cost']  =  $putty_cost['quantity'] * $goods['platform_price'];
            return $putty_cost;
        }
    }

    /**
     * 泥作面积
     * @param array $ground_area
     * @param array $craft
     * @param int $quantity
     * @param int $wall
     * @return float
     */
    public static function mudMakeArea($ground_area = [],$craft = [],$quantity = 1,$wall = 4)
    {
        if ($ground_area && $craft)
        {
            //        （卫生间地面积÷卫生间个数）开平方×【2.4m】×4 ×卫生间个数
            $sqrt= sqrt($ground_area / $quantity);
            $wall_area = $sqrt * $craft * $wall * $quantity;

        }
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
        if ($goods && $craft)
        {
            foreach ($goods as $one)
            {
               if ($one['title'] == $project)
               {
                   $goods_price = $one['platform_price'];
               }
            }
            $goods_unit = '';
            foreach ($goods_attr as $one_goods_attr)
            {
                if ($one_goods_attr['title'] == $project)
                {
                    $goods_unit = $one_goods_attr['value'];
                }
            }
            //        个数：（水泥面积×【15kg】÷抓取的商品的KG）
            $mud_make['quantity'] = ceil($area * $craft / $goods_unit);
            //        水泥费用:个数×抓取的商品价格
            $mud_make['cost'] = $mud_make['quantity'] * $goods_price;
        }
        return $mud_make;
    }

    /**
     * 杂工拆除
     * @param array $get_area
     * @param $day_area
     * @return mixed
     */
    public static function wallArea($get_area,$day_area)
    {
        if ($get_area && $day_area)
        {
            foreach ($day_area as $skill)
            {
                if ($skill['worker_kind_details'] == '拆除12墙')
                {
                    $dismantle_12 = $skill;
                }
                if ($skill['worker_kind_details'] == '拆除24墙')
                {
                    $dismantle_24 = $skill;
                }
                if ($skill['worker_kind_details'] == '新建12墙')
                {
                    $new_construction_12 = $skill;
                }
                if ($skill['worker_kind_details'] == '新建24墙')
                {
                    $new_construction_24 = $skill;
                }
                if ($skill['worker_kind_details'] == '补烂')
                {
                    $repair = $skill;
                }
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
        $day['total_day'] = ceil($day['dismantle_day'] + $day['new_construction_day'] + $day['repair_day']);

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
        if ($get_area && $craft)
        {
            $clear_12 = 0;
            $clear_24 = 0;
            foreach ($craft as $one_craft)
            {
                if ($one_craft['project_details'] == '清运12墙')
                {
                    $clear_12 = $one_craft['material'];
                }
                if ($one_craft['project_details'] == '清运24墙')
                {
                    $clear_24 = $one_craft['material'];
                }
            }
//            运到小区楼下费用=（12墙拆除面积）×【40】
            $transportation_cost['12_wall'] = $get_area['12_dismantle'] * $clear_12;
//            运到小区楼下费用=（12墙拆除面积）×【20】
            $transportation_cost['24_wall'] = $get_area['24_dismantle'] * $clear_24;
//            清运建渣费用=清运24墙费用+清运12墙费用
            $transportation_cost['cost'] = $transportation_cost['12_wall'] + $transportation_cost['24_wall'];
        }
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
        if ($get_area && $craft)
        {
            $clear_12 = 0;
            $vehicle_12_area = 0;
            $clear_24 = 0;
            $vehicle_24_area = 0;
            $vehicle_cost = 0;
            foreach ($craft as $one_craft)
            {
                if ($one_craft['project_details'] == '清运12墙')
                {
                    $clear_12 = $one_craft['material'];
                }
                if ($one_craft['project_details'] == '运渣车12墙面积')
                {
                    $vehicle_12_area = $one_craft['material'];
                }
                if ($one_craft['project_details'] == '清运24墙')
                {
                    $clear_24 = $one_craft['material'];
                }
                if ($one_craft['project_details'] == '运渣车24墙面积')
                {
                    $vehicle_24_area = $one_craft['material'];
                }
                if ($one_craft['project_details'] == '运渣车费用')
                {
                    $vehicle_cost = $one_craft['material'];
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
        }
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
        if ($get_area && $craft)
        {
            $cement_12 = 0;
            $cement_24 = 0;
            $repair = 0;
            foreach ($craft as $one_craft)
            {
                if ($one_craft['project_details'] == '12墙水泥用量')
                {
                    $cement_12 = $one_craft['material'];
                }
                if ($one_craft['project_details'] == '24墙水泥用量')
                {
                    $cement_24 = $one_craft['material'];
                }
                if ($one_craft['project_details'] == '补烂水泥用量')
                {
                    $repair = $one_craft['material'];
                }
            }
            $value = '';
            foreach ($goods_attr as $one_goods)
            {
                $value = $one_goods['value'];
            }

//            水泥用量=新建用量+补烂用量
//        新建用量=12墙新建面积×【10kg】+24墙新建面积×【15kg】+补烂长度×【2kg】
        $new_12 = $get_area['12_new_construction'] * $cement_12;
        $new_24 = $get_area['24_new_construction'] * $cement_24;
        $new_repair = $get_area['repair'] * $repair;
        $new_dosage = $new_12 + $new_24 + $new_repair;

            //        个数：（水泥用量÷抓取的商品的KG）
            $cement['quantity'] = ceil($new_dosage / $value);

        //        水泥费用：个数×抓取的商品价格
        $cement['cost'] = $cement['quantity'] * $goods['platform_price'];
        }
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
        if ($get_area && $goods && $goods_standard)
        {
            $length = 0;
            $wide = 0;
            $high = 0;
            foreach ($goods_standard as $standard)
            {
                if ($standard['name'] == '长')
                {
                    $length = $standard['value'] / 1000;
                }
                if ($standard['name'] == '宽')
                {
                    $wide = $standard['value'] / 1000;
                }
                if ($standard['name'] == '高')
                {
                    $high = $standard['value'] / 1000;
                }
            }
//        空心砖费用：个数×抓取的商品价格
//        个数：（空心砖用量）
//        空心砖用量=12墙新建面积÷长÷高+24墙新建面积÷宽÷高
            $dosage_12 = $get_area['12_new_construction'] / $length / $wide;
            $dosage_24 = $get_area['24_new_construction'] / $wide / $high;
            $brick['quantity'] = ceil($dosage_12 + $dosage_24);
            $brick['cost'] = $brick['quantity'] * $goods['platform_price'];
        }else
        {
            $brick['quantity'] = 0;
            $brick['cost'] = $brick['quantity'] * $goods['platform_price'];
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
        if ($get_area && $goods && $craft)
        {
            foreach ($craft as $one_craft)
            {
                if ($one_craft['project_details'] == '12墙河沙用量')
                {
                    $river_sand_12 = $one_craft['material'];
                }
                if ($one_craft['project_details'] == '24墙河沙用量')
                {
                    $river_sand_24 = $one_craft['material'];
                }
                if ($one_craft['project_details'] == '补烂河沙用量')
                {
                    $river_sand_repair = $one_craft['material'];
                }
            }

            $value = '';
            foreach ($goods_attr as $one_goods_attr)
            {
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
            $river_sand['cost'] =   $river_sand['quantity'] * $goods['platform_price'];
        }else
        {
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
        if ($bedroom_area && $area && $goods)
        {
            foreach ($bedroom_area as $one_bedroom)
            {
                //        卧室地面积=【z】%×（房屋面积）
                $bedroom = $one_bedroom['project_value'] * $area;
            }
//        木地板面积=卧室地面积
            $wood_floor_area =  $bedroom;

            foreach ($nature as $one_nature)
            {
                if ($one_nature['name'] == '长度' )
                {
                    $length = $one_nature['value'] / 1000;
                }
                if ($one_nature['name'] == '长度' )
                {
                    $wide = $one_nature['value'] / 1000;
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
        if ($post && $goods)
        {
            //        个数=飘窗米数
            $marble['quantity'] = $post;
            //        大理石费用：个数×抓取的商品价格
            $marble['cost'] = $marble['quantity'] * $goods['platform_price'];
        }
        return $marble;
    }

    /**
     * 价格转化
     * @param $goods
     * @return array
     */
    public static function priceConversion($goods)
    {
        if (empty($goods))
        {
           return false;
        }else
        {
            $conversion = [];
            foreach ($goods as $one_goods)
            {
                $one_goods['platform_price'] =  $one_goods['platform_price'] /100;
                $one_goods['supplier_price'] =  $one_goods['supplier_price'] /100;
                $one_goods['purchase_price_decoration_company'] =  $one_goods['purchase_price_decoration_company'] /100;
                $conversion [] = $one_goods;
            }
            return $conversion;
        }
    }

    /**
     * 条件判断
     * @param $goods
     * @param $post
     * @return array|bool
     */
    public static function judge($goods,$post)
    {
        if ($goods && $post)
        {
            foreach ($goods as $one_goods)
            {
                if ($one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style'])
                {
                    $goods_judge [] =$one_goods;
                }elseif ($one_goods['series_id'] == 0 && $one_goods['style_id'] == 0)
                {
                    $goods_judge [] =$one_goods;
                }else
                {
                    $goods_judge [] =$one_goods;
                }
            }
            return $goods_judge;
        }else
        {
            return false;
        }

    }

    /**
     * 软装配套
     * @param $goods_profit
     * @param $post
     * @param $material_property_classify
     * @return array
     */
    public static function mild($goods_profit,$post,$material_property_classify)
    {
        if (!empty($goods_profit))
        {
            if ($post['hall'] <= 1)
            {
                $hall = 1;
            }else
            {
                $hall = $post['hall'] -1;
            }
            $curtain = [];
            $socket = [];
            $light = [];
            $switch = [];
            foreach ($goods_profit as $one_goods)
            {
                foreach ($material_property_classify as $quantity)
                {
                    if ($one_goods['title'] == '开关' && $quantity['material'] == '开关' )
                    {
                        $one_goods['show_cost'] = $one_goods['platform_price'] * $quantity['quantity'];
                        $one_goods['show_quantity'] = $quantity['quantity'];
                        $switch = $one_goods;
                    }

                    if ($one_goods['title'] == '插座' && $quantity['material'] == '插座' )
                    {
                        $one_goods['show_cost'] = $one_goods['platform_price'] * $quantity['quantity'];
                        $one_goods['show_quantity'] = $quantity['quantity'];
                        $socket = $one_goods;
                    }
                    if ($one_goods['title'] == '灯具')
                    {
                        $quantity = $post['bedroom'] + $hall + $post['kitchen'];
                        $one_goods['show_cost'] = $one_goods['platform_price'] * $quantity;
                        $one_goods['show_quantity'] = $quantity;
                        $light = $one_goods;
                    }

                    if ($one_goods['title'] == '窗帘')
                    {
                        $quantity = $post['bedroom'] + $hall;
                        $one_goods['show_cost'] = $one_goods['platform_price'] * $quantity;
                        $one_goods['show_quantity'] = $quantity;
                        $curtain = $one_goods;
                    }
                }
            }
            $goods_price [] = $switch;
            $goods_price [] = $socket;
            $goods_price [] = $light;
            $goods_price [] = $curtain;
            return $goods_price;
        }else
        {
            return $goods_profit;
        }
    }

    /**
     * 泥作规格
     * @param $goods
     * @return mixed
     */
    public static function mudMakeMaterial($goods)
    {
        if ($goods)
        {
            $property = [];
            $id = [];
            foreach ($goods as $one_goods)
            {
                $id[] = $one_goods['id'];
            }
            $goods_property = GoodsAttr::findByGoodsIdUnit($id);
            foreach ($goods_property as $one_goods_property)
            {
                if ($one_goods_property['title'] == '河沙')
                {
                    $property['river_sand']['title'] = '河沙';
                    $property['river_sand']['value'] = $one_goods_property['value'];
                }
                if ($one_goods_property['title'] == '水泥')
                {
                    $property['concrete']['title'] = '水泥';
                    $property['concrete']['value'] = $one_goods_property['value'];
                }
                if ($one_goods_property['title'] == '自流平')
                {
                    $property['self_leveling']['title'] = '自流平';
                    $property['self_leveling']['value'] = $one_goods_property['value'];
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
        if ($goods)
        {
            if (count($goods) == count($goods, 1))
            {
                return $goods;
            } else
            {
                foreach($goods as $v)
                {
                    $r[$v['title']][$v['profit_rate']] = $v;
                    $max = max($v['profit_rate'],$r[$v['title']][$v['profit_rate']]);
                }
                return $max;
            }
        }
    }

    /**
     * 乳胶漆风格和系列
     * @param $goods_price
     * @param $crafts
     * @param $post
     * @return mixed
     */
    public static function coatingSeriesAndStyle($goods_price,$crafts,$post)
    {
        if ($goods_price && $crafts)
        {
            foreach ($goods_price as $goods)
            {
                if ($goods['title'] == '腻子' && $goods['series_id'] == $post['series'])
                {
                    $all_goods[] = $goods;
                    $goods_max = self::profitMargin($goods);
                    $goods_all ['putty'] = $goods_max;
                }

                if ($goods['title'] == '乳胶漆底漆' && $goods['series_id'] == $post['series'])
                {
                    $all_goods[] = $goods;
                    $goods_max = self::profitMargin($goods);
                    $goods_all ['primer'] = $goods_max;
                }

                if ($goods['title'] == '乳胶漆面漆' && $goods['series_id'] == $post['series'])
                {
                    $all_goods[] = $goods;
                    $goods_max = self::profitMargin($goods);
                    $goods_all ['finishing_coat'] = $goods_max;
                }

                if ($goods['title'] == '阴角线' && $goods['style_id'] == $post['style'])
                {
                    $all_goods[] = $goods;
                    $goods_max = self::profitMargin($goods);
                    $goods_all ['concave_line'] = $goods_max;
                }

                if ($goods['title'] == '石膏粉' && $goods['style_id'] == 0 && $goods['series_id'] == 0)
                {
                    $all_goods[] = $goods;
                    $goods_max = self::profitMargin($goods);
                    $goods_all ['gypsum_powder'] = $goods_max;
                }
            }
            return $goods_all;
        }
    }

    /**
     * 固定家具系列和风格
     * @param $goods
     * @param $post
     * @return array
     */
    public static function fixationFurnitureSeriesStyle($goods,$post,$material_one)
    {
        if ($goods)
        {
            $material = [];
            foreach ($goods as $one_goods)
            {
                if ($one_goods['title'] == '衣柜' && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style'])
                {
                    $one_goods['show_quantity'] = $post['bedroom'];
                    $one_goods['show_cost'] = $one_goods['platform_price'] * $one_goods['show_quantity'];
                    $chest [] = $one_goods;
                }
                if ($one_goods['title'] == '酒柜' && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style'])
                {
                    $one_goods['show_quantity'] = $material_one['酒柜']['quantity'];
                    $one_goods['show_cost'] = $one_goods['platform_price'] * $one_goods['show_quantity'];
                    $wine_cabinet []  = $one_goods;
                }else
                {
                    $wine_cabinet = false;
                }
                if ($one_goods['title'] == '橱柜' && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style'])
                {
                    $one_goods['show_quantity'] = $material_one['橱柜']['quantity'];
                    $one_goods['show_cost'] = $one_goods['platform_price'] * $one_goods['show_quantity'];
                    $cabinet [] = $one_goods;
                }
                if ($one_goods['title'] == '吊柜' && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style'])
                {
                    $one_goods['show_quantity'] = $material_one['吊柜']['quantity'];
                    $one_goods['show_cost'] = $one_goods['platform_price'] * $one_goods['show_quantity'];
                    $wall_cupboard [] = $one_goods;
                }else
                {
                    $wall_cupboard = false;
                }
                if ($one_goods['title'] == '鞋柜' && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style'])
                {
                    $one_goods['show_quantity'] = $material_one['鞋柜']['quantity'];
                    $one_goods['show_cost'] = $one_goods['platform_price'] * $one_goods['show_quantity'];
                    $shoe_cabinet [] = $one_goods;
                }else
                {
                    $shoe_cabinet = false;
                }
                if ($one_goods['title'] == '木门' && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style'])
                {
                    $one_goods['show_quantity'] = $post['bedroom'];
                    $one_goods['show_cost'] = $one_goods['platform_price'] * $one_goods['show_quantity'];
                    $timber_door [] = $one_goods;
                }
            }

            $material[] = self::profitMargin($chest);
            $material[] = self::profitMargin($wine_cabinet);
            $material[] = self::profitMargin($cabinet);
            $material[] = self::profitMargin($wall_cupboard);
            $material[] = self::profitMargin($shoe_cabinet);
            $material[] = self::profitMargin($timber_door);

            return $material;
        }
    }

    /**
     * 强电点位
     * @param $points
     * @param $post
     * @return mixed
     */
    public static function strongCurrentPoints($points,$post)
    {
        if ($points)
        {
            foreach ($points as $one_points)
            {
                if ($one_points['place'] == '客餐厅')
                {
                    if ($post['hall'] != 2)
                    {
                        $hall = $one_points['points_total'] * $post['hall'];
                    }else
                    {
                        $hall = $one_points['points_total'] + 2;
                    }
                }
                if ($one_points['place'] == '卧室')
                {
                    $bedroom = $one_points['points_total'] * $post['bedroom'];
                }
                if ($one_points['place'] == '厨房')
                {
                    $kitchen = $one_points['points_total'] * $post['kitchen'];
                }
                if ($one_points['place'] == '卫生间')
                {
                    $toilet = $one_points['points_total'] * $post['toilet'];
                }
                if ($one_points['place'] == '入户')
                {
                    $register = $one_points['points_total'];
                }
                if ($one_points['place'] == '客厅阳台')
                {
                    $balcony = $one_points['points_total'];
                }
                if ($one_points['place'] == '客厅卧室过道')
                {
                    $passage = $one_points['points_total'];
                }
                if ($one_points['place'] == '生活阳台')
                {
                    $live_balcony= $one_points['points_total'];
                }
            }
            $strong_current_points = $hall+$bedroom+$kitchen+$toilet+$register+$balcony+$passage+$live_balcony;

            return $strong_current_points;
        }
    }

    /**
     * 墙砖面积计算
     * @param $id
     * @return bool|float|int
     */
    public static function wallBrickAttr($id)
    {
        if ($id)
        {
            $goods_attr = GoodsAttr::findByGoodsIdUnit($id);
            foreach ($goods_attr as $one_goods_attr)
            {
                if ($one_goods_attr['name'] == '长度')
                {
                    $length = $one_goods_attr['value'] / 1000;
                }
                if ($one_goods_attr['name'] == '宽度')
                {
                    $wide = $one_goods_attr['value'] / 1000;
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
        if ($goods)
        {
            $id = [];
            $goods_attr_details = [];
            foreach ($goods as $one_goods)
            {
                $id [] = $one_goods['id'];
            }
            $goods_attr = GoodsAttr::findByGoodsIdUnit($id);

            foreach ($goods_attr as $one_goods_attr)
            {
                if ($one_goods_attr['value'] == '厨房')
                {
                    $kitchen_id = $one_goods_attr['goods_id'];
                    $goods_attr_details['kitchen']['id'] = $one_goods_attr['goods_id'];
                    $goods_attr_details['kitchen']['name'] = $one_goods_attr['value'];
                }
                if ($one_goods_attr['value'] == '卫生间')
                {
                    $toilet_id = $one_goods_attr['goods_id'];
                    $goods_attr_details['toilet']['id'] = $one_goods_attr['goods_id'];
                    $goods_attr_details['toilet']['name'] = $one_goods_attr['value'];
                }
                if ($one_goods_attr['value'] == '客厅')
                {
                    $hall_id = $one_goods_attr['goods_id'];
                    $goods_attr_details['hall']['id'] = $one_goods_attr['goods_id'];
                    $goods_attr_details['hall']['name'] = $one_goods_attr['value'];
                }
            }
            foreach ($goods_attr as  $goods_area)
            {
                if ($goods_area['goods_id'] == $kitchen_id)
                {
                    if ($goods_area['name'] == '长度')
                    {
                        $kitchen_length = $goods_area['value'] / 1000;
                    }
                    if ($goods_area['name'] == '宽度')
                    {
                        $kitchen_wide = $goods_area['value'] / 1000;
                    }
                }
                if ($goods_area['goods_id'] == $toilet_id)
                {
                    if ($goods_area['name'] == '长度')
                    {
                        $toilet_length = $goods_area['value'] / 1000;
                    }
                    if ($goods_area['name'] == '宽度')
                    {
                        $toilet_wide = $goods_area['value'] / 1000;
                    }
                }
                if ($goods_area['goods_id'] == $hall_id)
                {
                    if ($goods_area['name'] == '长度')
                    {
                        $hall_length = $goods_area['value'] / 1000;
                    }
                    if ($goods_area['name'] == '宽度')
                    {
                        $hall_wide = $goods_area['value'] / 1000;
                    }
                }
            }
            foreach ($goods as $goods_price)
            {
                if ($goods_price['id'] == $kitchen_id)
                {
                    $goods_attr_details['kitchen']['price'] =  $goods_price['platform_price'];
                }
                if ($goods_price['id'] == $toilet_id)
                {
                    $goods_attr_details['toilet']['price'] =  $goods_price['platform_price'];
                }
                if ($goods_price['id'] == $hall_id)
                {
                    $goods_attr_details['hall']['price'] =  $goods_price['platform_price'];
                }
            }
            $goods_attr_details['kitchen']['area'] = $kitchen_length * $kitchen_wide;
            $goods_attr_details['toilet']['area'] = $toilet_length * $toilet_wide;
            $goods_attr_details['hall']['area'] = $hall_length * $hall_wide;

          return $goods_attr_details;
        }else
        {
            return false;
        }
    }

    /**
     * 主材系列和风格
     * @param $goods
     * @param $add
     * @param $post
     * @param $area
     * @return array
     */
    public static function principalMaterialSeriesStyle($goods,$add,$post,$area)
    {
        if ($goods)
        {
            $material = [];
            foreach ($goods as $one_goods)
            {
                if ($one_goods['title'] == '木地板' && $one_goods['series_id'] == $post['series'])
                {
                    $bedroom_area = $post['area'] * $area['project_value'];
                    $goods_area = GoodsAttr::findByGoodsIdUnit($one_goods['id']);
                    foreach ($goods_area as $one_goods_area)
                    {
                        if ($one_goods_area['name'] == '长度')
                        {
                            $length = $one_goods_area['value'] / 1000;
                        }
                        if ($one_goods_area['name'] == '宽度')
                        {
                            $breadth = $one_goods_area['value'] / 1000;
                        }
                    }
                    $area = $length * $breadth;
                    $one_goods['quantity'] = ceil($bedroom_area / $area);
                    $one_goods['cost'] = $one_goods['platform_price'] * $one_goods['quantity'];
                    $wood_floor [] = $one_goods;
                }
                if ($one_goods['title'] == '铝合金门窗' && $one_goods['series_id'] == $post['series'])
                {
                    $one_goods['quantity'] = $post['toilet'] + $post['kitchen'];
                    $one_goods['cost'] = $one_goods['platform_price'] * $one_goods['quantity'];
                    $aluminium_alloy_door [] = $one_goods;
                }

                if ($one_goods['title'] == '界面剂')
                {
                    $one_goods['quantity'] = $add['界面剂']['quantity'];
                    $one_goods['cost'] = $one_goods['platform_price'] * $one_goods['quantity'];
                    $adhesion_agent [] = $one_goods;
                }

                if ($one_goods['title'] == '铝扣板' && $one_goods['series_id'] == $post['series'])
                {
                    $one_goods['quantity'] = $add['铝扣板']['quantity'];
                    $one_goods['cost'] = $one_goods['platform_price'] * $one_goods['quantity'];
                    $aluminous_gusset_plate [] = $one_goods;
                }

                if ($one_goods['title'] == '浴霸' && $one_goods['series_id'] == $post['series'])
                {
                    $one_goods['quantity'] = $add['浴霸']['quantity'];
                    $one_goods['cost'] = $one_goods['platform_price'] * $one_goods['quantity'];
                    $bath_heater [] = $one_goods;
                }
                if ($one_goods['title'] == '换气扇' && $one_goods['series_id'] == $post['series'])
                {
                    $one_goods['quantity'] = $add['换气扇']['quantity'];
                    $one_goods['cost'] = $one_goods['platform_price'] * $one_goods['quantity'];
                    $ventilator [] = $one_goods;
                }
                if ($one_goods['title'] == '吸顶灯' && $one_goods['series_id'] == $post['series'])
                {
                    $one_goods['quantity'] = $add['吸顶灯']['quantity'];
                    $one_goods['cost'] = $one_goods['platform_price'] * $one_goods['quantity'];
                    $ceiling_lamp [] = $one_goods;
                }
                if ($one_goods['title'] == '防盗门' && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style'])
                {
                    $one_goods['quantity'] = $add['防盗门']['quantity'];
                    $one_goods['cost'] = $one_goods['platform_price'] * $one_goods['quantity'];
                    $security [] = $one_goods;
                }
                if ($one_goods['title'] == '水龙头' && $one_goods['series_id'] == $post['series'] )
                {
                    $one_goods['quantity'] = $post['toilet'] + $post['kitchen'];
                    $one_goods['cost'] = $one_goods['platform_price'] * $one_goods['quantity'];
                    $faucet [] = $one_goods;
                }
                if ($one_goods['title'] == '人造大理石')
                {
                    $one_goods['quantity'] = $post['window'];
                    $one_goods['cost'] = $one_goods['platform_price'] * $one_goods['quantity'];
                    $marble [] = $one_goods;
                }
            }
            $material []  = self::profitMargin($wood_floor);
            $material []  = self::profitMargin($aluminium_alloy_door);
            $material []  = self::profitMargin($adhesion_agent);
            $material []  = self::profitMargin($aluminous_gusset_plate);
            $material []  = self::profitMargin($bath_heater);
            $material []  = self::profitMargin($ventilator);
            $material []  = self::profitMargin($ceiling_lamp);
            $material []  = self::profitMargin($security);
            $material []  = self::profitMargin($faucet);
            $material []  = self::profitMargin($marble);
        }

        return $material;
    }

    /**
     * 移动家具系列和风格
     * @param $goods
     * @param $add
     * @param $post
     * @return array
     */
    public static function moveFurnitureSeriesStyle($goods,$add,$post)
    {
        if ($goods)
        {
            $material = [];
            $hall = '';
            if ($post['hall'] < 2)
            {
                $hall = 1;
            }else
            {
                $hall = $post['hall'] - 1 ;
            }

            foreach ($goods as $one_goods)
            {
                if ($one_goods['title'] == '沙发' && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style'])
                {
                    $one_goods['show_quantity'] = $hall;
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $sofa [] = $one_goods;
                }
                if ($one_goods['title'] == '茶几' && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style'])
                {
                    $one_goods['show_quantity'] = $add['茶几']['quantity'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $end_table [] = $one_goods;
                }
                if ($one_goods['title'] == '电视柜' && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style'])
                {
                    $one_goods['show_quantity'] = $add['电视柜']['quantity'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $tv_bench [] = $one_goods;
                }
                if ($one_goods['title'] == '餐桌' && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style'])
                {
                    $one_goods['show_quantity'] = $add['餐桌']['quantity'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $dining_table [] = $one_goods;
                }
                if ($one_goods['title'] == '床' && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style'])
                {
                    $one_goods['show_quantity'] = $post['bedroom'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $bed [] = $one_goods;
                }
                if ($one_goods['title'] == '床头柜' && $one_goods['series_id'] == $post['series'] && $one_goods['style_id'] == $post['style'])
                {
                    $one_goods['show_quantity'] = $post['bedroom'] * 2;
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $night_table [] = $one_goods;
                }
            }
            $material [] = self::profitMargin($sofa);
            $material [] = self::profitMargin($end_table);
            $material [] = self::profitMargin($tv_bench);
            $material [] = self::profitMargin($dining_table);
            $material [] = self::profitMargin($bed);
            $material [] = self::profitMargin($night_table);
        }
        return $material;
    }

    /**
     * 家电配套系列和风格
     * @param $goods
     * @param $add
     * @param $post
     * @return array
     */
    public static function appliancesAssortSeriesStyle($goods,$add,$post)
    {
        if ($goods)
        {
            $material = [];
            $hall = '';
            if ($post['hall'] <= 1)
            {
                $hall = 1;
            }else
            {
                $hall = $post['hall'] - 1;
            }

            foreach ($goods as $one_goods)
            {
                if ($one_goods['title'] == '油烟机' && $one_goods['series_id'] == $post['series'])
                {
                    $one_goods['show_quantity'] = $post['kitchen'];
                    $one_goods['show_cost'] = $one_goods['platform_price'] * $one_goods['show_quantity'];
                    $kitchen_ventilator [] = $one_goods;
                }

                if ($one_goods['title'] == '灶具' && $one_goods['series_id'] == $post['series'])
                {
                    $one_goods['show_quantity'] = $post['kitchen'];
                    $one_goods['show_cost'] = $one_goods['platform_price'] * $one_goods['show_quantity'];
                    $stove [] = $one_goods;
                }

                if ($one_goods['title'] == '热水器' && $one_goods['series_id'] == $post['series'])
                {
                    $one_goods['show_quantity'] = $add['热水器']['quantity'];
                    $one_goods['show_cost'] = $one_goods['platform_price'] * $one_goods['show_quantity'];
                    $water_heater [] = $one_goods;
                }
                if ($one_goods['title'] == '冰箱' && $one_goods['series_id'] == $post['series'])
                {
                    $one_goods['show_quantity'] = $add['冰箱']['quantity'];
                    $one_goods['show_cost'] = $one_goods['platform_price'] * $one_goods['show_quantity'];
                    $refrigerator [] = $one_goods;
                }
                if ($one_goods['title'] == '洗衣机' && $one_goods['series_id'] == $post['series'])
                {
                    $one_goods['show_quantity'] = $add['洗衣机']['quantity'];
                    $one_goods['show_cost'] = $one_goods['platform_price'] * $one_goods['show_quantity'];
                    $washer [] = $one_goods;
                }
                if ($one_goods['title'] == '电视' && $one_goods['series_id'] == $post['series'])
                {
                    $one_goods['show_quantity'] = $add['电视']['quantity'];
                    $one_goods['show_cost'] = $one_goods['platform_price'] * $one_goods['show_quantity'];
                    $tv [] = $one_goods;
                }
                if ($one_goods['title'] == '立柜式空调' && $one_goods['series_id'] == $post['series'])
                {
                    if ($post['series'] < 3)
                    {
                        $one_goods['show_quantity'] = $hall;
                        $one_goods['show_cost'] = $one_goods['platform_price'] * $one_goods['show_quantity'];
                        $hall_air_conditioner [] = $one_goods;
                    }else
                    {
                        $hall_air_conditioner = null;
                    }
                }
                if ($one_goods['title'] == '挂壁式空调' && $one_goods['series_id'] == $post['series'])
                {
                    if ($post['series'] <= 2)
                    {
                        $one_goods['show_quantity'] = $post['bedroom'];
                        $one_goods['show_cost'] = $one_goods['platform_price'] * $one_goods['show_quantity'];
                        $bedroom_air_conditioner [] = $one_goods;
                    }else
                    {
                        $bedroom_air_conditioner = null;
                    }
                }
                if ($one_goods['title'] == '中央空调' && $one_goods['series_id'] == $post['series'])
                {
                    $one_goods['show_quantity'] = $add['中央空调']['quantity'];
                    $one_goods['show_cost'] = $one_goods['platform_price'] * $one_goods['show_quantity'];
                    $central_air_conditioning [] = $one_goods;
                }else
                {
                    $central_air_conditioning = null;
                }
            }
            $material [] = self::profitMargin($kitchen_ventilator);
            $material [] = self::profitMargin($stove);
            $material [] = self::profitMargin($water_heater);
            $material [] = self::profitMargin($refrigerator);
            $material [] = self::profitMargin($washer);
            $material [] = self::profitMargin($tv);
            $material [] = self::profitMargin($hall_air_conditioner);
            $material [] = self::profitMargin($bedroom_air_conditioner);
            $material [] = self::profitMargin($central_air_conditioning);
        }
        return $material;
    }

    public static function lifeAssortSeriesStyle($goods,$add,$post)
    {
        if ($goods)
        {
            $toilet = '';
            if ($post['toilet'] <= 2)
            {
                $toilet = 1;
            }else
            {
                $toilet = $post['toilet'] - 1;
            }
            $material = [];
            foreach ($goods as $one_goods)
            {
                if ($one_goods['title'] == '水槽')
                {
                    $one_goods['show_quantity'] = $add['水槽']['quantity'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $water_channel [] = $one_goods;
                }
                if ($one_goods['title'] == '刀具')
                {
                    $one_goods['show_quantity'] = $add['刀具']['quantity'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $cutter [] = $one_goods;
                }
                if ($one_goods['title'] == '消毒柜')
                {
                    $one_goods['show_quantity'] = $add['消毒柜']['quantity'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $disinfection_cabinet [] = $one_goods;
                }
                if ($one_goods['title'] == '不锈钢洗菜盆')
                {
                    $one_goods['show_quantity'] = $add['不锈钢洗菜盆']['quantity'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $lavatory [] = $one_goods;
                }
                if ($one_goods['title'] == '床垫' && $one_goods['series_id'] == $post['series'])
                {
                    $one_goods['show_quantity'] = $post['bedroom'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $mattress [] = $one_goods;
                }
                if ($one_goods['title'] == '马桶刷')
                {
                    $one_goods['show_quantity'] = $add['马桶刷']['quantity'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $mattress [] = $one_goods;
                }
                if ($one_goods['title'] == '洗衣机地漏')
                {
                    $one_goods['show_quantity'] = $add['洗衣机地漏']['quantity'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $floor_drain [] = $one_goods;
                }
                if ($one_goods['title'] == '拖布池龙头')
                {
                    $one_goods['show_quantity'] = $add['拖布池龙头']['quantity'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $bibcock [] = $one_goods;
                }
                if ($one_goods['title'] == '拖布池')
                {
                    $one_goods['show_quantity'] = $add['拖布池']['quantity'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $mop [] = $one_goods;
                }
                if ($one_goods['title'] == '高压管')
                {
                    $one_goods['show_quantity'] = $add['高压管']['quantity'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $high_voltage_tube [] = $one_goods;
                }
                if ($one_goods['title'] == '三角阀')
                {
                    $one_goods['show_quantity'] = $add['三角阀']['quantity'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $triangular_valve [] = $one_goods;
                }
                if ($one_goods['title'] == '淋浴隔断')
                {
                    $one_goods['show_quantity'] = $post['toilet'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $cut_off [] = $one_goods;
                }
                if ($one_goods['title'] == '花洒套装' && $one_goods['series_id'] == $post['series'])
                {
                    $one_goods['show_quantity'] = $post['toilet'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $sprinkler [] = $one_goods;
                }
                if ($one_goods['title'] == '浴柜' && $one_goods['series_id'] == $post['series'])
                {
                    $one_goods['show_quantity'] = $post['toilet'];
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $bath_cabinet [] = $one_goods;
                }
                if ($one_goods['title'] == '蹲便器' && $one_goods['series_id'] == $post['series'])
                {
                    if ($post['toilet'] < 2)
                    {
                        $squatting_pan = null;
                    }else
                    {
                        $one_goods['show_quantity'] = 1;
                        $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                        $squatting_pan [] = $one_goods;
                    }

                }
                if ($one_goods['title'] == '马桶' && $one_goods['series_id'] == $post['series'])
                {
                    $one_goods['show_quantity'] = $toilet;
                    $one_goods['show_cost'] =  $one_goods['show_quantity'] * $one_goods['platform_price'];
                    $closestool [] = $one_goods;
                }
            }
            $material [] = self::profitMargin($water_channel);
            $material [] = self::profitMargin($cutter);
            $material [] = self::profitMargin($disinfection_cabinet);
            $material [] = self::profitMargin($lavatory);
            $material [] = self::profitMargin($mattress);
            $material [] = self::profitMargin($floor_drain);
            $material [] = self::profitMargin($bibcock);
            $material [] = self::profitMargin($mop);
            $material [] = self::profitMargin($high_voltage_tube);
            $material [] = self::profitMargin($triangular_valve);
            $material [] = self::profitMargin($cut_off);
            $material [] = self::profitMargin($sprinkler);
            $material [] = self::profitMargin($bath_cabinet);
            $material [] = self::profitMargin($squatting_pan);
            $material [] = self::profitMargin($closestool);
        }
        return $material;
    }
}
