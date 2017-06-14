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
    const FROM_TYPE_LHZZ = 0;
    const FROM_TYPE_SUPPLIER = 1;

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
     * Get attributes(set by lhzz admin) by category id
     *
     * @param int $categoryId category id
     * @return array
     */
    public static function detailsByCategoryId($categoryId)
    {
        $categoryId = (int)$categoryId;
        if ($categoryId <= 0) {
            return [];
        }

        $sql = "select name, value, unit, addition_type"
            . " from {{%" . self::tableName() . "}}"
            . " where category_id = {$categoryId} and goods_id = 0";

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
     * Get attributes(set by supplier) by category id
     *
     * @param int $goodsId goods id
     * @return array
     */
    public static function detailsByGoodsId($goodsId)
    {
        $goodsId = (int)$goodsId;
        if ($goodsId <= 0) {
            return [];
        }

        $sql = "select name, value, unit, addition_type, category_id"
            . " from {{%" . self::tableName() . "}}"
            . " where goods_id = {$goodsId}";

        $attrs = Yii::$app->db
            ->createCommand($sql)
            ->queryAll();

        foreach ($attrs as &$attr) {
            $lhzzAttr = self::find()->where([
                'goods_id' => 0,
                'name' => $attr['name'],
                'category_id' => $attr['category_id']
            ])->one();

            if ($lhzzAttr) {
                $attr['from_type'] = self::FROM_TYPE_LHZZ;
                $attr['unit'] = self::UNITS[$lhzzAttr->unit];
                $attr['addition_type'] = $lhzzAttr->addition_type;
                if ($lhzzAttr->addition_type == self::ADDITION_TYPE_DROPDOWN_LIST) {
                    $attr['selected'] = $attr['value'];
                    $attr['value'] = explode(',', $lhzzAttr->value);
                }
            } else {
                $attr['from_type'] = self::FROM_TYPE_SUPPLIER;
                unset($attr['unit']);
                unset($attr['addition_type']);
            }

            unset($attr['category_id']);
        }

        return $attrs;
    }

    /**
     * Check if goods attributes changed
     *
     * @param int $goodsId goods id
     * @param $names array names
     * @param $values array values
     * @return bool
     */
    public static function changedAttr($goodsId, array $names, array $values)
    {
        if (!StringService::checkArrayIdentity($names, self::namesByGoodsId($goodsId))
            || !StringService::checkArrayIdentity($values, self::valuesByGoodsId($goodsId))
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get attribute names by goods id
     *
     * @param  int $goodsId goods id
     * @return array
     */
    public static function namesByGoodsId($goodsId)
    {
        $goodsId = (int)$goodsId;
        if ($goodsId <= 0) {
            return [];
        }

        return Yii::$app->db
            ->createCommand("select name from {{%" . self::tableName() . "}} where goods_id = {$goodsId}")
            ->queryColumn();
    }

    /**
     * Get attribute values by goods id
     *
     * @param  int $goodsId goods id
     * @return array
     */
    public static function valuesByGoodsId($goodsId)
    {
        $goodsId = (int)$goodsId;
        if ($goodsId <= 0) {
            return [];
        }

        return Yii::$app->db
            ->createCommand("select value from {{%" . self::tableName() . "}} where goods_id = {$goodsId}")
            ->queryColumn();
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