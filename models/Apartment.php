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
    const TABLE_NAME = 'apartment';
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
            [['min_area','max_area'],'integer'],
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

    public static function findByInsert($rows)
    {
        return \Yii::$app->db
            ->createCommand()
            ->insert(self::TABLE_NAME,[
                'min_area' =>$rows['min_area'],
                'max_area' =>$rows['max_area'],
                'points_id'=>$rows['points_id'],
                'project_name' =>$rows['project_name'],
                'project_value'=>$rows['project_value'],
            ])
            ->execute();
    }

    public static function findByUpdate($rows,$id)
    {
        $row = \Yii::$app->db->createCommand();
        return $row->update(self::TABLE_NAME,[
            'project_value'=>$rows
        ],['id'=>$id])->execute();
    }
}
