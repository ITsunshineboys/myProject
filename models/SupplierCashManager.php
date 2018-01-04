<?php

namespace app\models;

use app\controllers\SupplierCashController;
use app\services\ModelService;
use yii\data\Pagination;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Query;

class SupplierCashManager extends ActiveRecord
{
    const  USER_BANKINFO = 'user_bankinfo';
    const  SUPPLIER = 'supplier';
    const  USER_CASHREGISTER = 'user_cashregister';
    const  GOODS_ORDER = 'goods_order';
    const  ROLE_ID = 6;
    const STATUS_BRAND=[
        '0' => '待审核',
        '1' => '不通过',
        '2' => '通过',
    ];

    const SUPPLIER_BRAND_VIEW = [
        'id',
        'name',
        'supplier_id',
        'logo',
        'certificate',
        'create_time',
        'approve_time',
        'reject_time',
        'review_status',
        'status',
        'reason',
    ];
    const SUPPLIER_CATE_LIST = [
        'id',
        'title',
        'icon',
        'pid',
        'parent_title',
        'level',
        'create_time',
        'approve_time',
        'reject_time',
        'review_status',
        'supplier_id',
        'reason',
        'description',
        'path',
        'supplier_name',
        'path'
    ];
    const FIELDS_ADMIN = [

    ];

