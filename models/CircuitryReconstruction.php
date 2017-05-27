<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/26 0026
 * Time: 下午 14:10
 */
namespace app\models;
use yii\db\ActiveRecord;

class CircuitryReconstruction extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'circuitry_reconstruction';
    }

    public static function findByAll($id = '')
    {
        if($id){
            $circuitry = self::find()->where(['decoration_list_id'=>$id])->all();
        }
            return $circuitry;
    }
}