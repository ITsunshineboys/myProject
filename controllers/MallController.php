<?php

namespace app\controllers;

use app\models\Carousel;
use app\models\GoodsRecommend;
use app\models\GoodsCategory;
use app\services\ExceptionHandleService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;

class MallController extends Controller
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
                'only' => [''],
                'rules' => [
                    [
                        'actions' => [''],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
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

    /**
     * Carousel action.
     *
     * @return string
     */
    public function actionCarousel()
    {
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'carousel' => Carousel::carousel(),
            ],
        ]);
    }

    /**
     * Recommend goods for type first action.
     *
     * @return string
     */
    public function actionRecommendFirst()
    {
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'recommend-first' => GoodsRecommend::first(),
            ],
        ]);
    }

    /**
     * Recommend goods for type second action.
     *
     * @return string
     */
    public function actionRecommendSecond()
    {
        $getData = Yii::$app->request->get();
        $page = (int)($getData['page'] ?? 1);
        $size = (int)($getData['size'] ?? GoodsRecommend::PAGE_SIZE_DEFAULT);

        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'recommend-second' => GoodsRecommend::second($page, $size),
            ],
        ]);
    }

    /**
     * Get top goods categories action.
     *
     * @return string
     */
    public function actionCategoriesTop()
    {
        return Json::encode([
            'code' => 200,
            'msg' => 'OK',
            'data' => [
                'categories_top' => GoodsCategory::categoriesByPid(['id', 'title', 'icon'])
            ],
        ]);
    }
}