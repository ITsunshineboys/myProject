<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class Goods extends ActiveRecord
{
    const GOODS_DETAIL_URL_PREFIX = 'mall/goods?id=';
    const ORDERBY_SEPARATOR = ':';
    const PAGE_SIZE_DEFAULT = 12;

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * Get goods list by category id
     *
     * @param  int   $categoryId category id
     * @param  array $select     select fields default all fields
     * @param  int   $page       page number default 1
     * @param  int   $size       page size default 12
     * @param  array $orderBy    order by fields default sold_number desc
     * @return array
     */
    public static function findByCategoryId($categoryId, $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = ['sold_number' => SORT_DESC])
    {
        $offset = ($page - 1) * $size;
        $goodsList = self::find()
            ->select($select)
            ->where(['category_id' => $categoryId])
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();
        if (!$select
            || in_array('platform_price', $select)
            || in_array('supplier_price', $select)
            || in_array('market_price', $select)
            || in_array('purchase_price', $select)
        ) {
            foreach ($goodsList as &$goods) {
                isset($goods['platform_price']) && $goods['platform_price'] = $goods['platform_price'] / 100;
            }
        }
        return $goodsList;
    }
}