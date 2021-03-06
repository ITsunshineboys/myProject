<?php

namespace app\models;

use app\services\ModelService;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "user_cashregister".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $role_id
 * @property string $cash_money
 * @property string $real_money
 * @property integer $apply_time
 * @property integer $handle_time
 * @property integer $status
 * @property string $supplier_reason
 * @property string $transaction_no
 */
class UserCashregister extends \yii\db\ActiveRecord
{
    const SUPPLIER=6;
    const PAGE_SIZE_DEFAULT=10;
    const FIELDS_EXTRA=[];
    const FIELDS_ADMIN = [
        'id',
        'apply_time',
        'cash_money',
        'real_money',
        'handle_time',
        'transaction_no',
        'status'


    ];
    const  RECHARGE=1;
    const  DEBIT=2;
    const  CASH=3;
    const  CASH_IN=4;
    const  REJECT=5;
    const  GOODSPAYMENT=6;
    const  ACCESS_TYPE_LIST=
        [
            1=>self::RECHARGE,
            2=>self::DEBIT,
            3=>self::CASH,
            4=>self::CASH_IN,
            5=>self::REJECT,
            6=>self::GOODSPAYMENT,
            7=>7,
            11=>11,
        ];
    const STATUS_CSED='已提现';
    const CASH_STATUS_ALL=4;
    const CASH_STATUS_IN=1;
    const CASH_STATUS_OVER=2;
    const CASH_STATUS_FAIL=3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_cashregister';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'role_id', 'bank_log_id', 'cash_money', 'real_money', 'apply_time', 'handle_time', 'status'], 'integer'],
            [['apply_time', 'supplier_reason', 'transaction_no'], 'required'],
            [['supplier_reason'], 'string', 'max' => 150],
            [['transaction_no'], 'string', 'max' => 50],
        ];
    }

    /**
     * 已提现列表查询分页
     * @return array
     * */
    public static function pagination($uid,$where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC')
    {
        $select = array_diff($select, self::FIELDS_EXTRA);
        $andwhere=['uid' =>$uid];
        $offset = ($page - 1) * $size;
        $freezeList = self::find()
            ->select($select)
            ->where($where)
            ->andWhere($andwhere)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();
//        var_dump($freezeList);die;
        foreach ($freezeList as &$freeze) {
            $freeze['apply_time']=date('Y-m-d H:i',$freeze['apply_time']);
            $freeze['real_money']=sprintf('%.2f',(float)$freeze['real_money']*0.01);
            $freeze['cash_money']=sprintf('%.2f',(float)$freeze['cash_money']*0.01);
            $freeze['cost_money']=sprintf('%.2f',$freeze['cash_money']-$freeze['real_money']);
        }
        $total=self::find()->where($where)->andWhere($andwhere)->count();
        return ModelService::pageDeal($freezeList, $total, $page, $size);


    }

    /**
     * @param $where
     * @param $page
     * @param int $size
     * @param $select
     * @return array|\yii\db\ActiveRecord[]
     */
    public  static  function  paginationByOwner($where,$page,$size = self::PAGE_SIZE_DEFAULT,$select)
    {
        $select = array_diff($select, self::FIELDS_EXTRA);
        $cashList = (new Query())
            ->from(self::tableName().' as c')
            ->leftJoin(User::tableName().' as u','c.uid=u.id')
            ->select($select)
            ->where($where)
            ->offset(($page - 1) * $size)
            ->limit($size)
            ->all();
        $count = (new Query())
            ->from(self::tableName().' as c')
            ->leftJoin(User::tableName().' as u','c.uid=u.id')
            ->select($select)
            ->where($where)
            ->count();
        return ModelService::pageDeal($cashList, $count, $page, $size);
    }

    /**
     * 获取已提现商家账户信息
     * @return array
     * */
    public static function getcashviewdata($cash_id)
    {

        $query = new Query();
        $select = 'sc.id,sc.cash_money,sc.apply_time,sc.real_money,sc.status,sc.supplier_reason,sc.transaction_no,sc.handle_time,s.shop_name,sb.bankname,sb.bankcard,sb.username,sb.position,sb.bankbranch';
        $array = $query->from('user_cashregister as sc')
            ->select($select)
            ->leftJoin('supplier as s', 'sc.uid=s.uid')
            ->leftJoin('user_bankinfo as ub', 's.uid=ub.uid')
            ->leftJoin('bankinfo_log as sb','sb.id=ub.log_id')
            ->where(['sc.id' => $cash_id,'ub.role_id'=>self::SUPPLIER])
            ->one();
        if ($array) {

            $array['apply_time'] = date('Y-m-d H:i', $array['apply_time']);
            $array['handle_time'] = date('Y-m-d H:i', $array['handle_time']);
            $array['cash_money'] = sprintf('%.2f', (float)($array['cash_money']) * 0.01);
            $array['real_money'] = sprintf('%.2f', (float)($array['real_money']) * 0.01);
            $array['cost_money'] = sprintf('%.2f', (float)$array['cash_money'] - $array['real_money']);
            if($array['cost_money']==0){
                $array['cost_money']=null;
            }
            if($array['real_money']==0){
                $array['real_money']=null;
            }
            $array['status'] = self::STATUS_CSED;
            return $array;
        }
        return null;
    }
}
