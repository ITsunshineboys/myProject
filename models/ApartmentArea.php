<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/23 0023
 * Time: 下午 15:02
 */

namespace app\models;

use yii\db\ActiveRecord;

class ApartmentArea extends ActiveRecord
{

    const PAGE_SIZE_DEFAULT = 12;
    const FIELDS_NAME = [
        'province_code',
        'city_code',
        'min_area',
        'max_area',
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'apartment_area';
    }

    public static function findByAll($select)
    {
        return self::find()
            ->asArray()
            ->select($select)
            ->all();
    }
}