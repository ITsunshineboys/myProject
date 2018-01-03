<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;


class UserAddress extends  ActiveRecord
{
    const USER_ADDRESS = 'user_address';

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'user_address';
    }


    /**
     * 8.31
     * 无登录App-添加收货地址
     * @param $mobile
     * @param $consignee
     * @param $region
     * @param $districtcode
     * @return int
     */
    public static function InsertAddress($mobile,$consignee,$region,$districtcode){
        $addresstoken= md5($mobile.$consignee.date('Y-m-d H:i:s', time()));
        $data=self::find()
            ->where(['mobile'=>$mobile,'consignee'=>$consignee,'district'=>$districtcode])
            ->asArray()
            ->one();
        if ($data){
            $tran = Yii::$app->db->beginTransaction();
            try{
                $address=self::findOne($data['id']);
                $address->mobile=$mobile;
                $address->consignee=$consignee;
                $address->region=$region;
                $address->district=$districtcode;
                $address->addresstoken=$addresstoken;
                $res=$address->save(false);
                if (!$res)
                {
                    $code=500;
                    $tran->rollBack();
                    return $code;
                }
                $id=$address->id;
                $tran->commit();
                return $id;
            }catch(\Exception $e)
            {
                $code=500;
                $tran->rollBack();
                return null;
            }
        }else{
            $tran = Yii::$app->db->beginTransaction();
            try{
                $address=new self;
                $address->mobile=$mobile;
                $address->consignee=$consignee;
                $address->region=$region;
                $address->district=$districtcode;
                $address->addresstoken=$addresstoken;
                $res=$address->save(false);
                if (!$res)
                {
                    $code=500;
                    $tran->rollBack();
                    return null;
                }
                $id=$address->id;
                $tran->commit();
                return $id;
            }catch(\Exception $e)
            {
                $code=500;
                $tran->rollBack();
                return null;
            }
        }
    }

    /**
     * 无登录app-获取收货地址
     * @param $addressId
     * @return array|null|ActiveRecord
     */
    public static function GetAddress($addressId){
        $array  = self::find()
            ->select('id,mobile,consignee,region,district')
            ->where(['id' => $addressId])
            ->asArray()
            ->one();
        if ($array){
                $array['adCode']=$array['district'];
                $array['district']=LogisticsDistrict::getdistrict($array['district']);
            return $array;
        }else
        {
            return [];
        }
    }


    /**
     * 无登录app-获取收货地址
     * @param $addressId
     * @return array|null|ActiveRecord
     */
    public static function GetDistrict($addressId){
        $array  = self::find()
            ->select('id,mobile,consignee,region,district')
            ->where(['id' => $addressId])
            ->asArray()
            ->one();
        if ($array){
            $array['adCode']=$array['district'];
            $districtcode=$array['district'];
            $pro=substr($districtcode,0,2);
            $ci=substr($districtcode,2,2);
            $dis=substr($districtcode,4,2);
            $code=Yii::$app->params['districts'][0];
            if ($ci==0){
                $position=$code[86][$pro.'0000'];
            }else if($dis==0){
                $position=$code[86][$pro.'0000'].','.$code[$pro.'0000'][$pro.$ci.'00'];
            }else{
                $position=$code[86][$pro.'0000'].','.$code[$pro.'0000'][$pro.$ci.'00'].','.$code[$pro.$ci.'00'][$pro.$ci.$dis];
            }
            $array['district']=$position;
            return $array;
        }else
        {
            return [];
        }
    }

    /**
     * @param $district_code
     * @param $region
     * @param $consignee
     * @param $mobile
     * @param $user_id
     * @return int
     */
    public static function UserAddressAdd($district_code,$region,$consignee,$mobile,$user_id,$default)
    {
        $address_count=self::find()->where(['uid'=>$user_id])->count();
        if ($address_count>=10){
            $code=1026;
            return $code;
        }
        $tran = Yii::$app->db->beginTransaction();

        try{

            if ($default==1)
            {
                $address=self::find()->where(['uid'=>$user_id])->all();
                foreach ($address as &$list)
                {
                    $list->default=0;
                    $res=$list->save(false);
                    if (!$res){
                        $code=500;
                        $tran->rollBack();
                        return $code;
                    }
                }
                $user_address=new self;
                $user_address->consignee=$consignee;
                $user_address->region=$region;
                $user_address->mobile=$mobile;
                $user_address->district=$district_code;
                $user_address->uid=$user_id;
                $user_address->default=1;
                $res =$user_address->save(false);
                if (!$res){
                    $code=500;
                    $tran->rollBack();
                    return $code;
                }
            }else{
                $user_address=new self;
                $user_address->consignee=$consignee;
                $user_address->region=$region;
                $user_address->mobile=$mobile;
                $user_address->district=$district_code;
                $user_address->uid=$user_id;
                $user_address->default=0;
                $res =$user_address->save(false);
                if (!$res){
                    $code=500;
                    $tran->rollBack();
                    return $code;
                }
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (\Exception $e)
        {
            $code=500;
            $tran->rollBack();
            return $code;
        }
    }


    /**
     * select Default receive addres
     * @param $address_id
     * @param $user
     * @return int
     */
    public  static function  SetDefaultAddress($address_id,$user)
    {
        $user_address=self::find()->where(['id'=>$address_id])->one();
        if ($user->id!=$user_address->uid){
            $code=403;
            return $code;
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            $user_address->default=1;
            $res =$user_address->save(false);
            if (!$res){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            $address=self::find()
                ->where("id != {$address_id}")
                ->andWhere(['uid'=>$user->id])
                ->all();
            foreach ($address as &$list)
            {
                $list->default=0;
                $res=$list->save(false);
                if (!$res){
                    $code=500;
                    $tran->rollBack();
                    return $code;
                }
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (\Exception $e)
        {
            $code=500;
            $tran->rollBack();
            return $code;
        }
    }


    /**
     * @param $consignee
     * @param $address_id
     * @param $district_code
     * @param $mobile
     * @param $region
     * @return int
     */
    public static  function  updateAddress($consignee,$address_id,$district_code,$mobile,$region,$default)
    {
        $tran = Yii::$app->db->beginTransaction();
        try{
            if ($default==1)
            {
                $user = Yii::$app->user->identity;
                $address=self::find()->where(['uid'=>$user->id])->all();
                foreach ($address as &$list)
                {
                    $list->default=0;
                    $res=$list->save(false);
                    if (!$res){
                        $code=500;
                        $tran->rollBack();
                        return $code;
                    }
                }
                $user_address=self::find()->where(['id'=>$address_id])->one();
                $user_address->consignee=$consignee;
                $user_address->region=$region;
                $user_address->mobile=$mobile;
                $user_address->district=$district_code;
                $user_address->default=1;
                $res =$user_address->save(false);
                if (!$res){
                    $code=500;
                    $tran->rollBack();
                    return $code;
                }
            }else{
                $user_address=self::find()->where(['id'=>$address_id])->one();
                $user_address->consignee=$consignee;
                $user_address->region=$region;
                $user_address->mobile=$mobile;
                $user_address->district=$district_code;
                $res =$user_address->save(false);
                if (!$res){
                    $code=500;
                    $tran->rollBack();
                    return $code;
                }
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (\Exception $e)
        {
            $code=500;
            $tran->rollBack();
            return $code;
        }
    }

    /**
     * @param $address_id
     * @return int
     */
    public  static  function  DelAddress($address_id)
    {
        $address=self::findOne($address_id);
        if (!$address)
        {
            $code=1000;
            return $code;
        }
        $user = Yii::$app->user->identity;
        $tran = Yii::$app->db->beginTransaction();
        try{
            if ($address->default==1)
            {
                $res=$address->delete();
                if (!$res)
                {
                    $tran->rollBack();
                    $code=500;
                    return $code;
                }
                $addressData=self::find()
                    ->where(['uid'=>$user->id])
                    ->andWhere(['default'=>0])
                    ->one();
                if ($addressData)
                {
                    $addressData->default=1;
                    $res1=$addressData->save(false);
                    if (!$res1)
                    {
                        $tran->rollBack();
                        $code=500;
                        return $code;
                    }
                }
            }else{
                $res=$address->delete();
                if (!$res)
                {
                    $tran->rollBack();
                    $code=500;
                    return $code;
                }
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (\Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }

    }








}