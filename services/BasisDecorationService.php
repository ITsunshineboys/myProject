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
     * @carpentry  装修公式
     * @param array $arr
     * @param array $quantity
     * @param array $unitPrice
     */
    public static function  formula($arr = [],$quantity= [],$unitPrice=[])
    {
//        var_dump($arr);
//        var_dump($quantity);
//        var_dump($unitPrice);
        if (!empty($arr))
        {
            $unit_price = [];
            foreach ($unitPrice as $price)
            {
                $unit_price [] = $price;
            }
            // 材料费
            $materials_expenses =  0;
            foreach ($unit_price as $k=>$v)
            {
                $materials_expenses += $v * $quantity;
            }
            //人工费
            $labor_cost = $arr['day_price'] *(ceil($quantity / $arr['day_standard']));
            //单价
            $waterway_price = ($labor_cost + $materials_expenses) / $arr['profit'];
            //价格
//            $waterway_remould_price = $arr['total_standard'] * $waterway_price;
            return $waterway_price;
        }
        echo '请输入正确的值';
        exit;
    }

    /**
     * 电线计算公式
     * @param string $str
     */
    public static function wire($str = '',$norms = '100',$dot = '10')
    {
        //电线单位换算
        if(!$str == null){
            $wire = ($str / $norms)*$dot;
            $int = (int)$wire;
        }
        return $int;
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
    public static function carpentryLabor($arr = [],$modelling = '20',$area = '4',$television_walls = '1')
    {
       if($arr){
           //人工费：（造型天数+平顶天数+【1】天）×【工人每天费用】
           $day_cost = 0;
           $modelling_length = 0;
           $flat_area = 0;
           foreach ($arr as $one)
           {
               $day_cost = $one['univalence'];
               $modelling_length = $one['day_sculpt_length'];
               $flat_area = $one['day_area'];
           }
           $artificial_fee = ($modelling / $modelling_length + $area / $flat_area + $television_walls) * $day_cost;
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

}
