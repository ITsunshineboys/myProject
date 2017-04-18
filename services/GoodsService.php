<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/6/17
 * Time: 2:26 AM
 */

namespace app\services;

use Yii;

class GoodsService
{
    public function goodsCategories($pid = 0)
    {
        $cache = Yii::$app->cache;
        $key = 'goods_category_' . $pid;
        $categories = $cache->get($key);
        if (!$categories) {
            $categories = $this->_goodsCategories($pid);
            $categories && $cache->set($key, $categories);
        }
        return $categories;
    }

    private function _goodsCategories($pid = 0)
    {
        $db = Yii::$app->db;
        $sql = "select id, title from {{%goods_category}} where pid= :pid";
        $categories = $db->createCommand($sql)->bindParam(':pid', $pid)->queryAll();
        $arr = [];
        foreach ($categories as $category) {
            $category['children'] = $this->_goodsCategories($category['id']); // 调用函数，传入参数，继续查询下级
            $arr[] = $category; // 组合数组
        }
        return $arr;
    }
}