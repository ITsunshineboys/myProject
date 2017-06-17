<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/17 0017
 * Time: 上午 10:21
 */
namespace app\models;
use yii\db\ActiveRecord;

class PaintReconstruction extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'paint_reconstruction';
    }


}