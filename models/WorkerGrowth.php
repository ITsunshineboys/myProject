<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/15 0015
 * Time: 下午 16:40
 */

namespace app\models;

use yii\db\ActiveRecord;

class WorkerGrowth extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_growth';
    }
}