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
use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class Supplier extends ActiveRecord
{
    const ROLE_SUPPLIER = 6;
    const FIELDS_EXTRA = [];
    const STATUS_CASHED = 3;
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE = 1;
    const STATUS_WAIT_REVIEW = 2;
    const STATUS_NOT_APPROVED = 3;
    const STATUS_APPROVED = 4;
    const STATUS_DESC_ONLINE_APP = '审核通过';
    const STATUS_DESC_ONLINE_ADMIN = '正常营业';
    const STATUS_DESC_OFFLINE = '已闭店';
    const STATUS_DESC_WAIT_REVIEW = '等待审核';
    const STATUS_DESC_NOT_APPROVED = '审核未通过';
    const SCENARIO_ADD = 'add';
    const TYPE_ORG = [
        '个体工商户',
        '企业',
    ];
    const TYPE_SHOP = [
        0 => '旗舰店',
        1 => '专卖店',
        2 => '专营店',
        3 => '自营店',
    ];
    const TYPE_SHOP_APP = [
//        0 => '旗舰店',
        1 => '专卖店',
//        2 => '专营店',
//        3 => '自营店',
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
        'shop_name',
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
//        'support_offline_shop',
        'category_id',
        'type_shop',
    ];
    const FIELDS_VIEW_ADMIN_EXTRA = [
        'mobile',
        'legal_person',
        'identity_no',
        'identity_card_front_image',
        'identity_card_back_image',
    ];
    const FIELDS_VIEW_APP_MODEL = [
//        'status',
        'type_org',
        'shop_name',
        'category_id',
        'type_shop',
        'name',
        'licence',
        'licence_image',
        'reject_reason',
    ];
    const FIELDS_VIEW_MALL_MODEL = [
        'icon',
        'shop_no',
        'shop_name',
        'follower_number',
        'comprehensive_score',
        'store_service_score',
        'logistics_speed_score',
        'delivery_service_score',
        'quality_guarantee_deposit',
        'district_name',
        'district_code',
        'address',
    ];
    const FIELDS_VIEW_APP_EXTRA = [
        'legal_person',
        'identity_no',
        'identity_card_front_image',
        'identity_card_back_image',
        'review_status',
    ];
    const FIELDS_VIEW_MALL_EXTRA = [
        'open_shop_time',
    ];
    const FIELDS_SHOP_INDEX_MODEL = [
        'icon',
        'shop_name',
        'follower_number',
    ];
    const OFFLINE_SHOP_SUPPORT = 1; // 支持线下商店
    const OFFLINE_SHOP_NOT_SUPPORT = 0; // 不支持线下商店
    const PAGE_SIZE_DEFAULT = 12;
    const FIELDS_ADMIN = [
        'id',
        'shop_no',
        'status',
        'shop_name',
        'balance',
        'category_id',
        'create_time',
        'type_shop'
    ];
    const FIELDS_LIST = [
        'id',
        'type_shop',
        'shop_name',
        'shop_no',
        'category_id',
        'status',
        'sales_volumn_month',
        'sales_amount_month',
        'month',
    ];
    const FIELDS_LIST_EXTRA = [
//        'sales_volumn_month',
//        'sales_amount_month',
    ];
    const ERROR_CODE_SAME_SHOP_NAME = 1028;
    const ERROR_CODE_SAME_LICENCE = 1029;
    const ERROR_CODE_SAME_NAME = 1030;
    const FIELD_SALES_VOLUMN_MONTH = 'sales_volumn_month';
    const FIELD_SALES_AMOUNT_MONTH = 'sales_amount_month';

    /**
     * @return string 返回该AR类关联的数据表名
     */
    public static function tableName()
    {
        return 'supplier';
    }

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
     * Supplier applies for certification
     *
     * @param ActiveRecord $user user
     * @param array $attrs attributes to add
     * @param Supplier $sup supplier who applies for certification default null
     * @param ActiveRecord $operator operator default null
     * @return int
     */
    public static function certificationApplication(ActiveRecord $user, array $attrs, Supplier $sup = null, ActiveRecord $operator = null)
    {
        $supplier = $sup ? $sup : new self;
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
        $supplier->shop_name .= self::TYPE_SHOP[$supplier->type_shop];
//        $supplier->support_offline_shop = isset($attrs['support_offline_shop'])
//            ? (int)$attrs['support_offline_shop']
//            : self::OFFLINE_SHOP_NOT_SUPPORT;
        $supplier->support_offline_shop = self::OFFLINE_SHOP_NOT_SUPPORT;
        $supplier->icon = Yii::$app->params['user']['deault_icon_path'];
        $supplier->quality_guarantee_deposit = isset($attrs['quality_guarantee_deposit'])
            ? (int)$attrs['quality_guarantee_deposit']
            : Yii::$app->params['supplier']['quality_guarantee_deposit'];

        $supplier->scenario = self::SCENARIO_ADD;
        if (!$supplier->validate()) {
            $code = 1000;

            if (isset($supplier->errors['shop_name'])) {
                $customErrCode = ModelService::customErrCode($supplier->errors['shop_name'][0]);
                if ($customErrCode !== false) {
                    $code = $customErrCode;
                }
            } elseif (isset($supplier->errors['name'])) {
                $customErrCode = ModelService::customErrCode($supplier->errors['name'][0]);
                if ($customErrCode !== false) {
                    $code = $customErrCode;
                }
            } elseif (isset($supplier->errors['licence'])) {
                $customErrCode = ModelService::customErrCode($supplier->errors['licence'][0]);
                if ($customErrCode !== false) {
                    $code = $customErrCode;
                }
            }

            return $code;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
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

            UserRole::deleteAll(['user_id' => $user->id, 'role_id' => Yii::$app->params['supplierRoleId']]);
            $userRole = new UserRole;
            $userRole->user_id = $user->id;
            $userRole->role_id = Yii::$app->params['supplierRoleId'];
            $userRole->review_apply_time = time();
            $userRole->review_status = Role::AUTHENTICATION_STATUS_IN_PROCESS;
            if ($operator) {
                $userRole->review_time = time();
                $userRole->review_status = Role::AUTHENTICATION_STATUS_APPROVED;
                $userRole->reviewer_uid = $operator->id;

                $supplier->status = self::STATUS_OFFLINE;
                if (!$supplier->save()) {
                    $transaction->rollBack();

                    $code = 500;
                    return $code;
                }
            }
            if (!$userRole->save()) {
                $transaction->rollBack();

                $code = 500;
                return $code;
            }

            $user->refresh();
            if (empty($user->identity_no)) {
                $user->legal_person = isset($attrs['legal_person']) ? trim($attrs['legal_person']) : '';
                $user->identity_no = isset($attrs['identity_card_no']) ? trim($attrs['identity_card_no']) : '';
                $user->identity_card_front_image = isset($attrs['identity_card_front_image'])
                    ? trim($attrs['identity_card_front_image'])
                    : '';
                $user->identity_card_back_image = isset($attrs['identity_card_back_image'])
                    ? trim($attrs['identity_card_back_image'])
                    : '';

                if (!$user->validateIdentity($operator)) {
                    $transaction->rollBack();

                    $code = 1000;
                    return $code;
                }

                if (User::checkIdentityExisting($user->identity_no)) {
                    $transaction->rollBack();

                    $code = 1038;
                    return $code;
                }

                if (!$user->save()) {
                    $transaction->rollBack();

                    $code = 500;
                    return $code;
                }

                $userRole = UserRole::find()->where(['user_id' => $user->id, 'role_id' => Yii::$app->params['ownerRoleId']])->one();
                if (!$userRole) {
                    $transaction->rollBack();

                    $code = 500;
                    return $code;
                }

                $userRole->review_time = time();
                $userRole->review_status = Role::AUTHENTICATION_STATUS_APPROVED;
                if ($operator) {
                    $userRole->reviewer_uid = $operator->id;
                }
                if (!$userRole->save()) {
                    $transaction->rollBack();

                    $code = 500;
                    return $code;
                }
            }

            Yii::$app->cache->delete(UserRole::CACHE_KEY_PREFIX_ROLES_STATUS . $user->id);

            $transaction->commit();
            $code = 200;
            return $code;
        } catch (\Exception $e) {
            $transaction->rollBack();

            $code = 500;
            return $code;
        }
    }

    /**
     * Add supplier and have it certificated if necessary
     *
     * @param ActiveRecord $user user
     * @param array $attrs attributes to add
     * @param ActiveRecord $operator operator default null
     * @return int
     */
    public static function add(ActiveRecord $user, array $attrs, ActiveRecord $operator = null)
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
        $supplier->shop_name .= self::TYPE_SHOP[$supplier->type_shop];
//        $supplier->support_offline_shop = isset($attrs['support_offline_shop'])
//            ? (int)$attrs['support_offline_shop']
//            : self::OFFLINE_SHOP_NOT_SUPPORT;
        $supplier->support_offline_shop = self::OFFLINE_SHOP_NOT_SUPPORT;
        $supplier->icon = Yii::$app->params['user']['deault_icon_path'];
        $supplier->quality_guarantee_deposit = isset($attrs['quality_guarantee_deposit'])
            ? (int)$attrs['quality_guarantee_deposit']
            : Yii::$app->params['supplier']['quality_guarantee_deposit'];

        $supplier->scenario = self::SCENARIO_ADD;
        if (!$supplier->validate()) {
            $code = 1000;
            if (isset($supplier->errors['shop_name'])) {
                $customErrCode = ModelService::customErrCode($supplier->errors['shop_name'][0]);
                if ($customErrCode !== false) {
                    $code = $customErrCode;
                }
            } elseif (isset($supplier->errors['name'])) {
                $customErrCode = ModelService::customErrCode($supplier->errors['name'][0]);
                if ($customErrCode !== false) {
                    $code = $customErrCode;
                }
            } elseif (isset($supplier->errors['licence'])) {
                $customErrCode = ModelService::customErrCode($supplier->errors['licence'][0]);
                if ($customErrCode !== false) {
                    $code = $customErrCode;
                }
            }

            return $code;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
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

            UserRole::deleteAll(['user_id' => $user->id, 'role_id' => Yii::$app->params['supplierRoleId']]);
            $userRole = new UserRole;
            $userRole->user_id = $user->id;
            $userRole->role_id = Yii::$app->params['supplierRoleId'];
            $userRole->review_apply_time = time();
            $userRole->review_status = Role::AUTHENTICATION_STATUS_IN_PROCESS;
            if ($operator) {
                $userRole->review_time = time();
                $userRole->review_status = Role::AUTHENTICATION_STATUS_APPROVED;
                $userRole->reviewer_uid = $operator->id;

                $supplier->status = self::STATUS_OFFLINE;
                if (!$supplier->save()) {
                    $transaction->rollBack();

                    $code = 500;
                    return $code;
                }
            }
            if (!$userRole->save()) {
                $transaction->rollBack();

                $code = 500;
                return $code;
            }

            $user->refresh();
            if (empty($user->identity_no)) {
                $user->legal_person = isset($attrs['legal_person']) ? trim($attrs['legal_person']) : '';
                $user->identity_no = isset($attrs['identity_card_no']) ? trim($attrs['identity_card_no']) : '';
                $user->identity_card_front_image = isset($attrs['identity_card_front_image'])
                    ? trim($attrs['identity_card_front_image'])
                    : '';
                $user->identity_card_back_image = isset($attrs['identity_card_back_image'])
                    ? trim($attrs['identity_card_back_image'])
                    : '';

                if (!$user->validateIdentity($operator)) {
                    $transaction->rollBack();

                    $code = 1000;
                    return $code;
                }

                if (User::checkIdentityExisting($user->identity_no)) {
                    $transaction->rollBack();

                    $code = 1038;
                    return $code;
                }

                if (!$user->save()) {
                    $transaction->rollBack();

                    $code = 500;
                    return $code;
                }

                $userRole = UserRole::find()->where(['user_id' => $user->id, 'role_id' => Yii::$app->params['ownerRoleId']])->one();
                if (!$userRole) {
                    $transaction->rollBack();

                    $code = 500;
                    return $code;
                }

                $userRole->review_time = time();
                $userRole->review_status = Role::AUTHENTICATION_STATUS_APPROVED;
                if ($operator) {
                    $userRole->reviewer_uid = $operator->id;
                }
                if (!$userRole->save()) {
                    $transaction->rollBack();

                    $code = 500;
                    return $code;
                }
            }

            Yii::$app->cache->delete(UserRole::CACHE_KEY_PREFIX_ROLES_STATUS . $user->id);

            $transaction->commit();
            $code = 200;
            return $code;
        } catch (\Exception $e) {
            $transaction->rollBack();

            $code = 500;
            return $code;
        }
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
            $data['status'] = self::STATUSES_ONLINE_OFFLINE[$data['status']];
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

        $ym = date('Ym');
        if (isset($data['sales_amount_month'])) {
            if ($ym != $data['month']) {
                $data['sales_amount_month'] = 0;
            }
        }

        if (isset($data['sales_volumn_month'])) {
            if ($ym != $data['month']) {
                $data['sales_volumn_month'] = 0;
            }
        }
    }

    public static function extraData($id, array $extraFields)
    {
        $extraData = [];

        foreach ($extraFields as $extraField) {
            switch ($extraField) {
                case 'sales_volumn_month':
                    $extraData[$extraField] = GoodsOrder::supplierSalesVolumn($id, 'month');
                    break;
                case 'sales_amount_month':
                    $extraData[$extraField] = GoodsOrder::supplierSalesAmount($id, 'month');
                    break;
            }

        }

        return $extraData;

    }

    /**
     * 账户列表查询分页
     * @return array
     * */

    public static function pagination($where = [], $select = [], $page = 1, $size = self::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC')
    {

        $select = array_diff($select, self::FIELDS_EXTRA);
        $keys = implode(',', array_keys(Supplier::STATUSES_ONLINE_OFFLINE));
        $andwhere = "  status in ({$keys})";
        $offset = ($page - 1) * $size;
        $supplierList = self::find()
            ->select($select)
            ->where($where)
            ->andWhere($andwhere)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();

        foreach ($supplierList as &$supplier) {

            if (isset($supplier['create_time'])) {
                $supplier['create_time'] = date('Y-m-d H:i', $supplier['create_time']);
            }
            if (isset($supplier['balance'])) {
                $supplier['balance'] = sprintf('%.2f', (float)$supplier['balance'] * 0.01);
            }

            if (isset($supplier['status'])) {
                $supplier['status'] = self::STATUSES_ONLINE_OFFLINE[$supplier['status']];
            }

            if (isset($supplier['category_id'])) {
                $cat = GoodsCategory::findOne($supplier['category_id']);
                $supplier['category_name'] = $cat->fullTitle();
                unset($supplier['category_id']);
            }

            if (isset($supplier['type_shop'])) {
                $supplier['type_shop'] = self::TYPE_SHOP[$supplier['type_shop']];
            }
        }
        $total = self::find()->where($where)->andWhere($andwhere)->count();
        return ModelService::pageDeal($supplierList, $total, $page, $size);
    }

    /**
     * supplier view
     * @param $supplier_id
     * @param $uid
     * @return array|bool|null
     */
    public static function getsupplierdata($supplier_id, $uid)
    {

        $Supplier_info=Supplier::find()
            ->select('shop_name,id,balance,availableamount')
            ->where(['id'=>$supplier_id])
            ->asArray()
            ->one();
        $data=[];
        if($Supplier_info){
            $Supplier_info['cashwithdrawal_money'] = sprintf('%.2f', (float)$Supplier_info['availableamount'] * 0.01);
            $Supplier_info['balance'] = sprintf('%.2f', (float)$Supplier_info['balance'] * 0.01);
            unset($Supplier_info['availableamount']);
        }
        $array=(new Query())
            ->select('sb.bankname,sb.bankcard,sb.username,sb.position,sb.bankbranch')
            ->from('user_bankinfo as ub')
            ->leftJoin('bankinfo_log as sb', 'sb.id=ub.log_id')
            ->where(['ub.uid'=>$uid,'ub.role_id'=>self::ROLE_SUPPLIER])
            ->one();
        if(!$array){
            $array=[];
        }
        $freeze_money = (new Query())->from('user_freezelist')->where(['uid' => $uid])->andWhere(['role_id' => self::ROLE_SUPPLIER])->andWhere(['status' => 0])->sum('freeze_money');
        $cashed_money = (new Query())->from('user_cashregister')->where(['uid' => $uid])->andWhere(['role_id' => self::ROLE_SUPPLIER])->andWhere(['status' => 2])->sum('cash_money');
        $data['freeze_money'] = sprintf('%.2f', (float)$freeze_money * 0.01);
        $data['cashed_money'] = sprintf('%.2f', (float)$cashed_money * 0.01);
        $data= array_merge($array,$data,$Supplier_info);

        return $data;

    }

    /**
     * get category by pid
     * @param $pid
     * @return array
     */
    public static function getcategory($pid)
    {
        $cate = GoodsCategory::findOne($pid);
        $children = $cate->children;
        if ($children) {
            $child_id = [];
            foreach ($children as $child) {
                $category = $child->children;

                if ($category) {
                    foreach ($category as $cate) {
                        $child_id[] = $cate->id;
                    }
                }
                $child_id[] = $child->id;
            }
            return $child_id;
        } else {

            return $pid;
        }
    }

    /**
     * Get supplier statistics during some time
     *
     * @param int $supplierId supplier id
     * @param string $timeType timte type default today
     * @return array
     */
    public static function statData($supplierId, $timeType = 'today')
    {
        list($startTime, $endTime) = StringService::startEndDate($timeType);

        $intStartTime = strtotime($startTime);
        $intEndTime = strtotime($endTime);
        $todayOrderNumber = GoodsOrder::totalOrderNumber($intStartTime, $intEndTime, $supplierId);
        $todayAmountOrder = StringService::formatPrice(GoodsOrder::totalAmountOrder($intStartTime, $intEndTime, $supplierId) / 100);

        $where = "supplier_id = {$supplierId}";

        $startTime = explode(' ', $startTime)[0];
        $endTime = explode(' ', $endTime)[0];

        if ($startTime) {
            $startTime = str_replace('-', '', $startTime);
            $startTime && $where .= " and create_date >= {$startTime}";
        }
        if ($endTime) {
            $endTime = str_replace('-', '', $endTime);
            $endTime && $where .= " and create_date <= {$endTime}";
        }

        return [
            $timeType . '_amount_order' => $todayAmountOrder,
            $timeType . '_order_number' => $todayOrderNumber,
            $timeType . '_ip_number' => GoodsStat::totalIpNumber($where),
            $timeType . '_viewed_number' => GoodsStat::totalViewedNumber($where),
        ];
    }

    /** check  supplier handle  order  Jurisdiction
     * 商家处理订单权限
     * @param $user
     * @param $postData
     * @return int
     */
    public static function CheckOrderJurisdiction($user, $postData)
    {
        if (!array_key_exists('order_no', $postData)) {
            $code = 1000;
            return $code;
        }
        $supplier = self::find()
            ->where(['uid' => $user->id])
            ->one();
        $GoodsOrder = GoodsOrder::find()
            ->where(['order_no' => $postData['order_no']])
            ->one();
        if ($supplier->id != $GoodsOrder->supplier_id) {
            $code = 1034;
            return $code;
        }
        $code = 200;
        return $code;
    }

    /* Get extra fields
     *
     * @param int $id supplier id
     * @param array $extraFields extra fields
     * @return array
     */

    /**
     * Set shop no
     *
     * @return $this
     */
    public function setShopNo()
    {
        $this->shop_no = Yii::$app->params['supplierRoleId']
            . (Yii::$app->params['offsetGeneral'] + $this->id);
        $this->save();
        return $this;
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['type_org', 'category_id', 'type_shop', 'shop_name', 'name', 'licence', 'licence_image', 'shop_name'], 'required'],
            [['shop_name'], 'unique', 'on' => self::SCENARIO_ADD, 'message' => self::ERROR_CODE_SAME_SHOP_NAME . ModelService::SEPARATOR_ERRCODE_ERRMSG . Yii::$app->params['errorCodes'][self::ERROR_CODE_SAME_SHOP_NAME]],
            [['licence'], 'unique', 'on' => self::SCENARIO_ADD, 'message' => self::ERROR_CODE_SAME_LICENCE . ModelService::SEPARATOR_ERRCODE_ERRMSG . Yii::$app->params['errorCodes'][self::ERROR_CODE_SAME_LICENCE]],
            [['name'], 'unique', 'on' => self::SCENARIO_ADD, 'message' => self::ERROR_CODE_SAME_NAME . ModelService::SEPARATOR_ERRCODE_ERRMSG . Yii::$app->params['errorCodes'][self::ERROR_CODE_SAME_NAME]],
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
        return LineSupplier::_extraData($viewData);
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
                case 'open_shop_time':
                    $userRole = UserRole::find()
                        ->where(['user_id' => $this->uid, 'role_id' => Yii::$app->params['supplierRoleId']])
                        ->one();
                    $extraData[$extraField] = date('Y-m-d', $userRole->review_time);
                    break;
                case 'review_status':
                    $userRole = UserRole::find()->where(['user_id' => $this->uid, 'role_id' => Yii::$app->params['supplierRoleId']])->one();
                    $extraData[$extraField] = $userRole->review_status;
                    $extraData[$extraField . ModelService::SUFFIX_FIELD_DESCRIPTION] = Yii::$app->params['reviewStatuses'][$userRole->review_status];
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
            $data['status_desc'] = self::STATUSES[$data['status']];
        }

        if (isset($data['quality_guarantee_deposit'])) {
            $data['quality_guarantee_deposit'] /= 100;
        }

        if (isset($data['type_org'])) {
            $data['type_org'] = self::TYPE_ORG[$data['type_org']];
        }

        if (isset($data['category_id'])) {
            $cat = GoodsCategory::findOne($data['category_id']);
            $data['category_name'] = $cat->fullTitle();
            unset($data['category_id']);
        }

        if (isset($data['type_shop'])) {
            $data['type_shop'] = self::TYPE_SHOP[$data['type_shop']];
        }

        if (isset($data['sales_amount_month'])) {
            $data['sales_amount_month'] /= 100;
        }

        $ym = date('Ym');
        if (isset($data['sales_amount_month'])) {
            if ($ym == $data['month']) {
                $data['sales_amount_month'] /= 100;
            } else {
                $data['sales_amount_month'] = 0;
            }
        }

        if (isset($data['sales_volumn_month'])) {
            if ($ym != $data['month']) {
                $data['sales_volumn_month'] = 0;
            }
        }

        if (isset($data['support_offline_shop'])) {
            $data['support_offline_shop'] = Yii::$app->params['desc']['support'][$data['support_offline_shop']];
        }
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

    /**
     * Open supplier
     *
     * @param ActiveRecord $operator operator
     * @return int
     */
    public function online(ActiveRecord $operator)
    {
        if (User::find()->where(['id' => $this->uid])
            ->andWhere(['>', 'deadtime', 0])
            ->exists()
        ) {
            return 1037;
        }

        $this->status = self::STATUS_ONLINE;

        $tran = Yii::$app->db->beginTransaction();
        $code = 500;

        try {
            if (!$this->save()) {
                $tran->rollBack();
                return $code;
            }

            $tran->commit();
            return 200;
        } catch (\Exception $e) {
            $tran->rollBack();
            return $code;
        }
    }


    /**
     * 添加线下体验店
     * @param $post
     * @param $supplier_id
     * @return int
     */
    public  static  function  AddLineSupplier($post,$supplier_id)
    {
        if (
            !array_key_exists('district_code',$post)
            ||!array_key_exists('address',$post)
            ||!array_key_exists('mobile',$post))
        {
            $code=1000;
            return $code;
        }
        $tran = Yii::$app->db->beginTransaction();
        $time=time();
        try{
            $LineSupplier=new LineSupplier();
            $LineSupplier->supplier_id=$supplier_id;
            $LineSupplier->district_code=$post['district_code'];
            $LineSupplier->address=$post['address'];
            $LineSupplier->status=1;
            $LineSupplier->mobile=$post['mobile'];
            $LineSupplier->create_time=$time;
            if (!$LineSupplier->validate())
            {
                $tran->rollBack();
                $code=500;
                return $code;
            }
            if (!$LineSupplier->save())
            {
                $code=500;
                $tran->rollBack();
                return $code;
            };
            $tran->commit();
            $code=200;
            return $code;
        }catch (\Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }
    }


    /**
     * 编辑线下体验店
     * @param $post
     * @param $supplier_id
     * @return int
     */
    public  static  function  UpLineSupplier($post,$supplier_id)
    {
        if (
            !array_key_exists('district_code',$post)
            ||!array_key_exists('address',$post)
            ||!array_key_exists('mobile',$post))
        {
            $code=1000;
            return $code;
        }
        $LineSupplier=LineSupplier::find()
            ->where(['supplier_id'=>$supplier_id])
            ->one();
        if (!$LineSupplier)
        {
           $code=1000;
           return $code;
        }
        $tran = Yii::$app->db->beginTransaction();
        try{
            $LineSupplier->district_code=$post['district_code'];
            $LineSupplier->address=$post['address'];
            $LineSupplier->mobile=$post['mobile'];
            if (!$LineSupplier->save(false))
            {
                $code=500;
                $tran->rollBack();
                return $code;
            };
            $tran->commit();
            $code=200;
            return $code;
        }catch (\Exception $e){
            $tran->rollBack();
            $code=500;
            return $code;
        }
    }


}