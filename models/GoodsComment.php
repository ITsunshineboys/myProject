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
    const FIELDS_APP = ['id', 'name', 'icon', 'create_time', 'content', 'score', 'images'];
    const SCORE_GOOD = [8, 10];
    const SCORE_MEDIUM = [4, 6];
    const SCORE_POOR = [2];
    const DESC_SCORE_GOOD = '好评';
    const DESC_SCORE_MEDIUM = '中评';
    const DESC_SCORE_POOR = '差评';
    const MAX_LEN_CONTENT = 70;
    const PAGE_SIZE_DEFAULT = 12;
    const FIELDS_EXTRA = ['images'];

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
            || in_array('create_time', $selectOld)
            || in_array('score', $selectOld)
        ) {
            foreach ($commentList as &$comment) {
                if (isset($comment['create_time'])) {
                    $comment['create_time'] = date('Y-m-d', $comment['create_time']);
                }

                if (in_array('images', $selectOld)) {
                    $comment['images'] = CommentImage::find()
                        ->select(['image'])
                        ->where(['comment_id' => $comment['id']])
                        ->asArray()
                        ->all();
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
}