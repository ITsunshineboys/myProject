<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/6 0006
 * Time: 下午 14:42
 */
namespace app\services;

use phpDocumentor\Reflection\Types\Null_;

class BasisDecorationService
{

    /**
     * circuit_remould 电路改造(注：地面改造和顶上改造公式一样)
     * @param array $data  传入材料的数组
     * @param array $arr   传入其它的数组（如：点位）
     */
    public static function circuit_remould($data=array(),$arr=array())
    {
        if(!empty($data) && !empty($arr)){
            //材料费公式
            $materials_expenses =$data['wire']*$arr['spot']+$data['spool']*$arr['spot']+$data['bottom_case']*$arr['spot'];
            //人工费公式
            $labor_cost=$arr['labor_price']/$arr['circuit_spot'];
            //地面电路改造单价公式
            $unit_price = ($labor_cost+$materials_expenses)/$arr['profit'];
            $ground_circuit = $arr['standard_spot']*$unit_price;

            return $ground_circuit;
        }
        echo '请输入正确的值';
        exit;

    }

    public function water_remould()
    {

    }

    /**
     * @waterway_remould 水路改造
     * @param array $data
     * @param array $arr
     * @return mixed|void
     */
    public static function waterway_remould($data=array(),$arr=array())
    {
        if(!empty($data) && !empty($arr)){
            //材料费公式
            $materials_expenses =$data['joint_screw']*$arr['spot']+$data['joint_elbow']*$arr['spot']+$data['water_pipe']*$arr['spot'];
            //人工费公式
            $labor_cost=$arr['labor_price']/$arr['circuit_spot'];
            $waterway_price = ($labor_cost+$materials_expenses)/$arr['profit'];
            $waterway_remould_price = $arr['standard_spot']*$waterway_price;
            return $waterway_remould_price;
        }
        echo '请输入正确的值';
        exit;

    }

    public static function  carpentry($data=array())
    {
        if (!empty($data))
        {
            //材料费
            //人工费
            //单价
            //价格
        }
    }
}
