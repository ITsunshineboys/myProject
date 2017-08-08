<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/9 0009
 * Time: 下午 17:15
 */

namespace app\models;

use yii\db\ActiveRecord;

class Series extends ActiveRecord
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
        'status',
        'creation_time',
        'series_grade',
    ];
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'series';
    }

    public static function findByAll()
    {
        $select = 'series.id,series.series,series.intro,series.theme';
        $series = self::find()->asArray()->select($select)->all();
        return $series;
    }

    /**
     * Get series by category id
     *
     * @param int $categoryId category id
     * @param array $statuses status list default online status
     * @param array $select select fields default id and series
     * @return array
     */
    public static function seriesByCategoryId($categoryId, $statuses = [self::STATUS_ONLINE], $select = ['id','series'])
    {
        if (!GoodsCategory::find()->where(['id' => $categoryId, 'has_series' => 1])->exists()) {
            return [];
        }
        return self::find()->where(['in', 'status', $statuses])->select($select)->asArray()->all();
    }

    public static function findBySeries()
    {
        return self::find()
            ->asArray()
            ->select(['id','series'])
            ->orderBy(['id'=>SORT_ASC])
            ->all();
    }
}