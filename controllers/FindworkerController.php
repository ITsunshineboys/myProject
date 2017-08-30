<?php
namespace app\controllers;

use app\models\WorkerItem;
use app\models\WorkerType;
use app\services\ExceptionHandleService;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Request;

class FindworkerController extends Controller{

    const PARENT=0;
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
    /**
     *get Services type list
     *@return string
     */
    public function actionServiceList()
    {
        $parents = WorkerType::find()->where(['pid'=>self::PARENT])->asArray()->all();
        $data=WorkerType::getworkertype($parents);
            $parent=[];
            for ($i=0;$i<count($data);$i++){
                $parent[]=[
                    $parents[$i]['worker_type']=>$data[$i],
                ];
            }
        return json_encode([
            'code'=>200,
            'msg'=>'ok',
            'data'=>$parent

        ]);

    }

    /**
     *get home info by worker type
     *@return string
     */

    public function actionGetHomeInfo(){

        $code=1000;
        $request=new Request();
        $worker_type_id=trim($request->get('worker_type_id',''),'');
        if(!$worker_type_id){
            return json_encode([
                'code' => $code,
                'msg' => \Yii::$app->params['errorCodes'][$code]
            ]);
        }
         $data=WorkerItem::getparent($worker_type_id);

               return json_encode([
                   'code'=>200,
                   'msg'=>'ok',
                   'data'=>$data
               ]);

         }

    /**
     *get home info by worker type
     *@return string
     */

}