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
use yii\log\FileTarget;
use yii\log\Logger;

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
        if (!$code || mb_strlen($code) != $codeLen) {
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
        if (!$birthday || mb_strlen($birthday) != User::BIRTHDAY_LEN || !preg_match($pattern, $birthday, $matches)) {
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

    /**
     * Check if odd number
     *
     * @param int $number number to be checked
     * @return bool
     */
    public static function isOdd($number)
    {
        return (int)$number % 2 == 1;
    }

    /**
     * Get repeated times of some substring
     *
     * @param string $str
     * @param string $needle
     * @param bool $strict if strict mode
     * @return int
     */
    public static function countSubstr($str, $needle, $strict = false)
    {
        if (!$str || !$needle) {
            return 0;
        }

        if (!$strict) {
            $str = strtolower($str);
            $needle = strtolower($needle);
        }

        return substr_count($str, $needle);
    }

    public static function isTampered($str)
    {
        if (!$str) {
            return -1;
        }
    }

    /**get  client ip
     * @return array|false|string
     */
    public static function getClientIP()
    {
        global $ip;
        if (getenv("HTTP_CLIENT_IP"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR"))
            $ip = getenv("REMOTE_ADDR");
        else $ip = "Unknow";
        return $ip;
    }

    /**
     * Format price
     *
     * @param int|float $price price
     * @param int $decimalDigits decimal digits default 2
     * @return string
     */
    public static function formatPrice($price, $decimalDigits = 2)
    {
        return sprintf('%.' . $decimalDigits . 'f', $price);
    }

    /**
     * Http get
     *
     * @param string $url
     * @param string $username username default empty
     * @param string $password password default empty
     * @return string|bool
     */
    public static function httpGet($url, $username = '', $password = '')
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        if ($username && $password) {
            curl_setopt($oCurl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($oCurl, CURLOPT_USERPWD, "{$username}:{$password}");
        }
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * Http post
     *
     * @param string $url
     * @param string $param default empty
     * @param string $username username default empty
     * @param string $password password default empty
     * @return string|bool
     */
    public static function httpPost($url, $param = '', $username = '', $password = '')
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        }
        if (is_string($param)) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach ($param as $key => $val) {
                $aPOST[] = $key . "=" . urlencode($val);
            }
            $strPOST = join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
        if ($username && $password) {
            curl_setopt($oCurl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($oCurl, CURLOPT_USERPWD, "{$username}:{$password}");
        }
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (in_array(intval($aStatus["http_code"]), [200, 201])) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * Http put
     *
     * @param string $url
     * @param string $param
     * @param int $port default 80
     * @param string $username username default empty
     * @param string $password password default empty
     * @return string|bool
     */
    public static function httpPut($url, $param, $port = 80, $username = '', $password = '')
    {
        $ci = curl_init();

        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_PORT, $port);
        curl_setopt($ci, CURLOPT_TIMEOUT, 200);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ci, CURLOPT_FORBID_REUSE, 0);
        curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ci, CURLOPT_POSTFIELDS, $param);
        if ($username && $password) {
            curl_setopt($ci, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ci, CURLOPT_USERPWD, "{$username}:{$password}");
        }

        $sContent = curl_exec($ci);
        $aStatus = curl_getinfo($ci);
        curl_close($ci);

        if (intval($aStatus["http_code"]) == 201) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * Http delete
     *
     * @param string $url
     * @param string $username username default empty
     * @param string $password password default empty
     * @return string|bool
     */
    public static function httpDelete($url, $username = '', $password = '')
    {
        $ci = curl_init();

        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_TIMEOUT, 200);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ci, CURLOPT_FORBID_REUSE, 0);
        curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
        if ($username && $password) {
            curl_setopt($ci, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ci, CURLOPT_USERPWD, "{$username}:{$password}");
        }

        $sContent = curl_exec($ci);
        $aStatus = curl_getinfo($ci);
        curl_close($ci);

        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
    }

    /**
     * Get unique string by salt
     *
     * @param string $salt salt
     * @return string
     */
    public static function getUniqueStringBySalt($salt)
    {
        $ret = str_split($salt . time() . rand(10000, 99999));
        shuffle($ret);
        return join($ret);
    }

    /**
     * Write log under @runtime/logs/
     *
     * @param string $filename file name
     * @param string $msg message
     * @param string $category message category
     * @param int $level log level default error
     */
    public static function writeLog($filename, $msg, $category = '', $level = Logger::LEVEL_ERROR)
    {
        $log = new FileTarget;
        $log->logFile = Yii::$app->getRuntimePath() . '/logs/' . $filename . '.log';
        $log->messages[] = [$msg, $level, $category, time()];
        $log->export();
    }
}