<?php

namespace app\models;

use app\services\ModelService;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "supplier_cashregister".
 *
 * @property integer $id
 * @property string $cash_money
 * @property integer $apply_time
 * @property integer $supplier_id
 * @property integer $status
 * @property string $supplier_reason
 * @property string $transaction_no
 * @property integer $handle_time
 * @property integer $real_money
 */
class SupplierCashregister extends \yii\db\ActiveRecord
{

    const PAGE_SIZE_DEFAULT=12;
    const FIELDS_EXTRA=[];
    const FIELDS_ADMIN = [
        'apply_time',
        'cash_money',
        'real_money',
        'transaction_no',
        'status'


    ];
    const STATUS_CSED='已提现';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'supplier_cashregister';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cash_money', 'apply_time', 'supplier_id', 'status', 'handle_time'], 'integer'],
            [['apply_time', 'supplier_id', 'supplier_reason', 'transaction_no'], 'required'],
            [['supplier_reason'], 'string', 'max' => 150],
            [['transaction_no'], 'string', 'max' => 50],
        ];
    }

    /**
     * 已提现列表查询分页
     * @return array
     * */
    public static function pagination($where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC')
    {
        $select = array_diff($select, self::FIELDS_EXTRA);

        $offset = ($page - 1) * $size;
        $freezeList = self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();

        foreach ($freezeList as &$freeze) {

            if (isset($freeze['apply_time'])) {
                $freeze['apply_time']=date('Y-m-d H:i',$freeze['apply_time']);

            }
            $freeze['real_money']=sprintf('%.2f',$freeze['real_money']*0.01);
            $freeze['cash_money']=sprintf('%.2f',$freeze['cash_money']*0.01);
            $freeze['cost_money']=sprintf('%.2f', $freeze['cash_money']-$freeze['real_money']*0.01
            );
        }
        $total=(int)self::find()->where($where)->asArray()->count();
        return ModelService::pageDeal($freezeList, $total, $page, $size);


    }

    /**
     * 获取已提现商家账户信息
     * @return array
     * */
public static function getcashviewdata($cash_id)
{

    $query = new Query();
    $select = 'sc.cash_money,sc.apply_time,sc.real_money,sc.status,sc.supplier_reason,sc.transaction_no,sc.handle_time,s.shop_name,sb.bankname,sb.bankcard,sb.username,sb.position,sb.bankbranch';
    $array = $query->from('supplier_cashregister as sc')
        ->select($select)
        ->leftJoin('supplier as s', 'sc.supplier_id=s.id')
        ->leftJoin('user_bankinfo as sb', 's.uid=sb.u_id')
        ->where(['sc.id' => $cash_id])
        ->one();
    if ($array) {
        $array['apply_time'] = date('Y-m-d H:i', $array['apply_time']);
        $array['handle_time'] = date('Y-m-d H:i', $array['handle_time']);
        $array['cost_money'] = sprintf('%.2f', (float)($array['cash_money'] - $array['real_money']) * 0.01);
        $array['cash_money'] = sprintf('%.2f', (float)($array['cash_money']) * 0.01);
        $array['real_money'] = sprintf('%.2f', (float)($array['real_money']) * 0.01);
        $array['status'] = self::STATUS_CSED;

        return $array;


         }
         return null;
    }

}
