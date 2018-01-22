<?php

namespace app\models;

use Yii;

/**
 * * This is the model class for table "bankinfo_log"
 * @property integer $id
 * @property string $bankname
 * @property integer $bankcard
 * @property string $username
 * @property string $position
 * @property string $bankbranch
 * @property integer $create_time
 * @property integer $modify_time
 */
class BankinfoLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bankinfo_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bankname', 'bankcard', 'username', 'position', 'bankbranch', 'create_time'], 'required'],
            [['bankcard', 'create_time'], 'integer'],
            [['bankname', 'username'], 'string', 'max' => 50],
            [['position', 'bankbranch'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bankname' => '开户银行',
            'bankcard' => '银行卡号',
            'username' => '开户名',
            'position' => '开户行所在地',
            'bankbranch' => '开户行支行名',
            'create_time' => '创建时间',
        ];
    }
}
