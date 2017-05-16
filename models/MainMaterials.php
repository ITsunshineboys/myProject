<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/16 0016
 * Time: 下午 16:38
 */
namespace app\models;

use yii\db\ActiveRecord;

class MainMaterials extends ActiveRecord
{
    public static function tableName()
    {
        return 'main_materials';
    }
}