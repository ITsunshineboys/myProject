<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/8/17
 * Time: 1:57 PM
 */

namespace app\services;

use Yii;
use yii\helpers\Json;

class ExceptionHandleService
{
    /**
     * Error code
     *
     * @var int
     */
    private $_code;

    /**
     * Construct function
     *
     * @param int $code exception code
     */
    public function __construct($code)
    {
        $this->_code = $code;
        $action = 'handle' . $code;
        method_exists($this, $action) && $this->$action();
    }

    /**
     * Handle 500 exception
     */
    public function handle500()
    {
//        file_put_contents('/tmp/ex500.log', time() . PHP_EOL, FILE_APPEND);
    }

    /**
     * Handle 403 exception
     */
    public function handle403()
    {
        $errorCodes = Yii::$app->params['errorCodes'];
        echo Json::encode(['code' => $this->_code, 'msg' => $errorCodes[$this->_code]]);
    }

    /**
     * Handle 403 exception
     */
    public function handle1023()
    {
        $errorCodes = Yii::$app->params['errorCodes'];
        echo Json::encode(['code' => $this->_code, 'msg' => $errorCodes[$this->_code]]);
    }
}