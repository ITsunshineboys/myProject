<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/27 0027
 * Time: 下午 14:40
 */
namespace app\models;
use yii\db\ActiveRecord;

class WaterproofReconstruction extends ActiveRecord
{

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'waterproof_reconstruction';
    }


    public static function findByAll($id = '')
    {
        if($id){
            $circuitry = self::find()->where(['decoration_list_id'=>$id])->all();
        }
        return $circuitry;
    }
}