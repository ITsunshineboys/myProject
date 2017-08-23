<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use app\models\LogisticsDistrict;


class Addressadd extends  ActiveRecord
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
     public static function insertaddress($mobile,$consignee,$region,$districtcode){
         $addresstoken= md5($mobile.$consignee.date('Y-m-d H:i:s', time()));
         $data=self::find()->where(['mobile'=>$mobile,'consignee'=>$consignee,'district'=>$districtcode])->asArray()->one();
         if ($data){
             $res=Yii::$app->db->createCommand()->update(self::USER_ADDRESS,[
                 'mobile'      => $mobile,
                 'consignee'   => $consignee,
                 'region'      => $region,
                 'district'    => $districtcode,
                 'addresstoken'=>$addresstoken
             ],['id'=>$data['id']])->execute();
             $session = Yii::$app->session;
             $session['addresstoken']=$addresstoken;
             if ($res){
                 return true;
             }
         }else{
             $res=Yii::$app->db->createCommand()->insert(self::USER_ADDRESS,[
                 'mobile'      => $mobile,
                 'consignee'   => $consignee,
                 'region'      => $region,
                 'district'    => $districtcode,
                 'addresstoken'=>$addresstoken
             ])->execute();
             if ($res){
                 $session = Yii::$app->session;
                 $session['addresstoken']=$addresstoken;
                 return true;
             }

         }

     }
     
    /**
     * 无登录app-获取收货地址
     * @param $addresstoken
     * @return array|null
     */
    public static function getaddress($addresstoken){
        $array  = self::find()->select('id,mobile,consignee,region,district')->where(['addresstoken' => $addresstoken])->limit(1)->asArray()->all();
        if ($array){
            foreach ($array as $k=>$v){
                $model=new LogisticsDistrict();
                $array[$k]['district']=$model->getdistrict($array[$k]['district']);
            }
            return $array;
        }else
        {
            return null;
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
    public static function UserAddressAdd($district_code,$region,$consignee,$mobile,$user_id)
    {
            $tran = Yii::$app->db->beginTransaction();
            try{
                $address=self::find()->where(['uid'=>$user_id])->all();
                foreach ($address as $k =>$v)
                {
                    $address[$k]->default=0;
                    $res[$k]=$address[$k]->save();
                    if (!$res[$k]){
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
                $res =$user_address->save();
                if (!$res){
                    $code=500;
                    $tran->rollBack();
                    return $code;
                }
                $tran->commit();
                $code=200;
                return $code;
            }catch (Exception $e)
            {
                $code=500;
                $tran->rollBack();
                return $code;
            }
    }



}