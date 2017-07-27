<?php

namespace app\models;

use yii\db\ActiveRecord;

class District extends ActiveRecord
{
    const CODE_LEN = 6;

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'district';
    }

    /**
     * Validate district code
     *
     * @param int $districtCode district code
     * @return bool|null|ActiveRecord
     */
    public static function validateDistrictCode($districtCode)
    {
        if (self::CODE_LEN != strlen($districtCode)) {
            return false;
        }

        return self::findByCode($districtCode);
    }

    /**
     * Get district by code
     *
     * @param int $code district code
     * @return ActiveRecord|null
     */
    public static function findByCode($code)
    {
        return self::findOne($code);
    }
}
