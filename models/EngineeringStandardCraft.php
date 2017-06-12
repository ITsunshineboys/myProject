<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/10 0010
 * Time: 下午 17:40
 */
namespace app\models;
use yii\db\ActiveRecord;

class EngineeringStandardCraft  extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'engineering_standard_craft';
    }

    public static function findByOne($project='',$district =510100)
    {
        if($project){
            $craft = self::find()->asArray()->where(['and',['district_code'=>$district],['project'=>$project]])->one();
            $craft_material = $craft['material'];
        }
        return $craft_material;
    }
}