<?php
namespace app\services;

class LogisticsService
{
    /**
     * 单位
     */
    const PRICE_UNITS = 100;

    /**
     * @var array : goods array
     */
    private $_goods;

    /**
     * @var array : logistics array
     */
    private $_logistics;

    /**
     * LogisticsService constructor.
     * @param array :$logistics
     * @param array :$goods
     */
    public function __construct($logistics,$goods)
    {
        if (!$logistics || !$goods) {
            return false;
        }
        $this->_goods     = $goods;
        $this->_logistics = $logistics;
    }


    public function minQuantity()
    {
        if (array_key_exists(1,$this->_logistics)){
            foreach ($this->_goods as &$one_goods){
                foreach ($this->_logistics as $one_logistics){
                    if ($one_goods['goods_quantity'] < $one_logistics['delivery_number_default']){
                        $one_goods['freight'] = $one_logistics['delivery_cost_default'] / self::PRICE_UNITS;
                    }
                }
            }
        } else {
            foreach ($this->_goods as &$one_goods){
                if ($one_goods['goods_quantity'] < $this->_logistics['0']['delivery_number_default']){
                    $one_goods['freight'] = $this->_logistics['0']['delivery_cost_default'] / self::PRICE_UNITS;
                }
            }
        }

        return $this->_goods;
    }

    /**
     * 运费计算规则：
     *   A:当商品件数少于或等于默认件数
     *   若只有一个商品：
     *   运费=默认运费
     *   若有多个商品：（假设商品有a.b.c）
     *   运费a=默认运费 b=0,  c=0
     */
    /**
    B：
    当商品件数大于默认件数
    若只有一个商品：
    运费=默认运费+（商品数量-默认件数）*增加件运费
    若有多个商品：
    计算总数量；
    运费=默认运费+（商品总数量-默认件数）*增加件运费
    （假设有商品a，b，c）
    则平均分配运费
     */
}