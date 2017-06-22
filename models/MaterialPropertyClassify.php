<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/22 0022
 * Time: 上午 9:42
 */
namespace app\models;
use yii\db\ActiveRecord;

class MaterialPropertyClassify extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'material_property_classify';
    }

    public static function findByAll($classify = [])
    {
        if ($classify)
        {
            $all = self::find()->asArray()->where(['classify'=>$classify])->all();
            $material = [];
            foreach ($all as $one)
            {
                $material [] = $one['material'];
            }
        }

        return $material;
    }

}