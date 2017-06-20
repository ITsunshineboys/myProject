<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/27/17
 * Time: 2:08 PM
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class BrandCategory extends ActiveRecord
{
    const SCENARIO_ADD = 'add';

    /**
     * Get category ids by brand id
     *
     * @param  int $brandId brand id
     * @return array
     */
    public static function categoryIdsByBrandId($brandId)
    {
        $brandId = (int)$brandId;
        if ($brandId <= 0) {
            return [];
        }

        return Yii::$app->db
            ->createCommand("select category_id from {{%brand_category}} where brand_id = {$brandId} order by category_id_level2 asc")
            ->queryColumn();
    }

    /**
     * Get brand ids by category ids
     *
     * @param  array $categoryIds category ids
     * @return array
     */
    public static function brandIdsByCategoryIds(array $categoryIds)
    {
        $brandIds = [];
        foreach ($categoryIds as $categoryId) {
            $brandIds = array_merge($brandIds, self::brandIdsByCategoryId($categoryId));
        }
        return array_unique($brandIds);
    }

    /**
     * Get brand ids by category id
     *
     * @param  int $categoryId category id
     * @return array
     */
    public static function brandIdsByCategoryId($categoryId)
    {
        $categoryId = (int)$categoryId;
        if ($categoryId <= 0) {
            return [];
        }

        return Yii::$app->db
            ->createCommand("select brand_id from {{%brand_category}} where category_id = {$categoryId}")
            ->queryColumn();
    }

    /**
     * Get brands by category id
     *
     * @param  int $categoryId category id
     * @return array
     */
    public static function brandsByCategoryId($categoryId)
    {
        $categoryId = (int)$categoryId;
        if ($categoryId <= 0) {
            return [];
        }

        $sql = "select b.id, b.name";
        $from = " from {{%" . self::tableName() . "}} bc
            ,{{%" . GoodsBrand::tableName() . "}} b
            ,{{%" . GoodsCategory::tableName() . "}} c"
        ;
        $sql .= $from;
        $where = " where bc.brand_id = b.id 
            and bc.category_id = c.id 
            and b.status = " . GoodsBrand::STATUS_ONLINE . " 
            and c.deleted = 0
            and bc.category_id = {$categoryId}"
        ;
        $sql .= $where;
        $orderBy = " order by convert(b.name using gbk) asc";
        $sql .= $orderBy;

        return Yii::$app->db
            ->createCommand($sql)
            ->queryAll();
    }

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'brand_category';
    }

    /**
     * Get category names by brand id for details page
     *
     * @param  int $brandId brand id
     * @return array
     */
    public static function categoryNamesByBrandId($brandId)
    {
        $brandId = (int)$brandId;
        if ($brandId <= 0) {
            return [];
        }

        $categories = Yii::$app->db
            ->createCommand("select category_id, category_id_level1, category_id_level2 from {{%brand_category}} where brand_id = {$brandId} order by category_id_level2 asc")
            ->queryAll();

        $rows = [];
        foreach ($categories as $category) {
            $rows[$category['category_id_level2']][] = $category;
        }

        $ret = [];
        $rootIds = [];
        foreach ($rows as $k => $row) {
            $level3CategoryNames = [];
            $rootId = 0;

            foreach ($row as $v) {
                $level3CategoryNames[] = GoodsCategory::findOne($v['category_id'])->title;
                $rootId = $v['category_id_level1'];
            }

            if (!in_array($rootId, $rootIds)) {
                $rootIds[] = $rootId;

                $ret[] = [
                    'root_category_title' => GoodsCategory::findOne($rootId)->title,
                    'parent_category_title' => GoodsCategory::findOne($k)->title,
                    'level3_category_titles' => implode(',', $level3CategoryNames),
                ];
            } else {
                $ret[] = [
                    'root_category_title' => '',
                    'parent_category_title' => GoodsCategory::findOne($k)->title,
                    'level3_category_titles' => implode(',', $level3CategoryNames),
                ];
            }
        }

        return $ret;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['brand_id', 'category_id'], 'required'],
            [['brand_id'], 'validateBrandId', 'on' => self::SCENARIO_ADD],
            [['category_id'], 'validateCategoryId', 'on' => self::SCENARIO_ADD],
        ];
    }

    /**
     * Validates brand_id
     *
     * @param string $attribute brand_id to validate
     * @return bool
     */
    public function validateBrandId($attribute)
    {
        if ($this->$attribute > 0
            && GoodsBrand::findOne($this->$attribute)
            && !self::find()->where([$attribute => $this->$attribute, 'category_id' => $this->category_id])->exists()
        ) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }

    /**
     * Validates category_id
     *
     * @param string $attribute category_id to validate
     * @return bool
     */
    public function validateCategoryId($attribute)
    {
        $where = [
            'id' => $this->$attribute,
            'deleted' => GoodsCategory::STATUS_OFFLINE,
            'level' => GoodsCategory::LEVEL3
        ];

        if ($this->$attribute > 0
            && GoodsCategory::find()->where($where)->exists()
        ) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }
}