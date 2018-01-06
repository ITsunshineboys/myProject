<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_goods_brand".
 *
 * @property string $id
 * @property string $order_no
 * @property string $sku
 * @property string $name
 * @property string $logo
 * @property string $certificate
 */
class OrderGoodsBrand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_goods_brand';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_no', 'sku'], 'required'],
            [['order_no', 'sku', 'name'], 'string', 'max' => 50],
            [['logo', 'certificate'], 'string', 'max' => 255],
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
            'logo' => 'Logo',
            'certificate' => 'Certificate',
        ];
    }

    /**
     * @param $brand_id
     * @param $order_no
     * @param $sku
     * @return int
     */
    public  static  function AddNewData($brand_id,$order_no,$sku)
    {

        $tran = Yii::$app->db->beginTransaction();
        try {
            $GoodsBrand=GoodsBrand::findOne($brand_id);
            if ($GoodsBrand)
            {
                $orderGoodsBrand=new OrderGoodsBrand();
                $orderGoodsBrand->order_no=$order_no;
                $orderGoodsBrand->sku=$sku;
                $orderGoodsBrand->name=$GoodsBrand->name;
                $orderGoodsBrand->logo=$GoodsBrand->logo;
                $orderGoodsBrand->certificate=$GoodsBrand->certificate;
                if (!$orderGoodsBrand->save(false))
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
