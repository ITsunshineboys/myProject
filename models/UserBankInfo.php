<?php

namespace app\models;

use app\services\ModelService;
use app\services\StringService;
use Yii;
use yii\db\Query;
use yii\db\Exception;

class UserBankInfo extends \yii\db\ActiveRecord
{

    const  CACHE_SET_BANK_USER_ID='cache_set_bank_user_id_';
    const  CACHE_SET_BANK_ROLE_ID='cache_set_bank_role_id_';
    const DEBIT_CARD='借记卡';
    const CREDIT_CARD='信用卡';
    const EXCHANGE_CARD='贷记卡';

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
     * @throws Exception
     */
    public static  function  SetBankCard($bankname,$bankcard,$username,$position,$bankbranch,$role_id,$user)
    {
        $time=time();
        if ($role_id==Yii::$app->params['supplierRoleId'])
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
                } catch (\Exception $e) {
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
//            $cache = Yii::$app->cache;
//            $data = $cache->get(self::CACHE_SET_BANK_USER_ID . $user->id.'_'.self::CACHE_SET_BANK_ROLE_ID.$user->last_role_id_app);
//            $cacheData = 'ResetmobileSmscode' . $user->id . date('Y-m-d H', time());
//            $data = $cache->set(User::CACHE_PREFIX_GET_MOBILE . $user->id, $cacheData, 60 * 60);
            $cardType=Yii::$app->request->post('cardtype');
            if (!$cardType)
            {
                $code=1000;
                return $code;
            }
            if ($cardType==self::DEBIT_CARD)
            {
                $cardType=2;
            }
            if ($cardType==self::CREDIT_CARD)
            {
                $cardType=1;
            }
            $userRole=UserRole::find()
                ->where(['user_id'=>$user->id])
                ->andWhere(['role_id'=>$user->last_role_id_app])
                ->one();
            if (!$userRole || !$user->identity_no)
            {
               return  1091;
            }

            $code=$userRole->checkIsAuthentication();
            if ($code!=200)
            {
                return $code;
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
                if (!$res1)
                {
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
            ->andWhere(['status'=>UserCashregister::CASH_STATUS_IN])
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
            'balance'=> StringService::formatPrice($balance->balance*0.01),
            'availableamount'=> StringService::formatPrice($balance->availableamount*0.01),
            'cash_money'=> StringService::formatPrice($cash_money*0.01),
            'freeze_money'=> StringService::formatPrice($freeze_money*0.01)
        ];
    }


    /**
     * @param $bank_card
     * @return int
     */
    public  static  function  GetBankName($bank_card)
    {
        $bankList=\Yii::$app->params['bankList'];
        $card_8 = substr($bank_card, 0, 8);
        if (isset($bankList[$card_8]))
        {
            return $bankList[$card_8];
        }
        $card_6 = substr($bank_card, 0, 6);
        if (isset($bankList[$card_6]))
        {
            return $bankList[$card_6];
        }
        $card_5 = substr($bank_card, 0, 5);
        if (isset($bankList[$card_5]))
        {
            return $bankList[$card_5];
        }
        $card_4 = substr($bank_card, 0, 4);
        if (isset($bankList[$card_4]))
        {
            return $bankList[$card_4];
        }
        return 1048;
    }

    /**
     * @param $bankName
     * @return string
     */
    public  static  function  GetCardType($bankName)
    {
        if(strpos($bankName,self::EXCHANGE_CARD) !== false){
             return self::CREDIT_CARD;
        }
        if(strpos($bankName,self::CREDIT_CARD) !== false){
            return self::CREDIT_CARD;
        }
        if(strpos($bankName,self::DEBIT_CARD) !== false){
            return self::DEBIT_CARD;
        }
        return 1000;
    }
}
