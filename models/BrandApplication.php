<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\Json;

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
     * Add brand application by attributes
     *
     * @param ActiveRecord $user
     * @param $attrs brand application attributes
     * @return BrandApplication|int
     */
    public static function addByAttrs(ActiveRecord $user, $attrs)
    {
        $supplier = Supplier::find()->where(['uid' => $user->id])->one();

        $brandApplication = new self;
        $brandApplication->attributes = $attrs;
        $brandApplication->authorization_start = strtotime($brandApplication->authorization_start);
        $brandApplication->authorization_end = strtotime($brandApplication->authorization_end);
        $brandApplication->mobile = $user->mobile;
        $brandApplication->supplier_id = $supplier->id;
        $brandApplication->supplier_name = $supplier->name;
        $brandApplication->create_time = time();

        if (!$brandApplication->validate()) {
            $code = 1000;
            return $code;
        }

        $category = GoodsCategory::findOne($brandApplication->category_id);
        $brand = GoodsBrand::findOne($brandApplication->brand_id);

        $brandApplication->brand_name = $brand->name;
        $brandApplication->category_title = $category->fullTitle();
        if (!$brandApplication->save()) {
            $code = 500;
            return $code;
        }

        return $brandApplication;
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