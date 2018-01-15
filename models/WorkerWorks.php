<?php

namespace app\models;

use app\services\ModelService;
use Yii;
use yii\data\Pagination;
use yii\db\Query;

/**
 * This is the model class for table "worker_works".
 *
 * @property integer $id
 * @property integer $worker_id
 * @property integer $order_no
 * @property string $title
 * @property string $desc
 */
class WorkerWorks extends \yii\db\ActiveRecord
{

    const DEAUFT_T=3;
    const STUAT_LIN=0;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_works';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['worker_id', 'order_no'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['desc'], 'string', 'max' => 350],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'worker_id' => '工人id',
            'order_no' => '工人订单号',
            'title' => '标题',
            'desc' => '作品描述',
        ];
    }
    /**
     * 工人最近作品
     * @param $worker_id
     * @return array|bool|null
     */
    public static function getLatelyWorks($worker_id){
        $query=(new Query())
            ->from('worker_works as ww')
            ->select('ww.*,wrj.result_img,wo.start_time,wo.end_time')
            ->leftJoin('work_result as wr','wr.works_id=ww.id')
            ->leftJoin('work_result_img as wrj','wr.id=wrj.work_result_id')
            ->leftJoin('worker_order as wo','ww.worker_id=wo.worker_id')
            ->where(['ww.id'=>$worker_id])
            ->andWhere(['wo.is_old'=>1])
            ->orderBy('wo.end_time Desc')
            ->one();
        if(!$query){
            return null;
        }
        $query['start_time']=date('Y-n-j',$query['start_time']);
        $query['end_time']=date('Y-n-',$query['end_time']);

        return $query;
    }

    /**
     * 获取装修前图片
     * @param $worker_order_no
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function beforedecorationimgs($works_id,$worker_order_no){

        $data=WorkerOrderImg::find()
            ->select('order_img')
            ->where(['worker_order_no'=>$worker_order_no])
            ->asArray()
            ->all();

        if(!$data){
            $works_detail =WorkerWorksDetail::worksdetailbyworksId($works_id);
            if ($works_detail) {
                $img_ids = $works_detail->img_ids;

                if ($img_ids) {
                    $time=WorkResult::find()->asArray()->orderBy('create_time Asc')->where(['order_no'=>$worker_order_no])->all();
                    if(!$time){
                        return null;
                    }
                    $ids = explode(',', $img_ids);
                    foreach ($ids as $id){
                        $a[]= WorkResultImg::find()
                            ->select('result_img,work_result_id')
                            ->asArray()
                            ->where(['id'=>$id])
                            ->one();
                    }
                    foreach ($a as $d){
                        if($d['work_result_id']==$time[0]['id']){
                            $data[]=$d;
                        }
                    }
                }

            } else {
                $data = '';
            }

        }
        return $data;
    }
    /**
     * 获取装修中图片
     * @param $works_id
     * @return array|string
     */
    public static function Indecorationimgs($works_id,$order_no){
        $works_detail =WorkerWorksDetail::worksdetailbyworksId($works_id);
        if ($works_detail) {
            $img_ids = $works_detail->img_ids;

            if ($img_ids) {
                $time=WorkResult::find()->asArray()->orderBy('create_time Desc')->where(['order_no'=>$order_no])->all();
                if(!$time){
                    return null;
                }
                $ids = explode(',', $img_ids);
                foreach ($ids as $id){
                    $a[]= WorkResultImg::find()
                        ->select('result_img,work_result_id')
                        ->asArray()
                        ->where(['id'=>$id])
                        ->one();
                }
                foreach ($a as $d){
                    if($d['work_result_id']!=$time[0]['id'] && $d['work_result_id']!= end($time)['id']){
                        $data[]=$d;
                    }
                }
            }

        } else {
            $data = '';
        }

        return $data;
    }
    /**
     * 装修最后一天上传的图片
     * @param $works_id
     * @return array|string
     */
    public static function afterdecorationimgs($works_id,$order_no){
        $works_detail =WorkerWorksDetail::worksdetailbyworksId($works_id);
        if ($works_detail) {
            $img_ids = $works_detail->img_ids;
            $data=[];
            if ($img_ids) {
                $time=WorkResult::find()->asArray()->orderBy('create_time Desc')->where(['order_no'=>$order_no])->all();
                if(!$time){
                    return [];
                }
                $ids = explode(',', $img_ids);
                foreach ($ids as $id){
                    $a[]= WorkResultImg::find()
                        ->select('result_img,work_result_id')
                        ->asArray()
                        ->where(['id'=>$id])
                        ->one();
                }
                foreach ($a as $d){

                    if($d['work_result_id']==$time[0]['id']){
                        $data[]=$d;
                    }
                }

            }

        } else {
            $data = '';
        }

        return $data;
    }
    /**
     * 作品详情
     * @param $works_id
     *@return null
     */
    public static function GetWorksDetail($works_id){

        $array=self::find()
            ->asArray()
            ->where(['id'=>$works_id])
            ->one();

        if(!$array){
            return [];
        }
        $time=WorkerOrder::find()
            ->asArray()
            ->select('worker_order.start_time,worker_order.end_time,worker_service.service_name')
            ->leftJoin('worker_service','worker_order.worker_type_id=worker_service.id')
            ->where(['worker_order.order_no'=>$array['order_no']])
            ->one();
        $time['start_time']=date('Y-m-d',$time['start_time']);
        $time['end_time']=date('Y-m-d',$time['end_time']);

        $data['works']=array_merge($time,$array);
        //装修前---下单用户上传的
        $before_decoration_imgs=self::beforedecorationimgs($works_id,$array['order_no']);
        //装修中---工人上传的 截取中间日期
        $In_decoration_imgs=self::Indecorationimgs($works_id,$array['order_no']);
        //装修后--工人上传的 截取最后一次上传的日期
        $after_decoration_imgs=self::afterdecorationimgs($works_id,$array['order_no']);
        if($before_decoration_imgs || $In_decoration_imgs || $after_decoration_imgs){
            $data['before_decoration_imgs']=$before_decoration_imgs;
            $data['in_decoration_imgs']=$In_decoration_imgs;
            $data['after_decorationimgs']=$after_decoration_imgs;

        }else{
            $data['before_decoration_imgs']='';
            $data['In_decoration_imgs']='';
            $data['after_decoration_imgs']='';

        }
        $works_views=WorkerWorksReview::find()
            ->asArray()
            ->where(['works_id'=>$works_id])
            ->orderBy('create_time Desc')
            ->where(['pid'=>self::STUAT_LIN])
            ->limit(self::DEAUFT_T)
            ->all();
        if(!$works_views){
            return null;
        }
        foreach ($works_views as &$works_view){
                $works_view['create_time']=date('Y-n-d',$works_view['create_time']);
                $works_view['role']=Role::find()->asArray()->select('name')->where(['id'=>$works_view['role_id']])->one()['name'];
                $works_view['name']=User::find()->asArray()->select('nickname')->where(['id'=>$works_view['uid']])->one()['nickname'];
                $works_view['icon']=User::find()->asArray()->select('icon')->where(['id'=>$works_view['uid']])->one()['icon'];
                $works_view['worker_reply']=WorkerWorksReview::find()->asArray()->select('review')->where(['pid'=>$works_view['id']])->one()['review'];
                unset($works_view['role_id']);
                unset($works_view['uid']);
        }

        $data['comment']=$works_views;

        return $data;
    }
}
