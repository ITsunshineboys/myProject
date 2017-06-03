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
}