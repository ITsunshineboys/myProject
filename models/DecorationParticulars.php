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

    public static function plotAdd($post)
    {
        if ($post)
        {
            return 11;
        }
    }
}