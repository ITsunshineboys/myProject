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
    const SEPARATOR_ERRCODE_ERRMSG = ':';

    /**
     * Generate sorting statements for query
     *
     * @param  ActiveRecord $model active record model
     * @param  array $sort sorting fields with direction
     * @return bool|string
     */
    public static function sortFields(ActiveRecord $model, array $sort = ['id:' . SORT_DESC])
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
        foreach ($orderBy as $filed => $direction) {
            $orderByStr .= ',' . $filed . ' ' . $direction;
        }

        return trim($orderByStr, ',');
    }

    /**
     * Get custom error code by error message
     *
     * @param string $errMsg error message
     * @return bool|int
     */
    public static function customErrCode($errMsg)
    {
        if (preg_match('/\d+' . self::SEPARATOR_ERRCODE_ERRMSG . '/', $errMsg, $matches)) {
            return str_replace(self::SEPARATOR_ERRCODE_ERRMSG, '', $matches[0]);
        }

        return false;
    }

    /**
     * Select model fields
     *
     * @return array
     */
    public static function selectModelFields(ActiveRecord $model, array $fields = [])
    {
        $data = [];

        $modelAttrs = array_keys($model->attributes);

        if (!$fields) {
            $fields = $modelAttrs;
        } else {
            if (array_diff($fields, $modelAttrs)) {
                return $data;
            }
        }

        foreach ($fields as $field) {
            $data[$field] = $model->$field;
        }

        return $data;
    }
}