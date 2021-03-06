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
    const SEPARATOR_NAMES = ',';

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
    public static function getDistrict($district_code){
        return District::fullNameByCode($district_code,self::SEPARATOR_NAMES);
    }

    /**
     * 通过  district_code  获取 模糊 code
     * @param $district_code
     * @return string
     */
    public  static  function  GetVagueDistrictCode($district_code)
    {
        $pro=substr($district_code,0,2);
        $ci=substr($district_code,2,2);
        $dis=substr($district_code,4,2);
        if ($pro==0)
        {
            $code=0;
        }else{
            if ($ci==0){
                $code=$pro;
            }else if($dis==0){
                $code=$pro.$ci;
            }else{
                $code=$district_code;
            }
        }
        return $code;
    }





   /**
     * 判断是否在指定收货区域
     * @param $districtcode
     * @param $template_id
     * @return bool
     */
     public static function isApply($district_code, $template_id){
       $district=LogisticsDistrict::find()
            ->where(['template_id'=>$template_id])
            ->andWhere(['district_code'=>$district_code])
            ->one();
        if (!$district)
        {
            $pro=substr($district_code,0,2);
            if (LogisticsDistrict::find()
                ->where(['template_id'=>$template_id])
                ->andWhere(['district_code'=>$pro.'0000'])
                ->one())
            {
                $code=200;
                return $code;
            }
            $ci=substr($district_code,2,2);
            if (LogisticsDistrict::find()
                ->where(['template_id'=>$template_id])
                ->andWhere(['district_code'=>$pro.$ci.'00'])
                ->one())
            {
                $code=200;
                return $code;
            }
            $dis=substr($district_code,4,2);
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


    /**
     * @param $district_code
     * @return string
     */
    public  static  function  GetLineDistrictByDistrictCode($district_code)
    {
        return District::fullNameByCode($district_code);
    }

}