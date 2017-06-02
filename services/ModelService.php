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
        'DESC',
        'ASC',
    ];

    /**
     * Generate sorting statements for query
     *
     * @param  ActiveRecord $model active record model
     * @param  array        $sort  sorting fields with direction
     * @return bool|string
     */
    public static function sortFields(ActiveRecord $model, array $sort = ['id:' . self::SORT_DIRECTIONS[0]])
    {
        $attributes = $model->getAttributes();
        foreach ($sort as $v) {
            list($field, $direction) = explode(self::SEPARATOR_SORT, $v);
            !$direction && $direction = self::SORT_DIRECTIONS[0];
            if (!$field) {
                return false;
            }

            $direction = strtoupper($direction);

            if (in_array($field, array_keys($attributes))
                && in_array($direction, self::SORT_DIRECTIONS)
            ) {
                $orderBy[$field] = $direction;
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