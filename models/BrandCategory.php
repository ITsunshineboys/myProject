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
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'brand_category';
    }

    /**
     * Get category ids by brand id
     *
     * @param  int   $brandId brand id
     * @return array
     */
    public static function categoryIdsByBrandId($brandId)
    {
        $brandId = (int)$brandId;
        if ($brandId <=0 ) {
            return [];
        }

        return Yii::$app->db
            ->createCommand("select category_id from {{%brand_category}} where brand_id = {$brandId}")
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
     * @param  int   $categoryId category id
     * @return array
     */
    public static function brandIdsByCategoryId($categoryId)
    {
        $categoryId = (int)$categoryId;
        if ($categoryId <=0 ) {
            return [];
        }

        return Yii::$app->db
            ->createCommand("select brand_id from {{%brand_category}} where category_id = {$categoryId}")
            ->queryColumn();
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
        if ($this->$attribute > 0
            && GoodsCategory::find()->where(['id' => $this->$attribute, 'deleted' => GoodsCategory::STATUS_OFFLINE])->exists()
        ) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }
}