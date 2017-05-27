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
        if (!empty($arr))
        {
            // 材料费
            $materials_expenses =  0;
            foreach ($unitPrice as $k=>$v)
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
        }
        return $wire;
    }

    public static function pointsCalculate()
    {

    }

}
