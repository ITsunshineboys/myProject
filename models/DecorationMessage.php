<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/25 0025
 * Time: 下午 15:58
 */
namespace app\models;

use yii\db\ActiveRecord;

class DecorationMessage extends ActiveRecord
{
    const FIELDS_ADMIN = [
            'decoration_add_id',
            'quantity',
            'style_id',
            'series_id',
            'min_area',
            'max_area',
        ];
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'decoration_message';
    }
}