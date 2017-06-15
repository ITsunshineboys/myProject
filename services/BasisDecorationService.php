<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/6 0006
 * Time: 下午 14:42
 */
namespace app\services;

class BasisDecorationService
{

    /**
     * 人工费
     * @param string $points
     * @param array $labor
     * @return float
     *
     */
    public static function  laborFormula($points = '',$labor = [])
    {
        if($points && $labor){
            //人工费：（电路总点位÷【每天做工点位】）×【工人每天费用】
            $labor_formula = ceil(($points / $labor['day_points'])) * $labor['univalence'];
        }
        return $labor_formula;
    }

    /**
     * 电线计算公式
     * @param string $str
     */
    public static function quantity($points = '',$goods = [],$crafts= '')
    {
    //        电线费用：个数1×抓取的商品价格
    //        网线费用：个数2×抓取的商品价格
    //        个数1：（强电点位×【10m】÷抓取的商品的长度）
    //        个数2：（弱电点位×【10m】÷抓取的商品的长度）
        if($goods !== null && $points !== null){
            $material = 0;
            $spool = 0;
            foreach ($crafts as $craft){
                if($craft['project_details'] == '网线' || $craft['project_details'] == '电线'){
                    $material = $craft['material'];
                }
                if($craft['project_details'] == '线管'){
                    $spool = $craft['material'];
                }
            }

            $goods_value = 0;
            $goods_price = 0;
            $spool_value = 0;
            $spool_price = 0;
            $bottom_case = 0;
            foreach ($goods as $one){
                if($one['title'] == '网线' || $one['title'] == '电线' ){
                    $goods_value = $one['value'];
                    $goods_price = $one['platform_price'];
                }
//                线管费用：个数3×抓取的商品价格
//                个数3：（电路总点位×【10m】÷抓取的商品的长度）
                if($one['title'] == '线管'){
                    $spool_value = $one['value'];;
                    $spool_price = $one['platform_price'];
                }

                if($one['title'] == '底盒'){
                    $bottom_case = $one['platform_price'];
                }
            }
            //个数计算
            $quantity = ceil($points * $material / $goods_value);
            //费用计算
            $string = $quantity * $goods_price;
            //个数计算
            $spool_quantity = ceil($points * $spool / $spool_value);
            //费用计算
            $spool = $spool_quantity * $spool_price;
            $bottom_case = $points * $bottom_case;
            //总费用
            $cost = $string + $spool + $bottom_case;
        }
     return $cost;
    }

    /**
     * 水路商品
     * @param string $points
     * @param array $goods
     * @param string $crafts
     * @return float
     */
    public static function waterwayGoods($points = '',$goods = [],$crafts= '')
    {
        if ($points && $goods)
       {
            $pvc_value = 0;
            $pvc_price = 0;
            $ppr_value = 0;
            $ppr_price = 0;
            foreach ($goods as $one)
            {
                if($one['title'] == 'PVC')
                {
                    $pvc_value = $one['value'];
                    $pvc_price = $one['platform_price'];
                }
                if($one['title'] == 'PPR')
                {
                    $ppr_value = $one['value'];
                    $ppr_price = $one['platform_price'];
                }

            }
            $ppr = 0;
            $pvc = 0;
            foreach ($crafts as $craft)
            {
                if($craft['project_details'] == 'PPR')
                {
                    $ppr = $craft['material'];
                }

                if($craft['project_details'] == 'PVC')
                {
                    $pvc =  $craft['material'];
                }
            }
           //             PPR费用：个数×抓取的商品价格
//            个数：（水路总点位×【2m】÷抓取的商品的长度）
//            PVC费用：个数×抓取的商品价格
//            个数：（水路总点位×【2m】÷抓取的商品的长度）
            $ppr_quantity = ceil($points * $ppr / $ppr_value);
            //PPR费用
            $ppr_cost = $ppr_quantity * $ppr_price;
            $pvc_quantity = ceil($points * $ppr / $pvc_value);
            $pvc_cost = $pvc_quantity * $pvc_price;
            $cost = $ppr_cost + $pvc_cost;

       }
        return $cost;
    }

    /**
     * 防水面积计算
     * @param array $arr
     * @param string $house_area
     * @param int $quantity
     * @return float
     */
    public static  function waterproofArea($arr =[],$house_area ='',$quantity = 1)
    {
        if ($arr){
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
        }
        return $total_area;
    }

