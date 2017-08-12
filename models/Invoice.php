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
    public function addinvoice($invoice_type,$invoice_header_type,$invoice_header,$invoice_content,$invoicer_card)
    {
        $array=array();
        if (empty($invoice_type)||empty($invoice_header_type)||empty($invoice_header)||empty($invoice_content))
        {
            $code=1000;
            $array['code']=$code;
            $array['data']=null;
            return $array;
        }else{
            $creat_time=date('Y-m-d H:i:s',time());
            $invoicetoken=md5($invoice_type.$invoice_header.$creat_time);
            $data=self::find()->where(['invoice_type'=>$invoice_type,'invoice_header_type'=>$invoice_header_type,'invoice_header'=>$invoice_header,'invoice_content'=>$invoice_content,'invoicer_card'=>$invoicer_card])->asArray()->one();
            if ($data){
                $res=Yii::$app->db->createCommand()->update('invoice', [
                    'invoice_header_type'   => $invoice_header_type,
                    'invoice_header'        => $invoice_header,
                    'invoice_type'          => $invoice_type,
                    'invoice_content'       => $invoice_content,
                    'creat_time'            => $creat_time,
                    'invoicer_card'         => $invoicer_card,
                    'invoicetoken'          => $invoicetoken],'id='.$data['id'])->execute();
                    $session = Yii::$app->session;
                    $session['invoicetoken']=$invoicetoken;
                    $code =200;
                    $array['code']=$code;
                    $array['data']=$res;
                    return $array;
            }else{
                $res=Yii::$app->db->createCommand()->insert('invoice',[
                    'invoice_header_type'   => $invoice_header_type,
                    'invoice_header'        => $invoice_header,
                    'invoice_type'          => $invoice_type,
                    'invoice_content'       => $invoice_content,
                    'creat_time'            => $creat_time,
                    'invoicer_card'         => $invoicer_card,
                    'invoicetoken'          =>$invoicetoken
                ])->execute();
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