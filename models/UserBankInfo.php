<?php

namespace app\models;

use app\services\ModelService;
use Yii;
use yii\db\Query;
use yii\db\Exception;

class UserBankInfo extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_bankinfo';
    }
    /**
     * @param $user_id
     * @param $role_id
     * @return array|null|\yii\db\ActiveRecord
     */
    public  static  function  findByUidAndRole_id($user_id,$role_id)
    {
        $data=self::find()
            ->where(['uid'=>$user_id])
            ->andWhere(['role_id'=>$role_id])
            ->one();
        return $data?$data:[];
    }


    /**添加、修改银行卡操作
     * @param $bankname
     * @param $bankcard
     * @param $username
     * @param $position
     * @param $bankbranch
     * @param $role_id
     * @param $user
     * @return int
     */
    public static  function  SetBankCard($bankname,$bankcard,$username,$position,$bankbranch,$role_id,$user)
    {
            $bankInfo=self::find()
                ->where(['uid'=>$user->id,'role_id'=>$role_id])
                ->one();
            if ($bankInfo)
            {
                $trans = \Yii::$app->db->beginTransaction();
                try {
                    $bankInfo->bankname=$bankname;
                    $bankInfo->bankcard=$bankcard;
                    $bankInfo->username=$username;
                    $bankInfo->position=$position;
                    $bankInfo->bankbranch=$bankbranch;
                    $bankInfo->uid=$user->id;
                    $bankInfo->role_id=$role_id;
                    $res1=$bankInfo->save();
                    if (!$res1){
                        $code=500;
                        return $code;
                    }
                    $trans->commit();
                    $code=200;
                    return $code;
                } catch (Exception $e) {
                    $trans->rollBack();
                    $code=500;
                    return $code;
                }
            }else{
                $trans = \Yii::$app->db->beginTransaction();
                try {
                    $bankInfo=new self;
                    $bankInfo->bankname=$bankname;
                    $bankInfo->bankcard=$bankcard;
                    $bankInfo->username=$username;
                    $bankInfo->position=$position;
                    $bankInfo->bankbranch=$bankbranch;
                    $bankInfo->uid=$user->id;
                    $bankInfo->role_id=$role_id;
                    $res1=$bankInfo->save(false);
                    if (!$res1){
                        $code=500;
                        return $code;
                    }
                    $trans->commit();
                    $code=200;
                    return $code;
                } catch (Exception $e) {
                    $trans->rollBack();
                    $code=500;
                    return $code;
                }
            }
    }

        /**
     * @param $role_id
     * @param $user
     * @return array
     */
    public  static  function  findCapitalDetail($role_id,$user)
    {
            $select='balance,availableamount';

        if ($role_id==7)
        {
            $where=[
                'id'=>$user->id
            ];
        }else{
            $where=[
                'uid'=>$user->id
            ];
        }
        $balance=Role::CheckUserRole($role_id)
            ->select($select)
            ->where($where)
            ->one();
        $UserCash=UserCashregister::find()
            ->where(['role_id'=>$role_id])
            ->andWhere(['uid'=>$user->id])
            ->andWhere(['status'=>3])
            ->all();
        $cash_money=0;
        foreach ($UserCash as &$list)
        {
            $cash_money+=$list->cash_money;
        }
        $UserFreezelist=UserFreezelist::findByUidAndRoleId($user->id,$role_id);
        $freeze_money=0;
        foreach ($UserFreezelist as &$list)
        {
           $freeze_money+=$list->freeze_money;
        }
        return [
            'balance'=>GoodsOrder::switchMoney($balance->balance*0.01),
            'availableamount'=>GoodsOrder::switchMoney($balance->availableamount*0.01),
            'cash_money'=>GoodsOrder::switchMoney($cash_money*0.01),
            'freeze_money'=>GoodsOrder::switchMoney($freeze_money*0.01)
        ];
    }

}