    public static function waterproofGoods($points = '',$goods = [],$crafts= '')
    {
        if ($points && $goods){
            $material = 0;
            foreach ($crafts as $craft)
            {
                $material = $craft['material'];
            }
            $goods_value = 0;
            $goods_platform_price = 0;
            foreach ($goods as $one){
                $goods_value = $one['value'];
                $goods_platform_price = $one['platform_price'];
            }

//            个数：（防水总面积×【1.25】÷抓取的商品的KG）
            $quantity = ceil($points * $material /$goods_value);
//            防水涂剂费用：个数×抓取的商品价格
            $cost = $quantity * $goods_platform_price;
        }
        return $cost;
    }
    /**
     * 地面面积计算公式
     * @param array $arr
     * @return int|mixed
     */
    public static function groundArea($arr = [])
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
     * @param array $arr
     * @param string $modelling
     * @param string $area
     * @param string $television_walls
     * @return float|int
     */
    public static function carpentryLabor($modelling_day = '',$flat_day = '',$video_wall = 1,$worker_day_cost = '')
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
    public static function  carpentryModellingLength($arr = [],$coefficient_all = [],$series = '1')
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
        $length = 0;
        if($coefficient_all && $arr){
            $length = $arr['modelling_length'];
            foreach ($coefficient_all as $coefficient_one)
            {
                if( $coefficient_one['series'] == $series){
                    //造型长度 = 木作添加项 *  系数
                    $modelling_length = $coefficient_one ['modelling_length_coefficient'] * $length;
                }
            }
        }
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
    public static function carpentryModellingDay($modelling = '',$day_modelling = '',$series_all='',$style_all ='',$series =5,$style =5)
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
            if($series_find['series'] == '齐家' || $series_find['series'] == '享家'){
                $series_coefficient = $series_find['modelling_day_coefficient'];
            }elseif ($series_find['series'] == '享家+'){
                $series_coefficient = $series_find['modelling_day_coefficient'];
            }elseif ($series_find['series'] == '智家'){
                $series_coefficient = $enjoy_family * $series_find['modelling_day_coefficient'];
            }elseif ($series_find['series'] == '智家+'){
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
     * @param array $day_area
     * @param string $series_all
     * @param string $style_all
     * @param int $series
     * @param int $style
     */
    public static function flatDay($area = [],$day_area = '',$series_all = '',$style_all = '',$series = 1,$style = 5)
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
    public static function carpentryPlasterboardCost($modelling_length = '',$flat_area = '',$goods = [],$crafts = '',$video_wall = 1)
    {
        if(!empty($modelling_length) && !empty($flat_area)){
            $plasterboard = [];
            foreach ($goods as $goods_price ){
                if($goods_price['name'] == '石膏板'){
                    $plasterboard = $goods_price;
                }
            }
            $plasterboard_material = 0;
            foreach ($crafts as $craft){
                if($craft['project_details'] == '石膏板'){
                    $plasterboard_material = $craft['material'];
                }
            }
//            个数：（造型长度÷【2.5】m+平顶面积÷【2.5】m²+【1】张）
            $plasterboard_quantity = ceil($modelling_length / $plasterboard_material + $flat_area / $plasterboard_material +$video_wall);
//            石膏板费用：个数×商品价格
            $plasterboard_cost = $plasterboard_quantity * $plasterboard['platform_price'];
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
            $goods_price = [];
            foreach ($goods as $price)
            {
                if($price['name'] == '龙骨'){
                    $goods_price = $price;
                }
            }
            $plasterboard_material = 0;
            foreach ($crafts as $craft){
                if($craft['project_details'] == '龙骨'){
                    $plasterboard_material = $craft['material'];
                }
            }
//          个数=个数1+个数2
//          个数1：（造型长度÷【1.5m】）
//          个数2：（平顶面积÷【1.5m²】）
            $main_keel_quantity = ceil($modelling_length / $plasterboard_material + $flat_area /$plasterboard_material);
//          主龙骨费用：个数×商品价格
            $keel_cost = $main_keel_quantity * $goods_price['platform_price'];

        }
        return $keel_cost;
    }

    /**
     * @param string $modelling_length
     * @param string $flat_area
     * @param int $modelling_meter
     * @param int $flat_meter
     * @param array $goods
     * @return float
     */
    public static function carpentryPoleCost($modelling_length = '',$flat_area = '',$goods = [],$crafts = '')
    {
        if(!empty($modelling_length) && !empty($flat_area) && !empty($goods)){
            $goods_price = [];
            foreach ($goods as $price)
            {
                if($price['name'] == '丝杆'){
                    $goods_price = $price;
                }
            }
            $plasterboard_material = 0;
            foreach ($crafts as $craft){
                if($craft['project_details'] == '丝杆'){
                    $plasterboard_material = $craft['material'];
                }
            }

//            个数=个数1+个数2
//            个数1：（造型长度÷【2m】）
//            个数2：（平顶面积÷【2m²】
            $lead_screw_quantity = ceil($modelling_length / $plasterboard_material + $flat_area / $plasterboard_material);
//            丝杆费用：个数×抓取的商品价格
            $pole_cost = $lead_screw_quantity * $goods_price['platform_price'];
        }
        return $pole_cost;
    }
}
