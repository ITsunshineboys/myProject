<?php
namespace app\services;

class LogisticsService
{
    const PRICE_UNITS = 100;
    private $_goods;
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
        return $this->quantity();
    }

    public function quantity()
    {
        foreach ($this->_goods as $one_goods){
            foreach ($this->_logistics as $one_logistics){
                if ($one_goods['goods_quantity'] < $one_logistics['delivery_number_default']){
                    $min_freight = $this->minQuantity($one_goods,$one_logistics);
                } elseif($one_goods['goods_quantity'] > $one_logistics['delivery_number_default']) {
                    $max_freight = $this->maxQuantity($one_goods,$one_logistics);
                }
            }
        }

        return $min_freight;
    }

    public function minQuantity($one_goods,$one_logistics)
    {
        $goods = $one_goods;
        $goods['freight'] = $one_logistics['delivery_cost_default'] / self::PRICE_UNITS;
        return $goods;
    }

    /**
     * 运费计算规则：
     *   A:当商品件数少于或等于默认件数
     *   若只有一个商品：
     *   运费=默认运费
     *   若有多个商品：（假设商品有a.b.c）
     *   运费a=默认运费 b=0,  c=0
     */
    public function maxQuantity($one_goods,$one_logistics)
    {
        $goods = $one_goods;
       return $goods;
    }
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