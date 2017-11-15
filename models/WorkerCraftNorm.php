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
    const SELECT_FIND = 'quantity,worker_kind_details';
    const WEAK_CURRENT_DAY_POINTS = 5;
    const STRONG_CURRENT_DAY_POINTS = 5;
    const WATERWAY_DAY_POINTS = 6;
    const WATERPROOF_DAY_AREA = 30;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_craft_norm';
    }

    /**
     *  Find everything by id
     * @param $id
     * @return static[]
     */
    public static function findById($id)
    {
        return self::findAll(['labor_cost_id'=>$id]);
    }

    /**
     * Find labor cost by id
     * @param $id
     * @param $details
     * @return array|null|ActiveRecord
     */
    public static function findByLaborCostId($id,$details,$select =self::SELECT_FIND)
    {
        return self::find()
            ->asArray()
            ->select($select)
            ->where(['and',['labor_cost_id'=>$id],['worker_kind_details'=>$details]])
            ->one();
    }

    /**
     * Find everything by id all
     * @param $id
     * @return array|ActiveRecord[]
     */
    public static function findByLaborCostAll($id)
    {
        return self::find()
            ->asArray()
            ->select('quantity,worker_kind_details')
            ->where(['labor_cost_id'=>$id])
            ->all();
    }
}