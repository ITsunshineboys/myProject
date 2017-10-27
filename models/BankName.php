<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/5 0005
 * Time: 上午 11:52
 */
namespace app\models;
use yii\db\ActiveRecord;

class BankName extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'bank_name';
    }


}