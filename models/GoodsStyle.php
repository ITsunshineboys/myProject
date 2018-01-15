<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class GoodsStyle extends ActiveRecord
{
    /**
     * Add goods styles by attributes
     *
     * @param int $goodsId goods id
     * @param array $styleIds style id list
     * @return int
     */
    public static function addByAttrs($goodsId, array $styleIds)
    {
        $transaction = Yii::$app->db->beginTransaction();

        foreach ($styleIds as $styleId) {
            $goodsStyle = new self;
            $goodsStyle->goods_id = $goodsId;
            $goodsStyle->style_id = $styleId;

            if (!$goodsStyle->save()) {
                $transaction->rollBack();
                $code = 500;
                return $code;
            }
        }

        $transaction->commit();
        $code = 200;
        return $code;
    }

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_style';
    }

    /**
     * Get style id list by goods id
     *
     * @param $goodsId goods id
     * @return array
     */
    public static function styleIdsByGoodsId($goodsId)
    {
        $styleIds = self::find()
            ->asArray()
            ->select(['style_id'])
            ->where(['goods_id' => $goodsId])
            ->all();
        return array_map(function ($v) {
            return $v['style_id'];
        }, $styleIds);
    }

    /**
     * Get goods id list by style id
     *
     * @param $styleId style id
     * @return array
     */
    public static function goodsIdsByStyleId($styleId)
    {
        $goodsIds = self::find()
            ->asArray()
            ->select(['goods_id'])
            ->where(['style_id' => $styleId])
            ->all();
        return array_map(function ($v) {
            return $v['goods_id'];
        }, $goodsIds);
    }

    /**
     * Get goods ids which have only one style
     *
     * @param $styleId style id default 0
     * @return array
     */
    public static function goodsIdsOneStyle($styleId = 0)
    {
        $where = '1';
        $styleId && $where .= ' and style_id = ' . $styleId;
        return Yii::$app->db
            ->createCommand('select goods_id from ' . self::tableName() . ' where ' . $where . 'group by goods_id having count(*) = 1')
            ->queryColumn();
    }
}