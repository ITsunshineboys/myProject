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
     * 获取线下店商城-发票信息
     *
     */
    public function  getlineinvoice($invoicetoken){
        $query=new \yii\db\Query();
        $array  = $query->from('invoice')->select('invoice_content,invoice_header')->where(['invoicetoken' => $invoicetoken])->limit(1)->all();
//        $array=self::find()->select('invoice_content,invoice_header')->where(['invoicetoken' => $invoicetoken])->limit(1)->asArray()->all();
        if ($array){
            return $array;
        }else
        {
            return null;
        }
    }
}