<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/10 0010
 * Time: 下午 17:40
 */
namespace app\models;
use yii\db\ActiveRecord;

class EngineeringStandardCraft  extends ActiveRecord
{
    const FIELDS_ADMIN =[
        'id',
        'district_code',
        'project',
        'material',
        'project_details',
        'units',
    ];
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'engineering_standard_craft';
    }

    /**
     * condition find
     * @param string $project
     * @param int $district
     * @return array|bool|ActiveRecord[]
     */
    public static function findByAll($project='',$code =510100,$select=[])
    {
        return self::find()
            ->asArray()
            ->select($select)
            ->where(['and', ['district_code' => $code], ['project' => $project]])
            ->all();
    }

    public static function findByList(){
        return self::find()
            ->asArray()
            ->distinct()
            ->select('project')
            ->all();
    }
}