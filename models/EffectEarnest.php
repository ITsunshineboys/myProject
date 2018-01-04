<?php

namespace app\models;

use app\services\ModelService;
use Yii;
use yii\db\Exception;
use yii\db\Query;

/**
 * This is the model class for table "effect_earnest".
 *
 * @property string $id
 * @property integer $effect_id
 * @property string $phone
 * @property string $name
 * @property string $earnest
 * @property string $remark
 * @property integer $create_time
 * @property string $transaction_no
 * @property string $status
 */
class EffectEarnest extends \yii\db\ActiveRecord
{
    const STATUS_PAYED=1;
    const STATUS_NO=0;
    const INSET_EARNST = 8900;
    const PAGE_SIZE_DEFAULT = 10;
    const FIELDS_EXTRA = [];
    const EFFECT_LOGIN=[
        self::STATUS_NO=>'无登陆申请',
        self::STATUS_PAYED=>'APP内申请'
    ];
    const FIELDS_ADMIN = [
        'id',
        'create_time',
        'name',
        'phone',
        'earnest',
        'remark',
        'transaction_no',
        'status',
        'item'

    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'effect_earnest';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['effect_id', 'earnest', 'create_time'], 'integer'],
            [['remark'], 'string'],
            [['phone'], 'string', 'max' => 11],
            [['name'], 'string', 'max' => 255],
            [['transaction_no'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'effect_id' => 'Effect ID',
            'phone' => 'Phone',
            'name' => 'Name',
            'earnest' => 'Earnest',
            'remark' => 'Remark',
            'create_time' => 'Create Time',
            'transaction_no' => 'Transaction No',
        ];
    }
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {

        if ($insert) {
            $this->create_time = time();
            $this->status = 0;
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }


    public static function pagination($where = [], $select = [], $page = 1, $size = ModelService::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC')
    {
        $select = array_diff($select, self::FIELDS_EXTRA);
        $offset = ($page - 1) * $size;
        $effectList = self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();

        foreach ($effectList as &$effect) {

            $effect['item'] = self::EFFECT_LOGIN[$effect['item']];

            if($effect['earnest']==0){
                unset($effect['earnest']);
            }else{
                $effect['earnest']=sprintf('%.2f',(float)$effect['earnest']*0.01);
            }
            if($effect['transaction_no']==''){
                unset($effect['transaction_no']);
            }
            if(isset($effect['create_time'])){
                $effect['create_time']=date('Y-m-d H:i', $effect['create_time']);
            }


        }


        $total = (int)self::find()->where($where)->asArray()->count();
        return ModelService::pageDeal($effectList, $total, $page, $size);
    }

    public static function getToday()
    {
        $year = date("Y");
        $month = date("m");
        $day = date("d");
        $start = mktime(0, 0, 0, $month, $day, $year);//当天开始时间戳
        $end = mktime(23, 59, 59, $month, $day, $year);//当天结束时间戳
        return [$start, $end];
    }

    /**
     * 样板间获取申请总数
     * @return string
     *
     */
    public static function getallapply(){

        return $sum=(new Query())->from('effect_earnest')->where('type=0 and (status = 1 or item = 1)')->count('*');
    }


    /**
     * 样板间获取今日申请总数
     * @return string
     *
     */
    public static function gettodayapply(){
        $today=self::getToday();

        return $sum=(new Query())->from('effect_earnest')->where('create_time<='.$today[1])->andWhere('create_time>='.$today[0])->andWhere('type=0 and (status = 1 or item = 1)')->count('*');
    }

    /**
     * 样板间获取今日申请总定金
     * @return string
     *
     */
    public static function gettodayearnest(){

        $today=self::getToday();

        $sum=(new Query())->from('effect_earnest')->where('create_time<='.$today[1])->andWhere('create_time>='.$today[0])->andWhere(['status'=>self::STATUS_PAYED])->sum("earnest");
        return sprintf('%.2f',(float)$sum*0.01);
    }

    /**
     * 样板间获取申请总定金
     * @return string
     *
     */
    public static function getallearnest(){

        $sum=(new Query())->from('effect_earnest')->andWhere(['status'=>self::STATUS_PAYED])->sum("earnest");
        return sprintf('%.2f',(float)$sum*0.01);
    }

    /**
     * app 申请或保存样板间
     * @param $uid
     * @param $post
     * @return bool|string
     */
    public static function appAddEffect($uid,$post){

        $effects=Effect::find()
            ->select('sort_id')
            ->asArray()
            ->where(['toponymy'=>$post['toponymy']])
            ->all();
        if($effects){
            $sort_id=max($effects)['sort_id']+1;
        }else{
            $sort_id=0;
        }
        $province=District::findByCode($post['province_code'])['name'];
        $city=District::findByCode($post['city_code'])['name'];
        if(isset($post['district_code'])){
            $district=District::findByCode($post['district_code'])['name'];

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
            $particulars=Effect::chinanum($post['bedroom']).'室'.Effect::chinanum($post['sittingRoom_diningRoom']).'厅'.Effect::chinanum($post['kitchen']).'厨'.Effect::chinanum($post['toilet']).'卫';
            if(!isset($post['street'])){
               $street=null;
            }else{
                $street=$post['street'];
            }
            if(!isset($post['district_code'])){
                $district_code=null;
            }else{
                $district_code=$post['district_code'];
            }
            $res = \Yii::$app->db->createCommand()->insert(Effect::SUP_BANK_CARD,[
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
                'street'        => $street,
                'particulars'   => $particulars,
                'stairway'      => $post['stairway'],
                'add_time'      => time(),
                'type'          => Effect::TYPE_STATUS_NO,
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
                        'first_cate_id'=>$attributes['first_cate_id'],

                    ])->execute();
                }
                if(!$res){
                    $tran->rollBack();
                    return false;
                }

            }
            if(($uid && !isset($phone) && !isset($name))){
                $user=User::find()->where(['id'=>$uid])->select('nickname,mobile')->asArray()->one();
                $name=$user['nickname'];
                $phone=$user['mobile'];
                $earnest=0;
                $transaction_no='';
            }elseif(!$uid){
                $name=$post['name'];
                $phone=$post['phone'];
                $earnest=8900;
                $transaction_no=GoodsOrder::SetTransactionNo($phone);
            }

            $effect_earnest=new EffectEarnest();
            $effect_earnest->uid=$uid;
            $effect_earnest->effect_id=$id;
            $effect_earnest->phone=$phone;
            $effect_earnest->name=$name;
            $effect_earnest->earnest =$earnest;
            $effect_earnest->transaction_no=$transaction_no;
            $effect_earnest->requirement=$post['requirement'];
            $effect_earnest->original_price=$post['original_price']*100;
            $effect_earnest->sale_price=$post['sale_price']*100;
            $effect_earnest->type=$post['type'];
            $effect_earnest->item=Effect::TYPE_ITEM;
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
    public static function PlanList($uid,$type){
        $effect_earnests=EffectEarnest::find()
            ->where(['uid'=>$uid,'type'=>$type,'item'=>1])
            ->asArray()
            ->all();


        $data=[];
        if(!isset($effect_earnests)){
            $data=[];
        }
        foreach ($effect_earnests as &$effect_earnest){
            $data[]=(new Query())->from('effect as e')
                ->select('ee.id,e.add_time,st.style,se.series')
                ->leftJoin('effect_earnest as ee','e.id=ee.effect_id')
                ->leftJoin('effect_picture as ep','ep.effect_id='.$effect_earnest['effect_id'])
                ->leftJoin('style as st','st.id=ep.style_id')
                ->leftJoin('series as se','se.id=ep.series_id')
                ->where(['e.id'=>$effect_earnest['effect_id']])
                ->one();
            var_dump($data);

        }die;

        if(!$data){
            $data=[];
        }

        foreach ($data as &$v){
            $v['add_time']=date('Y-m-d H:i:s',$v['add_time']);
            $v['style']=$v['series'].'-'.$v['style'];
            unset($v['series']);
        }
       return $data;
    }
}
