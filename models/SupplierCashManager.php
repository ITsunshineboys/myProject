<?php

namespace app\models;


use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;

const  SUP_BANK_CARD = 'supplier_bankinformation';
const  SUPPLIER = 'supplier';
const  SUP_FREELIST = 'supplier_freezelist';
const  SUP_CASHREGISTER = 'supplier_cashregister';
const  GOODS_ORDER = 'goods_order';

class SupplierCashManager extends ActiveRecord
{

    /**
     * 查询商家现金流列表
     * @param $supplier_id
     * @return array|bool|null
     */
    public function getCashList($supplier_id, $page, $page_size, $time_id, $time_start, $time_end, $status)
    {
        $query = (new \yii\db\Query())->from(SUP_CASHREGISTER)->where(['supplier_id' => $supplier_id]);
        if ($time_id) {
            $query->andWhere(self::Timehandle($time_id, $time_start, $time_end, 'apply_time'));
        }
        if ($status) {
            $query->andWhere(['status' => $status]);
        }
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $page_size, 'pageSizeParam' => false]);
        $arr = $query->offset($pagination->offset)
            ->limit($pagination->limit)
//            ->select(['id', 'cash_money', 'apply_time', 'status'])
            ->all();
        foreach ($arr as $k => $v) {
            $arr[$k]['apply_time'] = date('Y-m-d H:i', $arr[$k]['apply_time']);
            $arr[$k]['handle_time'] = date('Y-m-d H:i', $arr[$k]['handle_time']);
        }
        $total_page = ceil($count / $page_size);
        if ($page > $total_page) {
            $page = $total_page;
        }
        if ($page < 1) {
            $page = 1;
        }
        $arr['total_page'] = $total_page;
        $arr['count'] = $count;
        $arr['page'] = $page;
        return $arr;
    }

    /**
     * 获取商家现金流详情
     * @param $supplier_id
     * @param $cash_id
     * @return array|bool
     */
    public function GetCash($supplier_id, $cash_id)
    {
        $arr = (new \yii\db\Query())->from(SUP_CASHREGISTER)->where(['supplier_id' => $supplier_id])->andWhere(['id' => $cash_id])->one();
        $arr['apply_time'] = date('Y-m-d H:i', $arr['apply_time']);
        $arr['handle_time'] = date('Y-m-d H:i', $arr['handle_time']);
        $arr['card_no'] = self::GetBankcard($supplier_id)['bankcard'];
        return $arr;
    }

    private function Timehandle($time_id, $time_start, $time_end, $time = 'create_time')
    {
        if ($time_id == 0) {
            return null;
        } else if ($time_id == 1) {
            $data = 'DATE(FROM_UNIXTIME(' . $time . ')) = CURDATE()';
            return $data;
        } else if ($time_id == 2) {
            $data = "DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= DATE(FROM_UNIXTIME(" . $time . "))";
            return $data;
        } else if ($time_id == 3) {
            $data = "DATE_SUB(CURDATE(), INTERVAL 1 MONTH) <= DATE(FROM_UNIXTIME(" . $time . "))";
            return $data;
        } else if ($time_id == 4) {
            $data = "DATE_SUB(CURDATE(), INTERVAL 365 DAY) <= DATE(FROM_UNIXTIME(" . $time . "))";
            return $data;
        } else if ($time_id == 5) {
            $data = $time . " >= " . strtotime($time_start) . " and " . $time . " <= " . strtotime($time_end);
            return $data;
        }
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
            $data = 0;
        }
        return $data;
    }

    /**
     * 获取今日已付款的订单金额之和
     */
    public function getPayedOrdersToday()
    {
        $today = $this->getToday();
        $data = (new Query())->from(GOODS_ORDER)->where(['pay_status' => 1])->andwhere('paytime >= ' . $today[0])->andWhere('paytime <= ' . $today[1])->sum('amount_order');
        if ($data == null) {
            $data = 0;
        }
        return $data;
    }

    /**
     * 获取所有商家已提现的现金之和
     */
    public function getPayedCashesAll()
    {
        $data = (new Query())->from(SUP_CASHREGISTER)->where(['status' => 3])->sum('cash_money');
        if ($data == null) {
            $data = 0;
        }
        return $data;
    }

    /**
     * 获取今日商家已提现的金额之和
     */
    public function getPayedCashesToday()
    {
        $today = $this->getToday();
        $data = (new Query())->from(SUP_CASHREGISTER)->where(['status' => 3])->andwhere('handle_time >= ' . $today[0])->andWhere('handle_time <= ' . $today[1])->sum('cash_money');
        if ($data == null) {
            $data = 0;
        }
        return $data;
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
        return (new Query())->from(SUP_CASHREGISTER)->where(['status' => 1])->count();
    }

    /**
     * 获取订单列表
     */
    public function getOrderList($page, $page_size, $time_id, $time_start, $time_end, $search)
    {
        $query = (new Query())->from(GOODS_ORDER . ' g')
            ->leftJoin(SUPPLIER . ' s', 'g.supplier_id = s.id')
            ->where(['g.pay_status' => 1]);
        if ($time_id) {
            $query->andWhere($this->Timehandle($time_id, $time_start, $time_end, 'g.paytime'));
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
        }
        $total_page = ceil($count / $page_size);
        if ($page > $total_page) {
            $page = $total_page;
        }
        if ($page < 1) {
            $page = 1;
        }
        $arr['total_page'] = $total_page;
        $arr['count'] = $count;
        $arr['page'] = $page;
        return $arr;
    }

    /**
     * 获取提现列表
     */
    public function getCashListAll($page, $page_size, $time_id, $time_start, $time_end, $status, $search)
    {
        $query = (new Query())->from(SUP_CASHREGISTER . ' as g')->leftJoin(SUPPLIER . ' s', 'g.supplier_id = s.id')
            ->select(['g.id', 'g.cash_money', 'g.apply_time', 's.shop_name', 'g.supplier_id', 'g.status']);
        if ($status) {
            $query->andWhere(['g.status' => $status]);
        }
        if ($time_id) {
            $query->andWhere($this->Timehandle($time_id, $time_start, $time_end, 'apply_time'));
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
        }
        $total_page = ceil($count / $page_size);
        if ($page > $total_page) {
            $page = $total_page;
        }
        if ($page < 1) {
            $page = 1;
        }
        $arr['total_page'] = $total_page;
        $arr['count'] = $count;
        $arr['page'] = $page;
        return $arr;
    }
}