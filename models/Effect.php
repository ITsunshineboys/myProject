<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/8 0008
 * Time: 下午 14:34
 */
namespace app\models;

use yii\db\ActiveRecord;

class Effect extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'effect';
    }

    /**
     * @param $toponymy
     * @param $street
     * @return array|ActiveRecord[]
     */
    public function districtSearch($search = '')
    {
        if (!empty($search))
        {
            $detail = $this->find()->where( ['or',['like','toponymy',$search],['like','street',$search]])->all() ;
        }
        return $detail;
    }
}