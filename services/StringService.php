<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/27/17
 * Time: 9:34 AM
 */

namespace app\services;

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
}