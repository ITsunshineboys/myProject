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
            [['province', 'city','district','street','toponymy','area','high','room','hall','toilet','kitchen','window'], 'required'],
            [['province', 'city','district','street','toponymy'],'string'],
            [['area','high','room','hall','toilet','kitchen','window'],'number']
        ];
    }
    /**
     * @param $toponymy
     * @param $street
     * @return array|ActiveRecord[]
     */
    public static function districtSearch($search = '')
    {
        if (!empty($search))
        {
            $detail = self::find()->where( ['or',['like','toponymy',$search],['like','street',$search]])->all() ;
        }else{
            echo '传入的值有错';
            exit;
        }
        return $detail;
    }

    public static function conditionQuery($arr = [])
    {
        $basis_condition = [];
        if($arr){
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
        }

            return $effect;
    }
}