<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class GoodsBrand extends ActiveRecord
{
    const SCENARIO_ADD = 'add';
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_brand';
    }

    /**
     * Get brands by brand name
     *
     * @param string $brandName brand name
     * @return array
     */
    public static function findByName($brandName, $select = [])
    {
        if (!$brandName) {
            return [];
        }

        $where = "name like '%{$brandName}%'";
        return self::find()->select($select)->where($where)->all();
    }

    /**
     * @param array $brandIds
     * @return array|ActiveRecord[]
     */
    public static function findByIds($brandIds = [])
    {
        if (empty($brandIds)) {
            return [];
        } else {
            foreach ($brandIds as $brandId) {
                $id [] = $brandId['brand_id'];
            }
        }
        return self::find()->where(['in', 'id', $id])->all();
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'certificate', 'logo'], 'required'],
            [['name'], 'unique', 'on' => self::SCENARIO_ADD],
        ];
    }

    /**
     * Do some ops before insertion
     *
     * @param bool $insert if is a new record
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $now = time();

            if ($insert) {
                $this->create_time = $now;
                $this->status = self::STATUS_OFFLINE;
                $this->offline_time = $now;

                $user = Yii::$app->user->identity;
                if (!$user) {
                    return false;
                }

                if ($user->login_role_id == Yii::$app->params['supplierRoleId']) {
                    $supplier = Supplier::find()->where(['uid' => $user->id])->one();
                    if (!$supplier) {
                        return false;
                    }

                    $this->supplier_id = $supplier->id;
                    $this->supplier_name = $supplier->nickname;
                } elseif ($user->login_role_id == Yii::$app->params['lhzzRoleId']) {
                    $this->deleted = self::STATUS_ONLINE;
                    $this->offline_time = $now;

                    $lhzz = Lhzz::find()->where(['uid' => $user->id])->one();
                    if (!$lhzz) {
                        return false;
                    }

                    $this->user_id = $lhzz->id;
                    $this->user_name = $lhzz->nickname;
                    $this->review_status = self::REVIEW_STATUS_APPROVE;
                    $this->approve_time = $now;
                }
            }

            return true;
        } else {
            return false;
        }
    }
}