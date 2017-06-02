<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/27/17
 * Time: 9:34 AM
 */

namespace app\services;

use yii\db\ActiveRecord;

class ModelService
{
    const SEPARATOR_SORT = ':';
    const SORT_DIRECTIONS = [
        SORT_DESC => 'DESC',
        SORT_ASC => 'ASC',
    ];

    /**
     * Generate sorting statements for query
     *
     * @param  ActiveRecord $model active record model
     * @param  array        $sort  sorting fields with direction
     * @return bool|string
     */
    public static function sortFields(ActiveRecord $model, array $sort = ['id:' . self::SORT_DIRECTIONS[SORT_DESC]])
    {
        $attributes = $model->getAttributes();
        $orderBy = [];

        foreach ($sort as $v) {
            list($field, $direction) = explode(self::SEPARATOR_SORT, $v);
            !$direction && $direction = SORT_DESC;
            if (!$field) {
                return false;
            }

            if (!isset(self::SORT_DIRECTIONS[$direction])) {
                return false;
            }

            if (in_array($field, array_keys($attributes))) {
                $orderBy[$field] = self::SORT_DIRECTIONS[$direction];
            } else {
                return false;
            }
        }

        $orderByStr = '';
        foreach  ($orderBy as $filed => $direction) {
            $orderByStr .= ',' . $filed . ' ' . $direction;
        }

        return trim($orderByStr, ',');
    }
}