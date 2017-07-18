<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
const USER = 'user_';

class Addressadd extends Model
{
    public $mobile;
    public $region;
    public $district;
    public $consignee;

    public function rules()
    {
        return [
            ['mobile', 'required','message'=>'收货人手机号不能为空'],
            ['region', 'required','message'=>'收货详细地址不能为空'],
            ['district', 'required','message'=>'收货地址不能为空'],
            ['consignee', 'required','message'=>'收货人不能为空'],
            ['mobile','match','pattern'=>'/0?(13|14|15|18)[0-9]{9}/','message'=>'手机号格式不正确'],
        ];
    }

    //添加收货地址
     public function insertaddress($mobile,$consignee,$region,$district){
         $addresstoken= md5($mobile.$consignee.date('Y-m-d H:i:s', time()));
         $res=Yii::$app->db->createCommand()->insert('user_address',[
             'mobile'    => $mobile,
             'consignee' =>$consignee,
             'region'      =>$region,
             'district'  =>$district,
             'addresstoken'=>$addresstoken,
         ])->execute();
         if ($res){
             $session = Yii::$app->session;
             $session['addresstoken']=$addresstoken;
         }
        return $res;
     }
    //获取用户收货地址
    public function getaddress($addresstoken){
        $query=new \yii\db\Query();
        $array  = $query->from('user_address')->select('mobile,consignee,region,district,addresstoken')->where(['addresstoken' => $addresstoken])->limit(1)->all();
        if ($array){
            return $array;
        }else
        {
            return null;
        }
    }

}