<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/27 0027
 * Time: 下午 14:08
 */

namespace app\models;

use yii\db\ActiveRecord;

class Apartment extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'apartment';
    }

    public function rules()
    {
        return [
            [['min_area','max_area','project_points'],'integer'],
            [['project_name'],'string']
        ];
    }

    public static function findByAll($select =[] ,$where = [])
    {
        return self::find()
            ->asArray()
            ->select($select)
            ->where($where)
            ->all();
    }
}
