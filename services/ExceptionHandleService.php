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
    public function __construct($code)
    {
        $action = 'handle' . $code;
        method_exists($this, $action) && $this->$action();
    }

    public function handle500()
    {
//        file_put_contents('/tmp/ex500.log', time() . PHP_EOL, FILE_APPEND);
    }

    public function handle0()
    {
//        file_put_contents('/tmp/ex0.log', time() . PHP_EOL, FILE_APPEND);
    }
}