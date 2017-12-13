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


    const UNIT =[
      1 => 'm/点位',
      2 => 'kg/m²',
      3 => 'm',
      4 => 'l/m²',
      5 => 'm/m',
      6 => '元/m²',
      7 => '张',
      8 => 'm²',
      9 => 'kg/m',
      10 => '元/车',
      11 => 'm²/车',
      12 => '元',
    ];

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
    public static function findByAll($project='',$code =510100)
    {

        $select = 'id,material,project_details,unit';
        $row =  self::find()
            ->asArray()
            ->select($select)
            ->where(['city_code'=>$code])
            ->andWhere(['project'=>$project])
            ->all();

        foreach ($row as &$one){
            $one['material'] = $one['material'] / self::PRICE_CONVERSION;
            $one['unit'] = self::UNIT[$one['unit']];
        }

        return $row;
    }

    public static function findByList($city){

        return self::find()
            ->asArray()
            ->where(['city_code'=>$city])
            ->distinct()
            ->select('project')
            ->all();
    }
}