<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class GoodsCategory extends ActiveRecord
{
    const CACHE_TOP = 'goods_categories_top';

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_category';
    }

    /**
     * Get direct goods categories by pid
     *
     * @param array $select           category fields default empty
     * @param int   $parentCategoryId parent category id default 0
     * @return array
     */
    public static function categoriesByPid($select = [], $parentCategoryId = 0)
    {
        $key = self::CACHE_TOP;
        $cache = Yii::$app->cache;
        $categories = $cache->get($key);
        if (!$categories) {
            $categories = self::find()->select($select)->where(['pid' => $parentCategoryId])->asArray()->all();
            if ($categories) {
                $cache->set($key, $categories);
            }
        }

        return $categories;
    }

    /**
     * Get goods categories(including subcategories) by pid
     *
     * @param int $pid parent id
     * @return array goods categories
     */
    public function categories($pid = 0)
    {
        $cache = Yii::$app->cache;
        $key = 'goods_category_' . $pid;
        $categories = $cache->get($key);
        if (!$categories) {
            $categories = $this->_categories($pid);
            $categories && $cache->set($key, $categories);
        }
        return $categories;
    }

    /**
     * Get goods categories by pid
     *
     * @access private
     * @param int $pid parent id
     * @return array goods categories
     */
    private function _categories($pid = 0)
    {
        $db = Yii::$app->db;
        $sql = "select id, title from {{%goods_category}} where pid= :pid";
        $categories = $db->createCommand($sql)->bindParam(':pid', $pid)->queryAll();
        $arr = [];
        foreach ($categories as $category) {
            $category['children'] = $this->_categories($category['id']); // 调用函数，传入参数，继续查询下级
            $arr[] = $category; // 组合数组
        }
        return $arr;
    }
}