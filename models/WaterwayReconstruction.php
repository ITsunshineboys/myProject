<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/27 0027
 * Time: 下午 14:33
 */
namespace app\models;
use yii\db\ActiveRecord;

class WaterwayReconstruction extends ActiveRecord
{

     /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'waterway_reconstruction';
    }


    public static function findByAll($id = '')
    {
        if($id){
            $circuitry = self::find()->where(['decoration_list_id'=>$id])->all();
        }
        return $circuitry;
    }
}