<?php
namespace yii\base;
/**
 *
 * 支付宝API异常类
 * @author widyhu
 *
 */
class AlipayException extends Exception {
    public function errorMessage()
    {
        return $this->getMessage();
    }
}
