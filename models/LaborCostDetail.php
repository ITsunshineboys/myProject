<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "labor_cost_detail".
 *
 * @property integer $id
 * @property string $worker_tpye_id
 * @property string $province_code
 * @property string $city_code
 * @property string $place
 * @property string $craft
 * @property string $price
 */
class LaborCostDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'labor_cost_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['worker_tpye_id', 'price'], 'integer'],
            [['province_code', 'city_code'], 'required'],
            [['province_code', 'city_code'], 'string', 'max' => 6],
            [['place'], 'string', 'max' => 25],
            [['craft'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'worker_tpye_id' => '工种id',
            'province_code' => '省份编码',
            'city_code' => '市编码',
            'place' => '具体地点',
            'craft' => '工艺',
            'price' => '价格 (分)',
        ];
    }
}
