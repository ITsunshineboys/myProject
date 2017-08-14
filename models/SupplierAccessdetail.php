<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "supplier_accessdetail".
 *
 * @property integer $id
 * @property integer $access_type
 * @property string $access_money
 * @property integer $create_time
 * @property string $order_no
 * @property string $transaction_no
 * @property integer $supplier_id
 */
class SupplierAccessdetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'supplier_accessdetail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['access_type', 'access_money', 'create_time', 'transaction_no', 'supplier_id'], 'required'],
            [['access_type', 'access_money', 'create_time', 'supplier_id'], 'integer'],
            [['order_no', 'transaction_no'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'access_type' => '1:货款 2.提现失败  3.充值  4.扣款  ',
            'access_money' => '收支金额',
            'create_time' => '创建时间',
            'order_no' => '订单号',
            'transaction_no' => '交易单号',
            'supplier_id' => 'Supplier ID',
        ];
    }
}
