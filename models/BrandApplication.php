<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class BrandApplication extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'brand_application';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['category_id', 'brand_id', 'authorization_start', 'authorization_end'], 'required'],
            [['brand_id'], 'validateBrandId'],
            [['category_id'], 'validateCategoryId'],
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
            && GoodsBrand::find()->where(['id' => $this->$attribute, 'status' => GoodsBrand::STATUS_ONLINE])->exists()
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