<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/27/17
 * Time: 9:34 AM
 */

namespace app\services;

use app\models\District;
use yii\db\ActiveRecord;
use yii\db\Query;
use Yii;

class ModelService
{
    const SEPARATOR_SORT = ':';
    const SEPARATOR_GENERAL = ',';
    const SORT_DIRECTIONS = [
        SORT_DESC => 'DESC',
        SORT_ASC => 'ASC',
    ];
    const SEPARATOR_ERRCODE_ERRMSG = ':';
    const PAGE_SIZE_DEFAULT = 12;
    const ORDER_BY_DEFAULT = ['id' => SORT_DESC];
    const SUFFIX_FIELD_DESCRIPTION = '_desc';
    const FORMAT_DATA_METHOD = 'formatData';
    const EXTRA_DATA_METHOD = 'extraData';
    const PAGINATION_RETURN_ARRAY_KEY_TOTAL = 'total';
    const PAGINATION_RETURN_ARRAY_KEY_DETAILS = 'details';
    const FIELD_IDENTITY = 'id';
    const FIELD_DISTRICT_CODE = 'district_code';
    const FIELD_DISTRICT_NAME = 'district_name';
    const FIELD_ADDRESS = 'address';
    const FIELD_ICON = 'icon';
    const MAIN_TABLE_AS = 't';
    const REVIEW_STATUS_APPROVE = 2;
    const REVIEW_STATUS_REJECT = 1;
    const REVIEW_STATUS_NOT_REVIEWED = 0;
    const REVIEW_STATUSES = [
        self::REVIEW_STATUS_REJECT,
        self::REVIEW_STATUS_APPROVE,
    ];
    const SCENARIO_REVIEW = 'review';
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';

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
            if (stripos($v, self::SEPARATOR_SORT) === false) {
                return false;
            }

            list($field, $direction) = explode(self::SEPARATOR_SORT, $v);
            !$direction && $direction = SORT_DESC;
            if (!$field) {
                return false;
            }

