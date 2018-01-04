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
        'm',
        'm²',
        'kg',
        'mm'
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
        foreach ($additionTypes as $i => $additionType) {
            if ($additionType == self::ADDITION_TYPE_NORMAL) {
                continue;
            }

            if (count($values) != count($additionTypes)) {
                return false;
            }

            $value = explode(',', $values[$i]);

            if (StringService::checkRepeatedElement($value)
                || StringService::checkEmptyElement($value)
//                || !StringService::checkIntList($value)
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
     * Get attributes(set by supplier) by category id
     *
     * @param int $goodsId goods id
     * @return array
     */
    public static function frontDetailsByGoodsId($goodsId)
    {
        $goodsId = (int)$goodsId;
        if ($goodsId <= 0) {
            return [];
        }

        $sql = "select name, value, unit"
            . " from {{%" . self::tableName() . "}}"
            . " where goods_id = {$goodsId}";

        $attrs = Yii::$app->db
            ->createCommand($sql)
            ->queryAll();

        foreach ($attrs as &$attr) {
            if ($attr['unit'] > 0) {
                $attr['value'] .= self::UNITS[$attr['unit']];
            }
            unset($attr['unit']);
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
     * Add goods attributes by names, values etc.
     *
     * @param ActiveRecord $goods goods model
     * @param array $names names
     * @param array $values values
     * @return int
     */
    public static function addByAttrs(ActiveRecord $goods, array $names, array $values)
    {
        $code = 1000;

        $values = array_map(function ($value) {
            return str_replace('，', ',', $value);
        }, $values);

        foreach ($names as $i => $name) {
            $goodsAttr = new self;
            $goodsAttr->name = $name;
            $goodsAttr->value = $values[$i];
            $goodsAttr->goods_id = $goods->id;
            $goodsAttr->category_id = $goods->category_id;

            $lhzzAttr = self::find()->where([
                'goods_id' => 0,
                'name' => $name,
                'category_id' => $goods->category_id
            ])->one();
            if ($lhzzAttr) {
                $goodsAttr->unit = $lhzzAttr->unit;
                $goodsAttr->addition_type = $lhzzAttr->addition_type;
                // cancel value check
//                if ($goodsAttr->addition_type == self::ADDITION_TYPE_DROPDOWN_LIST
//                    && !is_numeric($goodsAttr->value)
//                ) {
//                    return $code;
//                }
            }

            if (!$goodsAttr->validate()) {
                return $code;
            }

            if (!$goodsAttr->save()) {
                $code = 500;
                return $code;
            }
        }

        $code = 200;
        return $code;
    }

    public static function findByGoodsId($id)
    {
        if ($id) {
            $select = "goods_attr.goods_id,goods_attr.name,goods_attr.value";
            $standard = self::find()
                ->asArray()
                ->select($select)
                ->where(['in', 'goods_id', $id])
                ->all();
        } else {
            $standard = null;
        }
        return $standard;
    }

    /**
     * find goods_id
     * @param $goods_id
     * @param $name
     *  $name  商品属性名称
     * @return array
     */
    public static function findByGoodsIdUnit($goods_id,$name)
    {
        $row = self::find()
            ->select('goods_attr.goods_id,goods_category.title,goods_attr.name,goods_attr.value,goods_attr.unit')
            ->leftJoin('goods','goods_attr.goods_id = goods.id')
            ->leftJoin('goods_category','goods.category_id = goods_category.id')
            ->where(['goods_attr.goods_id'=>$goods_id])
            ->andwhere(['like','goods_attr.name',$name])
            ->asArray()
            ->one();

            if ($row['unit'] == 5){
                $row['value'] = $row['value'] * 100;
            }

        return $row;
    }

    public static function findByGoodsIdUnits($goods_id,$name)
    {
        $row = self::find()
            ->select('goods_attr.goods_id,goods_category.title,goods_attr.name,goods_attr.value,goods_attr.unit')
            ->leftJoin('goods','goods_attr.goods_id = goods.id')
            ->leftJoin('goods_category','goods.category_id = goods_category.id')
            ->where(['goods_attr.goods_id'=>$goods_id])
            ->andwhere(['like','goods_attr.name',$name])
            ->asArray()
            ->all();

        return $row;
    }

    /**
     * @param $goods
     * @return array|ActiveRecord[]
     */
    public static function goodsIdUnit($goods)
    {
        $select = "goods_attr.goods_id,goods_attr.name,goods_attr.value";
        $standard = self::find()
            ->asArray()
            ->select($select)
            ->where(['goods_id' => $goods['id']])
            ->all();
        return $standard;
    }

    public static function goodsByIds($ids)
    {
        $select = 'id,name,value,goods_id';
        return self::find()
            ->asArray()
            ->select($select)
            ->where(['goods_id' => $ids])
            ->all();
    }

    /**
     * Find necessary goods attributes
     *
     * @param int $categoryId category id
     * @return array
     */
    public static function findNecessaryAttrs($categoryId)
    {
        $names = self::find()->select(['name'])->where(['goods_id' => 0, 'category_id' => $categoryId])->asArray()->all();
        return StringService::valuesByKey($names, 'name');
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
            ['name', 'string', 'length' => [1, 50]],
            ['value', 'string', 'length' => [1, 50]],
            ['category_id', 'validateCategoryId'],
            ['unit', 'in', 'range' => array_keys(self::UNITS)],
            ['addition_type', 'in', 'range' => array_keys(self::ADDITION_TYPES)]
        ];
    }
}