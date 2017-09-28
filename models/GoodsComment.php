<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class GoodsComment extends ActiveRecord
{
    const FIELDS_APP = ['id', 'name', 'icon', 'create_time', 'content', 'score', 'images', 'replies'];
    const SCORE_GOOD = [8, 10];
    const SCORE_MEDIUM = [4, 6];
    const SCORE_POOR = [0, 2];
    const DESC_SCORE_GOOD = '好评';
    const DESC_SCORE_MEDIUM = '中评';
    const DESC_SCORE_POOR = '差评';
    const LEVELS_SCORE = [
        'good' => self::SCORE_GOOD,
        'medium' => self::SCORE_MEDIUM,
        'poor' => self::SCORE_POOR
    ];
    const MAX_LEN_CONTENT = 70;
    const PAGE_SIZE_DEFAULT = 12;
    const FIELDS_EXTRA = ['images', 'replies'];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_comment';
    }

    /**
     * Get goods comment list
     *
     * @param  array $where search condition
     * @param  array $select select fields default all fields
     * @param  int $page page number default 1
     * @param  int $size page size default 12
     * @param  array $orderBy order by fields default id desc
     * @return array
     */
    public static function pagination($where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = ['id' => SORT_DESC])
    {
        $selectOld = $select;

        $select = array_diff($select, self::FIELDS_EXTRA);

        $offset = ($page - 1) * $size;
        $commentList = self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();

        if (!$selectOld
            || in_array('images', $selectOld)
            || in_array('replies', $selectOld)
            || in_array('create_time', $selectOld)
            || in_array('score', $selectOld)
        ) {
            foreach ($commentList as &$comment) {
                if (isset($comment['create_time'])) {
                    $comment['create_time'] = date('Y-m-d', $comment['create_time']);
                }

                if (in_array('images', $selectOld)) {
                    $comment['images'] = CommentImage::findImagesByCommentId($comment['id']);
                }

                if (in_array('replies', $selectOld)) {
                    $comment['replies'] = CommentReply::findRepliesByCommentId($comment['id']);
                }

                if (isset($comment['score'])) {
                    if (in_array($comment['score'], self::SCORE_GOOD)) {
                        $comment['score'] = self::DESC_SCORE_GOOD;
                    } elseif (in_array($comment['score'], self::SCORE_MEDIUM)) {
                        $comment['score'] = self::DESC_SCORE_MEDIUM;
                    } else {
                        $comment['score'] = self::DESC_SCORE_POOR;
                    }
                }

                if (isset($comment['id'])) {
                    unset($comment['id']);
                }
            }
        }

        return $commentList;
    }

    /**
     * Get statistics by goods id
     *
     * @param int $goodsId goods id
     * @return array
     */
    public static function statByGoodsId($goodsId)
    {
        $stat = [];

        foreach (self::LEVELS_SCORE as $level => $score) {
            $stat[$level] = self::find()
                ->where(['goods_id' => $goodsId])
                ->andWhere(['>=', 'score', $score[0]])
                ->andWhere(['<=', 'score', $score[1]])
                ->count();
        }

        return $stat;
    }

     /**
     * @param $postData
     * @param $user
     * @return int
     */
    public  static  function  addComment($postData,$user,$uploadsData)
    {
        if(!array_key_exists('store_service_score', $postData)|| !array_key_exists('shipping_score', $postData)|| !array_key_exists('score', $postData)|| !array_key_exists('logistics_speed_score', $postData) || !array_key_exists('sku',$postData) || !array_key_exists('order_no',$postData) || ! array_key_exists('content',$postData) || !array_key_exists('anonymous',$postData))
        {
            $code=1000;
            return $code;
        }
        $code=self::checkIsSetComment($postData,$user);
        if ($code !=200){
            return $code;
        }
        $goods=Goods::find()
            ->where(['sku'=>$postData['sku']])
            ->one();
        $list=self::GetAverageScore($postData,$goods->supplier_id);

        $time=time();
        $tran = Yii::$app->db->beginTransaction();
        try{
            $res1=self::AddCommentData($postData,$user,$goods,$time,$uploadsData);
            if ($res1!=200){
                $tran->rollBack();
                return $res1;
            }
            $commendSupplierDatabase=self::commendSupplierDatabase($postData,$goods,$time,$list);
            if ($commendSupplierDatabase!=200){
                $tran->rollBack();
                return $commendSupplierDatabase;
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (Exception $e) {
            $code=500;
        $tran->rollBack();
            return $code;
        }

    }

    /**
     * @param $postData
     * @param $user
     * @param $goods
     * @param $time
     * @param $list
     * @return int
     */
    public static  function commendSupplierDatabase($postData,$goods,$time,$list)
    {
        $GetComment=self::find()
            ->select('id')
            ->where(['create_time'=>$time])
            ->asArray()
            ->one();
        $tran = Yii::$app->db->beginTransaction();
        try {
            $orderGoods=OrderGoods::find()
                ->where(['order_no'=>$postData['order_no'],'sku'=>$postData['sku']])
                ->one();
            $orderGoods->comment_id=$GetComment['id'];
            $res1=$orderGoods->save();
            if (!$res1){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            $supplier=Supplier::find()
                ->where(['id'=>$goods->supplier_id])
                ->one();
            $supplier->comprehensive_score=$list['score'];
            $supplier->logistics_speed_score=$list['logistics_speed_score'];
            $res2=$supplier->save(false);
            if (!$res2){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }
    }


     /**
     * add comment --goods_comment  table action
     * @param $postData
     * @param $user
     * @param $goods
     * @param $time
     * @return int
     */
    public static  function  AddCommentData($postData,$user,$goods,$time,$uploadsData)
    {
        $tran = Yii::$app->db->beginTransaction();
        try{
            $comment=new self;
            $comment->goods_id=$goods->id;
            $comment->uid=$user->id;
            $comment->role_id=$user->last_role_id_app;
            $comment->create_time=$time;
            $comment->content=$postData['content'];
            $comment->is_anonymous=$postData['anonymous'];
            $comment->name =$user->nickname;
            $comment->store_service_score=$postData['store_service_score'];
            $comment->logistics_speed_score=$postData['logistics_speed_score'];
            $comment->shipping_score=$postData['shipping_score'];
            $comment->score=$postData['score'];
            $res=$comment->save();
            if (!$res){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            if (is_array($uploadsData)){
                foreach ($uploadsData as &$uploads){
                    $comment_image=new CommentImage();
                    $comment_image->comment_id=$comment->id;
                    $comment_image->image=$uploads;
                    if (!$comment_image->save()){
                        $code=500;
                        return $code;
                    };
                }
            }
            $tran->commit();
            $code=200;
            return $code;
        }catch (Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }
    }


    /**
     * @param $postData
     * @param $user
     * @return int
     */
    public  static  function  checkIsSetComment($postData,$user)
    {
        $orderGoods=OrderGoods::find()
            ->select('comment_id')
            ->where(['order_no'=>$postData['order_no'],'sku'=>$postData['sku']])
            ->one();
        if ($orderGoods['comment_id']==0){
            $code=200;
            return $code;
        }else{
            $code=1032;
            return $code;
        }
    }


    /**
     * @param $postData
     * @param $supplier_id
     * @return array
     */
    public  static  function GetAverageScore($postData,$supplier_id){
        $orders=(new Query())
            ->from(GoodsOrder::tableName().' as a')
            ->select('c.comment_id')
            ->leftJoin(OrderGoods::tableName().' as c','a.order_no = c.order_no')
            ->where(['a.supplier_id'=>$supplier_id])
            ->all();
        $order=[];
        foreach ($orders as $k =>$v){
            if ($orders[$k]['comment_id']){
                $order[$k]=self::find()->where(['id'=>$orders[$k]['comment_id']])->one();
            }else{
                unset($order[$k]['comment_id']);
            }

        }
        $count=count($order);
        $score_list=[];
        $score_list['shipping_score']=0;
        $score_list['store_service_score']=0;
        $score_list['logistics_speed_score']=0;
        $score_list['score']=0;
        $score_list['good_score']=0;
        foreach ($order as $k =>$v){
            $score_list['shipping_score']+=$order[$k]['shipping_score'];
            $score_list['store_service_score']+=$order[$k]['store_service_score'];
            $score_list['logistics_speed_score']+=$order[$k]['logistics_speed_score'];
            $score_list['score']+=$order[$k]['score'];
            if ($order[$k]['score']>=8){
                $score_list['good_score']= $score_list['good_score']+1;
            }
        }
        $data['well_probability']=round($score_list['good_score']/$count,2)*100;
        $data['shipping_score']=round(($score_list['shipping_score']+$postData['shipping_score'])/$count+1,1);
        $data['store_service_score']=round(($score_list['store_service_score']+$postData['store_service_score'])/$count+1,1);
        $data['logistics_speed_score']=round(($score_list['logistics_speed_score']+$postData['logistics_speed_score'])/$count+1,1);
        $data['score']=round(($score_list['score']+$postData['score'])/$count+1,1);
        $data['count']=$count;
      return $data;
    }


    /**
     * @param $comment_id
     * @return string
     */
    public static function findCommentGrade($comment_id)
    {
        $comment=self::find()
            ->select('score')
            ->where(['id'=>$comment_id])
            ->one();
        if (!$comment){
            $grade='';
        }else{
            $score=$comment->score;
            if ($score==8 || $score==10){
                $grade='好评';
            }else if ($score ==4 && $score ==6)
            {
                $grade='中评';
            }else if ($score ==2){
                $grade='差评';
            }else{
                $grade='';
            }
        }
        return $grade;
    }


}