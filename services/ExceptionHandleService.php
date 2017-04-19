<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/8/17
 * Time: 1:57 PM
 */

namespace app\services;

class ExceptionHandleService
{
    /**
     * Construct function
     *
     * @param int $code exception code
     */
    public function __construct($code)
    {
        $action = 'handle' . $code;
        method_exists($this, $action) && $this->$action();
    }

    /**
     * Handle 500 exception
     *
     */
    public function handle500()
    {
//        file_put_contents('/tmp/ex500.log', time() . PHP_EOL, FILE_APPEND);
    }
}