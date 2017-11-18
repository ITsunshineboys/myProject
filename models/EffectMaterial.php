<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "effect_material".
 *
 * @property string $id
 * @property string $effect_id
 * @property integer $goods_id
 * @property string $price
 * @property integer $count
 */
class EffectMaterial extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'effect_material';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['effect_id'], 'required'],
            [['effect_id', 'goods_id', 'count'], 'integer'],
        ];
    }



}
