<?php
namespace app\models;


use app\controllers\SupplierCashController;
use app\services\ModelService;
use yii\data\Pagination;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Query;

class OwnerCashManager extends ActiveRecord {


    const FIELDS_USER_MANAGER = [
        'id',
        'nickname',
        'aite_cube_no',
        'balance',
    ];
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

    /**
     * 用户账户管理
     * @param array $where
     * @param $select
     * @param int $page
     * @param int $size
     * @param string $orderBy
     * @return array|null|ActiveRecord[]
     */
    public static function pagination($where = [], $select, $page = 1, $size = ModelService::PAGE_SIZE_DEFAULT, $orderBy = 'id DESC'){

        $offset = ($page - 1) * $size;
        $userList = User::find()
            ->select($select)
            ->where($where)
            ->orderBy($orderBy)
            ->offset($offset)
            ->limit($size)
            ->asArray()
            ->all();

        if($userList){
            foreach ($userList as &$user){
                $user['balance']=sprintf('%.2f',(float)$user['balance']*0.01);
            }
        }else{
            return null;
        }
        $count=count($userList);
        return ModelService::pageDeal($userList,$count,$page,$size);



    }


    /**
     * 账户详情
     * @param $user_id
     * @return array|bool|null
     */
    public static function getOwnerView($user_id){
        $array=(new Query())
            ->from('user as u')
            ->select('u.nickname,u.id,u.balance,u.availableamount,sb.bankname,sb.bankcard,sb.username,sb.position,sb.bankbranch')
            ->leftJoin('user_bankinfo as ub', 'ub.uid=u.id')
            ->leftJoin('bankinfo_log as sb', 'sb.id=ub.log_id')
            ->where(['u.id'=>$user_id])
            ->one();
        $freeze_money = (new Query())->from('user_freezelist')->where(['uid' => $user_id])->andWhere(['role_id' => self::OWNER_ROLE])->andWhere(['status' => self::STATUS_CASHING])->sum('freeze_money');
        $cashed_money = (new Query())->from('user_cashregister')->where(['uid' => $user_id])->andWhere(['role_id' => self::OWNER_ROLE])->andWhere(['status' => 2])->sum('cash_money');
        if($array){
            $array['freeze_money'] = sprintf('%.2f', (float)$freeze_money * 0.01);
            $array['balance'] = sprintf('%.2f', (float)$array['balance'] * 0.01);
            $array['cashed_money'] = sprintf('%.2f', (float)$cashed_money * 0.01);
            $array['availableamount'] = sprintf('%.2f', (float)$array['availableamount'] * 0.01);
        }else{
            return null;
        }

        return $array;


    }

    /**
     * 业主提现列表
     * @param array $where
     * @param int $page
     * @param int $size
     * @param string $orderBy
     * @return array
     */
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

