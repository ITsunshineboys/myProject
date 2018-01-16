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
    const PAGE_SIZE_DEFAULT = 12;
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

    public static function findByAll($select = [])
    {
        $series = self::find()
            ->asArray()
            ->select($select)
            ->where(['status'=>self::STATUS_ONLINE])
            ->orderBy(['series_grade'=>SORT_ASC])
            ->all();
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
            ->where(['status'=>self::STATUS_ONLINE])
            ->orderBy(['id'=>SORT_ASC])
            ->all();
    }

    public static function pagination($page = 1, $size = self::PAGE_SIZE_DEFAULT)
    {
        $offset = ($page - 1) * $size;
        $list = self::find()
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->orderBy('series_grade ASC')
            ->all();

        foreach ($list as &$effect) {

            if(isset($effect['creation_time'])){
                $effect['creation_time']=date('Y-m-d H:i', $effect['creation_time']);
            }

        }

        return [
            'total' => (int)self::find()->where([])->asArray()->count(),
            'page'=>$page,
            'size'=>$size,
            'details' => $list
        ];
    }

    public static function findByTimeSort($sort,$page = 1, $size = self::PAGE_SIZE_DEFAULT)
    {
        $offset = ($page - 1) * $size;
        if ($sort  == self::STATUS_OFFLINE){
            $series= self::find()
                ->asArray()
                ->orderBy(['creation_time'=>SORT_DESC])
                ->offset($offset)
                ->limit($size)
                ->all();
            foreach ($series as $one_series){
                $one_series['creation_time'] = date('Y-m-d H:i', $one_series['creation_time']);
                $all [] = $one_series;
            }
            return $all;
        }elseif ($sort  == self::STATUS_ONLINE){
            $series =  self::find()
                ->asArray()
                ->orderBy(['creation_time'=>SORT_ASC])
                ->offset($offset)
                ->limit($size)
                ->all();
            foreach ($series as $one_series){
                $one_series['creation_time'] = date('Y-m-d H:i', $one_series['creation_time']);
                $all [] = $one_series;
            }
            return $all;
        }
    }
}