<?php
/**
 * Created by PhpStorm.
 * User: hj
 * Date: 5/5/17
 * Time: 2:25 PM
 */

namespace app\models;

use app\services\ModelService;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class Supplier extends ActiveRecord
{
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;
    const STATUS_WAIT_REVIEW = 2;
    const STATUS_NOT_APPROVED = 3;
    const STATUS_APPROVED = 4;
    const STATUS_DESC_ONLINE_APP = '审核通过';
    const STATUS_DESC_ONLINE_ADMIN = '正常营业';
    const STATUS_DESC_OFFLINE = '已关闭';
    const STATUS_DESC_WAIT_REVIEW = '等待审核';
    const STATUS_DESC_NOT_APPROVED = '审核未通过';
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
        self::STATUS_OFFLINE => self::STATUS_DESC_OFFLINE,
        self::STATUS_ONLINE => self::STATUS_DESC_ONLINE_ADMIN,
        self::STATUS_WAIT_REVIEW => self::STATUS_DESC_WAIT_REVIEW,
        self::STATUS_NOT_APPROVED => self::STATUS_DESC_NOT_APPROVED,
        self::STATUS_APPROVED => self::STATUS_DESC_ONLINE_APP,
    ];
    const STATUSES_ONLINE_OFFLINE = [
        self::STATUS_OFFLINE => self::STATUS_DESC_OFFLINE,
        self::STATUS_ONLINE => self::STATUS_DESC_ONLINE_ADMIN,
    ];
    const FIELDS_VIEW_ADMIN_MODEL = [
        'id',
        'type_org',
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
        'quality_guarantee_deposit',
        'licence',
        'licence_image',
    ];
    const FIELDS_VIEW_ADMIN_EXTRA = [
        'mobile',
        'legal_person',
        'identity_no',
        'identity_card_front_image',
        'identity_card_back_image',
    ];
    const FIELDS_VIEW_APP_MODEL = [
        'status',
        'type_org',
        'shop_name',
        'category_id',
        'type_shop',
        'name',
        'licence',
        'licence_image',
    ];
    const FIELDS_VIEW_MALL_MODEL = [
        'icon',
        'shop_name',
        'follower_number',
        'create_time',
        'comprehensive_score',
        'store_service_score',
        'logistics_speed_score',
        'delivery_service_score',
        'quality_guarantee_deposit',
    ];
    const FIELDS_VIEW_APP_EXTRA = [
        'legal_person',
        'identity_no',
        'identity_card_front_image',
        'identity_card_back_image',
    ];
    const FIELDS_VIEW_MALL_EXTRA = [
    ];
    const FIELDS_SHOP_INDEX_MODEL = [
        'icon',
        'shop_name',
        'follower_number',
    ];
    const OFFLINE_SHOP_SUPPORT = 1; // 支持线下商店
    const OFFLINE_SHOP_NOT_SUPPORT = 0; // 不支持线下商店
    const FIELDS_LIST = [
        'id',
        'type_shop',
        'shop_name',
        'shop_no',
        'category_id',
        'status',
    ];

    /**
     * Get delta number
     *
     * @param int $startTime start time
     * @param int $endTime end time
     * @param int $status status default 1(online)
     * @return int
     */
    public static function deltaNumber($startTime, $endTime, $status = self::STATUS_ONLINE)
    {
        return (int)Supplier::find()
            ->where(['status' => $status])
            ->andWhere(['>=', 'create_time', $startTime])
            ->andWhere(['<=', 'create_time', $endTime])
            ->count();
    }

    /**
     * Add supplier and have it certificated if necessary
     *
     * @param ActiveRecord $user user
     * @return int
     */
    public static function add(ActiveRecord $user, array $attrs)
    {
        $supplier = new self;
        $supplier->type_org = isset($attrs['type_org']) ? (int)$attrs['type_org'] : 0;
        $supplier->category_id = isset($attrs['category_id']) ? (int)$attrs['category_id'] : 0;
        $supplier->type_shop = isset($attrs['type_shop']) ? (int)$attrs['type_shop'] : 0;
        $supplier->name = isset($attrs['name']) ? trim($attrs['name']) : '';
        $supplier->licence = isset($attrs['licence']) ? trim($attrs['licence']) : '';
        $supplier->licence_image = isset($attrs['licence_image']) ? trim($attrs['licence_image']) : '';
        $supplier->uid = $user->id;
        $supplier->create_time = time();
        $supplier->status = isset($attrs['status']) ? (int)$attrs['status'] : self::STATUS_WAIT_REVIEW;
        $supplier->shop_name = isset($attrs['shop_name']) ? trim($attrs['shop_name']) : '';
        $supplier->support_offline_shop = isset($attrs['support_offline_shop'])
            ? (int)$attrs['support_offline_shop']
            : self::OFFLINE_SHOP_NOT_SUPPORT;

        $supplier->scenario = self::SCENARIO_ADD;
        if (!$supplier->validate()) {
            $code = 1000;
            return $code;
        }

        $transaction = Yii::$app->db->beginTransaction();

        if (!$supplier->save()) {
            $transaction->rollBack();

            $code = 500;
            return $code;
        }

        $supplier->shop_no = Yii::$app->params['supplierRoleId']
            . (Yii::$app->params['offsetGeneral'] + $supplier->id);

        if (!$supplier->save()) {
            $transaction->rollBack();

            $code = 500;
            return $code;
        }

        $userRole = new UserRole;
        $userRole->user_id = $user->id;
        $userRole->role_id = Yii::$app->params['supplierRoleId'];
        if (!$userRole->save()) {
            $transaction->rollBack();

            $code = 500;
            return $code;
        }

        $user->refresh();
        if (empty($user->identity_no)) {
            $user->legal_person = isset($attrs['legal_person']) ? trim($attrs['legal_person']) : '';
            $user->identity_no = isset($attrs['identity_no']) ? trim($attrs['identity_no']) : '';
            $user->identity_card_front_image = isset($attrs['identity_card_front_image'])
                ? trim($attrs['identity_card_front_image'])
                : '';
            $user->identity_card_back_image = isset($attrs['identity_card_back_image'])
                ? trim($attrs['identity_card_back_image'])
                : '';

            if (!$user->validateIdentity()) {
                $transaction->rollBack();

                $code = 1000;
                return $code;
            }

            if (!$user->save()) {
                $transaction->rollBack();

                $code = 500;
                return $code;
            }
        }

        $transaction->commit();

        $code = 200;
        return $code;
    }

    /**
     * Get total number of suppliers
     *
     * @return int
     */
    public static function totalNumber()
    {
        return (int)self::find()->count();
    }

    /**
     * Check shop type
     *
     * @param $shopType shop type
     * @return bool
     */
    public static function checkShopType($shopType)
    {
        return in_array($shopType,
            array_merge(array_keys(Supplier::TYPE_SHOP), [Yii::$app->params['value_all']]));
    }

    /**
     * Check status
     *
     * @param $status status
     * @return bool
     */
    public static function checkStatus($status)
    {
        return in_array($status,
            array_merge(array_keys(self::STATUSES_ONLINE_OFFLINE), [Yii::$app->params['value_all']]));
    }

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'supplier';
    }

    /**
     * Format data
     *
     * @param array $data data to format
     */
    public static function formatData(array &$data)
    {
        if (isset($data['create_time'])) {
            $data['create_time'] = date('Y-m-d', $data['create_time']);
        }

        if (isset($data['status'])) {
            $data['status'] = self::STATUSES[$data['status']];
        }

        if (isset($data['quality_guarantee_deposit'])) {
            $data['quality_guarantee_deposit'] /= 100;
        }

        if (isset($data['type_org'])) {
            $data['type_org'] = self::TYPE_ORG[$data['type_org']];
        }

        if (isset($data['category_id'])) {
            static $categories = [];

            if (in_array($data['category_id'], array_keys($categories))) {
                $data['category_name'] = $categories[$data['category_id']];
            } else {
                $cat = GoodsCategory::findOne($data['category_id']);
                $data['category_name'] = $cat->fullTitle();
                $categories[$data['category_id']] = $data['category_name'];
            }

            unset($data['category_id']);
        }

        if (isset($data['type_shop'])) {
            $data['type_shop'] = self::TYPE_SHOP[$data['type_shop']];
        }
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['type_org', 'category_id', 'type_shop', 'shop_name', 'name', 'licence', 'licence_image', 'shop_name'], 'required'],
            [['name', 'licence', 'shop_name'], 'unique', 'on' => self::SCENARIO_ADD],
            ['category_id', 'validateCategoryId'],
            ['type_org', 'in', 'range' => array_keys(self::TYPE_ORG)],
            ['type_shop', 'in', 'range' => array_keys(self::TYPE_SHOP)],
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
            [['type_org', 'category_id', 'type_shop', 'quality_guarantee_deposit', 'support_offline_shop'], 'number', 'integerOnly' => true],
            [['nickname', 'shop_name', 'name', 'licence', 'licence_image', 'approve_reason', 'reject_reason', 'shop_name'], 'string'],
            ['name', 'string', 'length' => [1, 30]],
            ['licence', 'string', 'length' => [1, 18]],
            ['support_offline_shop', 'in', 'range' => [self::OFFLINE_SHOP_SUPPORT, self::OFFLINE_SHOP_NOT_SUPPORT]],
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
                case 'legal_person':
                    $user = User::findOne($this->uid);
                    $extraData[$extraField] = $user->$extraField;
                    break;
                case 'identity_no':
                    $user = User::findOne($this->uid);
                    $extraData[$extraField] = $user->$extraField;
                    break;
                case 'identity_card_front_image':
                    $user = User::findOne($this->uid);
                    $extraData[$extraField] = $user->$extraField;
                    break;
                case 'identity_card_back_image':
                    $user = User::findOne($this->uid);
                    $extraData[$extraField] = $user->$extraField;
                    break;
            }

        }

        return $extraData;
    }

    /**
     * Get certification view data
     *
     * @return array
     */
    public function viewCertification()
    {
        $modelData = ModelService::selectModelFields($this, self::FIELDS_VIEW_APP_MODEL);
        $viewData = $modelData
            ? array_merge($modelData, $this->_extraData(self::FIELDS_VIEW_APP_EXTRA))
            : $modelData;
        $this->_formatData($viewData);
        return $viewData;
    }

    /**
     * Get certification view data
     *
     * @return array
     */
    public function view()
    {
        $modelData = ModelService::selectModelFields($this, self::FIELDS_VIEW_MALL_MODEL);
        $viewData = $modelData
            ? array_merge($modelData, $this->_extraData(self::FIELDS_VIEW_MALL_EXTRA))
            : $modelData;
        $this->_formatData($viewData);
        return $viewData;
    }

    /**
     * Close supplier
     *
     * @param ActiveRecord $operator operator
     * @return int
     */
    public function offline(ActiveRecord $operator)
    {
        $this->status = self::STATUS_OFFLINE;

        $tran = Yii::$app->db->beginTransaction();
        $code = 500;

        try {
            if (!$this->save()) {
                $tran->rollBack();
                return $code;
            }

            Goods::disableGoodsBySupplierId($this->id, $operator);

            $tran->commit();
            return 200;
        } catch (\Exception $e) {
            $tran->rollBack();
            return $code;
        }
    }
}