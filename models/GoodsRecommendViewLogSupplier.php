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

class GoodsRecommendViewLogSupplier extends ActiveRecord
{
    const CAN_LOG_IP_NUMBER = 1;
    const CANNOT_LOG_IP_NUMBER = 0;

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_recommend_view_log_supplier';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['recommend_id'], 'required'],
            ['recommend_id', 'number', 'integerOnly' => true, 'min' => 1],
            ['recommend_id', 'validateRecommendId', 'skipOnEmpty' => false],
            ['ip', 'number', 'integerOnly' => true]
        ];
    }

    /**
     * Validates recommend id
     *
     * @param string $attribute recommend id to validate
     * @return bool
     */
    public function validateRecommendId($attribute)
    {
        if (!GoodsRecommend::findOne($this->$attribute)) {
            $this->addError($attribute);
            return false;
        }

        return true;
    }

    /**
     * Do some ops before insertion
     *
     * @param bool $insert if is a new record
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->create_time = time();

                if ($this->canLogIpNumber()) {
                    $this->log_ip_number = self::CAN_LOG_IP_NUMBER;
                } else {
                    $this->log_ip_number = self::CANNOT_LOG_IP_NUMBER;
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if can log ip number.
     *
     * The same recommend could be viewed by the same remote ip once per day.
     *
     * @return bool
     */
    public function canLogIpNumber()
    {
        $attribute = 'ip';

        list($startTime, $endTime) = StringService::startEndDate('today');
        $startTime = strtotime($startTime);
        $endTime = strtotime($endTime);

        $where = "create_time >= {$startTime} and create_time <= {$endTime}";
        $where .= " and {$attribute} = {$this->$attribute}";
        $where .= " and recommend_id = {$this->recommend_id}";

        return !self::find()->where($where)->exists();
    }
}