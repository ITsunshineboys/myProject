<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/8 0008
 * Time: 下午 14:34
 */
namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Query;

class Effect extends ActiveRecord
{
    const STATUS_STAIRWAY_YES = 1;
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
          'stairway',
          'add_time'
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
            $detail = self::find()
                ->asArray()
                ->where(['like','toponymy',$search])
                ->all();
            return $detail;
        }
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

    public function geteffectdata($effect_id){

        $query=new Query();

       $array= $query->from('effect As e')->select('e.toponymy,e.province,e.city,e.particulars,e.high,e.window,t.style,s.series')->leftJoin('series As s','s.id = e.series_id')->leftJoin('style As t','t.id = e.style_id')->where(['e.id'=>$effect_id])->one();
       $array1=(new Query())->from('effect_earnest')->select('phone,name,create_time,earnest,remark')->where(['effect_id'=>$effect_id])->one();

      $array['phone']=$array1['phone'];
      $array['create_time']=$array1['create_time'];
      $array['earnest']=$array1['earnest'];
      $array['name']=$array1['name'];
      $array['remark']=$array1['remark'];


       return $array;

    }



    /**
     * toponymy find all
     * @return string
     */
    public static function conditionFind($post)
    {
        $effect = self::find()
            ->select('effect.toponymy,effect.add_time,effect.district')
            ->groupBy('toponymy')
            ->where(['like','toponymy' => $post])
            ->asArray()
            ->all();
        $list = [];
        foreach ($effect as $one_model)
        {
            $one_model['add_time'] = date('Y-m-d H:i',$one_model['add_time']);
            $list [] = $one_model;
        }
        return $list;
    }

    public static function plotAdd($post)
    {
        if ($post)
        {

        }
        return 11;
    }
}