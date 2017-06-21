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
}