<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/27/17
 * Time: 2:08 PM
 */

namespace app\models;
use app\services\ModelService;
use yii\db\ActiveRecord;
use Yii;

class UserNewsRecord extends ActiveRecord
{


    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'user_news_record';
    }

    /**
     * @return int
     */
    public static function  AddOrderNewRecord($user, $title, $role_id, $content, $order_no, $sku, $type)
    {
        $tran = Yii::$app->db->beginTransaction();
        try{
            $record=new UserNewsRecord();
            $record->uid=$user->id;
            $record->role_id=$role_id;
            $record->title=$title;
            $record->content=$content;
            $record->send_time=time();
            $record->order_no=$order_no;
            $record->sku=$sku;
            if (!$record->save(false))
            {
                $tran->rollBack();
                $code=1000;
                return $code;
            }



            $registration_id=$user->registration_id;
            $push=new Jpush();
            $extras =[
                'role_id'=>$role_id,
                'order_no'=>$order_no,
                'sku'=>$sku,
                'type'=>$type,
            ];

            //推送附加字段的类型
            $m_time = '86400';//离线保留时间
            $receive = ['registration_id'=>[$registration_id]];//设备的id标识

            $result = $push->push($receive,$title,$content,$extras, $m_time);
            if (!$result)
            {
                $code=1000;
                $tran->rollBack();
                return $code;
            }
            $tran->commit();
            return 200;
        }catch (\Exception $e){
            $code=1000;
            $tran->rollBack();
            return $code;
        }

    }


}