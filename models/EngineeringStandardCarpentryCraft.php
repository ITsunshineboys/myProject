<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/13 0013
 * Time: 下午 13:56
 */
namespace app\models;

use yii\db\ActiveRecord;

class EngineeringStandardCarpentryCraft extends ActiveRecord
{

    const UNIT = [
        1 => 'm',
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'engineering_standard_carpentry_craft';
    }

    public static function findByAll($where)
    {
        $row =  self::find()
            ->asArray()
            ->select('id,title,value,unit')
            ->where($where)
            ->all();
        if($row==null){
            $row=WorkerType::find()
                ->select('worker_name,id')
                ->asArray()
                ->where(['status'=>4])
                ->all();
           foreach ($row as &$a){

              $a['title']=$a['worker_name'];
              $a['unit'] = self::UNIT[1];
              $a['value']='';
               unset($a['worker_name']);
           }

        }else{
            foreach ($row as &$one){

                $one['unit'] = self::UNIT[$one['unit']];
                $one['value'] = $one['value'] / 100;
                if($one['value']==0){
                    $one['value']='其它';//TODO 修改
                }
            }

        }

        return $row;

    }
}