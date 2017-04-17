<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 4/10/17
 * Time: 9:19 AM
 */

namespace app\services;

use Yii;

class EventHandleService
{
    /*
     * construct function
     *
     * @param mixed $data default null
     */
    public function __construct($data = null)
    {
        $events = Yii::$app->params['events'];

        // supplier login
        Yii::$app->on($events['supplier']['login'], function () use ($data) {
//            echo 'hi ', $data, ', you are logged in at ', date('Y-m-d H:i:s');
        });

        // supplier logout
        Yii::$app->on($events['supplier']['logout'], function () use ($data) {
//            echo 'you are logged out at ', date('Y-m-d H:i:s');
        });
    }
}