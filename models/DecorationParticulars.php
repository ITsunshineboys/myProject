<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/31 0031
 * Time: 上午 10:22
 */
namespace app\models;
use yii\db\ActiveRecord;

class DecorationParticulars extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'decoration_particulars';
    }

    /**
     * find id one
     * @param $id
     * @return array|bool|null|ActiveRecord
     */
    public static function findByOne($id)
    {
        if($id)
        {
            return  self::find()->asArray()->where(['decoration_list_id' => $id])->one();
        }else
        {
            return false;
        }
    }
}