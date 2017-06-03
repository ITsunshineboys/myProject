<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use app\services\StringService;
use yii\db\ActiveRecord;

class LogisticsDistrict extends ActiveRecord
{
    const SCENARIO_ADD = 'add';

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'logistics_district';
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