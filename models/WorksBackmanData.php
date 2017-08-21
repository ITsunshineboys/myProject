<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/19 0019
 * Time: 下午 15:04
 */
namespace app\models;

use yii\db\ActiveRecord;

class WorksBackmanData extends ActiveRecord
{
    const  SUP_BANK_CARD = 'works_backman_data';

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'works_backman_data';
    }

    public static function plotAdd($effect_id,$backman_option,$backman_value)
    {
        $res = \Yii::$app->db->createCommand()->insert(self::SUP_BANK_CARD,[
            'effect_id'     => $effect_id,
            'backman_option'   => $backman_option,
            'backman_value'  => $backman_value,
        ])->execute();

        return $res;
    }

    public static function findById($id)
    {
        return self::find()
            ->asArray()
            ->where(['effect_id'=>$id])
            ->all();
    }
}