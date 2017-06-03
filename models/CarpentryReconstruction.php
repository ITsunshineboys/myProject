<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/1 0001
 * Time: 上午 9:41
 */
namespace app\models;
use yii\db\ActiveRecord;

class CarpentryReconstruction extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'carpentry_reconstruction';
    }

}