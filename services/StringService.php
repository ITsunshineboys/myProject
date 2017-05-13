<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/27/17
 * Time: 9:34 AM
 */

namespace app\services;

use Yii;

class StringService
{
    /**
     * Get basename of fully qualified class
     *
     * @param string $classname fully qualified class name
     * @return string
     */
    public static function classBasename($classname)
    {
        $classname = trim($classname, '\\');
        if ($pos = strrpos($classname, '\\')) {
            return substr($classname, $pos + 1);
        }
        return $classname;
    }

    /**
     * Check if mobile number
     *
     * @param $mobile
     * @return bool
     */
    public static function isMobile($mobile)
    {
        $mobile = trim($mobile);
        if (!$mobile) {
            return false;
        }

        return preg_match("/^1[34578]{1}\d{9}$/", $mobile);
    }

    /**
     * Check if class const exists
     *
     * @param string $constName const name
     * @param string $className class name
     * @return bool
     */
    public static function constExists($constName, $className)
    {
        if (!$constName || !$className) {
            return false;
        }

        return array_key_exists($constName, self::classConstants($className));
    }

    /**
     * Get class constants
     *
     * @param $className
     * @return array class constants list
     */
    public static function classConstants($className)
    {
        if (!$className) {
            return [];
        }

        $reflect = new \ReflectionClass($className);
        return $reflect->getConstants();
    }

    /**
     * Get start and ent time of some time type defined in params['timeTypes']
     *
     * @param string $timeType time type
     * @return array
     */
    public static function startEndDate($timeType)
    {
        $startTime = $endTime = '';

        $yearMonthDay = date('Y-m-d');
        list($year, $month, $day) = explode('-', $yearMonthDay);
        switch ($timeType) {
            case 'today':
                $startTime = date("Y-m-d H:i:s", mktime(0, 0, 0, $month, $day, $year));
                $endTime = date("Y-m-d H:i:s", mktime(23, 59, 59, $month, $day, $year));
                break;
            case 'week':
                $startTime = date("Y-m-d H:i:s", mktime(0, 0, 0, $month, $day - date('w') + 1, $year));
                $endTime = date("Y-m-d H:i:s", mktime(23, 59, 59, $month, $day - date('w') + 7, $year));
                break;
            case 'month':
                $startTime = date("Y-m-d H:i:s", mktime(0, 0, 0, $month, 1, $year));
                $endTime = date("Y-m-d H:i:s", mktime(23, 59, 59, $month, date('t'), $year));
                break;
            case 'year':
                $startTime = date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, $year));
                $endTime = date("Y-m-d H:i:s", mktime(23, 59, 59, 12, 31, $year));
                break;
        }

        return [$startTime, $endTime];
    }

    /**
     * Check if valid date, both YYYY-mm-dd and YYYY-m-d are valid
     *
     * @param string $date date
     * @return bool
     */
    public static function checkDate($date)
    {
        return filter_var($date, FILTER_VALIDATE_REGEXP,
            [
                'options' => [
                    'regexp' => '/^\d{4}-\d{1,2}-\d{1,2}$/',
                ]
            ]
        );
    }
}