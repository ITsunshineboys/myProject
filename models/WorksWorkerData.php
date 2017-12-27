<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/19 0019
 * Time: 下午 14:35
 */
namespace app\models;

use yii\db\ActiveRecord;

class WorksWorkerData extends ActiveRecord
{
    const  SUP_BANK_CARD = 'works_worker_data';

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'works_worker_data';
    }

    public static function plotAdd($effect_id,$worker_kind,$worker_price)
    {
        $res = \Yii::$app->db->createCommand()->insert(self::SUP_BANK_CARD,[
            'effect_id'     => $effect_id,
            'worker_kind'   => $worker_kind,
            'worker_price'  => $worker_price,
        ])->execute();

        return $res;
    }

    public static function plotEdit($id,$worker_kind,$worker_price)
    {
        $res = \Yii::$app->db->createCommand()->update(self::SUP_BANK_CARD,[

            'worker_kind'   => $worker_kind,
            'worker_price'  => $worker_price,
        ],['id'=>$id])->execute();

        return $res;
    }

    public static function findById($id)
    {
        return self::find()
            ->asArray()
            ->where(['effect_id'=>$id])
            ->all();
    }



    public static function findByIds($ids)
    {
        $data= self::find()
            ->asArray()
            ->where(['in','effect_id',$ids])
            ->all();
        $worker_list=WorkerType::laborlist();
        foreach ($data as &$v){
            foreach ($worker_list as $item){
                if($v['worker_kind']==$item['worker_name']){
                    $v['worker_id']=$item['id'];
                }
            }
        }


        var_dump($data);die;
    }
}