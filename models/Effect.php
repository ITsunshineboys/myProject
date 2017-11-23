<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/8 0008
 * Time: 下午 14:34
 */
namespace app\models;

use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Query;

class Effect extends ActiveRecord
{
    const TYPE_STATUS=0;
    const TYPE_ITEM=1;
    const  SUP_BANK_CARD='effect';
    const TYPE_STATUS_YES = 1;
    const TYPE_STATUS_NO = 2;
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
    public static  function  chinanum($num){
    $china=array('零','一','二','三','四','五','六','七','八','九');
    $arr=str_split($num);
    for($i=0;$i<count($arr);$i++){
       return $china[$arr[$i]];
    }
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
        if(isset($post['district_code'])){
            $district=District::findByCode($post['district_code'])->name;

        }else{
            $district=null;
        }

        if($post['stair_id']==1){
            $post['stairway']=StairsDetails::find()->where(['id'=>$post['stairway']])->one()->id;
        }else{
            $post['stairway']=0;
        }
        $tran=\Yii::$app->db->beginTransaction();
        try{
            $particulars=self::chinanum($post['bedroom']).'室'.self::chinanum($post['sittingRoom_diningRoom']).'厅'.self::chinanum($post['kitchen']).'厨'.self::chinanum($post['toilet']).'卫';

            if(!isset($post['district_code'])){
                $district_code=null;
            }else{
                $district_code=$post['district_code'];
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
                'district_code' => $district_code,
                'toponymy'      => $post['toponymy'],
                'street'        => $post['street'],
                'particulars'   => $particulars,
                'stairway'      => $post['stairway'],
                'add_time'      => time(),
                'type'          => self::TYPE_STATUS_NO,
                'stair_id'      => $post['stair_id'],
                'sort_id'      => $sort_id,
            ])->execute();

            if(!$res){
                $tran->rollBack();
                return false;
            }
            $id=\Yii::$app->db->lastInsertID;
            //如果有材料
            if(array_key_exists('material',$post)){
                foreach ($post['material'] as $attributes){
                    $res= \Yii::$app->db->createCommand()->insert('effect_material',[
                        'effect_id'=>$id,
                        'count'=>$attributes['count'],
                        'price'=>$attributes['price']*100,
                        'goods_id'=>$attributes['goods_id'],
                        'first_cate_id'=>$attributes['first_cate_id']
                    ])->execute();
                }
                if(!$res){
                    $tran->rollBack();
                    return false;
                }

            }

            $effect_earnest=new EffectEarnest();
            $effect_earnest->effect_id=$id;
            $effect_earnest->phone=$post['phone'];
            $effect_earnest->name=$post['name'];
            $effect_earnest->transaction_no=GoodsOrder::SetTransactionNo($post['phone']);
            $effect_earnest->requirement=$post['requirement'];
            $effect_earnest->original_price=$post['original_price']*100;
            $effect_earnest->sale_price=$post['sale_price']*100;
            $effect_earnest->type=self::TYPE_STATUS;
            $effect_earnest->item=self::TYPE_STATUS;
            if(!$effect_earnest->save(false)){
                $tran->rollBack();
                return false;
            }
            $effect_picture=new EffectPicture();
            $effect_picture->effect_id=$id;
            $effect_picture->style_id=$post['style'];
            $effect_picture->series_id=$post['series'];
            if(!$effect_picture->save(false)){
                $tran->rollBack();
                return false;
            }
            $tran->commit();
            return $id;
        }catch (Exception $e){
            $tran->rollBack();
            return false;
        }

    }
    /**
     * get effect view info
     * @param int $effect_id
     * @return array
     */
    public function geteffectdata($effect_id){
        $data=[];
        $query=new Query();
        $array= $query->from('effect_earnest As ea')
            ->select('e.area,e.toponymy,e.city,e.particulars,e.district,e.street,e.high,e.window,e.stairway,t.style,s.series,ea.*')
            ->leftJoin('effect as e','ea.effect_id=e.id')
            ->leftJoin('effect_picture as ep','e.id=ep.effect_id')
            ->leftJoin('series As s','s.id = ep.series_id')
            ->leftJoin('style As t','t.id = ep.style_id')
            ->where(['ea.effect_id'=>$effect_id])->one();
        if(!$array){
            $data['particulars_view']=null;
        }
        if(isset($array['district'])){
            $array['address']=$array['city'].$array['district'].$array['street'];
        }else{
            $array['address']=$array['city'].$array['street'];
        }
        $array['create_time']=date('Y-m-d',$array['create_time']);
        $array['earnest']=sprintf('%.2f',(float)$array['earnest']*0.01);
        $array['sale_price']=sprintf('%.2f',(float)$array['sale_price']*0.01);
        $array['original_price']=sprintf('%.2f',(float)$array['original_price']*0.01);
        unset($array['city']);
        unset($array['district']);
        unset($array['street']);
        if(isset($array['stairway'])){
            $stairway_cl=(new Query())->from('effect')->select('attribute')->leftJoin('stairs_details','effect.stair_id=stairs_details.id')->where(['effect.id'=>$effect_id])->one();
            $array['stairway']=$stairway_cl['attribute'];
        }else{
            $array['stairway']=null;
        }
        $data['particulars_view']=$array;

        $material=EffectMaterial::find()->where(['effect_id'=>$effect_id])->asArray()->all();
        if(!$material){
            $data['material']=null;
        }
        foreach ($material as &$value){
            $goods_cate_id=Goods::find()->select('brand_id,category_id')->where(['id'=>$value['goods_id']])->asArray()->one();
            $value['price']= sprintf('%.2f',(float)$value['price']*0.01);
            $value['cate_level3']=GoodsCategory::find()->select('title')
                ->where(['id'=>$goods_cate_id['category_id']])
                ->asArray()
                ->one()['title'];
            $value['brand']=GoodsBrand::find()
                ->select('name')
                ->where(['id'=>$goods_cate_id['brand_id']])
                ->asArray()
                ->one()['name'];
            $value['first_cate_id']=GoodsCategory::find()
                ->select('title')
                ->where(['id'=>$value['first_cate_id']])
                ->asArray()->one()['title'];

        }

        $material_grop=self::array_group_by($material,'first_cate_id');

        $data['material']=$material_grop;
        return $data;

    }

    /**
     * app 端方案详情
     * @param $enst_id
     * @return array|null
     */
    public static  function getAppeffectdata($enst_id){
        $data=[];
        $query=new Query();
        $array= $query->from('effect_earnest As ea')
            ->select('e.add_time,e.area,e.toponymy,e.city,e.particulars,e.district,e.street,e.high,e.window,e.stairway,t.style,s.series,ea.*')
            ->leftJoin('effect as e','ea.effect_id=e.id')
            ->leftJoin('effect_picture as ep','ea.effect_id=ep.effect_id')
            ->leftJoin('series As s','s.id = ep.series_id')
            ->leftJoin('style As t','t.id = ep.style_id')
            ->where(['ea.id'=>$enst_id])->one();

        $effect_id=EffectEarnest::find()->where(['id'=>$enst_id])->select('effect_id')->asArray()->one()['effect_id'];

        if($array==false){
            $data=null;
        }
        if(!isset($array['sale_price'])){
            $array['sale_price']=null;
        }
        if(!isset($array['original_price'])){
            $array['original_price']=null;
        }

        $array['add_time']=date('Y-m-d H:i:s',$array['add_time']);
        $array['create_time']=date('Y-m-d H:i:s',$array['create_time']);
        $array['sale_price']=sprintf('%.2f',(float)$array['sale_price']*0.01);
        $array['original_price']=sprintf('%.2f',(float)$array['original_price']*0.01);
        $data['quote']=[
            ['name'=>'原价', 'value'=>'￥'.$array['original_price']],
            [ 'name'=>'优惠后价格', 'value'=>'￥'.$array['sale_price']],
            ['name'=>'','value'=>'（包含工人费用，不包含设计图纸费用）'],
            ['name'=>'保存时间','value'=>$array['add_time']]
        ];

            $data['user_view']=[
                ['name'=>'姓名','value'=>$array['name']],
                ['name'=>'电话','value'=>$array['phone']],
                ['name'=>'申请时间','value'=>$array['create_time']]
            ];
        if($array['name']=='' && $array['phone']==''){
           unset($data['user_view']);
        }


        $data['id']=$array['id'];
        if(isset($array['district'])){
            $array['address']=$array['city'].$array['district'].$array['street'];
        }else{
            $array['address']=$array['city'].$array['street'];
        }
        if($array['stairway']){
            $stairway_cl=(new Query())->from('effect')->select('attribute')->leftJoin('stairs_details','effect.stair_id=stairs_details.id')->where(['effect.id'=>$effect_id])->one();
            $array['stairway']=$stairway_cl['attribute'];
        }else{
            $array['stairway']='';
        }
        $data['particulars_view'] =[
            ['name'=>'小区名称','value'=>$array['toponymy']],
            ['name'=>'小区地址','value'=>$array['address']],
            ['name'=>'面积','value'=>$array['area'].'㎡'],
            ['name'=>'户型','value'=>$array['particulars']],
            ['name'=>'层高','value'=>$array['high'].'m'],
            ['name'=>'飘窗','value'=>$array['window'].'m'],
            ['name'=>'楼梯选择','value'=>$array['stairway']],
            ['name'=>'系列','value'=>$array['series']],
            ['name'=>'风格','value'=>$array['style']]
        ];

        $material=EffectMaterial::find()->where(['effect_id'=>$effect_id])->asArray()->all();
        if(!$material){
            $data['material']=null;
        }
        foreach ($material as $k=>&$value){
            $goods_cate_id=Goods::find()->select('brand_id,category_id')->where(['id'=>$value['goods_id']])->asArray()->one();
            $value['price']= '￥'.sprintf('%.2f',(float)$value['price']*0.01);
            $value['cate_level3']=GoodsCategory::find()->select('title')
                ->where(['id'=>$goods_cate_id['category_id']])
                ->asArray()
                ->one()['title'];
            $value['brand']=GoodsBrand::find()
                ->select('name')
                ->where(['id'=>$goods_cate_id['brand_id']])
                ->asArray()
                ->one()['name'];
            $value['first_cate_id']=GoodsCategory::find()
                ->select('title')
                ->where(['id'=>$value['first_cate_id']])
                ->asArray()->one()['title'];
            if($value['brand']==null){
                $value['brand']='';
            }
            if($value['cate_level3']==null){
                $value['cate_level3']='';
            }
            if($value['first_cate_id']==null){
                $value['first_cate_id']='';
            }
            unset($value['effect_id']);
            unset($value['goods_id']);

        }

        $material_grop=self::array_group_by($material,'first_cate_id');
        $material_grop=array_values($material_grop);
        $data['material']=$material_grop;

        return $data;

    }



    public static function array_group_by($arr, $key)
    {
        $grouped = [];
        foreach ($arr as $value) {
            $grouped[$value[$key]][] = $value;
        }
        if (func_num_args() > 2) {
            $args = func_get_args();
            foreach ($grouped as $key => $value) {
                $parms = array_merge([$value], array_slice($args, 2, func_num_args()));
                $grouped[$key] = call_user_func_array('array_group_by', $parms);
            }
        }
        return $grouped;
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
        ],['id'=>$id])->execute();

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

    public static function effectAndEffectPicture($select = [] ,$where)
    {
        return self::find()
            ->leftJoin('effect_picture','effect_picture.effect_id = effect.id')
            ->select($select)
            ->where($where)
            ->asArray()
            ->one();
    }
}