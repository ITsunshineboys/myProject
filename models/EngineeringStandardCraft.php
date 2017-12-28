<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/10 0010
 * Time: 下午 17:40
 */
namespace app\models;
use app\services\BasisDecorationService;
use dosamigos\qrcode\formats\vCard;
use yii\db\ActiveRecord;
use yii\db\Query;

class EngineeringStandardCraft  extends ActiveRecord
{

    const PRICE_CONVERSION = 100;


    const UNIT =[
      1 => 'm/点位',
      2 => 'kg/m²',
      3 => 'm',
      4 => 'L/m²',
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

    public static function findALLByid($id,$city_code){

        $chlidren= WorkerType::find()->asArray()->select('id,worker_name,unit')->where(['pid'=>$id])->all();

        $data=[];

        foreach ($chlidren as $chlid){

            $row =  self::find()
                ->asArray()
                ->select([])
                ->where(['city_code'=>$city_code,'project_id'=>$chlid['id']])
                ->one();

            if($row==null){
                $row['city_code']=$city_code;
                $row['material']='';
                $row['project_id']=$chlid['id'];
                $row['project']=$chlid['worker_name'];
            }else{
                if($row['project_id']==$chlid['id']){
                    $row['project']=$chlid['worker_name'];
                    $row['material']=$row['material']/100;
                }
            }

            $data[]=$row;
        }

        return $data;

    }
    /**
     * condition find
     * @param string $project
     * @param int $district
     * @return array|bool|ActiveRecord[]
     */
    public static function findByAll($project='',$code =510100)
    {

        $select = 'id,material,project_id';
        $row =  self::find()
            ->asArray()
            ->select($select)
            ->where(['city_code'=>$code])
            ->andWhere(['id'=>$project])
            ->all();

        foreach ($row as &$one){

            $one['project_details']=$one['project_details'];
            $one['material'] = $one['material'] / self::PRICE_CONVERSION;
            $one['unit'] = self::UNIT[$one['unit']];
        }

        return $row;
    }

    public static function CraftsAllbyId($id){
        return self::find()
            ->asArray()
            ->select('id,project_details,project')
            ->where(['id'=>$id])
            ->one();
    }

    public static function findByList(){

        return self::find()
            ->asArray()
//            ->where(['city_code'=>$city])
            ->distinct()
            ->select('project,id')
            ->all();
    }


    public static function findallbycity($city,$id){
        return self::find()
            ->asArray()
            ->where(['project_id'=>$id,'city_code'=>$city])
            ->one();
    }
}