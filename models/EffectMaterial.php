<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "effect_material".
 *
 * @property string $id
 * @property string $effect_id
 * @property integer $goods_id
 * @property string $price
 * @property integer $count
 */
class EffectMaterial extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'effect_material';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['effect_id'], 'required'],
            [['effect_id', 'goods_id', 'count'], 'integer'],
        ];
    }


    public static  function geteffectdata($effect_id){
        $data=[];
        $query=new Query();
        $array= $query->from('effect_earnest As ea')
            ->select('e.area,e.toponymy,e.city,e.particulars,e.district,e.street,e.high,e.window,e.stairway,t.style,s.series,ea.*')
            ->leftJoin('effect as e','ea.effect_id=e.id')
            ->leftJoin('effect_picture as ep','e.id=ep.effect_id')
            ->leftJoin('series As s','s.id = ep.series_id')
            ->leftJoin('style As t','t.id = ep.style_id')
            ->where(['ea.effect_id'=>$effect_id])->one();
        $array['particulars']=mb_substr($array['particulars'],0,4);
        if(!isset($array['sale_price'])){
            $array['sale_price']=null;
        }
        if(!isset($array['original_price'])){
            $array['original_price']=null;
        }
        $array['create_time']=date('Y-m-d H:i:s',$array['create_time']);
        $array['sale_price']=sprintf('%.2f',(float)$array['sale_price']*0.01);
        $array['original_price']=sprintf('%.2f',(float)$array['original_price']*0.01);
        $data['quote']=[
            [ 'name'=>'优惠后价格', 'vaule'=>$array['sale_price']],
            ['name'=>'原价', 'vaule'=>$array['original_price']],
            ['name'=>'保存时间','value'=>$array['create_time']]
        ];
        $data['user_view']=[
            ['name'=>'电话','value'=>$array['phone']],
            ['name'=>'姓名','value'=>$array['name']],
            ['name'=>'申请时间','value'=>$array['create_time']]
        ];
        if($array['district']){
            $array['address']=$array['city'].$array['district'].$array['street'];
        }else{
            $array['address']=$array['city'].$array['street'];
        }
        if($array['stairway']){
            $stairway_cl=(new Query())->from('effect')->select('attribute')->leftJoin('stairs_details','effect.stair_id=stairs_details.id')->where(['effect.id'=>$effect_id])->one();
            $array['stairway']=$stairway_cl['attribute'];
        }else{
            $array['stairway']=null;
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
        return $data;

    }

}
