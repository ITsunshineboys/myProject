<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class GoodsComment extends ActiveRecord
{
    const FIELDS_APP = ['name', 'icon', 'create_time', 'content'];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_comment';
    }
}