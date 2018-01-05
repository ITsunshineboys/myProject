<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class CommentReply extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'comment_reply';
    }

    /**
     * Get replies by comment id
     *
     * @param  init $commentId comment id
     * @return array
     */
    public static function findRepliesByCommentId($commentId)
    {
        $commentId = (int)$commentId;
        if ($commentId <= 0) {
            return [];
        }

        return Yii::$app->db
            ->createCommand("select content from {{%" . self::tableName() . "}} where comment_id = {$commentId}")
            ->queryColumn();
    }

    /**
         * @param $postData
         * @param $user
         * @return int
         */
       public static function  CommentReplyAction($postData)
       {
           if (
               !array_key_exists('reply_content',$postData)
               || !array_key_exists('order_no',$postData)
               || !array_key_exists('sku',$postData)
           )
           {
               $code=1000;
               return $code;
           }
           $OrderGoods=OrderGoods::find()
               ->where(['order_no'=>$postData['order_no'],'sku'=>$postData['sku']])
               ->one();
           var_dump($OrderGoods);die;
           if ($OrderGoods->comment_id==0){
               $code=1000;
               return $code;
           }
           $tran = Yii::$app->db->beginTransaction();
           try{
               $commentReply=new self;
               $commentReply->comment_id=$OrderGoods->comment_id;
               $commentReply->content=$postData['reply_content'];
               $res=$commentReply->save(false);
               if (!$res){
                   $tran->rollBack();
                   $code=500;
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
}