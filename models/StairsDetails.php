<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/13 0013
 * Time: 上午 10:58
 */
namespace app\models;
use yii\db\ActiveRecord;

class StairsDetails extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'stairs_details';
    }

    /**
     * find all information
     * @return array|ActiveRecord[]
     */
    public static function findByAll()
    {
        return StairsDetails::find()
            ->orderBy(['id'=>SORT_ASC])
            ->asArray()
            ->all();
    }
}