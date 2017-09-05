<?php

namespace app\models;
use Yii;
use yii\db\Exception;
use yii\db\ActiveRecord;

class OrderAfterSaleImage extends ActiveRecord
{


    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'order_after_sale_image';
    }




}
