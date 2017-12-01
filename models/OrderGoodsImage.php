<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_goods_image".
 *
 * @property integer $id
 * @property string $order_no
 * @property string $sku
 * @property string $goods_id
 * @property string $image
 */
class OrderGoodsImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_goods_image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_no', 'sku'], 'required'],
            [['goods_id'], 'integer'],
            [['order_no', 'sku'], 'string', 'max' => 50],
            [['image'], 'string', 'max' => 255],
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
            'goods_id' => 'Goods ID',
            'image' => 'Image',
        ];
    }
}
