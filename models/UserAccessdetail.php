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

    const PAGE_SIZE_DEFAULT=12;
    const FIELDS_EXTRA=[];
    const FIELDS_ADMIN = [
        'id' ,
        'uid',
        'role_id',
        'cash_money' ,
        'real_money' ,
        'apply_time',
        'handle_time',
        'status',
        'supplier_reason',
        'transaction_no',
        'order_no'
    ];
    const ACCESS_TYPE_DESC_RECHARGE='充值';
    const ACCESS_TYPE_DESC_DEBIT='扣款';
    const ACCESS_TYPE_DESC_CASH='已提现';
    const ACCESS_TYPE_DESC_UNCASH_IN='提现中';
    const ACCESS_TYPE_DESC_DISAGREE='驳回';
    const ACCESS_TYPE_DESC_PAYMENT_GOODS='货款';
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

       /**
     * @param array $where
     * @param array $select
     * @param int $page
     * @param int $size
     * @param string $orderBy
     * @return array
     */
    public  static  function  pagination($where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC')
    {
        $select = array_diff($select, self::FIELDS_EXTRA);
        $offset = ($page - 1) * $size;
        $Accessdetaillist=self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();
        foreach ($Accessdetaillist as &$list) {
            $list['access_type']=self::findAccessType($list['access_type']);
            $list['access_money']=sprintf('%.2f',(float)$list['access_money']*0.01);
            if ($list['access_type']==self::ACCESS_TYPE_DESC_DEBIT)
            {
                $list['access_money']=-sprintf('%.2f',(float)$list['access_money']*0.01);
            }
            $list['create_time']=date('Y-m-d H:i',$list['create_time']);
        }
        $total=count($Accessdetaillist);
        return ModelService::pageDeal($Accessdetaillist, $total, $page, $size);
    }

        /**
     * @param $access_type
     * @return string
     */
    public static  function  findAccessType($access_type)
    {

        switch ($access_type)
        {
            case 1:
                $type=self::ACCESS_TYPE_DESC_RECHARGE;
                break;
            case 2:
                $type=self::ACCESS_TYPE_DESC_DEBIT;
                break;
            case 3:
                $type=self::ACCESS_TYPE_DESC_CASH;
                break;
            case 4:
                $type=self::ACCESS_TYPE_DESC_UNCASH_IN;
                break;
            case 5:
                $type=self::ACCESS_TYPE_DESC_DISAGREE;
                break;
            case 6:
                $type=self::ACCESS_TYPE_DESC_PAYMENT_GOODS;
                break;
        }
        return $type;
    }
}
