<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/9 0009
 * Time: 下午 17:25
 */

namespace app\models;

use yii\db\ActiveRecord;

class Style extends ActiveRecord
{
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;
    const FIELDS_ADMIN= [
        'id',
        'series',
        'intro',
        'theme',
        'modelling_length_coefficient',
        'modelling_day_coefficient',
        'flat_area_coefficient',
        'flat_day_coefficient',
        'category_id',
        'status',
        'creation_time',
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'style';
    }

    public static function findByAll()
    {
        $select = 'style.id,style.style,style.intro,style.theme';
        $series = self::find()->asArray()->select($select)->all();
        return $series;
    }

    /**
     * Get styles by category id
     *
     * @param int $categoryId category id
     * @param array $select select fields default id and style
     * @return array
     */
    public static function stylesByCategoryId($categoryId, $select = ['id', 'style'])
    {
        $categoryId = (int)$categoryId;
        if ($categoryId <= 0) {
            return [];
        }

        return self::find()->where(['category_id' => $categoryId])->select($select)->asArray()->all();
    }
}
