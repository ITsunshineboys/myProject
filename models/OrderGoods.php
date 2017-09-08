<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class OrderGoods extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'order_goodslist';
    }

    /**
     * @param $order_no
     * @param $sku
     * @return array|null|ActiveRecord
     */
      public static  function  FindByOrderNoAndSku($order_no,$sku){
        $data=self::find()
            ->where(['order_no'=>$order_no])
            ->andWhere(['sku'=>$sku])
            ->asArray()
            ->one();
        return $data;
    }
}