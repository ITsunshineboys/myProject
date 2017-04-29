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
    const TYPE_REGISTER = 'register';
    const TYPE_RESET_PASSWORD = 'resetPassword';

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
            || !method_exists($this, $data['type'])
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
        $type = $data['type'];
        $this->$type();
    }

    /**
     * 注册发送验证码
     */
    public function register()
    {
        $cache = Yii::$app->cache;

        // check send interval
       $intervalKey = self::TYPE_REGISTER . '_' . $this->_mobile . '_interval';
        if ($cache->get($intervalKey)) {
            return;
        }

        // generate validation code
        $validationCodeKey = self::TYPE_REGISTER . '_' . $this->_mobile . '_validationCode';
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
     * 修改密码发送验证码
     */
    public function resetPassword()
    {
        $cache = Yii::$app->cache;

        // check send interval
        $intervalKey = self::TYPE_RESET_PASSWORD . '_' . $this->_mobile . '_interval';
        if ($cache->get($intervalKey)) {
            return;
        }

        // generate validation code
        $validationCodeKey = self::TYPE_RESET_PASSWORD . '_' . $this->_mobile . '_validationCode';
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
     * @param $type
     * @param $mobile
     * @param $validationCode
     * @return bool|mixed
     */
    public static function validCode($type, $mobile, $validationCode)
    {
        if (!$type || !$mobile || !$validationCode) {
            return false;
        }

        $validationCodeKey = $type . '_' . $mobile . '_validationCode';
        return Yii::$app->cache->get($validationCodeKey);
    }

    /**
     * Generate random four digits
     *
     * @return int
     */
    private function _fourDigits()
    {
        return rand(1000, 9999);
    }
}
