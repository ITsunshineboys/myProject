<?php

namespace app\models;

use yii\db\ActiveRecord;

class District extends ActiveRecord
{
    const CODE_LEN = 6;
    const SEPARATOR_NAMES = '-';

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
        if (self::CODE_LEN != mb_strlen($districtCode)) {
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

    /**
     * Get full name by district code
     *
     * @param int $code district code
     * @param string $separator separator default '-'
     * @return string
     */
    public static function fullNameByCode($code, $separator = self::SEPARATOR_NAMES)
    {
        $names = [];

        $district = self::findByCode($code);
        if ($district) {
            $names[] = $district->name;
            if (mb_strlen($district->pid) == self::CODE_LEN) {
                $parentDistrict = self::findByCode($district->pid);
                if ($parentDistrict) {
                    $names[] = $parentDistrict->name;
                    if (mb_strlen($parentDistrict->pid) == self::CODE_LEN) {
                        $rootDistrict = self::findByCode($parentDistrict->pid);
                        $names[] = $rootDistrict->name;
                    }
                }
            }
        }

        $names = array_reverse($names);
        return implode($separator, $names);
    }
}
