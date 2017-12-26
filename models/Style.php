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
    const PAGE_SIZE_DEFAULT = 10;
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
        'imges'
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'style';
    }

    public static function findByAll($select=[])
    {
        $series = self::find()
            ->asArray()
            ->select($select)
            ->where(['status'=>self::STATUS_ONLINE])
            ->orderBy(['id'=>SORT_ASC])
            ->all();
        return $series;
    }

    /**
     * Get styles by category id
     *
     * @param int $categoryId category id
     * @param array $statuses status list default online status
     * @param array $select select fields default id and style
     * @return array
     */
    public static function stylesByCategoryId($categoryId, $statuses = [self::STATUS_ONLINE], $select = ['id', 'style'])
    {
        if (!GoodsCategory::find()->where(['id' => $categoryId, 'has_style' => 1])->exists()) {
            return [];
        }
        return self::find()->where(['in', 'status', $statuses])->select($select)->asArray()->all();
    }

    public static function findByStyle()
    {
        return self::find()
            ->asArray()
            ->select(['id','style'])
            ->where(['status'=>self::STATUS_ONLINE])
            ->orderBy(['id'=>SORT_ASC])
            ->all();
    }

    public static function pagination($page = 1, $size = self::PAGE_SIZE_DEFAULT)
    {
        $offset = ($page - 1) * $size;
        $style = Style::find()
            ->asArray()
            ->select('creation_time,theme,intro,style,id,status,images')
            ->offset($offset)
            ->limit($size)
            ->All();
        $all = [];
        foreach ($style as $one_series) {
            $one_series['creation_time'] = date('Y-m-d H:i', $one_series['creation_time']);
            $all [] = $one_series;
        }

        return [
            'total' => (int)self::find()->where([])->asArray()->count(),
            'page'=>$page,
            'size'=>$size,
            'details' => $all
        ];
    }

    public static function findByTimeSort($sort,$page = 1, $size = self::PAGE_SIZE_DEFAULT)
    {
        $offset = ($page - 1) * $size;
        if ($sort  == self::STATUS_OFFLINE){
            $style = self::find()
                ->asArray()
                ->orderBy(['creation_time'=>SORT_DESC])
                ->offset($offset)
                ->limit($size)
                ->all();
            foreach ($style as $one_series){
                $one_series['creation_time'] = date('Y-m-d H:i', $one_series['creation_time']);
                $all [] = $one_series;
            }
            return [
                'total' => (int)self::find()->where([])->asArray()->count(),
                'page'=>$page,
                'size'=>$size,
                'details' => $all
            ];
        }elseif ($sort  == self::STATUS_ONLINE){
            $style= self::find()
                ->asArray()
                ->orderBy(['creation_time'=>SORT_ASC])
                ->offset($offset)
                ->limit($size)
                ->all();
            foreach ($style as $one_series){
                $one_series['creation_time'] = date('Y-m-d H:i', $one_series['creation_time']);
                $all [] = $one_series;
            }
            return [
                'total' => (int)self::find()->where([])->asArray()->count(),
                'page'=>$page,
                'size'=>$size,
                'details' => $all
            ];
        }
    }

    /**
     * Find style names
     *
     * @param array $where query condition default empty
     * @return array
     */
    public static function findNames(array $where = [])
    {
        $names = self::find()
            ->asArray()
            ->select(['style'])
            ->where($where)
            ->all();
        return array_map(function ($v) {
            return $v['style'];
        }, $names);
    }
}
