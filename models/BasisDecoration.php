<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/18 0018
 * Time: 下午 14:35
 */
namespace app\models;
use yii\db\ActiveRecord;

class BasisDecoration extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'basis_decoration';
    }
}