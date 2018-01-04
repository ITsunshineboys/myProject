<?php

namespace app\models;

use app\services\ModelService;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "effect_toponymy".
 *
 * @property integer $id
 * @property string $effect_id
 * @property integer $add_time
 * @property integer $province_code
 * @property integer $city_code
 * @property integer $district_code
 * @property string $toponymy
 */
class EffectToponymy extends \yii\db\ActiveRecord
{
    const FIELDS_EXTRA=[
        'id',
        'add_time',
        'toponymy'
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'effect_toponymy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['effect_id', 'add_time'], 'required'],
            [['add_time', 'province_code', 'city_code', 'district_code'], 'integer'],
            [['effect_id'], 'string', 'max' => 20],
            [['toponymy'], 'string', 'max' => 50],
        ];
    }



    public static function PlotView($id){
        $effect_ids =self::find()->asArray()->where(['id'=>$id])->select('effect_id')->one()['effect_id'];
        if(!$effect_ids){
            return 1000;
        }
        $data=[];
        $effect_ids = explode(',',$effect_ids);
        foreach ($effect_ids as &$effect_id){
           $effect_datas = Effect::find()
               ->where(['id'=>$effect_id])
               ->asArray()
               ->orderBy('sort_id as ASC')
               ->one();
            $data[]=$effect_datas;
        }
        return $data;
    }


    public static function pagination($where = [], $select = [], $page = 1, $size = ModelService::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC'){
        $select = array_diff($select, self::FIELDS_EXTRA);
        $offset = ($page - 1) * $size;
        $toponymylist = self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();
        foreach ($toponymylist as &$toponymy) {
            $toponymy['add_time']=date('Y-m-d H:i:s',$toponymy['add_time']);
            $toponymy['district']=District::findByCode($toponymy['district_code'])->name;
            unset($toponymy['district_code']);
        }
        $total=self::find()->where($where)->count();;
        return ModelService::pageDeal($toponymylist, $total, $page, $size);
    }
}
