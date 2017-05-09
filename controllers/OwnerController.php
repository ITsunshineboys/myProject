<?php

namespace app\controllers;

use app\models\Effect;
use app\models\EffectPicture;
use app\models\Series;
use app\models\Style;
use app\models\StylePicture;
use app\services\ExceptionHandleService;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;

class OwnerController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    $code = 403;
                    new ExceptionHandleService($code);
                    exit;
                },
                'only' => ['logout', 'about'],
                'rules' => [
                    [
                        'actions' => ['logout', 'about'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post',],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionSearch()
    {
        // 搜索框
        $post = '末日路';
        $search_condition = new Effect();
        $lists = $search_condition->districtSearch($post);
        foreach ($lists as $list){
            $search_picture = new EffectPicture();
            $list_picture = $search_picture->find()->where(['effect_id' =>$list['id']])->all();
        }

        // 系列列表
        $series = new Series();
        $series_list = $series->find()->all();

        // 风格列表
        $style = new Style();
        $style_list = $style ->find()->all();
        foreach ($style_list as $s){
            $style_picture = new StylePicture();
            $style_picture_list = $style_picture->find()->where(['style_id'=>$s['id']])->all();
        }
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'list' => $lists,
                'list_picture' => $list_picture,
                'series_list' => $series_list,
                'style_list' => $style_list,
                'style_picture_list' => $style_picture_list

            ]
        ]);
    }

}