<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use app\services\StringService;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class LogisticsDistrict extends ActiveRecord
{
    const SCENARIO_ADD = 'add';

    /**
     * Get district code list by template id
     *
     * @param  int $templateId template id
     * @return array
     */
    public static function districtCodesByTemplateId($templateId)
    {
        $templateId = (int)$templateId;
        if ($templateId <= 0) {
            return [];
        }

        return Yii::$app->db
            ->createCommand("select district_code from {{%" . self::tableName() . "}} where template_id = {$templateId}")
            ->queryColumn();
    }

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'logistics_district';
    }

    /**
     * Insert records according to template id and district codes
     *
     * @param $templateId template id
     * @param array $districtCodesArr district codes list
     * @return int
     */
    public static function insertByTemplateIdAndDistrictCodes($templateId, array $districtCodesArr)
    {
        $code = 1000;

        $templateId = (int)$templateId;
        if ($templateId <= 0 || !$districtCodesArr) {
            return $code;
        }

        foreach ($districtCodesArr as $districtCode) {
            $districtName = StringService::checkDistrict($districtCode);
            if ($districtName === false) {
                return $code;
            }

            $logisticsDistrict = new LogisticsDistrict;
            $logisticsDistrict->template_id = $templateId;
            $logisticsDistrict->district_code = $districtCode;
            $logisticsDistrict->district_name = $districtName;

            $logisticsDistrict->scenario = self::SCENARIO_ADD;
            if (!$logisticsDistrict->validate()) {
                return $code;
            }

            if (!$logisticsDistrict->save()) {
                $code = 500;
                return $code;
            }
        }

        $code = 200;
        return $code;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['template_id', 'district_code', 'district_name'], 'required'],
            [['district_code'], 'validateDistrictCode', 'on' => self::SCENARIO_ADD]
        ];
    }

    /**
     * Validates district_code
     *
     * @param string $attribute district_code to validate
     * @return bool
     */
    public function validateDistrictCode($attribute)
    {
        if (!StringService::checkDistrict($this->$attribute)
            || self::find()->where(['template_id' => $this->template_id, $attribute => $this->$attribute])->exists()
        ) {
            $this->addError($attribute);
            return false;
        }

        return true;
    }

     /**
     *   get district
     * 通过districtcode获取地址
     * @param $districtcode
     * @return mixed
     */
    public function getdistrict($districtcode){
        $pro=substr($districtcode,0,2);
        $ci=substr($districtcode,2,2);
        $dis=substr($districtcode,4,2);
        $code=Yii::$app->params['districts'][0];
        if ($ci==0){
            $position=$code[86][$pro.'0000'];
        }else if($dis==0){
            $position=$code[86][$pro.'0000'].$code[$pro.'0000'][$pro.$ci.'00'];
        }else{
            $position=$code[86][$pro.'0000'].$code[$pro.'0000'][$pro.$ci.'00'].$code[$pro.$ci.'00'][$pro.$ci.$dis];
        }
        return $position;
    }


    /**
     *
     * @param $province
     * @param $city
     * @param $district
     * @return array
     */
    public function getdistrictcode($province, $city, $district){
        $code=Yii::$app->params['districts'][0];
        foreach($code[86] as $k=>$v){
            if ($code[86][$k]==$province){
                $provincecode=$k;
            }
        }
        foreach($code[$provincecode] as $k =>$v ){
            if ($code[$provincecode][$k]==$city){
                $citycode=$k;
            }
        }
        foreach ($code[$citycode] as $k =>$v ){
            if ($code[$citycode][$k]==$district){
                $districtcode=$k;
            }
        }
        $arr=array(
            'provincecode'=>$provincecode,
            'citycode'    =>$citycode,
            'districtcode'=>$districtcode

        );
        return $arr;
    }


   /**
     * 判断是否在指定收货区域
     * @param $districtcode
     * @param $template_id
     * @return bool
     */
     public static function is_apply($districtcode, $template_id){
        $data=LogisticsDistrict::find()
            ->where(['template_id'=>$template_id])
            ->all();
        var_dump($data);die;
       $district=LogisticsDistrict::find()
            ->where(['template_id'=>$template_id])
            ->andWhere(['district_code'=>$districtcode])
            ->one();
        if (!$district)
        {
            $pro=substr($districtcode,0,2);
            if (LogisticsDistrict::find()
                ->where(['template_id'=>$template_id])
                ->andWhere(['district_code'=>$pro.'0000'])
                ->one())
            {
                $code=200;
                return $code;
            }
            $ci=substr($districtcode,2,2);
            if (LogisticsDistrict::find()
                ->where(['template_id'=>$template_id])
                ->andWhere(['district_code'=>$pro.$ci.'00'])
                ->one())
            {
                $code=200;
                return $code;
            }
            $dis=substr($districtcode,4,2);
            if (LogisticsDistrict::find()
                ->where(['template_id'=>$template_id])
                ->andWhere(['district_code'=>$pro.$ci.$dis])
                ->one())
            {
                $code=200;
                return $code;
            }
            $code=1000;
            return $code;
        }else{
            $code=200;
            return $code;
        }
    }
}