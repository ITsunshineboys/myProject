<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_accessdetail".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $role_id
 * @property integer $access_type
 * @property string $access_money
 * @property integer $create_time
 * @property string $order_no
 * @property string $transaction_no
 */
class UserAccessdetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_accessdetail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'role_id', 'access_type', 'access_money', 'create_time'], 'integer'],
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
            'uid' => '用户id',
            'role_id' => '角色id',
            'access_type' => '1.充值 2.扣款 3.已提现 4.提现中  5.驳回 6.货款',
            'access_money' => '收支金额',
            'create_time' => '创建时间',
            'order_no' => '订单号',
            'transaction_no' => '交易单号',
        ];
    }
}
