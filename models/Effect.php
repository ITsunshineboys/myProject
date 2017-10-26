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
            [['series_id', 'style_id','bedroom','sittingRoom_diningRoom','toilet','kitchen','window','area','high','province','province_code','city','city_code','district','district_code', 'toponymy','street','particulars','stairway','house_image','effect_images','images_name','type','site_particulars'], 'required'],
            [['province', 'city','district','toponymy','street','particulars'],'string'],
            [['bedroom','sittingRoom_diningRoom','toilet','kitchen','window','area','high'],'number']
        ];
    }

    /**
     * @param string $search
     * @return array|ActiveRecord[]
     */
    public static function districtSearch($search = '花好月圆',$select = [])
    {
        $detail = self::find()
            ->asArray()
            ->select($select)
            ->where(['like','toponymy',$search])
            ->groupBy('toponymy')
            ->all();
        return $detail;
    }
    /**
     * 生成新的样板间
     * @param $post
     * @return int
     */
    public static function addneweffect($post){
        $effects=self::find()
            ->select('sort_id')
            ->asArray()
            ->where(['toponymy'=>$post['toponymy']])
            ->all();
        if($effects){
            $sort_id=max($effects)['sort_id']+1;
        }else{
            $sort_id=0;
        }

        $province=District::findByCode($post['province_code'])->name;
        $city=District::findByCode($post['city_code'])->name;
        $district=District::findByCode($post['district_code'])->name;

        if($post['stair_id']==1){
            $post['stairway']=StairsDetails::find()->where(['id'=>$post['stairway']])->one()->id;
        }else{
            $post['stairway']=0;
        }
        $res = \Yii::$app->db->createCommand()->insert(self::SUP_BANK_CARD,[
            'bedroom'       => $post['bedroom'],
            'sittingRoom_diningRoom' => $post['sittingRoom_diningRoom'],
            'toilet'        => $post['toilet'],
            'kitchen'       => $post['kitchen'],
            'window'        => $post['window'],
            'area'          => $post['area'],
            'high'          => $post['high'],
            'province'      => $province,
            'province_code' => $post['province_code'],
            'city'          => $city,
            'city_code'     => $post['city_code'],
            'district'      => $district,
            'district_code' => $post['district_code'],
            'toponymy'      => $post['toponymy'],
            'street'        => $post['street'],
            'particulars'   => $post['particulars'],
            'stairway'      => $post['stairway'],
            'add_time'      => time(),
            'house_image'   => $post['house_image'],
            'type'          => self::TYPE_STATUS_NO,
            'stair_id'      => $post['stair_id'],
            'sort_id'      => $sort_id,
        ])->execute();

         $id=\Yii::$app->db->lastInsertID;
         $effect_picture=new EffectPicture();
         $effect_picture->effect_id=$id;
         $effect_picture->style_id=$post['style_id'];
         $effect_picture->series_id=$post['series_id'];
         $data['id']=$id;
         if(!$effect_picture->save(false)){
             $code=500;
             return $code;
         }
        if(!$res){
          $code=500;
          return $code;
         }
        return $data;
    }
    /**
     * get effect view info
     * @param int $effect_id
     * @return array
     */
    public function geteffectdata($effect_id){
        $query=new Query();
        $array= $query->from('effect_earnst As ea')
            ->select('e.area,e.toponymy,e.city,e.particulars,e.district,e.street,e.high,e.window,e.stairway,t.style,s.series,ea.*')
            ->leftJoin('effect as e','ea.effect_id=e.id')
            ->leftJoin('effect_picture as ep','e.id=ep.effect_id')
            ->leftJoin('series As s','s.id = ep.series_id')
            ->leftJoin('style As t','t.id = ep.style_id')
            ->where(['ea.id'=>$effect_id])->one();
        if($array){
            $array['particulars']=mb_substr($array['particulars'],0,4);
            $array['create_time']=date('Y-m-d',$array['create_time']);
            $array['earnest']=sprintf('%.2f',(float)$array['earnest']*0.01);
            $array['address']=$array['city'].$array['district'].$array['street'];
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
            ->where(['and',['district_code'=>$district_code],['street'=>$street],['toponymy'=>$toponymy]])
            ->orderBy(['sort_id'=>SORT_ASC])
            ->all();
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