    /**
     * 查询商家提现列表
     * @param $supplier_id int 商家id
     * @param $page  int 哪一页
     * @param $page_size int 页面尺寸
     * @param $time_type string 时间类型
     * @param $time_start string 开始时间
     * @param $time_end string 结束时间
     * @param $status int 状态
     * @return array
     */
    public static function getCashList($user, $where = [], $page = 1, $size = ModelService::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC')
    {

        $query = (new Query())
            ->from(self::USER_CASHREGISTER)
            ->where($where)
            ->orderBy('apply_time Desc');

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        foreach ($arr as &$v) {
            $v['apply_time'] = date('Y-m-d H:i', $v['apply_time']);
            if ($v['handle_time']) {
                $v['handle_time'] = date('Y-m-d H:i', $v['handle_time']);
            }
            $v['cash_money'] = sprintf('%.2f', (float)$v['cash_money'] / 100);
            if ($v['real_money']) {
                $v['real_money'] = sprintf('%.2f', (float)$v['real_money'] / 100);
                $v['lost_money'] = sprintf('%.2f', $v['cash_money'] - $v['real_money']);
            } else {
                $v['lost_money'] = sprintf('%.2f', 0);
                $v['real_money'] = sprintf('%.2f', 0);
            }
            $v['status'] = SupplierCashController::USER_CASH_STATUSES[$v['status']];
            unset($v['uid'], $v['role_id']);
        }
        $data = ModelService::pageDeal($arr, $count, $page, $size);

        $data['supplier_id'] = Supplier::find()
            ->select('id')
            ->where(['uid' => $user])
            ->asArray()
            ->one()['id'];

        return $data;
    }

    /**
     * 获取商家现金流详情
     * @param $supplier_id
     * @param $cash_id
     * @return array|bool
     */
    public static function GetCash($transaction_no, $supplier_id)
    {

        $query = UserCashregister::find()
            ->asArray()
            ->where(['transaction_no' => $transaction_no, 'role_id' => self::ROLE_ID]);

        $arr = $query->one();
        if (!$arr) {
            return null;
        }
        if ($arr['apply_time']) {
            $arr['apply_time'] = date('Y-m-d H:i', $arr['apply_time']);
        }
        if ($arr['handle_time']) {
            $arr['handle_time'] = date('Y-m-d H:i', $arr['handle_time']);
        }
        $bankcard = self::GetBankcard($arr['bank_log_id']);
        $supplier = self::GetSupplier($supplier_id);
        if ($supplier) {
            $arr['supplier_name'] = $supplier['shop_name'];

        }else{
            $supplier=null;
        }
        if ($bankcard) {
            $arr['card_no'] = $bankcard['bankcard'];

            $arr['bank_name'] = $bankcard['bankname'];
            $arr['position'] = $bankcard['position'];
            $arr['bank_branch'] = $bankcard['bankbranch'];
            $arr['username'] = $bankcard['username'];

        }else{
            $bankcard=null;
        }

        $arr['cash_money'] = sprintf('%.2f', (float)$arr['cash_money'] / 100);
        if ($arr['real_money']) {
            $arr['real_money'] = sprintf('%.2f', (float)$arr['real_money'] / 100);
            $arr['lost_money'] = sprintf('%.2f', $arr['cash_money'] - $arr['real_money']);
        } else {
            $arr['lost_money'] = null;
            $arr['real_money'] = null;
        }

        $arr['status'] = SupplierCashController::USER_CASH_STATUSES[$arr['status']];
        return $arr;
    }

    /**
     * 查询银行卡信息
     * @param $bank_log_id
     * @return ActiveRecord
     */
    public static function GetBankcard($bank_log_id)
    {
        return BankinfoLog::find()->where(['id'=>$bank_log_id])->asArray()->one();
//        return BankinfoLog::find()
//            ->select('bankinfo_log.*')
//            ->leftJoin('user_bankinfo','user_bankinfo.log_id=bankinfo_log.id')
//            ->where(['bankinfo_log.id' => $bank_log_id,'user_bankinfo.role_id'=>$role_id])
//            ->one();
    }

    public static function GetSupplier($supplier_id)
    {
        return (new Query())->from(self::SUPPLIER)->where(['id' => $supplier_id])->one();
    }

    public static function GetUser($user_id)
    {
        return (new Query())->from(OwnerCashManager::USER)->where(['id' => $user_id])->one();
    }


    /**
     * @return array
     */
    public static function getToday()
    {
        $year = date("Y");
        $month = date("m");
        $day = date("d");
        $start = mktime(0, 0, 0, $month, $day, $year);//当天开始时间戳
        $end = mktime(23, 59, 59, $month, $day, $year);//当天结束时间戳
        return [$start, $end];
    }

    /**
     * 获取全部已付款的订单金额之和
     */
    public function getPayedOrdersAll()
    {
        $data = (new Query())
            ->from(self::GOODS_ORDER)
            ->where(['pay_status' => 1])
            ->sum('amount_order');

        if ($data == null) {
            return 0;
        }
        return sprintf('%.2f', (float)$data / 100);
    }

    /**
     * 获取今日已付款的订单金额之和
     */
    public function getPayedOrdersToday()
    {
        $today = self::getToday();
        $data = (new Query())
            ->from(self::GOODS_ORDER)
            ->where(['pay_status' => 1])
            ->andwhere('paytime >= ' . $today[0])
            ->andWhere('paytime <= ' . $today[1])
            ->sum('amount_order');
        if ($data == null) {
            return 0;
        }
        return sprintf('%.2f', (float)$data / 100);
    }

    /**
     * 获取所有商家已提现的现金之和
     */
    public function getPayedCashesAll()
    {
        $data = (new Query())
            ->from(self::USER_CASHREGISTER)
            ->where(['status' => SupplierCashController::ACCESS_TYPE_CHARGE, 'role_id' => self::ROLE_ID])
            ->sum('cash_money');

        if ($data == null) {
            return 0;
        }
        return sprintf('%.2f', (float)$data / 100);
    }

    /**
     * 获取今日商家已提现的金额之和
     */
    public function getPayedCashesToday()
    {
        $today = self::getToday();
        $data = (new Query())
            ->from(self::USER_CASHREGISTER)
            ->where(['status' => SupplierCashController::ACCESS_TYPE_CHARGE, 'role_id' => self::ROLE_ID])
            ->andwhere('handle_time >= ' . $today[0])
            ->andWhere('handle_time <= ' . $today[1])
            ->sum('cash_money');

        if ($data == null) {
            return 0;
        }
        return sprintf('%.2f', (float)$data / 100);
    }

    /**
     * 获取所有已处理的提现单数量
     */
    public function getPayedCashesCountAll()
    {
        return (new Query())->from(self::USER_CASHREGISTER)
            ->where([
                'status' => [SupplierCashController::CASH_STATUS_DONE, SupplierCashController::CASH_STATUS_FAIL],
                'role_id' => self::ROLE_ID
            ])
            ->count();
    }

    /**
     * 获取所有未处理的提现单数量
     */
    public function getNotPayedCashesCountAll()
    {
        return (new Query())->from(self::USER_CASHREGISTER)
            ->where([
                'status' => SupplierCashController::CASH_STATUS_ING, 'role_id' => self::ROLE_ID
            ])
            ->count();
    }

    /**
     * 获取订单列表
     * @param $page
     * @param $page_size
     * @param $time_type
     * @param $time_start
     * @param $time_end
     * @param $search
     * @return array
     */
    public static function getOrderList($where = [], $page = 1, $size = ModelService::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC')
    {

        $query = (new Query())
            ->from(self::GOODS_ORDER . ' g')
            ->select(['g.id', 'g.order_no', 'g.paytime', 's.shop_name', 'g.supplier_id', 'o.sku', 'o.goods_name', 'o.goods_price', 'o.goods_number', 'o.freight', 's.shop_no'])
            ->leftJoin(self::SUPPLIER . ' s', 'g.supplier_id = s.id')
            ->leftJoin(OrderGoods::tableName() . ' o', 'o.order_no=g.order_no')
            ->where($where)
            ->orderBy('g.paytime Desc');

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        foreach ($arr as &$v) {
            $v['paytime'] = date('Y-m-d H:i', $v['paytime']);
            $v['amount_order'] = sprintf('%.2f', (float)($v['goods_price'] * $v['goods_number'] + $v['freight']) / 100);
            unset($v['freight']);
            unset($v['goods_number']);
            unset($v['goods_price']);
        }
        return ModelService::pageDeal($arr, $count, $page, $size);
    }


    /**
     * 获取提现列表
     * @param $page
     * @param $page_size
     * @param $time_type
     * @param $time_start
     * @param $time_end
     * @param $status
     * @param $search
     * @return array
     */
    public static function getCashListAll($where = [], $page = 1, $size = ModelService::PAGE_SIZE_DEFAULT, $time_s,$orderBy = 'id DESC')
    {

        $query = (new Query())
            ->from(self::USER_CASHREGISTER . ' as g')
            ->leftJoin(self::SUPPLIER . ' s', 'g.uid = s.uid')
            ->select(['g.id', 'g.cash_money', 'g.apply_time', 's.shop_name', 's.shop_no', 'g.uid', 'g.status', 'g.real_money', 'g.transaction_no', 'g.handle_time'])
            ->where($where)
            ->orderBy("$time_s Desc");


        $count = $query->count();

        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        foreach ($arr as &$v) {
            if (!$v['handle_time']) {
                $v['handle_time'] = '-';
            } else {
                $v['handle_time'] = date('Y-m-d H:i', $v['handle_time']);
            }
            $v['apply_time'] = date('Y-m-d H:i', $v['apply_time']);
            $v['cash_money'] = sprintf('%.2f', (float)$v['cash_money'] / 100);
            if ($v['real_money']) {
                $v['real_money'] = sprintf('%.2f', (float)$v['real_money'] / 100);
                $v['lost_money'] = sprintf('%.2f', $v['cash_money'] - $v['real_money']);
            } else {
                $v['lost_money'] = sprintf('%.2f', 0);
                $v['real_money'] = sprintf('%.2f', 0);
            }

            $v['status'] = SupplierCashController::USER_CASH_STATUSES[$v['status']];
        }

        return ModelService::pageDeal($arr, $count, $page, $size);
    }


    /**
     * 处理提现请求
     * @param $cash_id
     * @param $status
     * @param $reason
     * @param $real_money
     * @return \Exception|int|null|Exception
     */
    public static function doCashDeal($cash_id, $status, $reason, $real_money)
    {

        $supplier_cash = (new Query())
            ->from(self::USER_CASHREGISTER)
            ->where(['id' => $cash_id, 'role_id' => self::ROLE_ID])
            ->select(['cash_money', 'uid', 'status', 'transaction_no'])
            ->one();
        $code = 1000;
        $cash_money = $supplier_cash['cash_money'];
        $supplier_uid = (int)$supplier_cash['uid'];
        $old_status = (int)$supplier_cash['status'];
        $transaction_no = $supplier_cash['transaction_no'];
        //初始状态不能为已经处理过的
        if (!$cash_money || !$supplier_uid || !$old_status
        ) {
            return $code;
        }
        //提现失败
        if ($status == SupplierCashController::CASH_STATUS_FAIL) {
            $real_money = 0;
        }
        //提现成功
        if ($status == SupplierCashController::CASH_STATUS_DONE) {
            $real_money && $real_money *= 100;
            $real_money > $cash_money && $real_money = $cash_money;
        }
        $supplier_accessdetail = UserAccessdetail::find()
            ->where(['transaction_no' => $transaction_no, 'role_id' => self::ROLE_ID])
            ->one();


        if (!$supplier_accessdetail) {
            return $code;
        }

        $time = time();
        $trans = \Yii::$app->db->beginTransaction();
        try {
            \Yii::$app->db->createCommand()
                ->update(self::USER_CASHREGISTER, [
                    'status' => $status,
                    'supplier_reason' => $reason,
                    'real_money' => $real_money,
                    'handle_time' => $time
                ], [
                    'id' => $cash_id
                ])
                ->execute();
            //提现失败
            if ($status == SupplierCashController::CASH_STATUS_FAIL) {
                //钱退回供货商
                $supplier = Supplier::find()->where(['uid' => $supplier_uid])->one();
                if (!$supplier) {
                    return $code;
                }
                $supplier->balance += $cash_money;
                $supplier->availableamount += $cash_money;
                $supplier->save(false);
                //修改明细单数据
                $supplier_accessdetail->access_type = SupplierCashController::ACCESS_TYPE_REJECT;
                $supplier_accessdetail->update(false);


            }
            //提现成功
            if ($status == SupplierCashController::CASH_STATUS_DONE) {
                $supplier_accessdetail->access_type = SupplierCashController::ACCESS_TYPE_CASH_DONE;
                $supplier_accessdetail->update(false);
            }

            $trans->commit();
            $code = 200;
            return $code;

        } catch (Exception $e) {
            $trans->rollBack();
            $code = 500;
            return $code;
        }

    }

    /**
     * 商家品牌列表
     * @param array $where
     * @param array $select
     * @param int $page
     * @param int $size
     * @param array $orderBy
     * @return array|ActiveRecord[]
     */
    public static function pagination($where = [], $select = [], $page = 1, $size = ModelService::PAGE_SIZE_DEFAULT, $orderBy = ['id' => SORT_ASC])
    {
        $offset = ($page - 1) * $size;
        $brandList = GoodsBrand::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();
        foreach ($brandList as &$brand) {
            $brand['apply_people']=Supplier::find()->where(['id'=>$brand['supplier_id']])->select('shop_name')->one()['shop_name'];
            if (isset($brand['create_time'])) {
                $brand['create_time'] = date('Y-m-d H:i', $brand['create_time']);
            }
            if (isset($brand['status'])) {
                $brand['status'] = GoodsBrand::$statuses[$brand['status']];
            }

            if (isset($brand['review_status'])) {
                $brand['review_status'] = self::STATUS_BRAND[$brand['review_status']];
            }

            if (isset($brand['approve_time']) || isset($brand['reject_time'])) {
                $brand['review_time'] = date('Y-m-d H:i', $brand['approve_time'] > 0 ? $brand['approve_time'] : $brand['reject_time']);
                if (isset($brand['approve_time'])) {
                    unset($brand['approve_time']);
                }
                if (isset($brand['reject_time'])) {
                    unset($brand['reject_time']);
                }
            }
            if (isset($brand['id'])) {
                $brand['category_titles'] = BrandCategory::categoryNamesByBrandId($brand['id']);

            }


        }

        return $brandList;
    }

    /**
     * 商家品牌详情
     * @param $id
     * @return array|null|ActiveRecord
     */
    public static function brandview($id)
    {

        $brand = GoodsBrand::find()
            ->select(self::SUPPLIER_BRAND_VIEW)
            ->asArray()
            ->where(['id' => $id])
            ->one();
        if ($brand) {
            $brand['apply_people']=Supplier::find()->where(['id'=>$brand['supplier_id']])->select('shop_name')->one()['shop_name'];
            $brand['create_time'] = date('Y-m-d H:i', $brand['create_time']);
            $brand['review_status'] = self::STATUS_BRAND[$brand['review_status']];
            if ($brand['approve_time'] != 0 || $brand['reject_time'] != 0) {
                $brand['review_time'] = date('Y-m-d H:i', $brand['approve_time'] > 0 ? $brand['approve_time'] : $brand['reject_time']);
                if (isset($brand['approve_time'])) {
                    unset($brand['approve_time']);
                }
                if (isset($brand['reject_time'])) {
                    unset($brand['reject_time']);
                }
            }
            unset($brand['approve_time']);
            unset($brand['reject_time']);
            $brand['category_titles'] = BrandCategory::categoryNamesByBrandId($brand['id']);
            $brand['categories'] = BrandCategory::categoriesByBrandId($brand['id'], GoodsCategory::FIELDS_EDIT_BRAND_SELECTED_CATEGORIES);
        }
        return $brand;
    }

    /**
     * 分类列表
     * @param array $where
     * @param array $select
     * @param int $page
     * @param int $size
     * @param array $orderBy
     * @return array|ActiveRecord[]
     */
    public static function Catepagination($where = [], $select = [], $page = 1, $size = ModelService::PAGE_SIZE_DEFAULT, $orderBy = ['id' => SORT_ASC])
    {
        $offset = ($page - 1) * $size;
        $categoryList = GoodsCategory::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();
        foreach ($categoryList as &$category) {
            if(isset($category['supplier_id'])){
                $category['apply_people']=Supplier::find()->where(['id'=>$category['supplier_id']])->select('shop_name')->one()['shop_name'];
            }
            if (isset($category['create_time'])) {
                $category['create_time'] = date('Y-m-d H:i', $category['create_time']);
            }

            if (isset($category['level']) && isset($category['path'])) {
                $category['titles'] = '';
                if ($category['level'] == GoodsCategory::LEVEL3) {
                    $path = trim($category['path'], ',');
                    list($rootId, $parentId, $id) = explode(',', $path);
                    $rootCategory = GoodsCategory::findOne($rootId);
                    $category['titles'] = $rootCategory->title
                        . GoodsCategory::SEPARATOR_TITLES
                        . $category['parent_title']
                        . GoodsCategory::SEPARATOR_TITLES
                        . $category['title'];
                } elseif ($category['level'] == GoodsCategory::LEVEL2) {
                    $category['titles'] = $category['parent_title']
                        . GoodsCategory::SEPARATOR_TITLES
                        . $category['title'];
                } elseif ($category['level'] == GoodsCategory::LEVEL1) {
                    $category['titles'] = $category['title'];
                }

                $category['level'] = GoodsCategory::$levels[$category['level']];
            }

            if (isset($category['review_status'])) {
                $category['review_status'] = self::STATUS_BRAND[$category['review_status']];
            }

        }

        return $categoryList;
    }

    /**
     * 分类详情
     * @param $cate_id
     * @return array|null|ActiveRecord
     */
    public static function categoryview($cate_id)
    {
        $category = GoodsCategory::find()
            ->select(self::SUPPLIER_CATE_LIST)
            ->asArray()
            ->where(['id' => $cate_id])
            ->one();
        if ($category) {
            if(isset($category['supplier_id'])){
                $category['apply_people']=Supplier::find()->where(['id'=>$category['supplier_id']])->select('shop_name')->one()['shop_name'];
            }
            if (isset($category['level']) && isset($category['path'])) {
                $category['titles'] = '';
                if ($category['level'] == GoodsCategory::LEVEL3) {
                    $path = trim($category['path'], ',');
                    list($rootId, $parentId, $id) = explode(',', $path);
                    $rootCategory = GoodsCategory::findOne($rootId);
                    $category['titles'] = $rootCategory->title
                        . GoodsCategory::SEPARATOR_TITLES
                        . $category['parent_title']
                        . GoodsCategory::SEPARATOR_TITLES
                        . $category['title'];
                } elseif ($category['level'] == GoodsCategory::LEVEL2) {
                    $category['titles'] = $category['parent_title']
                        . GoodsCategory::SEPARATOR_TITLES
                        . $category['title'];
                } elseif ($category['level'] == GoodsCategory::LEVEL1) {
                    $category['titles'] = $category['title'];
                }
                $category['level'] = GoodsCategory::$levels[$category['level']];
            }
            $category['create_time'] = date('Y-m-d H:i', $category['create_time']);
            $category['review_status'] = self::STATUS_BRAND[$category['review_status']];
            if (isset($category['approve_time']) || isset($category['reject_time'])) {
                $category['review_time'] = date('Y-m-d H:i', $category['approve_time'] > 0 ? $category['approve_time'] : $category['reject_time']);
                if (isset($category['approve_time'])) {
                    unset($category['approve_time']);
                }
                if (isset($category['reject_time'])) {
                    unset($category['reject_time']);
                }
            }
            unset($category['approve_time']);
            unset($category['reject_time']);
        }
        return $category;
    }
}
