<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use app\services\ModelService;
use app\services\StringService;
use yii\db\ActiveRecord;

class GoodsAttr extends ActiveRecord
{
    const UNITS = [
        '无',
        'L',
        'M',
        'M^2',
        'Kg'
    ];

    const ADDITION_TYPES = [
        '普通添加',
        '下拉框添加'
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_attr';
    }

    /**
     * Check if has the same attribute name of some goods
     *
     * @param array $names names to validate
     * @return bool
     */
    public static function validateNames($names)
    {
        return !StringService::checkRepeatedElement($names);
    }

    /**
     * Validates name when add goods attribute
     *
     * @param string $attribute name to validate
     * @return bool
     */
    public function validateName($attribute)
    {
        if (self::find()->where(['goods_id' => 0, $attribute => $this->$attribute])->exists()) {
            $this->addError($attribute . ModelService::POSTFIX_EXISTS);
            return false;
        }

        return true;
    }

    /**
     * Validates category_id
     *
     * @param string $attribute category_id to validate
     * @return bool
     */
    public function validateCategoryId($attribute)
    {
        if (!GoodsCategory::findOne($this->$attribute)) {
            $this->addError($attribute);
            return false;
        }

        return true;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'value', 'unit', 'category_id'], 'required'],
            ['name', 'validateName'],
            ['category_id', 'validateCategoryId'],
            ['unit', 'in', 'range' => array_keys(self::UNITS)],
            ['addition_type', 'in', 'range' => array_keys(self::ADDITION_TYPES)]
        ];
    }
}