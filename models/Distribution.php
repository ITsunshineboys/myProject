<?php

namespace app\models;

use Faker\Provider\Address;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Query;
use app\services\StringService;
use yii\data\Pagination;
use app\services\ModelService;
const  GOODS_ORDER = 'goods_order';
const  DISTRIBUTTION = 'distribution';
const  USER='user';

class Distribution extends ActiveRecord
{


    const PAGE_SIZE_DEFAULT=10;
    const FIELDS_EXTRA=[];
    const FIELDS_ADMIN = [
        'id',
        'parent_id',
        'mobile',
        'profit',
        'create_time',
        'applydis_time',
    ];
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'distribution';
    }

    /**
     * @param $mobile
     * @return array|null|ActiveRecord
     */
    public  static  function  findByMobile($mobile)
    {
            $data=self::find()
                ->where(['mobile'=>$mobile])
                ->one();
            return $data?$data:[];
    }
    /**线下店个人中心
     * @param $mobile
     * @return array
     */
    public static  function DistributionUserCenter($mobile){
        $data=self::find()
            ->where(['mobile'=>$mobile])
            ->one();
        $parent=self::find()
            ->where(['id'=>$data['parent_id']])
            ->one();
        $son=self::find()
            ->select('mobile,applydis_time')
            ->where(['parent_id'=>$data['id']])
            ->asArray()
            ->all();
        $son_count=count($son);
        $goodsOrder_line_count=0;
        $goodsOrder_line_money=0;
        $goodsOrder_online_count=0;
        $goodsOrder_online_money=0;
        $goodsOrder_count=0;
        $goodsOrder_money=0.00;
        if ($son)
        {
            foreach ($son as &$list)
            {
                $list['time']=date('Y-m-d H:i',$list['applydis_time']);
                $goodsOrder_line=GoodsOrder::Find()->where(['consignee_mobile'=>$list['mobile'],'order_refer'=>1]);
                $goodsOrder_line_data=$goodsOrder_line->asArray()->all();
                foreach ($goodsOrder_line_data as &$goodsOrder_line_data_list)
                {
                    $orderGoods=OrderGoods::find()
                        ->where(['order_no'=>$goodsOrder_line_data_list['order_no']])
                        ->asArray()
                        ->all();
                    foreach ($orderGoods as &$list)
                    {
                        if (!$list['order_status']==2)
                        {
                            $goodsOrder_line_money+=($list['goods_price']*0.01*$list['goods_number']+$list['freight']*0.01);
                            $goodsOrder_line_count+=$goodsOrder_line->count();
                        }
                    }

                }

                $user=User::find()->where(['mobile'=>$data->mobile])->one();
                if ($user)
                {
                    $goodsOrder_online=GoodsOrder::Find()->where(['user_id'=>$user->id,'order_refer'=>2]);
                    $goodsOrder_online_data=$goodsOrder_online->asArray()->all();
                    foreach ($goodsOrder_online_data as &$goodsOrder_online_data_list)
                    {
//                        $goodsOrder_online_money+=$goodsOrder_online_data_list['amount_order']*0.01;
                        $orderGoods=OrderGoods::find()
                            ->where(['order_no'=>$goodsOrder_online_data_list['order_no']])
                            ->asArray()
                            ->all();
                        $add_data=0;
                        foreach ($orderGoods as &$list)
                        {
                            if (!$list['order_status']==2)
                            {
                                $goodsOrder_online_money+=($list['goods_price']*0.01*$list['goods_number']+$list['freight']*0.01);
                                $add_data=1;
                            }

                        }
                        if ($add_data==1)
                        {
                            $goodsOrder_online_count+=1;
                        }
                    }
//                    $goodsOrder_online_count+=$goodsOrder_online->count();
                }

                unset($list['applydis_time']);
            }

            $goodsOrder_count=$goodsOrder_online_count+$goodsOrder_line_count;
            $goodsOrder_money=GoodsOrder::switchMoney($goodsOrder_online_money+$goodsOrder_line_money);
        }

        if ($parent)
        {
            $parents=['mobile'=>$parent->mobile,'time'=>date('Y-m-d H:i',$data->applydis_time)];
        }else{
            $parents=[];
        }
        return [
            'binding_count'=>$son_count,
            'order_count'=>$goodsOrder_count,
            'order_money'=>$goodsOrder_money,
            'MyProfit'=>GoodsOrder::switchMoney($data['profit']*0.01),
            'mobile' => $mobile,
            'parent' => $parents,
            'son'=>$son
        ];
    }
    /**
     *
     * @param $page
     * @param $page_size
     * @param $time_type
     * @param $time_start
     * @param $time_end
     * @param $search
     * @return array
     */
    public static  function Getdistributionlist($page,$page_size,$time_type,$time_start,$time_end,$search){
        $query =(new Query())->from(DISTRIBUTTION);
        $time_area = self::timeDeal($time_type, $time_start, $time_end);
        $time_start = $time_area[0];
        $time_end = $time_area[1];
        if ($time_start && $time_end && $time_end > $time_start) {
            $query->andWhere(['>', 'create_time', $time_start])
                ->andWhere(['<', 'create_time', $time_end]);
        }
        if ($search) {
            $query->andFilterWhere(['like', 'mobile', $search]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $page_size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->select('parent_id,mobile,create_time,id')
            ->all();
        foreach ($arr as $k =>$v){
           //自身所产生的订单
            $user[$k]=User::find()->select('mobile,id')->where(['mobile'=>$arr[$k]['mobile']])->asArray()->one();
           if ($user[$k]){
                $arr[$k]['order_num']=GoodsOrder::find()->where(['user_id'=>$user[$k]['id']])->count();
           }else{
               $address[$k]=Addressadd::find()->select('mobile,id')->where(['mobile'=>$arr[$k]['mobile']])->asArray()->one();
               if (!$address[$k]){
                   $arr[$k]['order_num']=0;
               }
               $arr[$k]['order_num']=GoodsOrder::find()->where(['address_id'=>$address[$k]['id']])->count();
           }
           //关联账号所产生的订单
            $dis=self::find()->select('id,mobile')->where(['parent_id'=>$arr[$k]['id']])->asArray()->all();
           $arr[$k]['order_subsetnum']=0;
            foreach ($dis as $key =>$val){
                $user_subset[$key]=User::find()->select('mobile,id')->where(['mobile'=>$dis[$key]['mobile']])->asArray()->one();
                if ($user_subset[$key]){
                    $dis[$key]['order_num']=GoodsOrder::find()->where(['user_id'=>$user_subset[$key]['id']])->count();
                }else{
                    $address_subset[$key]=Addressadd::find()->select('mobile,id')->where(['mobile'=>$dis[$key]['mobile']])->asArray()->one();
                    if (!$address_subset[$key]){
                        $dis[$key]['order_num']=0;
                    }
                    $dis[$key]['order_num']=GoodsOrder::find()->where(['address_id'=>$address_subset[$key]['id']])->count();
                }
                $arr[$k]['order_subsetnum']+=$dis[$key]['order_num'];
            }
            $arr[$k]['order_num']=$arr[$k]['order_subsetnum']+$arr[$k]['order_num'];
            $arr[$k]['create_time']=date('Y-m-d H:i',$arr[$k]['create_time']);
        }

//        $total_page = ceil($count / $page_size);
//
//        if ($page < 1) {
//            $page = 1;
//        }
//        $arr['total_page'] = $total_page;
//        $arr['count'] = $count;
//        $arr['page'] = $page;
        //获取今日新加入 和 分销总人数
        $total_user=self::find()->asArray()->count();
        $time=strtotime(date('Y-m-d',time()));
        $nowday_user=self::find()->asArray()->where('create_time>'.$time)->count();
        $data=self::page($count,$page_size,$page,$arr);
        $data['todayadd']=$nowday_user;
        $data['totaladd']=$total_user;
        return $data;
    }

    /**
     * 得到开始和结束时间
     * @param $time_type
     * @param $time_start
     * @param $time_end
     * @return array
     */
    private static function timeDeal($time_type, $time_start, $time_end)
    {
        if ($time_type == 'custom' && $time_start && $time_end) {
            $time_start = strtotime($time_start);
            $time_end = strtotime($time_end);
        } else {
            $time_area = StringService::startEndDate($time_type, 1);
            $time_start = $time_area[0];
            $time_end = $time_area[1];
        }
        return [$time_start, $time_end];
    }

    private static  function page($count,$page_size,$page,$arr){
        $totalpage=ceil($count/$page_size);
        if ($page>$totalpage){
            $sd= array(
                'list'=>'',
                'totalpage'=>$totalpage,
                'count'=>$count,
                'pagesize'=>$page_size,
                'page'=>$page
            );
        }else{
            $sd=array(
                'list'=>$arr,
                'totalpage'=>$totalpage,
                'count'=>$count,
                'pagesize'=>$page_size,
                'page'=>$page
            );
        }
        return $sd;
    }


    /**
     * @param array $where
     * @param array $select
     * @param int $page
     * @param int $size
     * @param string $orderBy
     * @return array
     */
    public static function pagination($where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC')
    {
        $select = array_diff($select, self::FIELDS_EXTRA);

        $offset = ($page - 1) * $size;
        $DisList = self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();
        foreach ($DisList as &$list) {
            $list['create_time']=date('Y-m-d H:i',$list['create_time']);
            $list['applydis_time']=date('Y-m-d H:i',$list['applydis_time']);
        }
        $total=(int)self::find()->where($where)->asArray()->count();
        return ModelService::pageDeal($DisList, $total, $page, $size);

    }
}