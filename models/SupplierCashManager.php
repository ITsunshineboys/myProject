<?php

namespace app\models;


use yii\data\Pagination;

class SupplierCashManager extends Supplieramountmanage
{
    /**
     * 查询商家现金流列表
     * @param $supplier_id
     * @return array|bool|null
     */
    public function GetCashList($supplier_id,$page,$page_size,$time_id,$time_start,$time_end)
    {
        if ($time_id == 0) {
            $array=(new \yii\db\Query())->from(SUP_CASHREGISTER)->where(['supplier_id'=>$supplier_id]);
        } else {
            $array=(new \yii\db\Query())->from(SUP_CASHREGISTER)->where(['supplier_id'=>$supplier_id])->andwhere($this->TimehandleCash($time_id,$time_start,$time_end));
        }
        $count = $array->count();
        $pagination = new Pagination(['totalCount' =>$count,'pageSize' => $page_size, 'pageSizeParam'=>false]);
        $arr = $array->offset($pagination->offset)
            ->limit($pagination->limit)
//            ->select(['id', 'cash_money', 'apply_time', 'status'])
            ->all();
        foreach ($arr as $k=>$v) {
            $arr[$k]['apply_time'] = date('Y-m-d H:i', $arr[$k]['apply_time']);
            $arr[$k]['handle_time'] = date('Y-m-d H:i', $arr[$k]['handle_time']);
            switch ($arr[$k]['status']) {
                case 1:
                    $arr[$k]['status'] = '未提现';
                    break;
                case 2:
                    $arr[$k]['status'] = '提现中';
                    break;
                case 3:
                    $arr[$k]['status'] = '已提现';
                    break;
                case 4:
                    $arr[$k]['status'] = '提现失败';
                    break;
                default:
                    $arr[$k]['status'] = '驳回';
            }
        }
        $data = $this->pageCash($count, $page_size, $page, $arr);
        return $data;
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
        switch ($arr['status']) {
            case 1:
                $arr['status'] = '未提现';
                break;
            case 2:
                $arr['status'] = '提现中';
                break;
            case 3:
                $arr['status'] = '已提现';
                break;
            case 4:
                $arr['status'] = '提现失败';
                break;
            default:
                $arr['status'] = '驳回';
        }
        return $arr;
    }
    private function TimehandleCash($time_id,$time_start,$time_end)
    {
        if ($time_id==0){
            return null;
        }else if ($time_id==1){
            $data='DATE(FROM_UNIXTIME(create_time))=CURDATE()';
            return $data;
        }else if ($time_id==2){
            $data="DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= DATE(FROM_UNIXTIME(apply_time))";
            return $data;
        }else if ($time_id==3){
            $data="DATE_SUB(CURDATE(), INTERVAL 1 MONTH) <= DATE(FROM_UNIXTIME(apply_time))";
            return $data;
        }else if($time_id==4){
            $data="DATE_SUB(CURDATE(), INTERVAL 365 DAY) <= DATE(FROM_UNIXTIME(apply_time))";
            return $data;
        }else if ($time_id==5){
            $data="apply_time>='".strtotime($time_start)."' and apply_time<= '".strtotime($time_end)."'";
            return $data;
        }
    }
    private function pageCash($count,$pagesize,$page,$arr)
    {
        $totalpage=ceil($count/$pagesize);
        if ($page>$totalpage){
            $sd = array(
                'cashlist'=>'',
                'totalpage'=>$totalpage,
                'count'=>$count,
                'page'=>$page
            );
        }else{
            $sd = array(
                'cashlist'=>$arr,
                'totalpage'=>$totalpage,
                'count'=>$count,
                'page'=>$page
            );
        }
        return $sd;
    }
    /**
     * 查询银行卡信息
     * @param $supplier_id
     * @return array
     */
    private function GetBankcard($supplier_id){
        $data=(new \yii\db\Query())->from(SUP_BANK_CARD)->where(['supplier_id'=>$supplier_id])->one();
        return $data;
    }
}