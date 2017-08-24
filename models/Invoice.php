<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/16 0016
 * Time: 下午 16:42
 */
namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Json;

class Invoice extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'invoice';
    }
    /**
     * @param   线下商城添加发票信息
     * @return array
     */
    public static function addinvoice($invoice_type,$invoice_header_type,$invoice_header,$invoice_content,$invoicer_card)
    {
            $array=array();
            $creat_time=date('Y-m-d H:i:s',time());
            $invoicetoken=md5($invoice_type.$invoice_header.$creat_time);
            $data=self::find()->where(['invoice_type'=>$invoice_type,'invoice_header_type'=>$invoice_header_type,'invoice_header'=>$invoice_header,'invoice_content'=>$invoice_content,'invoicer_card'=>$invoicer_card])->asArray()->one();
            if ($data){
                $invoice=self::find()->where(['id'=>$data['id']])->one();
                $invoice->invoice_header_type=$invoice_header_type;
                $invoice->invoice_header=$invoice_header;
                $invoice->invoice_type=$invoice_type;
                $invoice->invoice_content=$invoice_content;
                $invoice->creat_time=$creat_time;
                $invoice->invoicer_card=$invoicer_card;
                $invoice->invoicetoken=$invoicetoken;
                $res=$invoice->save();
                if ($res==true){
                    $session = Yii::$app->session;
                    $session['invoicetoken']=$invoicetoken;
                    $code =200;
                    $array['code']=$code;
                    $array['data']=$res;
                    return $array;
                }
            }else{
                $model=new self();
                $model->invoice_header_type=$invoice_header_type;
                $model->invoice_header=$invoice_header;
                $model->invoice_type=$invoice_type;
                $model->invoice_content=$invoice_content;
                $model->creat_time=$creat_time;
                $model->invoicer_card=$invoicer_card;
                $model->invoicetoken=$invoicetoken;
                $res=$model->save();
                if ($res==true){
                    $session = Yii::$app->session;
                    $session['invoicetoken']=$invoicetoken;
                    $code =200;
                    $array['code']=$code;
                    $array['data']=$res;
                    return $array;
                }else{
                    $code=1050;
                    $array['code']=$code;
                    $array['data']=null;
                    return $array;
                }
            }
    }

    /**
     * @param $invoice_type
     * @param $invoice_header_type
     * @param $invoice_header
     * @param $invoice_content
     * @param $invoicer_card
     * @param $user
     * @return int
     */
    public static  function  AddUserInvoice($invoice_type,$invoice_header_type,$invoice_header,$invoice_content,$invoicer_card,$user)
    {
        $address_count=self::find()->where(['uid'=>$user->id])->count();
        if ($address_count>=6){
            $code=1026;
            return $code;
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            $invoice=self::find()->where(['uid'=>$user->id])->all();
            foreach ($invoice as $k =>$v)
            {
                $invoice[$k]->default=0;
                $res[$k]=$invoice[$k]->save();
                if (!$res[$k]){
                    $code=500;
                    $tran->rollBack();
                    return $code;
                }
            }
            $time=date('Y-m-d H:i:s',time());
            $invoice_add=new self;
            $invoice_add->invoice_type=$invoice_type;
            $invoice_add->invoice_header_type=$invoice_header_type;
            $invoice_add->invoice_header=$invoice_header;
            $invoice_add->invoicer_card=$invoicer_card;
            $invoice_add->invoice_content=$invoice_content;
            $invoice_add->uid=$user->id;
            $invoice_add->create_time=$time;
            $invoice_add->default=1;
            $res =$invoice_add->save();
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

    /**
     * 获取线下店商城-发票信息
     *
     */
    public function  getlineinvoice($invoicetoken){
        $array  = self::find()->select('invoice_content,invoice_header')->where(['invoicetoken' => $invoicetoken])->one();
        if ($array){
            return $array;
        }else
        {
            return null;
        }
    }

    /**
     * @param $invoice_id
     * @param $user
     * @return int
     */
    public static  function  setDefaultInvoice($invoice_id,$user)
    {
        $tran = Yii::$app->db->beginTransaction();
        try{
            $invoice=self::find()->where("id !=  {$invoice_id}")->andWhere(['uid'=>$user->id])->all();
            foreach ( $invoice as $k =>$v){
                $invoice[$k]->default=0;
                $res[$k]=$invoice[$k]->save();
                if (!$res[$k]){
                    $code=500;
                    $tran->rollBack();
                    return $code;
                }
            }
            $invoice_set=self::find()->where(['id'=>$invoice_id])->andWhere(['uid'=>$user->id])->one();
            $invoice_set->default=1;
            $res1=$invoice_set->save();
            if (!$res1){
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


    /**
     * @param $invoice_type
     * @param $invoice_header_type
     * @param $invoice_header
     * @param $invoice_content
     * @param $invoicer_card
     * @param $user
     * @param $invoice_id
     * @return int
     */
    public static function  updateUserInvoice($invoice_type,$invoice_header_type,$invoice_header,$invoice_content,$invoicer_card,$user,$invoice_id)
    {
        $tran = Yii::$app->db->beginTransaction();
        try{
            $time=date('Y-m-d H:i:s',time());
            $invoice_up=self::find()->where(['id'=>$invoice_id])->one();
            $invoice_up->invoice_type=$invoice_type;
            $invoice_up->invoice_header_type=$invoice_header_type;
            $invoice_up->invoice_header=$invoice_header;
            $invoice_up->invoicer_card=$invoicer_card;
            $invoice_up->invoice_content=$invoice_content;
            $invoice_up->uid=$user->id;
            $invoice_up->create_time=$time;
            $res=$invoice_up->save();
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