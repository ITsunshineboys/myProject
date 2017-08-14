<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "supplier_freezelist".
 *
 * @property integer $id
 * @property string $freeze_money
 * @property integer $supplier_id
 * @property integer $create_time
 * @property string $freeze_reason
 */
class SupplierFreezelist extends \yii\db\ActiveRecord
{
   const PAGE_SIZE_DEFAULT=10;
   const FIELDS_EXTRA=[];
    const FIELDS_ADMIN = [
        'create_time',
        'freeze_money',
        'freeze_reason'

    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'supplier_freezelist';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['freeze_money', 'supplier_id', 'create_time', 'freeze_reason'], 'required'],
            [['freeze_money', 'supplier_id', 'create_time'], 'integer'],
            [['freeze_reason'], 'string', 'max' => 255],
        ];
    }




    public static function pagination($where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC')
    {
        $select = array_diff($select, self::FIELDS_EXTRA);

        $offset = ($page - 1) * $size;
        $freezeList = self::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();

        foreach ($freezeList as &$freeze) {
            $freeze['create_time']=date('Y-m-d H:i',$freeze['create_time']);
            $freeze['freeze_money']=sprintf('%.2f',(float)$freeze['freeze_money']*0.01);
        }

        $data=[

            'details' => $freezeList
        ];
        $count=(int)self::find()->where($where)->asArray()->count();
        $total_page = ceil($count / $size);

        $data['details']['size']=$size;
        $data['details']['total_page']=$total_page;
        $data['details']['total']=$count;
        return $data;

    }
//
}
