<?php

namespace app\models;


use app\services\ModelService;
use app\services\StringService;
use function GuzzleHttp\Psr7\str;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Query;

const  SUP_BANK_CARD = 'supplier_bankinformation';
const  SUPPLIER = 'supplier';
const  SUP_FREELIST = 'supplier_freezelist';
const  SUP_CASHREGISTER = 'supplier_cashregister';
const  GOODS_ORDER = 'goods_order';

class SupplierCashManager extends ActiveRecord
{

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
    public function getCashList($supplier_id, $page, $page_size, $time_type, $time_start, $time_end, $status)
    {
        $query = (new \yii\db\Query())->from(SUP_CASHREGISTER)->where(['supplier_id' => $supplier_id]);
        $time_area = ModelService::timeDeal($time_type, $time_start, $time_end);
        $time_start = $time_area[0];
        $time_end = $time_area[1];
        if ($time_start && $time_end && $time_end > $time_start) {
            $query->andWhere(['>', 'apply_time', $time_start])
                ->andWhere(['<', 'apply_time', $time_end]);
        }
        if ($status) {
            $query->andWhere(['status' => $status]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $page_size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        foreach ($arr as $k => $v) {
            $arr[$k]['apply_time'] = date('Y-m-d H:i', $arr[$k]['apply_time']);
            if ($arr[$k]['handle_time']) {
                $arr[$k]['handle_time'] = date('Y-m-d H:i', $arr[$k]['handle_time']);
            }
            $arr[$k]['cash_money'] = sprintf('%.2f', (float)$arr[$k]['cash_money'] / 100);
            if ($arr[$k]['real_money']) {
                $arr[$k]['real_money'] = sprintf('%.2f', (float)$arr[$k]['real_money'] / 100);
                $arr[$k]['lost_money'] = sprintf('%.2f', $arr[$k]['cash_money'] - $arr[$k]['real_money']);
            } else {
                $arr[$k]['lost_money'] = sprintf('%.2f', 0);
                $arr[$k]['real_money'] = sprintf('%.2f', 0);
            }
        }
        $data = ModelService::pageDeal($arr, $count, $page_size, $page);
        return $data;
    }

    /**
     * 获取商家现金流详情
     * @param $supplier_id
     * @param $cash_id
     * @return array|bool
     */
    public function GetCash($cash_id, $supplier_id = 0)
    {
        $query = (new \yii\db\Query())->from(SUP_CASHREGISTER)->where(['id' => $cash_id]);
        if ($supplier_id) {
            $query->andWhere(['supplier_id' => $supplier_id]);
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
        $data = ModelService::pageDeal($arr, $count, $page_size, $page);
        return $data;
    }

    /**
     * 查询银行卡信息
     * @param $supplier_id
     * @return array
     */
    private function GetBankcard($supplier_id)
    {
        $data = (new Query())->from(SUP_BANK_CARD)->where(['supplier_id' => $supplier_id])->one();
        return $data;
    }

    private function GetSupplier($supplier_id)
    {
        $data = (new Query())->from(SUPPLIER)->where(['id' => $supplier_id])->one();
        return $data;
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
        $data = (new Query())->from(GOODS_ORDER)->where(['pay_status' => 1])->sum('amount_order');
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
        $data = (new Query())->from(GOODS_ORDER)->where(['pay_status' => 1])->andwhere('paytime >= ' . $today[0])->andWhere('paytime <= ' . $today[1])->sum('amount_order');
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
        $data = (new Query())->from(SUP_CASHREGISTER)->where(['status' => 3])->sum('cash_money');
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
        $data = (new Query())->from(SUP_CASHREGISTER)->where(['status' => 3])->andwhere('handle_time >= ' . $today[0])->andWhere('handle_time <= ' . $today[1])->sum('cash_money');
        if ($data == null) {
            return 0;
        }
        return sprintf('%.2f', (float)$data / 100);
    }

    /**
     * 获取所有已提现的提现单数量
     */
    public function getPayedCashesCountAll()
    {
        return (new Query())->from(SUP_CASHREGISTER)->where(['status' => 3])->count();
    }

    /**
     * 获取所有未处理的提现单数量
     */
    public function getNotPayedCashesCountAll()
    {
        return (new Query())->from(SUP_CASHREGISTER)->where(['<>', 'status', 3])->count();
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
    public function getOrderList($page, $page_size, $time_type, $time_start, $time_end, $search)
    {
        $query = (new Query())->from(GOODS_ORDER . ' g')
            ->leftJoin(SUPPLIER . ' s', 'g.supplier_id = s.id')
            ->where(['g.pay_status' => 1]);
        $time_area = ModelService::timeDeal($time_type, $time_start, $time_end);
        $time_start = $time_area[0];
        $time_end = $time_area[1];
        if ($time_start && $time_end && $time_end > $time_start) {
            $query->andWhere(['>', 'g.paytime', $time_start])
                ->andWhere(['<', 'g.paytime', $time_end]);
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
        foreach ($arr as $k => $v) {
            $arr[$k]['paytime'] = date('Y-m-d H:i', $arr[$k]['paytime']);
            $arr[$k]['amount_order'] = sprintf('%.2f', (float)$arr[$k]['amount_order'] / 100);
        }
        $data = ModelService::pageDeal($arr, $count, $page_size, $page);
        return $data;
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
    public function getCashListAll($page, $page_size, $time_type, $time_start, $time_end, $status, $search)
    {
        $query = (new Query())->from(SUP_CASHREGISTER . ' as g')->leftJoin(SUPPLIER . ' s', 'g.supplier_id = s.id')
            ->select(['g.id', 'g.cash_money', 'g.apply_time', 's.shop_name', 'g.supplier_id', 'g.status', 'g.real_money']);
        if ($status) {
            $query->andWhere(['g.status' => $status]);
        }
        $time_area = ModelService::timeDeal($time_type, $time_start, $time_end);
        $time_start = $time_area[0];
        $time_end = $time_area[1];
        if ($time_start && $time_end && $time_end > $time_start) {
            $query->andWhere(['>', 'apply_time', $time_start])
                ->andWhere(['<', 'apply_time', $time_end]);
        }
        if ($search) {
            $query->andFilterWhere(['like', 'g.supplier_id', $search])
                ->orFilterWhere(['like', 's.shop_name', $search]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $page_size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        foreach ($arr as $k => $v) {
            $arr[$k]['apply_time'] = date('Y-m-d H:i', $arr[$k]['apply_time']);
            $arr[$k]['cash_money'] = sprintf('%.2f', (float)$arr[$k]['cash_money'] / 100);
            if ($arr[$k]['real_money']) {
                $arr[$k]['real_money'] = sprintf('%.2f', (float)$arr[$k]['real_money'] / 100);
                $arr[$k]['lost_money'] = sprintf('%.2f', $arr[$k]['cash_money'] - $arr[$k]['real_money']);
            } else {
                $arr[$k]['lost_money'] = sprintf('%.2f', 0);
                $arr[$k]['real_money'] = sprintf('%.2f', 0);
            }
        }
        $data = ModelService::pageDeal($arr, $count, $page_size, $page);
        return $data;
    }


    /**
     * 处理提现请求
     * @param $cash_id
     * @param $status
     * @param $reason
     * @param $real_money
     * @return \Exception|int|null|Exception
     */
    public function doCashDeal($cash_id, $status, $reason, $real_money)
    {
        $supplier_cash = (new Query())->from(SUP_CASHREGISTER)
            ->where(['id' => $cash_id])->select(['cash_money', 'supplier_id', 'status', 'transaction_no'])->one();
        $cash_money = $supplier_cash['cash_money'];
        $supplier_id = (int)$supplier_cash['supplier_id'];
        $old_status = (int)$supplier_cash['status'];
        $transaction_no = $supplier_cash['transaction_no'];
        //初始状态不能为已经处理过的
        if (!$cash_money || !$supplier_id || !$old_status || $old_status == 3 || $old_status == 4) {
            return null;
        }
        if ($status == 4) {
            $real_money = 0;
        }
        if ($real_money) {
            $real_money = $real_money * 100;
            if ($status == 3 && $real_money > $cash_money) {
                $real_money = $cash_money;
            }
        }
        $time = time();
        $trans = \Yii::$app->db->beginTransaction();
        $e = 1;
        try {
            \Yii::$app->db->createCommand()
                ->update(SUP_CASHREGISTER, [
                    'status' => $status,
                    'supplier_reason' => $reason,
                    'real_money' => $real_money,
                    'handle_time' => $time
                ], [
                    'id' => $cash_id
                ])
                ->execute();
            if ($status == 4) {
                //钱退回供货商
                $supplier = Supplier::find()->where(['id' => $supplier_id])->one();
                if (!$supplier) {
                    return null;
                }
                $supplier->balance += $cash_money;
                $supplier->availableamount += $cash_money;
                $supplier->save(false);
                //新建一条明细单数据
                $supplier_accessdetail = new SupplierAccessdetail();
                $supplier_accessdetail->setAttributes([
                    'access_type' => 4,
                    'access_money' => $cash_money,
                    'create_time' => $time,
                    'transaction_no' => $transaction_no,
                    'supplier_id' => $supplier_id
                ]);
                $supplier_accessdetail->save();
            }
        } catch (Exception $e) {
            $trans->rollBack();
        }
        $trans->commit();
        return $e;
    }
}
