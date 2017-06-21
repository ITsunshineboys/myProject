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

class CommentImage extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'comment_image';
    }

    /**
     * Get images by comment id
     *
     * @param  init $commentId comment id
     * @return array
     */
    public static function findImagesByCommentId($commentId)
    {
        $commentId = (int)$commentId;
        if ($commentId <= 0) {
            return [];
        }

        return Yii::$app->db
            ->createCommand("select image from {{%" . self::tableName() . "}} where comment_id = {$commentId}")
            ->queryColumn();
    }
}