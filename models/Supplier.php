<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use app\services\ModelService;
use app\services\StringService;
use yii\db\ActiveRecord;

class Supplier extends ActiveRecord
{
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;
    const SCENARIO_ADD = 'add';
    const TYPE_ORG = [
        '个体工商户',
        '企业',
    ];
    const TYPE_SHOP = [
        '旗舰店',
        '自营店',
        '专营店',
        '专卖店',
    ];
    const STATUSES = [
        self::STATUS_OFFLINE => '已关闭',
        self::STATUS_ONLINE => '正常营业',
    ];
    const FIELDS_VIEW_ADMIN_MODEL = [
        'id',
        'name',
        'shop_no',
        'create_time',
        'status',
        'icon',
        'comprehensive_score',
        'store_service_score',
        'logistics_speed_score',
        'delivery_service_score',
        'follower_number',
    ];
    const FIELDS_VIEW_ADMIN_EXTRA = [
        'mobile',
    ];

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'supplier';
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['type_org', 'category_id', 'type_shop', 'nickname', 'name', 'licence', 'licence_image', 'legal_person', 'identity_card_no', 'identity_card_front_image', 'identity_card_back_image'], 'required'],
            [['nickname', 'name', 'licence'], 'unique', 'on' => self::SCENARIO_ADD],
            ['category_id', 'validateCategoryId'],
            ['type_org', 'in', 'range' => array_keys(self::TYPE_ORG)],
            ['type_shop', 'in', 'range' => array_keys(self::TYPE_SHOP)],
            ['identity_card_no', 'validateIdentityCardNo'],
            ['status', 'in', 'range' => [self::STATUS_ONLINE, self::STATUS_OFFLINE]],
            [['type_org', 'category_id', 'type_shop',], 'number', 'integerOnly' => true],
            [['nickname', 'name', 'licence', 'licence_image', 'legal_person', 'identity_card_no', 'identity_card_front_image', 'identity_card_back_image', 'approve_reason', 'reject_reason'], 'string'],
        ];
    }

    /**
     * Validates category_id
     *
     * @param string $attribute category_id to validate
     * @return bool
     */
    public function validateCategoryId($attribute)
    {
        $where = [
            'id' => $this->$attribute,
            'deleted' => GoodsCategory::STATUS_OFFLINE,
            'level' => GoodsCategory::LEVEL3
        ];

        if ($this->$attribute > 0
            && GoodsCategory::find()->where($where)->exists()
        ) {
            return true;
        }

        $this->addError($attribute);
        return false;
    }

    /**
     * Validates identity card no
     *
     * @param string $attribute licence to validate
     * @return bool
     */
    public function validateIdentityCardNo($attribute)
    {
        if (!StringService::checkIdentityCardNo($this->$attribute)) {
            $this->addError($attribute);
            return false;
        }

        return true;
    }

    /**
     * Get admin view data
     *
     * @return array
     */
    public function viewAdmin()
    {
        $modelData = ModelService::selectModelFields($this, self::FIELDS_VIEW_ADMIN_MODEL);
        $viewData = $modelData
            ? array_merge($modelData, $this->_extraData(self::FIELDS_VIEW_ADMIN_EXTRA))
            : $modelData;
        $this->_formatData($viewData);
        return $viewData;
    }

    /**
     * Get extra fields
     *
     * @access private
     * @param array $extraFields extra fields
     * @return array
     */
    private function _extraData(array $extraFields)
    {
        $extraData = [];

        foreach ($extraFields as $extraField) {
            switch ($extraField) {
                case 'mobile':
                    $user = User::findOne($this->uid);
                    $extraData[$extraField] = $user->$extraField;
                    break;
            }

        }

        return $extraData;
    }

    /**
     * Format data
     *
     * @param array $data data to format
     */
    private function _formatData(array &$data)
    {
        if (isset($data['create_time'])) {
            $data['create_time'] = date('Y-m-d', $data['create_time']);
        }

        if (isset($data['status'])) {
            $data['status'] = self::STATUSES[$data['status']];
        }
    }
}