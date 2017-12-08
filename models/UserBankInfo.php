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
     /**
     * 添加、修改银行卡操作
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
        $time=time();
        if ($role_id==6)
        {
            $bankInfo=self::find()
                ->where(['uid'=>$user->id,'role_id'=>$role_id,'selected'=>1])
                ->one();
            if ($bankInfo)
            {
                $trans = \Yii::$app->db->beginTransaction();
                try {
                    $log=new BankinfoLog();
                    $log->bankname=$bankname;
                    $log->bankcard=(int)$bankcard;
                    $log->username=$username;
                    $log->position=$position;
                    $log->bankbranch=$bankbranch;
                    $log->create_time=$time;
                    $res2=$log->save(false);
                    if (!$res2)
                    {
                        $code=500;
                        $trans->rollBack();
                        return $code;
                    }
                    $bankInfo->log_id=$log->id;
                    $bankInfo->uid=$user->id;
                    $bankInfo->role_id=$role_id;
                    $res1=$bankInfo->save(false);
                    if (!$res1){
                        $code=500;
                        $trans->rollBack();
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
                    $log=new BankinfoLog();
                    $log->bankname=$bankname;
                    $log->bankcard=$bankcard;
                    $log->username=$username;
                    $log->position=$position;
                    $log->bankbranch=$bankbranch;
                    $log->create_time=$time;
                    $res2=$log->save(false);
                    if (!$res2)
                    {
                        $code=500;
                        $trans->rollBack();
                        return $code;
                    }
                    $bank=UserBankInfo::find()
                        ->where(['uid'=>$user->id,'role_id'=>$role_id])
                        ->all();
                    if ($bank)
                    {
                        foreach ( $bank as &$list)
                        {
                            $list->selected=0;
                            $resu=$list->save(false);
                            if (!$resu)
                            {
                                $code=500;
                                $trans->rollBack();
                                return $code;
                            }
                        }
                    }
                        $bankInfo=new self;
                        $bankInfo->log_id=$log->id;
                        $bankInfo->uid=$user->id;
                        $bankInfo->role_id=$role_id;
                        $bankInfo->selected=1;
                        $res1=$bankInfo->save(false);
                        if (!$res1){
                            $code=500;
                            $trans->rollBack();
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
        }else
        {
           $cardType=Yii::$app->request->post('cardtype');
            if (!$cardType)
            {
                $code=1000;
                return $code;
            }
            if ($cardType=='借记卡')
            {
                $cardType=2;
            }
            if ($cardType=='信用卡')
            {
                $cardType=1;
            }
            $bankInfolog=(new Query())
                ->from(self::tableName().' as B')
                ->leftJoin(BankinfoLog::tableName().' as L','B.log_id=L.id')
                ->where(['B.uid'=>$user->id])
                ->andWhere(['B.role_id'=>$user->last_role_id_app])
                ->andWhere(['L.bankcard'=>$bankcard])
                ->select('L.bankcard')
                ->one();
            if ($bankInfolog)
            {
                $code=1074;
                return $code;
            }
            $trans = \Yii::$app->db->beginTransaction();
            try {
                $log=new BankinfoLog();
                $log->bankname=$bankname;
                $log->bankcard=$bankcard;
                $log->username=$username;
                $log->position=$position;
                $log->bankbranch=$bankbranch;
                $log->create_time=$time;
                $log->bank_type=$cardType;
                $res2=$log->save(false);
                if (!$res2)
                {
                    $code=500;
                    $trans->rollBack();
                    return $code;
                }
                $bank=UserBankInfo::find()
                    ->where(['uid'=>$user->id,'role_id'=>$role_id])
                    ->all();
                if ($bank)
                {
                    foreach ( $bank as &$list)
                    {
                        $list->selected=0;
                        $resu=$list->save(false);
                        if (!$resu)
                        {
                            $code=500;
                            $trans->rollBack();
                            return $code;
                        }
                    }

                }
                $bankInfo=new self;
                $bankInfo->log_id=$log->id;
                $bankInfo->uid=$user->id;
                $bankInfo->role_id=$role_id;
                $bankInfo->selected=1;
                $res1=$bankInfo->save(false);
                if (!$res1){
                    $code=500;
                    $trans->rollBack();
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
