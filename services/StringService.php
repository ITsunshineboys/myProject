<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/27/17
 * Time: 9:34 AM
 */

namespace app\services;

use app\models\UploadForm;
use app\models\User;
use dosamigos\qrcode\QrCode;
use Yii;

class StringService
{
    const SEPARATOR_BIRTHDAY = '-';

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
     * @param int $mobile mobile
     * @return bool
     */
    public static function isMobile($mobile)
    {
        $mobile = trim($mobile);
        if (!$mobile) {
            return false;
        }

        return preg_match('/^1[34578]{1}\d{9}$/', $mobile);
    }

    /**
     * Check if birthday
     *
     * @param string $birthday birthday
     * @param string $separator separator default empty
     * @return bool
     */
    public static function isBirthday($birthday, $separator = '')
    {
        $birthday = trim($birthday);
        if (!$birthday) {
            return false;
        }

        $pattern = '/^\d{4}' . $separator . '\d{2}' . $separator . '\d{2}$/';
        return preg_match($pattern, $birthday);
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
     * @param bool $returnTimestamp if start_time and end_time of timestamp
     * @return array
     */
    public static function startEndDate($timeType, $returnTimestamp = false)
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

        if ($returnTimestamp) {
            $startTime = (int)strtotime($startTime);
            $endTime = (int)strtotime($endTime);
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

    /**
     * Get district names
     *
     * @param  array $codes strict code list
     * @return array
     */
    public static function districtNamesByCodes($codes)
    {
        foreach ($codes as &$districtCode) {
            $districtCode = (string)self::checkDistrict($districtCode);
        }

        return $codes;
    }

    /**
     * Check if valid district code
     *
     * @param  string $code strict code
     * @return bool|int
     */
    public static function checkDistrict($code)
    {
        $codeLen = 6;
        $parentCodeLastCodes = '0000';

        $code = trim($code);
        if (!$code || strlen($code) != $codeLen) {
            return false;
        }

        $parentCode = substr($code, 0, 2) . $parentCodeLastCodes;
        $codes = Yii::$app->params['districts'][0];
        if (empty($codes[$parentCode][$code])) {
            return false;
        }

        return $codes[$parentCode][$code];
    }

    /**
     * Check if has repeated element in a list
     *
     * @param array $elements elments to check
     * @return bool
     */
    public static function checkRepeatedElement(array $elements)
    {
        return count($elements) != count(array_unique($elements));
    }

    /**
     * Check if has empty element in a list
     *
     * @param array $elements elments to check
     * @return bool
     */
    public static function checkEmptyElement(array $elements)
    {
        if (!$elements) {
            return true;
        }

        foreach ($elements as $element) {
            if (!$element) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if list composed of int numbers
     *
     * @param array $elements elments to check
     * @return bool
     */
    public static function checkIntList(array $elements)
    {
        if (!$elements) {
            return false;
        }

        foreach ($elements as $element) {
            if (!is_numeric($element)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if two lists have same elements
     *
     * @param array $arr1 one array to check
     * @param array $arr2 the other array check
     * @return bool
     */
    public static function checkArrayIdentity(array $arr1, array $arr2)
    {
        return empty(array_diff($arr1, $arr2)) && empty(array_diff($arr2, $arr1));
    }

    /**
     * Check if identity card no
     *
     * @param string $cardNo identity card no
     * @return bool
     */
    public static function checkIdentityCardNo($cardNo)
    {
        return filter_var($cardNo, FILTER_VALIDATE_REGEXP,
            [
                'options' => [
                    'regexp' => '/^\d{17}[0-9xX]$/',
                ]
            ]
        );
    }

    /**
     * Get user agent
     *
     * @return string
     */
    public static function userAgent()
    {
        $type = 'other';

        $userAgent = Yii::$app->request->headers->get('user-agent');
        if (stripos($userAgent, 'iPhone') !== false
            || stripos($userAgent, 'iPad') !== false
        ) {
            $type = 'ios';
        } elseif (stripos($userAgent, 'Android')) {
            $type = 'android';
        }

        return $type;
    }

    /**
     * Generate qr code image
     *
     * @param string $str string
     * @param string $filename filename to saved as
     */
    public static function generateQrCodeImage($str, $filename)
    {
        $dir = Yii::getAlias('@webroot') . '/' . UploadForm::DIR_PUBLIC . '/';
        QrCode::png($str, $dir . "{$filename}.png");
    }

    /**
     * Format birthday
     *
     * @param int $birthday birthday
     * @param string $separator separator default -
     * @return string
     */
    public static function formatBirthday($birthday, $separator = self::SEPARATOR_BIRTHDAY)
    {
        $birthday = trim($birthday);
        $pattern = '/(\d{4})(\d{2})(\d{2})/';
        if (!$birthday || strlen($birthday) != User::BIRTHDAY_LEN || !preg_match($pattern, $birthday, $matches)) {
            return '';
        }

        unset($matches[0]);
        return implode($separator, $matches);
    }

    /**
     * Merge elements
     *
     * @param array $data data to merge
     * @return array
     */
    public static function merge(array $data)
    {
        $arr = [];
        array_map(function ($row) use (&$arr) {
            $arr = array_merge($row, $arr);
        }, $data);
        return $arr;
    }

    /**
     * Get values by key
     *
     * @param array $data data
     * @param string $key key name
     * @return array
     */
    public static function valuesByKey(array $data, $key)
    {
        if (!$key) {
            return [];
        }

        return array_map(function ($row) use ($key) {
            return $row[$key];
        }, $data);
    }
}