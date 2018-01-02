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
    const SELECT_FIND = 'quantity,worker_kind_id';
    const WEAK_CURRENT_DAY_POINTS = 5;
    const STRONG_CURRENT_DAY_POINTS = 5;
    const WATERWAY_DAY_POINTS = 6;
    const WATERPROOF_DAY_AREA = 30;
    const CARPENTRY_DAY_MODELLING = 10;
    const CARPENTRY_DAY_FLAT = 10;

    const UNIT = [
      0 => '',
      1 => 'm/天',
      2 => 'm²/天',
      3 => '个/天',
    ];

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
        $row =  self::find()
            ->asArray()
            ->select('id,quantity,labor_cost_id,worker_type_id')
            ->where(['labor_cost_id'=>$id])
            ->All();


        foreach ($row as &$one){
            $data=WorkerType::find()
                ->where(['id'=>$one['worker_type_id']])
                ->select('worker_name,unit')
                ->asArray()
                ->one();
            $one['worker_kind_details']=$data['worker_name'];
            $one['quantity'] = $one['quantity'] / 100;
            $one['unit'] = self::UNIT[$data['unit']];
            unset($one['worker_type_id']);
        }

        return $row;
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
        $rows = self::find()
            ->asArray()
            ->select('id,quantity,worker_type_id')
            ->where(['labor_cost_id'=>$id])
            ->all();

        foreach ($rows as &$row){
            $row['quantity'] = $row['quantity'] / 100;
        }

        return $rows;
    }
}