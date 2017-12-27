<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/27 0027
 * Time: 上午 11:01
 */
namespace app\models;

use yii\db\ActiveRecord;

class EditCategory extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'edit_category';
    }

}