<?php
namespace app\services;

class LogisticsService
{
    public static function Count($quantity,$logistics)
    {
        /**
         * 运费计算规则：
        A:当商品件数少于或等于默认件数
        若只有一个商品：
        运费=默认运费
        若有多个商品：（假设商品有a.b.c）
        运费a=默认运费 b=0,  c=0
         */
        if ($quantity) {

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
}