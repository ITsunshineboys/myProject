<?php
namespace vendor\wxpay;
use yii\base\WxPayException;
//以下为日志

interface ILogHandler
{
    public function write($msg);

}
