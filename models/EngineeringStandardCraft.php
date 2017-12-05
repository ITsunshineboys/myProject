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
    const PRICE_CONVERSION = 100;
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
        $row =  self::find()
            ->asArray()
            ->select($select)
            ->where(['district_code'=>$code])
            ->andWhere(['points_id'=>$project])
            ->all();

        foreach ($row as &$one){
            $one['material'] = $one['material'] / self::PRICE_CONVERSION;
        }

        return $row;
    }

    public static function findByList(){
        return self::find()
            ->asArray()
            ->distinct()
            ->select('project')
            ->all();
    }
}