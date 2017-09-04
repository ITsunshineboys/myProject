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
    const  SUP_BANK_CARD='effect';
    const TYPE_STATUS_YES = 1;
    const TYPE_STATUS_NO = 0;
    const STATUS_STAIRWAY_YES = 1;
    const PAGE_SIZE_DEFAULT = 12;
    const FIELDS_EXTRA = [];
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
          'province_code',
          'city',
          'city_code',
          'district',
          'district_code',
          'toponymy',
          'street',
          'particulars',
          'stairway',
          'add_time',
          'house_image',
          'effect_images',
          'images_name',
          'type',
          'stair_id',
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
            [['series_id', 'style_id','bedroom','sittingRoom_diningRoom','toilet','kitchen','window','area','high','province','province_code','city','city_code','district','district_code', 'toponymy','street','particulars','stairway','house_image','effect_images','images_name','type'], 'required'],
            [['province', 'city','district','toponymy','street','particulars'],'string'],
            [['bedroom','sittingRoom_diningRoom','toilet','kitchen','window','area','high'],'number']
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
     * get effect view info
     * @param int $effect_id
     * @return array
     */
    public function geteffectdata($effect_id){
        $query=new Query();
        $array= $query->from('effect As e')->select('e.toponymy,e.province,e.city,e.particulars,e.high,e.window,e.stairway,t.style,s.series')->leftJoin('effect_picture as ep','e.id=ep.effect_id')->leftJoin('series As s','s.id = ep.series_id')->leftJoin('style As t','t.id = ep.style_id')->where(['e.id'=>$effect_id])->one();
        $array1=(new Query())->from('effect_earnst')->select('phone,name,create_time,earnest,remark')->where(['effect_id'=>$effect_id])->one();
        if($array){
            $array['phone']=$array1['phone'];
            $array['create_time']=$array1['create_time'];
            $array['earnest']=sprintf('%.2f',(float)$array1['earnest']*0.01);
            $array['name']=$array1['name'];
            $array['remark']=$array1['remark'];
            if($array['stairway']){
                $stairway_cl=(new Query())->from('effect')->select('attribute')->leftJoin('stairs_details','effect.stair_id=stairs_details.id')->where(['effect.id'=>$effect_id])->one();
                $array['stairway']=$stairway_cl['attribute'];
            }else{
                $array['stairway']=null;
            }
            return $array;
        }

        return null;
    }
    public function beforeSave($insert)
    {
        if($insert){
            $this->add_time=time();
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
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

    public static function pagination($where,$page = 1, $size = self::PAGE_SIZE_DEFAULT)
    {
        $offset = ($page - 1) * $size;
        $effectList = self::find()
            ->select('id,toponymy,add_time,district,street')
            ->where($where)
            ->groupBy('toponymy')
            ->orderBy(['add_time' => SORT_ASC])
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();

        foreach ($effectList as &$effect) {

            if(isset($effect['add_time'])){
                $effect['add_time']=date('Y-m-d H:i', $effect['add_time']);
            }

        }

        return [
            'total' => (int)self::find()->where($where) ->groupBy('toponymy')->asArray()->count(),
            'page'=>$page,
            'size'=>$size,
            'details' => $effectList
        ];
    }

    public function plotAdd($bedroom,$sittingRoom_diningRoom,$toilet,$kitchen,$window,$area,$high,$province,$province_code,$city,$city_code,$district,$district_code,$toponymy,$street,$particulars,$stairway,$house_image,$type,$sort_id,$stair_id = 0)
    {
        $res = \Yii::$app->db->createCommand()->insert(self::SUP_BANK_CARD,[
            'bedroom'       => $bedroom,
            'sittingRoom_diningRoom' => $sittingRoom_diningRoom,
            'toilet'        => $toilet,
            'kitchen'       => $kitchen,
            'window'        => $window,
            'area'          => $area,
            'high'          => $high,
            'province'      => $province,
            'province_code' => $province_code,
            'city'          => $city,
            'city_code'     => $city_code,
            'district'      => $district,
            'district_code' => $district_code,
            'toponymy'      => $toponymy,
            'street'        => $street,
            'particulars'   => $particulars,
            'stairway'      => $stairway,
            'add_time'      => time(),
            'house_image'   => $house_image,
            'type'          => $type,
            'stair_id'      => $stair_id,
            'sort_id'      => $sort_id,
        ])->execute();

        return $res;
    }

    public function plotEdit($id,$bedroom,$sittingRoom_diningRoom,$toilet,$kitchen,$window,$area,$high,$province,$province_code,$city,$city_code,$district,$district_code,$toponymy,$street,$particulars,$stairway,$house_image,$type,$sort_id,$stair_id)
    {
        $res = \Yii::$app->db->createCommand()->update(self::SUP_BANK_CARD,[
            'bedroom'       => $bedroom,
            'sittingRoom_diningRoom' => $sittingRoom_diningRoom,
            'toilet'        => $toilet,
            'kitchen'       => $kitchen,
            'window'        => $window,
            'area'          => $area,
            'high'          => $high,
            'province'      => $province,
            'province_code' => $province_code,
            'city'          => $city,
            'city_code'     => $city_code,
            'district'      => $district,
            'district_code' => $district_code,
            'toponymy'      => $toponymy,
            'street'        => $street,
            'particulars'   => $particulars,
            'stairway'      => $stairway,
            'house_image'   => $house_image,
            'stair_id'      => $stair_id,
            'type'          => $type,
            'sort_id'       => $sort_id
        ],'id='. $id)->execute();

        return $res;
    }

    /**
     * according to condition find
     * @param $street
     * @param $toponymy
     * @param $district
     * @return array|ActiveRecord[]
     */
    public static function condition($street,$toponymy,$district)
    {
        return self::find()
            ->asArray()
            ->where(['and',['street'=>$street],['toponymy'=>$toponymy],['district'=>$district]])
            ->orderBy(['sort_id'=>SORT_ASC])
            ->all();
    }

    /**
     *
     * @param $district_code
     * @param $street
     * @param $toponymy
     * @return array|null|ActiveRecord
     */
    public static function findByCode($district_code,$street,$toponymy)
    {
        return self::find()
            ->asArray()
            ->where(['and',['district_code'=>$district_code],['street'=>$street],['toponymy'=>$toponymy],['type'=>self::TYPE_STATUS_YES]])
            ->one();
    }

    /**
     * @param $province
     * @param $city
     * @return array|ActiveRecord[]
     */
    public static function findCode($province,$city)
    {
        return self::find()
            ->asArray()
            ->select('district,district_code')
            ->where(['and',['province_code'=>$province],['city_code'=>$city],['type'=>self::TYPE_STATUS_YES]])
            ->groupBy('district')
            ->all();
    }


    /**
     *
     * @param $province
     * @param $city
     * @param $district
     * @return array|ActiveRecord[]
     */
    public static function findToponymy($province,$city,$district)
    {
        return self::find()
            ->asArray()
            ->select('toponymy')
            ->where(['and',
                ['province_code'=>$province],
                ['city_code'=>$city],
                ['district_code'=>$district],
                ['type'=>self::TYPE_STATUS_YES]
            ])
            ->groupBy('toponymy')
            ->all();
    }

    /**
     * @param $province
     * @param $city
     * @param $district
     * @param $toponymy
     * @return array|ActiveRecord[]
     */
    public static function findStreet($province,$city,$district,$toponymy)
    {
        return self::find()
            ->asArray()
            ->select('street')
            ->where(['and',
                ['province_code'=>$province],
                ['city_code'=>$city],
                ['district_code'=>$district],
                ['toponymy'=>$toponymy],
                ['type'=>self::TYPE_STATUS_YES]
            ])
            ->groupBy('street')
            ->all();
    }

    public static function findCase($province,$city,$district,$toponymy,$street)
    {
        return self::find()
            ->asArray()
            ->select('particulars')
            ->where(['and',
                ['province_code'=>$province],
                ['city_code'=>$city],
                ['district_code'=>$district],
                ['toponymy'=>$toponymy],
                ['street'=>$street],
                ['type'=>self::TYPE_STATUS_YES]
            ])
            ->all();
    }
}