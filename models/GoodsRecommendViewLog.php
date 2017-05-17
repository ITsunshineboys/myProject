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

class GoodsRecommendViewLog extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_recommend_view_log';
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
            ['ip', 'number', 'integerOnly' => true],
            ['ip', 'validateIp', 'skipOnEmpty' => false]
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
     * Validates ip
     *
     * @param string $attribute ip to validate
     * @return bool
     */
    public function validateIp($attribute)
    {
        list($startTime, $endTime) = StringService::startEndDate('today');
        $startTime = strtotime($startTime);
        $endTime = strtotime($endTime);

        $where = "create_time >= {$startTime} and create_time <= {$endTime}";
        $where .= " and {$attribute} = {$this->$attribute}";
        $where .= " and recommend_id = {$this->recommend_id}";

        if (self::find()->where($where)->exists()) {
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
            }
            return true;
        } else {
            return false;
        }
    }
}