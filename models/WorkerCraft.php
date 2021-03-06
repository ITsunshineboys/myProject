<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "worker_craft".
 *
 * @property integer $id
 * @property integer $item_id
 * @property string $craft
 */
class WorkerCraft extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker_craft';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id'], 'integer'],
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
            'item_id' => '工种条目id',
            'craft' => '工艺',
        ];
    }
    /**
     * 获取工艺名称
     * @param $id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getcraftitle($id){
        return self::find()
            ->asArray()
            ->select('id,craft,item_id')
            ->where(['id'=>$id])
            ->one();

    }
}
