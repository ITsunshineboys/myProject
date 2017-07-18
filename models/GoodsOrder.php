<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class GoodsOrder extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_order';
    }

    /**
     * Get total amount order
     *
     * @param int $startTime start time
     * @param int $endTime end time
     * @param int $supplierId supplier id default 0
     * @return int
     */
    public static function totalAmountOrder($startTime, $endTime, $supplierId = 0)
    {
        $query = self::find()
            ->select('sum(amount_order) as total_amount_order')
            ->where(['>=', 'create_time', $startTime])
            ->andWhere(['<=', 'create_time', $endTime]);
        $supplierId > 0 && $query->andWhere(['supplier_id' => $supplierId]);
        return (int)$query->asArray()->all()[0]['total_amount_order'];
    }

    /**
     * Get total order number
     *
     * @param int $startTime start time
     * @param int $endTime end time
     * @param int $supplierId supplier id default 0
     * @return int
     */
    public static function totalOrderNumber($startTime, $endTime, $supplierId = 0)
    {
        $query = self::find()
            ->where(['>=', 'create_time', $startTime])
            ->andWhere(['<=', 'create_time', $endTime]);

        $supplierId > 0 && $query->andWhere(['supplier_id' => $supplierId]);

        return (int)$query->count();
    }

    /**
     *
     * 获取商品信息-线下店商城
     *
     *
     *
     */
        public function Getlinegoodsdata($goods_id,$goods_num,$goods_attr){
            $query=new \yii\db\Query();
            $array  = $query->from('goods AS a')->select('a.supplier_id,a.title,a.subtitle,b.nickname,c.name,a.cover_image,a.market_price,b.icon')->leftJoin('supplier AS b', 'b.id = a.supplier_id')->leftJoin('goods_brand AS c','c.id = a.brand_id')->where(['a.id' =>$goods_id])->all();
            foreach ($array as $k=> $v) {
                $array[0]['price']=$array[0]['market_price'];
                $array[0]['cover_image']=$_SERVER['SERVER_NAME'].'/'.$array[0]['cover_image'];
                $array[0]['goods_attr']=$goods_attr;
                $array[0]['goods_num']=$goods_num;
            }
            return $array;
        }


}