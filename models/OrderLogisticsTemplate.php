<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_logistics_template".
 *
 * @property integer $id
 * @property string $order_no
 * @property string $sku
 * @property integer $supplier_id
 * @property string $name
 * @property integer $delivery_method
 * @property string $delivery_cost_default
 * @property integer $delivery_number_default
 * @property string $delivery_cost_delta
 * @property integer $delivery_number_delta
 * @property integer $status
 */
class OrderLogisticsTemplate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_logistics_template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_no', 'sku'], 'required'],
            [['delivery_method', 'delivery_cost_default', 'delivery_number_default', 'delivery_cost_delta', 'delivery_number_delta'], 'integer'],
            [['order_no', 'sku', 'name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_no' => 'Order No',
            'sku' => 'Sku',
            'supplier_id' => 'Supplier ID',
            'name' => 'Name',
            'delivery_method' => 'Delivery Method',
            'delivery_cost_default' => 'Delivery Cost Default',
            'delivery_number_default' => 'Delivery Number Default',
            'delivery_cost_delta' => 'Delivery Cost Delta',
            'delivery_number_delta' => 'Delivery Number Delta',
            'status' => 'Status',
        ];
    }


    /**
     * @param $logistics_template_id
     * @param $order_no
     * @param $sku
     * @return bool|int
     */
    public  static  function  AddNewData($logistics_template_id,$order_no,$sku)
    {
        $tran = Yii::$app->db->beginTransaction();
        try {
            $LogisticTemp=LogisticsTemplate::find()
                ->where(['id'=>$logistics_template_id])
                ->asArray()
                ->one();
            if ($LogisticTemp)
            {
                $orderLogisticTemp=new  OrderLogisticsTemplate();
                $orderLogisticTemp->order_no=$order_no;
                $orderLogisticTemp->sku=$sku;
                $orderLogisticTemp->name=$LogisticTemp['name'];
                $orderLogisticTemp->delivery_method=$LogisticTemp['delivery_method'];
                $orderLogisticTemp->delivery_cost_default=$LogisticTemp['delivery_cost_default'];
                $orderLogisticTemp->delivery_number_default=$LogisticTemp['delivery_number_default'];
                $orderLogisticTemp->delivery_cost_delta=$LogisticTemp['delivery_cost_delta'];
                $orderLogisticTemp->delivery_number_delta=$LogisticTemp['delivery_number_delta'];
                if (!$orderLogisticTemp->save(false))
                {
                    $tran->rollBack();
                    return false;
                }
                $LogisticDis=LogisticsDistrict::find()
                    ->where(['template_id'=>$logistics_template_id])
                    ->all();
                if ($LogisticDis)
                {
                    foreach ($LogisticDis as  &$dis)
                    {
                        $OrderLogisticDis=new OrderLogisticsDistrict();
                        $OrderLogisticDis->order_template_id=$orderLogisticTemp->id;
                        $OrderLogisticDis->district_code=$dis->district_code;
                        $OrderLogisticDis->district_name=$dis->district_name;
                        if (!$OrderLogisticDis->save(false))
                        {
                            $tran->rollBack();
                            return false;
                        }
                    }
                }
            }
            $tran->commit();
            return 200;
        }catch (\Exception $e) {
            $tran->rollBack();
            return 500;
        }
    }
}
