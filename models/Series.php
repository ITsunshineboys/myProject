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
     * @param  int $categoryId category id
     * @return array
     */
    public static function seriesByCategoryId($categoryId)
    {
        $categoryId = (int)$categoryId;
        if ($categoryId <= 0) {
            return [];
        }

        return self::find()->where(['category_id' => $categoryId])->select(['id', 'series'])->asArray()->all();
    }
}