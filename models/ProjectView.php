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
    const UNIT = [
      1 => 'M',
      2 => '%',
      3 => 'M²',
    ];
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'project_view';
    }

    public static function findByAll($select = [] , $where = [])
    {
        $row = self::find()
            ->asArray()
            ->select($select)
            ->where($where)
            ->all();
        foreach ($row as &$one){
            $one['project_value'] = $one['project_value'] / 100;
            if (isset($one['unit'])){
                $one['unit'] = self::UNIT[$one['unit']];
            }
        }

        return $row;
    }

    public static function findByUpdate($rows,$id)
    {
        $row = \Yii::$app->db->createCommand();
        return $row->update(self::TABLE_NAME,[
                'project_value'=>$rows
            ],['id'=>$id])->execute();
    }

    public static function findByOne($project,$points_id)
    {
        $row =  self::find()
                ->asArray()
                ->where(['project'=>$project])
                ->andWhere(['points_id'=>$points_id])
                ->one();
        $row['project_value'] =  $row['project_value'] / 100;

        return $row;
    }
}