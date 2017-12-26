<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;
use yii;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Query;

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
        if(
            !array_key_exists('store_service_score', $postData)
            || !array_key_exists('shipping_score', $postData)
            || !array_key_exists('score', $postData)
            || !array_key_exists('logistics_speed_score', $postData)
            || !array_key_exists('sku',$postData)
            || !array_key_exists('order_no',$postData)
            || ! array_key_exists('content',$postData)
            || !array_key_exists('anonymous',$postData)
        )
        {
            $postData=yii::$app->request->post();
            if(
                !array_key_exists('store_service_score', $postData)
                || !array_key_exists('shipping_score', $postData)
                || !array_key_exists('score', $postData)
                || !array_key_exists('logistics_speed_score', $postData)
                || !array_key_exists('sku',$postData)
                || !array_key_exists('order_no',$postData)
                || ! array_key_exists('content',$postData)
                || !array_key_exists('anonymous',$postData)
            )
            {
                $code=1000;
                return $code;
            }
        }
        $goodsOrder=GoodsOrder::find()
            ->where(['order_no'=>$postData['order_no']])
            ->one();
        $orderGoods=OrderGoods::find()
            ->where(['order_no'=>$postData['order_no'],'sku'=>$postData['sku']])
            ->one();
        if (!$orderGoods || !$goodsOrder)
        {
            $code=1000;
            return $code;
        }
        $code=self::checkIsSetComment($orderGoods);
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
            $comment=new GoodsComment();
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
            $res=$comment->save(false);
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
                    if (!$comment_image->save(false)){
                        $code=500;
                        return $code;
                    };
                }
            }
            $orderGoods->comment_id=$comment->id;
            $res1=$orderGoods->save(false);
            if (!$res1){
                $code=500;
                $tran->rollBack();
                return $code;
            }
            $supplier=Supplier::find()
                ->where(['id'=>$goods->supplier_id])
                ->one();
            if (!$supplier)
            {
                $code=1000;
                $tran->rollBack();
                return $code;
            }
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
        }catch (Exception $e) {
            $code=500;
            $tran->rollBack();
            return $code;
        }

    }
    /**
     * @param $postData
     * @param $user
     * @return int
     */
    public  static  function  addCommentByModel($postData)
    {
        if(
            !array_key_exists('store_service_score', $postData)
            || !array_key_exists('shipping_score', $postData)
            || !array_key_exists('score', $postData)
            || !array_key_exists('logistics_speed_score', $postData)
            || !array_key_exists('sku',$postData)
            || !array_key_exists('order_no',$postData)
            || ! array_key_exists('content',$postData)
            || !array_key_exists('anonymous',$postData)
        )
        {
            $code=1000;
            return $code;
        }
        $goodsOrder=GoodsOrder::find()
            ->where(['order_no'=>$postData['order_no']])
            ->one();
        $orderGoods=OrderGoods::find()
            ->where(['order_no'=>$postData['order_no'],'sku'=>$postData['sku']])
            ->one();
        if (!$orderGoods || !$goodsOrder)
        {
            $code=1000;
            return $code;
        }
        $user=User::findOne($goodsOrder->user_id);
        if (!$user)
        {
            $code=1000;
            return $code;
        }
        $code=self::checkIsSetComment($orderGoods);
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
            $comment=new GoodsComment();
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
            $res=$comment->save(false);
            if (!$res){
                $code=1000;
                $tran->rollBack();
                return $code;
            }
            $orderGoods->comment_id=$comment->id;
            $res1=$orderGoods->save(false);
            if (!$res1){
                $code=1000;
                $tran->rollBack();
                return $code;
            }
            $supplier=Supplier::find()
                ->where(['id'=>$goods->supplier_id])
                ->one();
            if (!$supplier)
            {
                $code=1000;
                $tran->rollBack();
                return $code;
            }
            $supplier->comprehensive_score=$list['score'];
            $supplier->logistics_speed_score=$list['logistics_speed_score'];
            $res2=$supplier->save(false);
            if (!$res2){
                $code=1000;
                $tran->rollBack();
                return $code;
            }
            $tran->commit();
        }catch (Exception $e) {
            $code=1000;
            $tran->rollBack();
            return $code;
        }
        return ['comment_id'=>$comment->id];

    }
    /**
     * @param $orderGoods
     * @param $comment
     * @param $postData
     * @param $goods
     * @param $time
     * @param $list
     * @return int
     */
    public static  function commendSupplierDatabase($orderGoods,$comment,$postData,$goods,$time,$list)
    {
        $tran = Yii::$app->db->beginTransaction();
        try {
            $orderGoods->comment_id=$comment->id;
            $res1=$orderGoods->save(false);
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
     * 判断是否设置果评论
     * @param $postData
     * @return int
     */
    public  static  function  checkIsSetComment($postData)
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
                $order[$k]=self::find()
                    ->where(['id'=>$orders[$k]['comment_id']])
                    ->one();
            }else{
                unset($order[$k]['comment_id']);
            }
        }
        $count=count($order);
        if ($count !=0)
        {
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
        }else{
            $data['logistics_speed_score']=0;
            $data['score']=0;
        }

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
                $grade=self::DESC_SCORE_GOOD;
            }else if ($score ==4 && $score ==6)
            {
                $grade=self::DESC_SCORE_MEDIUM;
            }else if ($score ==2){
                $grade=self::DESC_SCORE_POOR;
            }else{
                $grade='';
            }
        }
        return $grade;
    }


    /**
     * 判断能否自动评论
     * @param $comp_time
     * @return int
     */
    public  static  function  CheckIsAuToComment($comp_time)
    {
        if ((15*60*60*24+$comp_time)>time())
        {
            $code=1000;
        }else
        {
            $code=200;
        }
        return $code;
    }




}