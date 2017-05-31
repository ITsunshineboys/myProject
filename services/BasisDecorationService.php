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

}
