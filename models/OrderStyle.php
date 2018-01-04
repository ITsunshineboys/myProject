<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_style".
 *
 * @property string $id
 * @property string $order_no
 * @property string $sku
 * @property string $style
 * @property string $intro
 * @property string $theme
 * @property string $images
 */
class OrderStyle extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_style';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_no', 'sku'], 'required'],
            [['order_no', 'sku', 'style', 'theme'], 'string', 'max' => 50],
            [['intro', 'images'], 'string', 'max' => 255],
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
            'style' => 'Style',
            'intro' => 'Intro',
            'theme' => 'Theme',
            'images' => 'Images',
        ];
    }

    /**
     * @param $style_id
     * @param $order_no
     * @param $sku
     * @return int
     */
    public  static  function  AddNewData($style_id,$order_no,$sku)
    {
        $tran = Yii::$app->db->beginTransaction();
        try {
            $style=Style::findOne($style_id);
            if ($style)
            {
                $OrderStyle=new self();
                $OrderStyle->order_no=$order_no;
                $OrderStyle->sku=$sku;
                $OrderStyle->style=$style->style;
                $OrderStyle->intro=$style->intro;
                $OrderStyle->theme=$style->theme;
                $OrderStyle->images=$style->images;
                $res1=$OrderStyle->save(false);
                if (!$res1)
                {
                    $tran->rollBack();
                    return 500;
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
