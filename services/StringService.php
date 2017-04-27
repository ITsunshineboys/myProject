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
}