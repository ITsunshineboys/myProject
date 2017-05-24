<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use yii\db\ActiveRecord;

class GoodsBrand extends ActiveRecord
{
    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'goods_brand';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'certificate', 'logo', 'category_id'], 'required'],
            [['name'], 'unique', 'on' => self::SCENARIO_ADD],
            [['title'], 'validateTitle', 'on' => self::SCENARIO_EDIT],
            [['pid', 'approve_time', 'review_status', 'supplier_id'], 'number', 'integerOnly' => true, 'min' => 0],
            ['pid', 'validatePid'],
            [['reason', 'description', 'icon'], 'string'],
            ['description', 'safe'],
            ['description', 'default', 'value' => ''],
            ['review_status', 'in', 'range' => array_keys(Yii::$app->params['reviewStatuses'])],
            ['review_status', 'validateReviewStatus', 'on' => self::SCENARIO_REVIEW],
            ['supplier_id', 'validateSupplierId', 'on' => self::SCENARIO_REVIEW],
            ['approve_time', 'validateApproveTime', 'on' => self::SCENARIO_REVIEW],
        ];
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
        if(empty($brandIds))
        {
            return [];
        }else{
            foreach ($brandIds as $brandId){
                $id [] = $brandId['brand_id'];
            }
        }
        return self::find()->where(['in','id',$id])->all();
    }
}