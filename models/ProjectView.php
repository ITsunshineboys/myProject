<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/26 0026
 * Time: 下午 16:41
 */
namespace app\models;

use yii\db\ActiveRecord;

class ProjectView extends ActiveRecord
{
    const TABLE_NAME = 'project_view';
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'project_view';
    }

    public static function findByAll($select = [] , $where = [])
    {
        return self::find()
            ->asArray()
            ->select($select)
            ->where($where)
            ->all();
    }

    public static function findByUpdate($rows,$id)
    {
        $row = \Yii::$app->db->createCommand();
        return $row->update(self::TABLE_NAME,[
                'project_value'=>$rows
            ],['id'=>$id])->execute();
    }
}