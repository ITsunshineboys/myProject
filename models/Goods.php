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
     * @param  array $orderBy    order by fields default sold_number desc
     * @return array
     */
    public static function findByCategoryId($categoryId, $select = [], $orderBy = ['sold_number' => SORT_DESC])
    {
        $goodsList = self::find()->select($select)->where(['category_id' => $categoryId])->asArray()->orderBy($orderBy)->all();
        if (!$select || in_array('platform_price', $select)) {
            foreach ($goodsList as &$goods) {
                $goods['platform_price'] = $goods['platform_price'] / 100;
            }
        }
        return $goodsList;
    }
}