    /**
     * 业主提现详情
     * @param $transaction_no
     * @param $user_id
     * @return array|null|ActiveRecord
     */
    public static function GetCashView($transaction_no,$user_id)
    {

        $query =UserCashregister::find()
            ->asArray()
            ->where(['transaction_no' => $transaction_no, 'role_id' => self::OWNER_ROLE]);

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
        $bankcard = SupplierCashManager::GetBankcard($arr['bank_log_id']);
        $user = SupplierCashManager::GetUser($user_id);
        if (!$bankcard || !$user) {
            return null;
        }

        $arr['card_no'] = $bankcard['bankcard'];
        $arr['nickname'] = $user['nickname'];
        $arr['bank_name'] = $bankcard['bankname'];
        $arr['position'] = $bankcard['position'];
        $arr['bank_branch'] = $bankcard['bankbranch'];
        $arr['username'] = $bankcard['username'];
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
     * 处理业主提现
     * @param $cash_id
     * @param $status
     * @param $reason
     * @param $real_money
     * @return int
     */
    public static function doCash($cash_id, $status, $reason, $real_money){
        $owner_cash = (new Query())
            ->from(self::USER_CASHREGISTER)
            ->where(['id' => $cash_id, 'role_id' => self::OWNER_ROLE])
            ->select(['cash_money', 'uid', 'status', 'transaction_no'])
            ->one();
        $code=1000;
        $cash_money = $owner_cash['cash_money'];
        $user_id = (int)$owner_cash['uid'];
        $old_status = (int)$owner_cash['status'];
        $transaction_no = $owner_cash['transaction_no'];
        //初始状态不能为已经处理过的
        if (!$cash_money || !$user_id || !$old_status
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
            ->where(['transaction_no' => $transaction_no, 'role_id' => self::OWNER_ROLE])
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
                $user = User::find()->where(['uid' => $user_id])->one();
                if (!$user) {
                    return $code;
                }
                $user->balance += $cash_money;
                $user->availableamount += $cash_money;
                if(!$user->save(false)){
                    $trans->rollBack();
                    $code=500;
                    return $code;

                }
                //修改明细单数据
                $supplier_accessdetail->access_type = SupplierCashController::ACCESS_TYPE_REJECT;
                if(!$supplier_accessdetail->update(false)){
                    $trans->rollBack();
                    $code=500;
                    return $code;
                }



            }
            //提现成功
            if ($status == SupplierCashController::CASH_STATUS_DONE) {
                $supplier_accessdetail->access_type = SupplierCashController::ACCESS_TYPE_CASH_DONE;
                if(!$supplier_accessdetail->update(false)){
                    $trans->rollBack();
                    $code=500;
                    return $code;
                }
            }

            $trans->commit();
            $code=200;
            return $code;

        } catch (Exception $e) {
            $trans->rollBack();
            $code=500;
            return $code;
        }

    }


    /**
     * 冻结金额处理
     * @param $uid
     * @param $freeze_money
     * @param string $feeze_seaon
     * @param $role_id
     * @return int
     */
    public static function applyfreeze($uid,$freeze_money,$feeze_seaon='',$role_id){

        $model = new UserFreezelist();
        $model->uid = $uid;
        $model->role_id = $role_id;
        $model->freeze_money = $freeze_money;
        $model->freeze_reason =$feeze_seaon;
        $model->create_time = time();

        $transaction=\Yii::$app->db->beginTransaction();
        try{
            if(!$model->save(false)){
                $transaction->rollBack();
                $code=500;
                return $code;
            }
            if($role_id==6){
                $user=Supplier::find()->where(['uid'=>$uid])->one();
            }elseif($role_id==7){
                $user=User::find()->where(['id'=>$uid])->one();
            }
            $user->availableamount-=$freeze_money*100;
            $model->freeze_money=$freeze_money*100;
            if(!$user->update(false)){
                $transaction->rollBack();
                $code=500;
                return $code;
            }
            $transaction->commit();
            $code=200;
            return $code;
        }catch(Exception $e){
            $transaction->rollBack();
            $code=500;
            return $code;
        }
    }


    public static function freezeTaw($freeze_id,$role_id){
        $freeze=UserFreezelist::find()->where(['id'=>$freeze_id,'role_id'=>$role_id])->one();
        if($role_id==6){
            $user=Supplier::find()->where(['uid'=>$freeze->uid])->one();
        }elseif($role_id==7){
            $user=User::find()->where(['id'=>$freeze->uid])->one();
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            if($user){
                $user->availableamount+=$freeze->freeze_money;

                $freeze->status=self::STATUS_CASHING;
                if(!$user->update(false) || !$freeze->update(false)){
                    $transaction->rollBack();
                    $code=500;
                    return $code;
                }
                $transaction->commit();
                return 200;
            }else{
                $transaction->rollBack();
                $code=500;
                return $code;
            }

        }catch (Exception $e){
            $transaction->rollBack();
            $code=500;
            return $code;
        }
    }



}
