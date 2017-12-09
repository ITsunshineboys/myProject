<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "line_supplier_goods".
 *
 * @property integer $id
 * @property integer $line_supllier_id
 * @property integer $goods_id
 * @property integer $create_time
 * @property integer $status
 */
class LineSupplierGoods extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'line_supplier_goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['line_supllier_id', 'goods_id', 'create_time'], 'required'],
            [['line_supllier_id', 'goods_id', 'create_time', 'status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'line_supllier_id' => 'Line Supllier ID',
            'goods_id' => 'Goods ID',
            'create_time' => 'Create Time',
            'status' => 'Status',
        ];
    }
}