            if (!in_array($direction, array_keys(self::SORT_DIRECTIONS))) {
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
     * @param ActiveRecord $model model
     * @param array $fields default empty
     * @return array
     */
    public static function selectModelFields(ActiveRecord $model, array $fields = [])
    {
        $model->refresh();

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

    /**
     * View model by fields
     *
     * @param ActiveRecord $model model
     * @param array $fields model fields
     * @return array
     */
    public static function viewModelByFields(ActiveRecord $model, array $fields)
    {
        $viewData = [];

        foreach ($fields as $field) {
            $viewData[$field] = $model->$field;
        }

        return $viewData;
    }

    /**
     * Check if some attribute value has been updated
     *
     * @param array $attrs attributes to be checked
     * @param ActiveRecord $model model
     * @return bool
     */
    public static function hasChangedAttr(array $attrs, ActiveRecord $model)
    {
        return count(array_intersect($attrs, $model->getDirtyAttributes())) != count($attrs);
    }

    /**
     * Check if unique error
     *
     * @param ActiveRecord $model model
     * @param $attr attribute name
     * @return bool
     */
    public static function uniqueError(ActiveRecord $model, $attr)
    {
        $errors = $model->errors;
        return isset($errors[$attr]) && false !== stripos($errors[$attr][0], 'has already been taken');
    }

    /**
     * Get model list
     *
     * @param Query $query query object
     * @param array $select select fields default all fields
     * @param array $extraFields extra fields default empty
     * @param ActiveRecord $model model
     * @param string $formatMethod format method default 'formatData'
     * @param string $extraMethod extra method default 'extraData'
     * @param int $page page number default 1
     * @param int $size page size default 12
     * @param array $orderBy order by fields default id desc
     * @return array
     */
    public static function pagination(Query $query, array $select = [], array $extraFields = [], ActiveRecord $model, $page = 1, $size = self::PAGE_SIZE_DEFAULT, $formatMethod = self::FORMAT_DATA_METHOD, $extraMethod = self::EXTRA_DATA_METHOD, $orderBy = self::ORDER_BY_DEFAULT)
    {
        !in_array(self::FIELD_IDENTITY, $select) && $select[] = self::FIELD_IDENTITY;
        $query->select($select)->from($model->tableName());
        $offset = ($page - 1) * $size;
        $data = [
            self::PAGINATION_RETURN_ARRAY_KEY_TOTAL => $query->count(),
            self::PAGINATION_RETURN_ARRAY_KEY_DETAILS => $query
                ->orderBy($orderBy)
                ->offset($offset)
                ->limit($size)
                ->all()
        ];

        foreach ($data[self::PAGINATION_RETURN_ARRAY_KEY_DETAILS] as &$row) {
            if ($extraFields && method_exists($model, $extraMethod)) {
                $row = array_merge($model::$extraMethod($row[self::FIELD_IDENTITY], $extraFields), $row);
            }
            if (method_exists($model, $formatMethod)) {
                $model::$formatMethod($row);
            }
        }

        return $data;
    }

    /**
     * 得到开始和结束时间 戳
     * @param $time_type
     * @param $time_start
     * @param $time_end
     * @return array
     */
    public static function timeDeal($time_start)
    {
        if($time_start){
            list($year, $month, $day) = explode('-', $time_start);
            $startTime = date("Y-m-d H:i:s", mktime(0, 0, 0, $month, $day, $year));
            $endTime = date("Y-m-d H:i:s", mktime(23, 59, 59, $month, $day, $year));

            return [$startTime, $endTime];

        }
    }

    /**
     * 简单的分页处理
     * @param array $arr 数据数组
     * @param $count
     * @param $page_size
     * @param $page
     * @return array
     */
    public static function pageDeal(array $arr, $count, $page, $page_size = self::PAGE_SIZE_DEFAULT)
    {
        $total_page = ceil($count / $page_size);
        $page = $page < 1 ? 1 : $page;
        $arr = $page > $total_page ? [] : $arr;
        $return = [
            'list' => $arr,
            'total_page' => $total_page,
            'count' => $count,
            'page' => $page
        ];
        return $return;
    }

    /**
     * Reset some model's district
     *
     * @param ActiveRecord $model model
     * @param string $districtCode district code
     * @param string $address address
     * @param string $districtCodeField district code field default district_code
     * @param string $districtNameField district name field default district_name
     * @param string $addressField address field default address
     * @return int
     */
    public static function resetDistrict(ActiveRecord $model, $districtCode, $address = '', $districtCodeField = self::FIELD_DISTRICT_CODE, $districtNameField = self::FIELD_DISTRICT_NAME, $addressField = self::FIELD_ADDRESS)
    {
        $district = District::validateDistrictCode($districtCode);
        $modelAttrs = $model->getAttributes();
        if (!$district
            || !in_array($districtCodeField, $modelAttrs)
            || ($districtNameField && !in_array($districtNameField, $modelAttrs))
            || ($addressField && !in_array($addressField, $modelAttrs))
        ) {
            return 1000;
        }

        if ($model->$districtCodeField == $districtCode) {
            return 200;
        }

        $model->$districtCodeField = $districtCode;
        $districtNameField && $model->$districtNameField = District::fullNameByCode($districtCode);
        $address && $model->$addressField = $address;
        if (!$model->save()) {
            return 500;
        }

        return 200;
    }

    /**
     * Reset some model's icon
     *
     * @param ActiveRecord $model model
     * @param string $icon icon
     * @param string $iconField icon field default icon
     * @return int
     */
    public static function resetIcon(ActiveRecord $model, $icon, $iconField = self::FIELD_ICON)
    {
        $modelAttrs = $model->getAttributes();
        if (!$icon
            || !in_array($iconField, $modelAttrs)
        ) {
            return 1000;
        }

        if ($model->icon == $icon) {
            return 200;
        }

        $model->icon = $icon;
        if (!$model->save()) {
            return 500;
        }

        return 200;
    }

    /**
     * Add table abbreviation prefix to select fields
     *
     * @param array $select select fields
     * @param string $tblAs table abbreviation
     * @return array
     */
    public static function addTableAbbreviationPrefixToSelectFields(array $select, $tblAs = self::MAIN_TABLE_AS)
    {
        if (!$select) {
            return [];
        }

        return array_map(function ($v) use ($tblAs) {
            return $tblAs . '.' . $v;
        }, $select);
    }

    /**
     * Get raw sql by query object
     *
     * @param Query $query query object
     * @return string
     */
    public static function getSqlByQuery(Query $query)
    {
        $db = Yii::$app->db;
        list ($sql, $params) = $db->getQueryBuilder()->build($query);
        return $db->createCommand($sql, $params)->getRawSql();
    }

}