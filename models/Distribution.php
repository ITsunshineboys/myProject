<?php

namespace app\models;

use Faker\Provider\Address;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Query;
use app\services\StringService;
use yii\data\Pagination;
use app\models\User;

const  GOODS_ORDER = 'goods_order';
const  DISTRIBUTTION = 'distribution';
const  USER='user';
class Distribution extends ActiveRecord
{

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'distribution';
    }

    public static  function Distributionusercenter($mobile){
        $data=self::find()->where(['mobile'=>$mobile])->asArray()->one();
        $parent=self::find()->select('mobile,applydis_time')->where(['id'=>$data['parent_id']])->asArray()->one();
        $son=self::find()->select('mobile,applydis_time')->where(['parent_id'=>$data['id']])->asArray()->all();
        $res=[
            'mobile' => $mobile,
            'parent' => $parent,
            'son'=>$son
        ];
        return $res;
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
     * 获取分销详情
     * @param $mobile
     * @return array
     */
    public static  function Gettransactiondetail($mobile){
        $data=Distribution::find()->where(['mobile'=>$mobile])->one();
        $subset=Distribution::find()->select('mobile,applydis_time')->where(['parent_id'=>$data['id']])->asArray()->all();
        //subset
        $alist=array();
        foreach ( $subset as $k =>$v){
           $ares[$k]=(new Query())->from('user')->select('id,mobile')->where(['mobile'=>$subset[$k]['mobile']])->one();
           if ($ares[$k]){
               $order_list[$k]=(new Query())->from('goods_order as a')->leftJoin('order_goodslist as b','a.order_no=b.order_no')->select('a.order_no,a.amount_order,a.paytime,a.remarks,a.address_id')->where(['a.user_id'=>$ares[$k]['id'],'b.order_status'=>1])->all();
               foreach ($order_list[$k] as $key =>$val){
                   $order_list[$k][$key]['mobile']=$ares[$k]['mobile'];
                   $alist[]=$order_list[$k][$key];
               }
           }else{
                $auser_address[$k]=Addressadd::find()->where(['mobile'=>$subset[$k]['mobile']])->one();
                if ($auser_address[$k]) {
                    $order_list[$k]=(new Query())->from('goods_order as a')->leftJoin('order_goodslist as b','a.order_no=b.order_no')->select('a.order_no,a.amount_order,a.paytime,a.remarks,a.address_id')->where(['a.address_id'=>$auser_address[$k]['id'],'b.order_status'=>1])->all();
                    foreach ($order_list[$k] as $key =>$val){
                        $order_list[$k][$key]['mobile']=$auser_address[$k]['mobile'];
                        $alist[]=$order_list[$k][$key];
                    }
                }
           }
        }
        return $alist;
    }

    private static function Isuser($mobile)
    {
        $user=User::find()->select('id')->where(['mobile'=>$mobile])->asArray()->one();
        return $user;
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
}