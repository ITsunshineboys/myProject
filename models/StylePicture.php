<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/9 0009
 * Time: 下午 17:33
 */
 namespace app\models;
 use yii\db\ActiveRecord;

 class StylePicture extends ActiveRecord
 {
     /**
      * @return string 返回该AR类关联的数据表名
      */
     public static function tableName()
     {
         return 'style_picture';
     }
 }
