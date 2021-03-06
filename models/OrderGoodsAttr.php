<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_goods_attr".
 *
 * @property integer $id
 * @property string $order_no
 * @property string $sku
 * @property string $name
 * @property string $value
 * @property integer $unit
 * @property integer $addition_type
 * @property integer $goods_id
 * @property integer $category_id
 */
class OrderGoodsAttr extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_goods_attr';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_no', 'sku'], 'required'],
            [['unit', 'addition_type', 'goods_id', 'category_id'], 'integer'],
            [['order_no', 'sku', 'name', 'value'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_no' => 'Order No',
            'sku' => 'Sku',
            'name' => 'Name',
            'value' => 'Value',
            'unit' => 'Unit',
            'addition_type' => 'Addition Type',
            'goods_id' => 'Goods ID',
            'category_id' => 'Category ID',
        ];
    }

    /**
     * @param $goods_id
     * @param $order_no
     * @param $sku
     * @return int
     */
    public  static  function  AddNewData($goods_id,$order_no,$sku)
    {

        $tran = Yii::$app->db->beginTransaction();
        try {
            $goodsAttr=GoodsAttr::find()
                ->where(['goods_id'=>$goods_id])
                ->all();
            if ($goodsAttr)
            {
                foreach ( $goodsAttr as &$attr)
                {
                    $orderGoodsAttr=new OrderGoodsAttr();
                    $orderGoodsAttr->order_no=$order_no;
                    $orderGoodsAttr->sku=$sku;
                    $orderGoodsAttr->name=$attr->name;
                    $orderGoodsAttr->value=$attr->value;
                    $orderGoodsAttr->unit=$attr->unit;
                    $orderGoodsAttr->addition_type=$attr->addition_type;
                    $orderGoodsAttr->goods_id=$attr->goods_id;
                    if (!$orderGoodsAttr->save(false))
                    {
                        $tran->rollBack();
                        return 500;
                    }
                }
            }
            $tran->commit();
            return 200;
        }catch (\Exception $e) {
            $tran->rollBack();
            return 500;
        }
    }


}
