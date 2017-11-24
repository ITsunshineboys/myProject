<?php
namespace app\models;


use app\controllers\SupplierCashController;
use app\services\ModelService;
use yii\data\Pagination;
use yii\db\ActiveRecord;
use yii\db\Query;

class OwnerCashManager extends ActiveRecord {

    const USER_CASHREGISTER='user_cashregister';
    const USER='user';
    const STATUS_CASHING=1;
    const STATUS_CASHED=2;
    const OWNER_ROLE=7;
    /**
     * 业主历史已提现金额
     * @return int|string
     */
    public static function getOwnerCashedAll(){
        $data=UserCashregister::find()
            ->where(['status'=>self::STATUS_CASHED,'role_id'=>self::OWNER_ROLE])
            ->sum('cash_money');
        if ($data == null) {
            return 0;
        }
        return sprintf('%.2f', (float)$data / 100);
    }
    /**
     * 业主今日提现
     * @return int|string
     */
    public static function getOwnerCashedToday(){
        $today=SupplierCashManager::getToday();
        $data=UserCashregister::find()
            ->where(['status'=>self::STATUS_CASHED,'role_id'=>self::OWNER_ROLE])
            ->andwhere('handle_time >= ' . $today[0])
            ->andWhere('handle_time <= ' . $today[1])
            ->sum('cash_money');
        if ($data == null) {
            return 0;
        }
        return sprintf('%.2f', (float)$data / 100);
    }
    /**
     * 已提现总数
     * @return int|string
     */
    public static function getOwnerCashed(){
        $data=UserCashregister::find()
            ->where(['status'=>self::STATUS_CASHED,'role_id'=>self::OWNER_ROLE])
            ->count();

        return $data;
    }
    /**
     * 未提现总数
     * @return int|string
     */
    public static function getOwnerCashing(){

        return UserCashregister::find()
            ->where(['status'=>self::STATUS_CASHING,'role_id'=>self::OWNER_ROLE])
            ->count();
    }

    public static function  getCashListAll($where = [], $page = 1, $size = ModelService::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC')
    {

        $query = (new Query())
            ->from(self::USER_CASHREGISTER . ' as g')
            ->leftJoin(self::USER . ' u', 'g.uid = u.id')
            ->select(['g.id', 'g.cash_money', 'g.apply_time', 'u.aite_cube_no', 'u.nickname', 'g.uid', 'g.status', 'g.real_money', 'g.transaction_no', 'g.handle_time'])
            ->where($where)
            ->orderBy('g.handle_time');


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

}