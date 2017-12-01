<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_logistics_district".
 *
 * @property integer $id
 * @property integer $order_template_id
 * @property string $district_code
 * @property string $district_name
 */
class OrderLogisticsDistrict extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_logistics_district';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_template_id', 'district_code'], 'integer'],
            [['district_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_template_id' => 'Order Template ID',
            'district_code' => 'District Code',
            'district_name' => 'District Name',
        ];
    }
}
