<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use app\models\LogisticsDistrict;
const USER_ADDRESS = 'user_address';

class Addressadd extends  ActiveRecord
{
    public $mobile;
    public $region;
    public $district;
    public $consignee;

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
     public function insertaddress($mobile,$consignee,$region,$districtcode){
         $addresstoken= md5($mobile.$consignee.date('Y-m-d H:i:s', time()));
         $data=self::find()->where(['mobile'=>$mobile,'consignee'=>$consignee,'district'=>$districtcode])->asArray()->one();
         if ($data){
             $res=Yii::$app->db->createCommand()->update(USER_ADDRESS, ['mobile'=>$mobile,'consignee'=>$consignee,'district'=>$districtcode,'region'=>$region,'addresstoken'=>$addresstoken],'id='.$data['id'])->execute();
             $session = Yii::$app->session;
             $session['addresstoken']=$addresstoken;
             return true;
         }else{
             $res=Yii::$app->db->createCommand()->insert(USER_ADDRESS,[
                 'mobile'      => $mobile,
                 'consignee'   => $consignee,
                 'region'      => $region,
                 'district'    => $districtcode,
                 'addresstoken'=>$addresstoken
             ])->execute();
             if ($res){
                 $session = Yii::$app->session;
                 $session['addresstoken']=$addresstoken;
             }
         }
         return $res;
     }
    /**
     * 无登录app-获取收货地址
     * @param $addresstoken
     * @return array|null
     */
    public function getaddress($addresstoken){
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



}