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

}
