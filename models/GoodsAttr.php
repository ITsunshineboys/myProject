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
use Yii;
use yii\db\ActiveRecord;

class GoodsAttr extends ActiveRecord
{
    const ADDITION_TYPE_NORMAL = 0;
    const ADDITION_TYPE_DROPDOWN_LIST = 1;

    const UNITS = [
        '无',
        'L',
        'M',
        'M^2',
        'Kg'
    ];

    const ADDITION_TYPES = [
        self::ADDITION_TYPE_NORMAL => '普通添加',
        self::ADDITION_TYPE_DROPDOWN_LIST => '下拉框添加'
    ];

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
     * Check if has the repeated attribute value of some attribute
     *
     * @param array $values values to validate
     * @return bool
     */
    public static function validateValues($values)
    {
        foreach ($values as $row) {
            $row = explode(',', $row);

            if (StringService::checkRepeatedElement($row)
                || StringService::checkEmptyElement($row)
                || !StringService::checkIntList($row)
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get attributes by category id
     *
     * @param int $categoryId category id
     * @param bool $isLhzzAdmin if operator is lhzz admin
     * @return array
     */
    public static function detailsByCategoryId($categoryId, $isLhzzAdmin = true)
    {
        $categoryId = (int)$categoryId;
        if ($categoryId <= 0) {
            return [];
        }

        $sql = "select name, value, unit, addition_type"
            . " from {{%" . self::tableName() . "}}"
            . " where category_id = {$categoryId}";
        $isLhzzAdmin && $sql .= ' and goods_id = 0';

        $attrs = Yii::$app->db
            ->createCommand($sql)
            ->queryAll();

        foreach ($attrs as &$attr) {
            $attr['unit'] = self::UNITS[$attr['unit']];
            $attr['addition_type'] == self::ADDITION_TYPE_DROPDOWN_LIST
            && $attr['value'] = explode(',', $attr['value']);
        }

        return $attrs;
    }

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_attr';
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
            [['name', 'unit', 'category_id'], 'required'],
            ['name', 'string', 'length' => [1, 6]],
            ['name', 'validateName'],
            ['category_id', 'validateCategoryId'],
            ['unit', 'in', 'range' => array_keys(self::UNITS)],
            ['addition_type', 'in', 'range' => array_keys(self::ADDITION_TYPES)]
        ];
    }
}