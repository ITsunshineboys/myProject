<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/8 0008
 * Time: 下午 17:05
 */
namespace app\models;

use yii\db\ActiveRecord;

class WorkerCraftNorm extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_craft_norm';
    }

    public static function findById($id)
    {
        return self::findAll(['labor_cost_id'=>$id]);
    }
}