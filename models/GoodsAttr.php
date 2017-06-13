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
    const ERROR_CODE_SAME_NAME = 1009;

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
     * @param array $additionTypes addition types
     * @return bool
     */
    public static function validateValues($values, $additionTypes)
    {
        foreach ($values as $i => $row) {
            if ($additionTypes[$i] == self::ADDITION_TYPE_NORMAL) {
                continue;
            }

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
     * Validates category_id
     *
     * @param string $attribute category_id to validate
     * @return bool
     */
    public function validateCategoryId($attribute)
    {
        if (!GoodsCategory::find()->where(['id' => $this->$attribute, 'level' => GoodsCategory::LEVEL3])->one()) {
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
            [['name', 'category_id'], 'required'],
            ['name', 'string', 'length' => [1, 6]],
            ['category_id', 'validateCategoryId'],
            ['unit', 'in', 'range' => array_keys(self::UNITS)],
            ['addition_type', 'in', 'range' => array_keys(self::ADDITION_TYPES)]
        ];
    }
}