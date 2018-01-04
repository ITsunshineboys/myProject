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

    /**
     * @param $series_id
     * @param $order_no
     * @param $sku
     * @return int
     */
    public  static  function  AddNewData($series_id,$order_no,$sku)
    {
        $tran = Yii::$app->db->beginTransaction();
        try {
            $serires=Series::findOne($series_id);
            if ($serires)
            {
                $orderSeries=new OrderSeries();
                $orderSeries->order_no=$order_no;
                $orderSeries->sku=$sku;
                $orderSeries->series=$serires->series;
                $orderSeries->intro=$serires->intro;
                if (!$orderSeries->save(false))
                {
                    $tran->rollBack();
                    return 500;
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
