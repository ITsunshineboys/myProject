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
    const  SUP_CASHREGISTER = 'user_cashregister';
    const  GOODS_ORDER = 'goods_order';
    const  ROLE_ID = 6;

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
    public static function getCashList($supplier_id, $page, $page_size, $time_type, $time_start, $time_end, $status)
    {
        $query = (new \yii\db\Query())
            ->from(self::SUP_CASHREGISTER)
            ->where(['uid' => $supplier_id, 'role_id' => self::ROLE_ID]);

        list($time_start, $time_end) = ModelService::timeDeal($time_type, $time_start, $time_end);
        if ($time_start && $time_end && $time_end > $time_start) {
            $query->andWhere(['between', 'apply_time', $time_start, $time_end]);
        }
        if ($status) {
            $query->andWhere(['status' => $status]);
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $page_size, 'pageSizeParam' => false]);
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
        }

        $data = ModelService::pageDeal($arr, $count, $page, $page_size);
        return $data;
    }

    /**
     * 获取商家现金流详情
     * @param $supplier_id
     * @param $cash_id
     * @return array|bool
     */
    public static function GetCash($cash_id, $supplier_id = 0)
    {
        $query = (new \yii\db\Query())
            ->from(self::SUP_CASHREGISTER)
            ->where(['id' => $cash_id, 'role_id' => self::ROLE_ID]);

        if ($supplier_id) {
            $query->andWhere(['uid' => $supplier_id]);
        }
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

        $supplier_id = $arr['supplier_id'];
        $bankcard = self::GetBankcard($supplier_id);
        $supplier = self::GetSupplier($supplier_id);
        if (!$bankcard) {
            return null;
        }

        $arr['card_no'] = $bankcard['bankcard'];
        $arr['supplier_name'] = $supplier['shop_name'];
        $arr['bank_name'] = $bankcard['bankname'];
        $arr['position'] = $bankcard['position'];
        $arr['bank_branch'] = $bankcard['bankbranch'];
        $arr['username'] = $bankcard['username'];
        $arr['cash_money'] = sprintf('%.2f', (float)$arr['cash_money'] / 100);
        if ($arr['real_money']) {
            $arr['real_money'] = sprintf('%.2f', (float)$arr['real_money'] / 100);
            $arr['lost_money'] = sprintf('%.2f', $arr['cash_money'] - $arr['real_money']);
        } else {
            $arr['lost_money'] = sprintf('%.2f', 0);
            $arr['real_money'] = sprintf('%.2f', 0);
        }
        return $arr;
    }

    /**
     * 查询银行卡信息
     * @param $supplier_id
     * @return array
     */
    public static function GetBankcard($supplier_id)
    {
        $uid = (int)self::GetSupplier($supplier_id)['uid'];
        return (new Query())->from(self::USER_BANKINFO)->where(['uid' => $uid])->one();
    }

    public static function GetSupplier($supplier_id)
    {
        return (new Query())->from(self::SUPPLIER)->where(['id' => $supplier_id])->one();
    }

    /**
     * @return array
     */
    public function getToday()
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
        $today = $this->getToday();
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
            ->from(self::SUP_CASHREGISTER)
            ->where(['status' => SupplierCashController::CASH_STATUS_DONE, 'role_id' => self::ROLE_ID])
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
        $today = $this->getToday();
        $data = (new Query())
            ->from(self::SUP_CASHREGISTER)
            ->where(['status' => SupplierCashController::CASH_STATUS_DONE, 'role_id' => self::ROLE_ID])
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
        return (new Query())->from(self::SUP_CASHREGISTER)
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
        return (new Query())->from(self::SUP_CASHREGISTER)
            ->where([
                'status' => [SupplierCashController::CASH_STATUS_NOT_BEGIN, SupplierCashController::CASH_STATUS_ING], 'role_id' => self::ROLE_ID
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
    public static function getOrderList($page, $page_size, $time_type, $time_start, $time_end, $search)
    {
        $query = (new Query())
            ->from(self::GOODS_ORDER . ' g')
            ->leftJoin(self::SUPPLIER . ' s', 'g.supplier_id = s.id')
            ->where(['g.pay_status' => 1]);

        list($time_start, $time_end) = ModelService::timeDeal($time_type, $time_start, $time_end);
        if ($time_start && $time_end && $time_end > $time_start) {
            $query->andWhere(['between', 'g.paytime', $time_start, $time_end]);
        }

        if ($search) {
            $query->andFilterWhere(['like', 'g.supplier_id', $search])
                ->orFilterWhere(['like', 's.shop_name', $search])
                ->orFilterWhere(['like', 'g.order_no', $search]);
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $page_size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->select(['g.id', 'g.order_no', 'g.amount_order', 'g.paytime', 's.shop_name', 'g.supplier_id'])
            ->all();
        foreach ($arr as &$v) {
            $v['paytime'] = date('Y-m-d H:i', $v['paytime']);
            $v['amount_order'] = sprintf('%.2f', (float)$v['amount_order'] / 100);
        }

        return ModelService::pageDeal($arr, $count, $page, $page_size);
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
    public static function getCashListAll($page, $page_size, $time_type, $time_start, $time_end, $status, $search)
    {
        $query = (new Query())
            ->from(self::SUP_CASHREGISTER . ' as g')
            ->where(['g.role_id' => self::ROLE_ID])
            ->leftJoin(self::SUPPLIER . ' s', 'g.supplier_id = s.id')
            ->select(['g.id', 'g.cash_money', 'g.apply_time', 's.shop_name', 'g.supplier_id', 'g.status', 'g.real_money']);
        if ($status) {
            $query->andWhere(['g.status' => $status]);
        }

        list($time_start, $time_end) = ModelService::timeDeal($time_type, $time_start, $time_end);
        if ($time_start && $time_end && $time_end > $time_start) {
            $query->andWhere(['between', 'g.apply_time', $time_start, $time_end]);
        }
        if (isset($search) && trim($search) == $search) {
            $query->andFilterWhere(['like', 'g.supplier_id', $search])
                ->orFilterWhere(['like', 's.shop_name', $search]);
        }

        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $page_size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        foreach ($arr as &$v) {
            $v['apply_time'] = date('Y-m-d H:i', $v['apply_time']);
            $v['cash_money'] = sprintf('%.2f', (float)$v['cash_money'] / 100);
            if ($v['real_money']) {
                $v['real_money'] = sprintf('%.2f', (float)$v['real_money'] / 100);
                $v['lost_money'] = sprintf('%.2f', $v['cash_money'] - $v['real_money']);
            } else {
                $v['lost_money'] = sprintf('%.2f', 0);
                $v['real_money'] = sprintf('%.2f', 0);
            }
        }

        return ModelService::pageDeal($arr, $count, $page, $page_size);
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
            ->from(self::SUP_CASHREGISTER)
            ->where(['id' => $cash_id, 'role_id' => self::ROLE_ID])
            ->select(['cash_money', 'supplier_id', 'status', 'transaction_no'])
            ->one();

        $cash_money = $supplier_cash['cash_money'];
        $supplier_id = (int)$supplier_cash['supplier_id'];
        $old_status = (int)$supplier_cash['status'];
        $transaction_no = $supplier_cash['transaction_no'];

        //初始状态不能为已经处理过的
        if (!$cash_money || !$supplier_id || !$old_status
            || $old_status == SupplierCashController::CASH_STATUS_DONE
            || $old_status == SupplierCashController::CASH_STATUS_FAIL) {
            return null;
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
        if ($supplier_accessdetail == null) {
            return false;
        }

        $time = time();
        $trans = \Yii::$app->db->beginTransaction();
        $e = 1;
        try {
            \Yii::$app->db->createCommand()
                ->update(self::SUP_CASHREGISTER, [
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
                $supplier = Supplier::find()->where(['id' => $supplier_id])->one();
                if (!$supplier) {
                    return null;
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
            }

            $trans->commit();

        } catch (Exception $e) {
            $trans->rollBack();
        }

        return $e;
    }
}
