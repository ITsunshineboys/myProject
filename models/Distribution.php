<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class Distribution extends ActiveRecord
{

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'distribution';
    }

    public static  function Distributionusercenter($mobile){
        $data=self::find()->where(['mobile'=>$mobile])->asArray()->one();
        $parent=self::find()->select('mobile,applydis_time')->where(['id'=>$data['parent_id']])->asArray()->one();
        $son=self::find()->select('mobile,applydis_time')->where(['parent_id'=>$data['id']])->asArray()->all();
        $res=[
            'mobile' => $mobile,
            'parent' => $parent,
            'son'=>$son
        ];
        return $res;
    }

}