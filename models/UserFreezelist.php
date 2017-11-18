<?php

namespace app\models;

use app\controllers\SupplieraccountController;
use app\services\ModelService;
use Yii;

/**
 * This is the model class for table "user_freezelist".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $role_id
 * @property string $freeze_money
 * @property integer $create_time
 * @property string $freeze_reason
 */
class UserFreezelist extends \yii\db\ActiveRecord
{
    const PAGE_SIZE_DEFAULT=12;
    const FIELDS_EXTRA=[];
    const FIELDS_ADMIN = [
        'id',
        'uid',
        'role_id',
        'create_time',
        'freeze_money',
        'freeze_reason'

    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_freezelist';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'role_id', 'create_time', 'freeze_reason'], 'required'],
            [['uid', 'role_id', 'freeze_money', 'create_time'], 'integer'],
            [['freeze_reason'], 'string', 'max' => 255],
        ];
    }

    public static function pagination($uid,$where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC')
    {
        $select = array_diff($select, self::FIELDS_EXTRA);
        $andwhere=['uid'=>$uid];
        $offset = ($page - 1) * $size;
        $freezeList = self::find()
            ->select($select)
            ->where($where)
            ->andWhere($andwhere)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();
        foreach ($freezeList as &$freeze) {
            $freeze['create_time']=date('Y-m-d H:i',$freeze['create_time']);
            $freeze['freeze_money']=sprintf('%.2f',(float)$freeze['freeze_money']*0.01);
        }
        $total=self::find()->where($where)->andWhere($andwhere)->count();;
        return ModelService::pageDeal($freezeList, $total, $page, $size);

    }

        /**
     * @param $uid
     * @param $role_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static  function  findByUidAndRoleId($uid,$role_id)
    {
        $list=self::find()
            ->where(['uid'=>$uid])
            ->andWhere(['role_id'=>$role_id])
            ->all();
        return $list?$list:[];
    }

}
