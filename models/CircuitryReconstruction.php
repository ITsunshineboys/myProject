<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/26 0026
 * Time: 下午 14:10
 */
namespace app\models;
use yii\db\ActiveRecord;

class CircuitryReconstruction extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'circuitry_reconstruction';
    }

    public static function findByAll($id = '',$project ='')
    {
        $project_id = 0;
        if($id){
            $circuitry = self::find()->where(['and',['decoration_list_id'=>$id],['project'=>$project]])->all();
            foreach ($circuitry as $one)
            {
                if($one['points_id'] == ){
                    $project_id = $one['points_id'];
                }

            }
            var_dump($project_id);exit;
        }
            return $circuitry;
    }
}