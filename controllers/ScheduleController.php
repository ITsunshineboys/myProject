<?php

namespace app\controllers;

use app\services\ScheduleAuthService;
use app\services\ExceptionHandleService;
use app\services\StringService;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\log\Logger;

class ScheduleController extends Controller
{
    /**
     * Actions accessed by logged-in users
     */
    const ACCESS_LOGGED_IN_USER = [
        'cache-delete',
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => ScheduleAuthService::className(),
                'denyCallback' => function ($rule, $action) {
                    new ExceptionHandleService(func_get_args()[0]);
                    exit;
                },
                'only' => self::ACCESS_LOGGED_IN_USER,
                'rules' => [
                    [
                        'actions' => self::ACCESS_LOGGED_IN_USER,
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'cache-delete' => ['post',],
                ],
            ],
        ];
    }

    /**
     * Get token action.
     *
     * @return string
     */
    public function actionToken()
    {
        return ScheduleAuthService::getToken();
    }

    /**
     * Delete cache action.
     *
     * @return string
     */
    public function actionCacheDelete()
    {
        $code = 1000;

        $key = trim(Yii::$app->request->post('key', ''));
        if (!$key) {
            return Json::encode([
                'code' => $code,
                'msg' => Yii::$app->params['errorCodes'][$code],
            ]);
        }

        Yii::$app->cache->delete($key);
        StringService::writeLog('test', time(), '', Logger::LEVEL_INFO);
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
        ]);
    }
}