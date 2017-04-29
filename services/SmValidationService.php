<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/26 0026
 * Time: 下午 15:19
 */

namespace app\services;

use Yii;
use yii\web\ServerErrorHttpException;
use Flc\Alidayu\App;
use Flc\Alidayu\Client;
use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;
use app\services\StringService;

class SmValidationService
{
    const SUFFIX_INTERVAL = '_interval';
    const SUFFIX_VALIDATION_CODE = '_validationCode';

    private $_appKey;
    private $_appSecret;
    private $_signName;
    private $_templateId;
    private $_mobile;
    private $_interval;
    private $_validationCodeExpire;
    private $_validationCodeMethod;

    /**
     * SmValidationService constructor.
     *
     * @param $data array
     *        $data['mobile']  mobile
     *        $data['type']    validation code type, register | resetPassword
     * @throws \InvalidArgumentException|ServerErrorHttpException
     */
    public function __construct($data)
    {
        $smParams = Yii::$app->params['sm'];

        if (empty($data['type'])
            || empty($data['mobile'])
            || !StringService::isMobile($data['mobile'])
            || !isset($smParams[$data['type']])) {
            throw new \InvalidArgumentException;
        }

        $validationCodeMethod = '_' . $smParams['validationCode']['rule'];
        if (!method_exists($this, $validationCodeMethod)) {
            throw new ServerErrorHttpException;
        }

        $this->_validationCodeMethod = $validationCodeMethod;
        $this->_appKey = $smParams['appKey'];
        $this->_appSecret = $smParams['appSecret'];
        $this->_signName = $smParams['signName'];
        $this->_templateId = $smParams[$data['type']]['templateId'];
        $this->_interval = $smParams['interval'];
        $this->_validationCodeExpire = $smParams['validationCode']['expire'];
        $this->_mobile = $data['mobile'];
        $this->send();
    }

    /**
     * 发送验证码
     */
    public function send()
    {
        $cache = Yii::$app->cache;

        // check sending interval
       $intervalKey = $this->_mobile . self::SUFFIX_INTERVAL;
        if ($cache->get($intervalKey)) {
            return;
        }

        // generate validation code
        $validationCodeKey = $this->_mobile . self::SUFFIX_VALIDATION_CODE;
        if (!($validationCode = $cache->get($validationCodeKey))) {
            $validationCodeMethod = $this->_validationCodeMethod;
            $validationCode = $this->$validationCodeMethod();
            $cache->set($validationCodeKey, $validationCode, $this->_validationCodeExpire);
        }

        $config = [
            'app_key' => $this->_appKey,
            'app_secret' => $this->_appSecret,
        ];

        $client = new Client(new App($config));
        $req = new AlibabaAliqinFcSmsNumSend;
        $req
            ->setRecNum($this->_mobile) // 手机号码
            ->setSmsParam(['product' => $this->_signName, 'code' => $validationCode]) // 模版数据
            ->setSmsFreeSignName($this->_signName) // 短信签名
            ->setSmsTemplateCode($this->_templateId); // 短信模版ID

        $res = $client->execute($req);

        if (isset($res->result) && $res->result->err_code == 0) {
            $cache->set($intervalKey, 1, $this->_interval);
            return true;
        }
    }

    /**
     * Check validation code
     *
     * @param int $mobile mobile
     * @return bool if valid validation code
     */
    public static function validCode($mobile)
    {
        if (!$mobile) {
            return false;
        }

        $validationCodeKey = $mobile . self::SUFFIX_VALIDATION_CODE;
        return Yii::$app->cache->get($validationCodeKey);
    }

    /**
     * Delete validation code
     *
     * @param int $mobile mobile
     */
    public static function deleteCode($mobile)
    {
        if (!$mobile) {
            return;
        }

        $validationCodeKey = $mobile . self::SUFFIX_VALIDATION_CODE;
        Yii::$app->cache->delete($validationCodeKey);
    }

    /**
     * Generate random four digits
     *
     * @return int random four digits
     */
    private function _fourDigits()
    {
        return rand(1000, 9999);
    }
}
