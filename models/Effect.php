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
        $detail = self::find()
            ->asArray()
            ->where(['like','toponymy',$search])
            ->all();
        return $detail;
    }

    /**
     * @param $arr
     * @return array|null|ActiveRecord
     */
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

    /**
     * find all
     * @return string
     */
    public static function conditionFind($post)
    {
        if (!is_array($post))
        {
            $data = strlen($post);
            switch ($data) {
                case 6:
                    $effect = self::find()
                        ->select('effect.toponymy,effect.add_time,effect.district')
                        ->groupBy('toponymy')
                        ->where(['city' => $post])
                        ->asArray()
                        ->all();
                    return $effect;
                    break;
                case 12:
                    $effect = self::find()
                        ->select('effect.toponymy,effect.add_time,effect.district')
                        ->groupBy('toponymy')
                        ->where(['toponymy' => $post])
                        ->asArray()
                        ->all();
                    return $effect;
                    break;
            }
        }else {
            $effect = self::find()
                ->select('effect.toponymy,effect.add_time,effect.district')
                ->groupBy('toponymy')
                ->where(['and',['>=','add_time',$post['min']],['<=','add_time',$post['max']]])
                ->asArray()
                ->orderBy(['add_time'=>SORT_ASC])
                ->all();
            return $effect;
        }
    }
}