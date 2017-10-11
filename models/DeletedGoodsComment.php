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
use yii\db\Query;

class DeletedGoodsComment extends ActiveRecord
{

    const  PAGE_SIZE_DEFAULT=12;
    const FIELDS_COMMENT_ADMIN=[
        'd.order_no',
        'd.sku',
        'o.goods_name',
        'd.create_time',
        'u.nickname',

    ];
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'deleted_goods_comment';
    }


    /**
     * @param $where
     * @param $select
     * @param $page
     * @param $size
     * @return array
     */
    public static function pagination($where, $select, $page, $size)
    {
        $offset = ($page - 1) * $size;
        $commentList =(new Query())
            ->from(self::tableName().' as d')
            ->leftJoin(OrderGoods::tableName().' as o','d.order_no=o.order_no and d.sku=o.sku')
            ->leftJoin(GoodsOrder::tableName().' as g','g.order_no=d.order_no')
            ->leftJoin(User::tableName(). ' as u','u.id=d.handle_uid')
            ->select($select)
            ->where($where)
            ->offset($offset)
            ->orderBy('d.create_time desc')
            ->limit($size)
            ->all();
        return $commentList;
    }
}