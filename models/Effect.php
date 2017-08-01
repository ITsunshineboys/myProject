<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/8 0008
 * Time: 下午 14:34
 */
namespace app\models;

use yii\db\ActiveRecord;

class Effect extends ActiveRecord
{

    const FIELDS_VIEW_ADMIN_MODEL = [
          'id',
          'series_id',
          'style_id',
          'bedroom',
          'sittingRoom_diningRoom',
          'toilet',
          'kitchen',
          'window',
          'area',
          'high',
          'province',
          'city',
          'district',
          'toponymy',
          'particulars',
          'site_particulars',
        ];
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'effect';
    }

    public function rules()
    {
        return [
            [['series_id', 'style_id','bedroom','sittingRoom_diningRoom','toilet','kitchen','window','area','high','province','city','district', 'toponymy','street','particulars','site_particulars'], 'required'],
            [['province', 'city','district','toponymy','street','particulars','site_particulars'],'string'],
            [['series_id','style_id','bedroom','sittingRoom_diningRoom','toilet','kitchen','window','area','high'],'number']
        ];
    }

    /**
     * @param string $search
     * @return array|ActiveRecord[]
     */
    public static function districtSearch($search = '花好月圆')
    {
        if (!empty($search))
        {
            $detail = self::find()->asArray()->where(['like','toponymy',$search])->all();
        }
        return $detail;
    }

    public static function conditionQuery($arr)
    {
        $basis_condition = [];
        $basis_condition ['room'] = $arr['room'];
        $basis_condition ['hall'] = $arr['hall'];
        $basis_condition ['toilet'] = $arr['toilet'];
        $basis_condition ['kitchen'] = $arr['kitchen'];
        $basis_condition ['area'] = $arr['area'];
        $basis_condition ['high'] = $arr['high'];
        $basis_condition ['window'] = $arr['window'];
        $basis_condition ['series_id'] = $arr['series'];
        $basis_condition ['style_id'] = $arr['style'];

        $effect = self::find()->where(['and','room'=> $basis_condition ['room'],
            'hall'=> $basis_condition ['hall'],
            'toilet'=> $basis_condition ['toilet'],
            'kitchen'=> $basis_condition ['kitchen'],
            'high'=> $basis_condition ['high'],
            'window'=> $basis_condition ['window'],
            'series'=> $basis_condition ['series_id'],
            'style'=> $basis_condition ['style_id']])->one();
        return $effect;
    }
}