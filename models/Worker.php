<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "worker".
 *
 * @property integer $id
 * @property string $uid
 * @property string $project_manager_id
 * @property string $labor_cost_id
 * @property string $nickname
 * @property string $icon
 * @property string $follower_number
 * @property double $comprehensive_score
 * @property string $create_time
 * @property string $signature
 * @property string $balance
 * @property string $pay_password
 * @property string $address
 * @property integer $status
 */
class Worker extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'worker';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'project_manager_id', 'labor_cost_id', 'follower_number', 'create_time', 'balance', 'status'], 'integer'],
            [['comprehensive_score'], 'number'],
            [['nickname'], 'string', 'max' => 25],
            [['icon'], 'string', 'max' => 255],
            [['signature', 'pay_password', 'address'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'project_manager_id' => '项目经理id',
            'labor_cost_id' => '工费地区id (包含工人类型和等级)',
            'nickname' => '工人名字',
            'icon' => '头像',
            'follower_number' => '关注人数',
            'comprehensive_score' => '综合评分',
            'create_time' => '注册时间',
            'signature' => '个性签名',
            'balance' => '余额, unit: fen',
            'pay_password' => '支付密码',
            'address' => '详细地址',
            'status' => '接单状态: 1,接单 0,不接单',
        ];
    }

    /**
     * Get total number of workers
     *
     * @return int
     */
    public static function totalNumber()
    {
        return (int)self::find()->count();
    }
}
