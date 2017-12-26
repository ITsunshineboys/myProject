<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31 0031
 * Time: 上午 10:22
 */
namespace app\models;
use yii\db\ActiveRecord;

class DecorationParticulars extends ActiveRecord
{
    const SUP_BANK_CARD = 'decoration_particulars';
    const FIELDS_VIEW_ADMIN_MODEL = [
        'id',
        'decoration_list_id',
        'hall_area',
        'bedroom_area',
        'toilet_area',
        'kitchen_area',
        'hallway_area',
        'hall_perimeter',
        'bedroom_perimeter',
        'toilet_perimeter',
        'kitchen_perimeter',
        'hallway_perimeter',
        'shoe_cabinet_length',
        'masterBedroom_garderobe_length',
        'secondaryBedroom_garderobe_length',
        'toilet_pipe',
        'kitchen_pipe',
        'cabinet_length',
        'drawingRoom_curtain_length',
        'masterBedroom_curtain_length',
        'secondaryBedroom_curtain_length',
        'wallCabinet_length',
        'modelling_length',
        'flat_area',
        'balcony_area'
    ];
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'decoration_particulars';
    }

    /**
     * find id one
     * @param $id
     * @return array|bool|null|ActiveRecord
     */
    public static function findByOne($id)
    {
        if($id)
        {
            return  self::find()
                ->asArray()
                ->where(['decoration_list_id' => $id])
                ->one();
        }else
        {
            return false;
        }
    }

    public static function plotAdd($effect_id,$hall_area,$hall_perimeter,$bedroom_area,$bedroom_perimeter,$toilet_area,$toilet_perimeter,$kitchen_area,$kitchen_perimeter,$modelling_length,$flat_area,$balcony_area)
    {
        $res = \Yii::$app->db->createCommand()->insert(self::SUP_BANK_CARD,[
            'effect_id'         => $effect_id,
            'hall_area'         => $hall_area,
            'hall_perimeter'    => $hall_perimeter,
            'bedroom_area'      => $bedroom_area,
            'bedroom_perimeter' => $bedroom_perimeter,
            'toilet_area'       => $toilet_area,
            'toilet_perimeter'  => $toilet_perimeter,
            'kitchen_area'      => $kitchen_area,
            'kitchen_perimeter' => $kitchen_perimeter,
            'modelling_length'  => $modelling_length,
            'flat_area'         => $flat_area,
            'balcony_area'      => $balcony_area,
        ])->execute();

        return $res;
    }

    public static function plotEdit($id,$hall_area,$hall_perimeter,$bedroom_area,$bedroom_perimeter,$toilet_area,$toilet_perimeter,$kitchen_area,$kitchen_perimeter,$modelling_length,$flat_area,$balcony_area)
    {
        $res = \Yii::$app->db->createCommand()->update(self::SUP_BANK_CARD,[
            'hall_area'         => $hall_area,
            'hall_perimeter'    => $hall_perimeter,
            'bedroom_area'      => $bedroom_area,
            'bedroom_perimeter' => $bedroom_perimeter,
            'toilet_area'       => $toilet_area,
            'toilet_perimeter'  => $toilet_perimeter,
            'kitchen_area'      => $kitchen_area,
            'kitchen_perimeter' => $kitchen_perimeter,
            'modelling_length'  => $modelling_length,
            'flat_area'         => $flat_area,
            'balcony_area'      => $balcony_area,
        ],['id'=>$id])->execute();

        return $res;
    }

    public static function findById($id)
    {
        return self::find()
            ->asArray()
            ->select('id,effect_id,hall_area,bedroom_area,toilet_area,kitchen_area,hall_perimeter,bedroom_perimeter,toilet_perimeter,kitchen_perimeter,modelling_length,flat_area,balcony_area')
            ->where(['effect_id'=>$id])
            ->all();
    }

    public static function findByIds($ids)
    {
        return self::find()
            ->asArray()
            ->select('id,effect_id,hall_area,bedroom_area,toilet_area,kitchen_area,hall_perimeter,bedroom_perimeter,toilet_perimeter,kitchen_perimeter,modelling_length,flat_area,balcony_area')
            ->where(['in','effect_id',$ids])
            ->all();
    }

}