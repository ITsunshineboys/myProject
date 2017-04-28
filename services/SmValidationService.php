<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/26 0026
 * Time: 下午 15:19
 */

namespace app\services;

use Yii;
use app\services\StringService;
use Flc\Alidayu\App;
use Flc\Alidayu\Client;
use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;

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

    /**
     * SmValidationService constructor.
     *
     * @param $type valication type
     * @param $mobile mobile
     * @throws \InvalidArgumentException
     */
    public function __construct($type, $mobile)
    {
        $smParams = Yii::$app->params['sm'];

        if (!StringService::isMobile($mobile) || !method_exists($this, $type) || !isset($smParams[$type])) {
            throw new \InvalidArgumentException;
        }

        $this->_appKey = $smParams['appKey'];
        $this->_appSecret = $smParams['appSecret'];
        $this->_signName = $smParams['signName'];
        $this->_templateId = $smParams[$type]['templateId'];
        $this->_interval = $smParams['interval'];
        $this->_validationCodeExpire = $smParams['validationCodeExpire'];
        $this->_mobile = $mobile;
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
            $validationCode = rand(1000, 9999);
            $cache->set($validationCodeKey, $validationCode, $this->_validationCodeExpire);
        }

        $config = [
            'app_key' => $this->_appKey,
            'app_secret' => $this->_appSecret,
        ];

        $client = new Client(new App($config));
        $req = new AlibabaAliqinFcSmsNumSend;
        $req
            ->setRecNum($this->_mobile)// 手机号码
            ->setSmsParam(['product' => $this->_signName, 'code' => $validationCode]) // 模版数据
            ->setSmsFreeSignName($this->_signName)// 短信签名
            ->setSmsTemplateCode($this->_templateId); // 短信模版ID

        $res = $client->execute($req);

        if (isset($res->result) && $res->result->err_code == 0) {
            $cache->set($intervalKey, 1, $this->_interval);
            return true;
        }
    }
}
